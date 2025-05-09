<?php
session_start();
require_once 'db.php';

if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['action'])) {
    if ($_POST['action'] === "ceo_login") {
        $username = trim($_POST['ceo-username'] ?? '');
        $password = trim($_POST['ceo-password'] ?? '');

        // Fetch user data based on user_id or email
        $query = "SELECT * FROM users WHERE id = ? OR email = ? LIMIT 1";
        $stmt = $conn->prepare($query);

        if (!$stmt) {
            echo json_encode(['redirect' => 'login.php?error=1']);
            exit;
        }

        $stmt->bind_param("ss", $username, $username);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result && $result->num_rows === 1) {
            $user = $result->fetch_assoc();

            // Check if the password is hashed
            if (password_verify($password, $user['password']) || $password === $user['password']) {
                session_regenerate_id(true); // Prevent session fixation

                $_SESSION['user_id'] = $user['id'];
                $_SESSION['name'] = $user['name'];
                $_SESSION['email'] = $user['email'];
                $_SESSION['department'] = $user['department'];
                $_SESSION['role'] = $user['role'];
                $_SESSION['dob'] = $user['dob'];
                $_SESSION['mobile'] = $user['mobile'];
                $_SESSION['photo'] = $user['photo'];
                $_SESSION['details'] = $user['details'];

                $redirect = (strtolower($user['role']) === 'ceo') ? 'dashboard.php' : 'engineer-dashboard.php';
            } else {
                $redirect = 'login.php?error=1'; // Incorrect password
            }
        } else {
            $redirect = 'login.php?error=1'; // User not found
        }

        echo json_encode(['redirect' => $redirect]);
        exit;
    }

    // Logout Function
    if ($_POST['action'] === "logout") {
        session_unset();
        session_destroy();
        echo json_encode(['redirect' => 'login.php']);
        exit;
    }

    if ($_POST['action'] === 'announce_scheme') {
        $title = $_POST['title'] ?? '';
        $description = $_POST['description'] ?? '';
        $region = $_POST['region'] ?? '';
        $assigned_engineer_id = $_POST['assigned_engineer_id'] ?? null;
        $deadline = $_POST['deadline'] ?? '';
        $budget = $_POST['budget'] ?? 0;
        $status = 'Ongoing'; // Default status
        $startdate = $_POST['startdate'] ?? '';
        $created_by_ceo_id = $_SESSION['user_id'] ?? null; // CEO ID from session
        $department = $_SESSION['department'] ?? ''; // Department from session

        if (empty($title) || empty($description) || empty($region) || empty($deadline) || empty($startdate) || !$created_by_ceo_id || empty($department)) {
            echo json_encode(['success' => false, 'message' => 'All fields are required.']);
            exit;
        }

        // Assuming a database connection is already established
        $query = "INSERT INTO schemes (title, description, region, assigned_engineer_id, deadline, budget, status, startdate, created_by_ceo_id, department) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($query);

        if (!$stmt) {
            echo json_encode(['success' => false, 'message' => 'Database error: ' . $conn->error]);
            exit;
        }

        $stmt->bind_param('sssisdssis', $title, $description, $region, $assigned_engineer_id, $deadline, $budget, $status, $startdate, $created_by_ceo_id, $department);

        if ($stmt->execute()) {
            echo json_encode(['success' => true, 'message' => 'Scheme announced successfully.']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Failed to announce scheme: ' . $stmt->error]);
        }

        $stmt->close();
        exit;
    }

    // Add this new condition inside the existing if block
    if ($_POST['action'] === 'delete_engineer') {
        $engineer_id = $_POST['id'] ?? null;

        if (!$engineer_id) {
            echo json_encode(['success' => false, 'message' => 'Engineer ID is required.']);
            exit;
        }

        // First check if the engineer is inactive
        $check_query = "
            SELECT e.id 
            FROM users e
            LEFT JOIN schemes s ON e.id = s.assigned_engineer_id
            WHERE e.id = ? 
            AND (s.assigned_engineer_id IS NULL OR s.status != 'ongoing')
        ";
        $check_stmt = $conn->prepare($check_query);
        $check_stmt->bind_param('i', $engineer_id);
        $check_stmt->execute();
        $check_result = $check_stmt->get_result();

        if ($check_result->num_rows === 0) {
            echo json_encode(['success' => false, 'message' => 'Cannot delete an active engineer.']);
            exit;
        }

        // Proceed with deletion
        $delete_query = "DELETE FROM users WHERE id = ? AND role = 'engineer'";
        $delete_stmt = $conn->prepare($delete_query);
        $delete_stmt->bind_param('i', $engineer_id);

        if ($delete_stmt->execute()) {
            echo json_encode(['success' => true, 'message' => 'Engineer deleted successfully.']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Failed to delete engineer.']);
        }
        exit;
    }

    if ($_POST['action'] === 'addTask') {
        $description = $_POST['description'];
        $schemeId = intval($_POST['schemeId']);
        $engineerId = intval($_POST['engineerId']);
        $status = 'ongoing'; // Set default status

        // Insert the task
        $query = "INSERT INTO tasks (scheme_id, engineer_id, description, status) VALUES (?, ?, ?, ?)";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("iiss", $schemeId, $engineerId, $description, $status);

        if ($stmt->execute()) {
            echo json_encode(['success' => true]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Database error']);
        }
    }

    if ($_POST['action'] === 'add_inventory_item') {
        $name = $_POST['name'];
        $quantity = $_POST['quantity'];
        $department = $_SESSION['department'];

        // Check if item already exists
        $checkQuery = "SELECT id FROM resources WHERE name = ? AND department = ?";
        $checkStmt = $conn->prepare($checkQuery);
        $checkStmt->bind_param("ss", $name, $department);
        $checkStmt->execute();
        $result = $checkStmt->get_result();

        if ($result->num_rows > 0) {
            echo json_encode([
                'success' => false,
                'message' => 'This item already exists in your department\'s inventory'
            ]);
            exit;
        }

        // Insert new item
        $insertQuery = "INSERT INTO resources (name, total_quantity, department) VALUES (?, ?, ?)";
        $insertStmt = $conn->prepare($insertQuery);
        $insertStmt->bind_param("sis", $name, $quantity, $department);
        
        if ($insertStmt->execute()) {
            echo json_encode([
                'success' => true,
                'message' => 'Item added successfully to inventory'
            ]);
        } else {
            echo json_encode([
                'success' => false,
                'message' => 'Failed to add item to inventory. Please try again.'
            ]);
        }
        exit;
    }

    if ($_POST['action'] === 'increase_inventory_quantity') {
        $itemId = $_POST['itemId'];
        $quantityToAdd = $_POST['quantity'];

        // Validate inputs
        if (empty($itemId) || empty($quantityToAdd) || $quantityToAdd <= 0) {
            echo json_encode(['success' => false, 'message' => 'Invalid input']);
            exit;
        }

        // Update the total_quantity in the database
        $query = "UPDATE resources SET total_quantity = total_quantity + ? WHERE id = ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("ii", $quantityToAdd, $itemId);

        if ($stmt->execute()) {
            echo json_encode(['success' => true]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Failed to update quantity']);
        }
        exit;
    }

    if ($_POST['action'] === 'update_profile') {
        $user_id = $_SESSION['user_id'];
        $name = $_POST['name'] ?? '';
        $email = $_POST['email'] ?? '';
        $phone = $_POST['phone'] ?? '';
        $current_password = $_POST['current_password'] ?? null;
        $new_password = $_POST['new_password'] ?? null;

        // Validate inputs
        if (empty($name) || empty($email) || empty($phone)) {
            echo json_encode(['success' => false, 'message' => 'Name, email, and phone are required.']);
            exit;
        }

        // Check if password change is requested
        if (!empty($current_password) && !empty($new_password)) {
            $query = "SELECT password FROM users WHERE id = ?";
            $stmt = $conn->prepare($query);
            $stmt->bind_param("i", $user_id);
            $stmt->execute();
            $result = $stmt->get_result();
            $user = $result->fetch_assoc();
            $stmt->close();

            // Verify current password
            if ($current_password!= $user['password']) {
                echo json_encode(['success' => false, 'message' => 'Current password is incorrect.']);
                exit;
            }

            

            // Update password in the database
            $query = "UPDATE users SET password = ? WHERE id = ?";
            $stmt = $conn->prepare($query);
            $stmt->bind_param("si", $new_password, $user_id);
            $stmt->execute();
            $stmt->close();
        }

        // Update other profile fields
        $query = "UPDATE users SET name = ?, email = ?, mobile = ? WHERE id = ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("sssi", $name, $email, $phone, $user_id);

        if ($stmt->execute()) {
            echo json_encode(['success' => true, 'message' => 'Profile updated successfully.']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Failed to update profile.']);
        }

        $stmt->close();
        exit;
    }

    if ($_POST['action'] === 'requestResources') {
        $resourceId = $_POST['resourceId'] ?? null;
        $quantity = $_POST['quantity'] ?? null;
        $taskId = $_POST['taskId'] ?? null;
        $engineerId = $_SESSION['user_id'] ?? null;
        $schemeId = $_POST['schemeId'] ?? null;

        // Validate input
        if (!$resourceId || !$quantity || !$engineerId) {
            echo json_encode(['success' => false, 'message' => 'Missing required fields.']);
            exit;
        }

        // Fetch resource name for the type field
        $type = '';
        $stmt = $conn->prepare("SELECT name FROM resources WHERE id = ?");
        $stmt->bind_param("i", $resourceId);
        $stmt->execute();
        $result = $stmt->get_result();
        if ($row = $result->fetch_assoc()) {
            $type = $row['name'];
        } else {
            echo json_encode(['success' => false, 'message' => 'Resource not found.']);
            exit;
        }
        $stmt->close();

        // Insert into resource_requests table
        $status = 'pending';
        $insert = $conn->prepare("INSERT INTO resource_requests (type, requested_quantity, engineer_id, scheme_id, status) VALUES (?, ?, ?, ?, ?)");
        $insert->bind_param("siiis", $type, $quantity, $engineerId, $schemeId, $status);

        if ($insert->execute()) {
            echo json_encode(['success' => true]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Failed to submit resource request.']);
        }
        $insert->close();
        exit;
    }

    if ($_POST['action'] === 'completeTask') {
        $taskId = intval($_POST['taskId'] ?? 0);
        if ($taskId <= 0) {
            echo json_encode(['success' => false, 'message' => 'Invalid task ID.']);
            exit;
        }
        $stmt = $conn->prepare("UPDATE tasks SET status = 'completed' WHERE id = ?");
        $stmt->bind_param("i", $taskId);
        if ($stmt->execute()) {
            echo json_encode(['success' => true]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Failed to update task status.']);
        }
        $stmt->close();
        exit;
    }

    if ($_POST['action'] === 'update_resource_request_status') {
        $request_id = intval($_POST['request_id'] ?? 0);
        $status = $_POST['status'] ?? '';
        if (!$request_id || !in_array($status, ['approved', 'rejected'])) {
            echo json_encode(['success' => false, 'message' => 'Invalid request.']);
            exit;
        }
        $stmt = $conn->prepare("UPDATE resource_requests SET status = ? WHERE id = ?");
        $stmt->bind_param("si", $status, $request_id);
        if ($stmt->execute()) {
            echo json_encode(['success' => true, 'message' => "Request status updated to $status."]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Failed to update request status.']);
        }
        $stmt->close();
        exit;
    }

}

if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['action']) && $_GET['action'] === 'get_scheme') {
    $scheme_id = $_GET['scheme_id'] ?? null;

    if (!$scheme_id) {
        echo json_encode(['success' => false, 'message' => 'Scheme ID is required.']);
        exit;
    }

    $query = "SELECT schemes.title, schemes.description, schemes.region, schemes.startdate, schemes.budget, schemes.deadline, users.name AS engineer FROM schemes LEFT JOIN users ON schemes.assigned_engineer_id = users.id WHERE schemes.id = ?";
    $stmt = $conn->prepare($query);

    if (!$stmt) {
        echo json_encode(['success' => false, 'message' => 'Database error: ' . $conn->error]);
        exit;
    }

    $stmt->bind_param('i', $scheme_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 1) {
        $scheme = $result->fetch_assoc();
        echo json_encode(['success' => true, 'data' => $scheme]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Scheme not found.']);
    }

    $stmt->close();
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['action']) && $_GET['action'] === 'get_engineer') {
    $engineer_id = $_GET['id'] ?? null;

    if (!$engineer_id) {
        header('Content-Type: application/json');
        echo json_encode(['success' => false, 'message' => 'Engineer ID is required.']);
        exit;
    }

    $query = "SELECT id, name, email, dob, mobile, details FROM users WHERE id = ?";
    $stmt = $conn->prepare($query);

    if (!$stmt) {
        header('Content-Type: application/json');
        echo json_encode(['success' => false, 'message' => 'Database error: ' . $conn->error]);
        exit;
    }

    $stmt->bind_param('i', $engineer_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 1) {
        $engineer = $result->fetch_assoc();
        header('Content-Type: application/json');
        echo json_encode(['success' => true, 'data' => $engineer]);
    } else {
        header('Content-Type: application/json');
        echo json_encode(['success' => false, 'message' => 'Engineer not found.']);
    }

    $stmt->close();
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id'])) {
    $id = $_POST['id'];
    $name = $_POST['name'];
    $email = $_POST['email'];
    $dob = $_POST['dob'];
    $mobile = $_POST['mobile'];
    $details = $_POST['details'];
    $photo_path = null;

    if (isset($_FILES['photo']) && $_FILES['photo']['error'] === UPLOAD_ERR_OK) {
        $photo_path = 'uploads/' . basename($_FILES['photo']['name']);
        move_uploaded_file($_FILES['photo']['tmp_name'], $photo_path);
    }

    $query = "
        UPDATE users 
        SET name = ?, email = ?, dob = ?, mobile = ?, details = ?, photo = COALESCE(?, photo) 
        WHERE id = ?
    ";
    $stmt = $conn->prepare($query);

    if (!$stmt) {
        header('Content-Type: application/json');
        echo json_encode(['success' => false, 'message' => 'Database error: ' . $conn->error]);
        exit;
    }

    $stmt->bind_param("ssssssi", $name, $email, $dob, $mobile, $details, $photo_path, $id);

    header('Content-Type: application/json');
    if ($stmt->execute()) {
        echo json_encode(['success' => true, 'message' => 'Engineer details updated successfully.']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Failed to update engineer details.']);
    }
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['name'])) {
    include 'db.php';

    $name = $_POST['name'];
    $email = $_POST['email'];
    $dob = $_POST['dob'];
    $mobile = $_POST['mobile'];
    $details = $_POST['details'];
    $role = 'engineer';
    $department = $_SESSION['department'];
    $password = $name; // Default password is the name

    // Handle photo upload
    $photo_path = 'uploads/' . basename($_FILES['photo']['name']);
    if (!move_uploaded_file($_FILES['photo']['tmp_name'], $photo_path)) {
        header('Content-Type: application/json');
        echo json_encode(['success' => false, 'message' => 'Failed to upload photo.']);
        exit;
    }

    // Insert into database
    $query = "
        INSERT INTO users (name, email, dob, mobile, details, photo, role, department, password) 
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)
    ";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("sssssssss", $name, $email, $dob, $mobile, $details, $photo_path, $role, $department, $password);

    header('Content-Type: application/json');
    if ($stmt->execute()) {
        echo json_encode(['success' => true, 'message' => 'Engineer added successfully.']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Failed to add engineer.']);
    }
    exit;
}
?>

