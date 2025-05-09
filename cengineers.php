<?php
include 'db.php';
session_start();
if (empty($_SESSION['user_id'])||$_SESSION['role'] != 'ceo') {
  header('Location: login.php');
  exit;
}

// Database connection


// Fetch engineers based on user's department
$user_department = $_SESSION['department']; // Assuming department_id is stored in session
$query = "
    SELECT e.id, e.name, 
           CASE 
               WHEN s.assigned_engineer_id IS NOT NULL AND s.status = 'ongoing' THEN 'Active'
               ELSE 'Inactive'
           END AS status
    FROM users e
    LEFT JOIN schemes s ON e.id = s.assigned_engineer_id
    WHERE e.department = ? AND e.role = 'engineer'
    GROUP BY e.id, e.name
";
$stmt = $conn->prepare($query);
$stmt->bind_param("s", $user_department);
$stmt->execute();
$result = $stmt->get_result();

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_engineer'])) {
  $name = $_POST['name'];
  $email = $_POST['email'];
  $dob = $_POST['dob'];
  $mobile = $_POST['mobile'];
  $details = $_POST['details'];
  $photo = $_FILES['photo'];
  $role = 'engineer';
  $department = $_SESSION['department'];
  $password = $name; // Default password is the name

  // Handle photo upload
  $photo_path = 'uploads/' . basename($photo['name']);
  move_uploaded_file($photo['tmp_name'], $photo_path);

  // Insert into database
  $query = "
        INSERT INTO users (name, email, dob, mobile, details, photo, role, department, password) 
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)
    ";
  $stmt = $conn->prepare($query);
  $stmt->bind_param("sssssssss", $name, $email, $dob, $mobile, $details, $photo_path, $role, $department, $password);
  $stmt->execute();

  // Redirect to refresh the page
  header('Location: cengineers.php');
  exit;
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>UUPCS Engineers</title>
  <link rel="stylesheet" href="style.css" />
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" />
  <!-- Added Bootstrap 5.3.2 CSS -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <!-- Added jQuery and Bootstrap Bundle JS -->
  <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
  <!-- Added SweetAlert2 -->
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
  <!-- Added DataTables CSS and JS -->
  <link rel="stylesheet" href="https://cdn.datatables.net/1.11.5/css/jquery.dataTables.min.css">
  <script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
</head>

<body>
  <div class="sidebar">
    <div class="logo">
      <i class="fas fa-city"></i>
      <span class="logo-name">UUPCS</span>
    </div>
    <nav>
      <ul>
        <li>
          <a href="dashboard.php">
            <i class="fas fa-chart-bar"></i>
            <span class="nav-item">Dashboard</span>
          </a>
        </li>
        <li>
          <a href="schemes.php">
            <i class="fas fa-file-contract"></i>
            <span class="nav-item">Schemes</span>
          </a>
        </li>
        <li>
          <a href="cengineers.php" class="active">
            <i class="fas fa-hard-hat"></i>
            <span class="nav-item">Engineers</span>
          </a>
        </li>
        <li>
          <a href="inventory.php">
            <i class="fas fa-warehouse"></i>
            <span class="nav-item">Inventory</span>
          </a>
        </li>
        <li>
          <a href="requests.php">
            <i class="fas fa-envelope-open-text"></i>
            <span class="nav-item">Requests</span>
          </a>
        </li>
      </ul>
    </nav>
  </div>

  <div class="main-content">
    <header>
      <div class="header-content">
        <div class="toggle">
          <i class="fas fa-bars menu-icon"></i>
        </div>
        <h1>Engineers</h1>
      
      
      <!-- Added dropdown button -->
      <div class="dropdown" style="position:absolute;  right:25px;">
        <button class="btn btn-secondary dropdown-toggle rounded-circle" type="button" id="userDropdown"
          data-bs-toggle="dropdown" aria-expanded="false">
          <i class="fas fa-user"></i>
        </button>
        <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="userDropdown">
          <li><a class="dropdown-item" href="#" id="changePassword">Change Password</a></li>
          <li><a class="dropdown-item" href="#" id="logoutBtn">Logout</a></li>
        </ul>
      </div>
      </div>
    </header>

    <div class="engineers-container">
      <div class="engineers-header">
        <h2>Project Engineers</h2>
        <button class="add-engineer-btn">
          <i class="fas fa-plus"></i> Add Engineer
        </button>
      </div>

      <div class="schemes-table">
        <table id="engineerstable" class="table table-striped w-100 text-center">
          <thead>
            <tr>
              <th class="text-center">EID</th>
              <th class="text-center">Engineer Name</th>
              <th class="text-center">Status</th>
              <th class="text-center">Action</th>
            </tr>
          </thead>
          <tbody>
            <?php while ($row = $result->fetch_assoc()): ?>
              <tr>
                <td class="text-center"><?php echo htmlspecialchars($row['id']); ?></td>
                <td class="text-center"><?php echo htmlspecialchars($row['name']); ?></td>
                <td class="text-center">
                  <span class="status-badge <?php echo strtolower($row['status']); ?>">
                    <?php echo htmlspecialchars($row['status']); ?>
                  </span>
                </td>
                <td class="text-center action-buttons">
                  <button class="edit-btn" data-id="<?php echo htmlspecialchars($row['id']); ?>">
                    <i class="fas fa-edit"></i>
                  </button>
                  <?php if ($row['status'] === 'Inactive'): ?>
                    <button class="delete-btn" data-id="<?php echo htmlspecialchars($row['id']); ?>">
                      <i class="fas fa-trash"></i>
                    </button>
                  <?php endif; ?>
                </td>
              </tr>
            <?php endwhile; ?>
          </tbody>
        </table>
      </div>
    </div>
  </div>

  <!-- Add Engineer Modal -->
  <div id="addEngineerModal" class="modal">
    <div class="modal-content">
      <span class="close-modal">&times;</span>
      <h2>Add New Engineer</h2>
      <form id="addEngineerForm" class="engineer-form" enctype="multipart/form-data">
        <div class="form-group">
          <label for="eng-name">Name</label>
          <input type="text" id="eng-name" name="name" placeholder="Enter engineer name" required />
        </div>
        <div class="form-group">
          <label for="eng-email">Email</label>
          <input type="email" id="eng-email" name="email" placeholder="Enter email address" required />
        </div>
        <div class="form-group">
          <label for="eng-dob">Date of Birth</label>
          <input type="date" id="eng-dob" name="dob" required />
        </div>
        <div class="form-group">
          <label for="eng-mobile">Mobile Number</label>
          <input type="tel" id="eng-mobile" name="mobile" placeholder="Enter mobile number" required />
        </div>
        <div class="form-group">
          <label for="eng-photo">Photo</label>
          <input type="file" id="eng-photo" name="photo" accept="image/*" required />
        </div>
        <div class="form-group">
          <label for="eng-details">Details</label>
          <textarea id="eng-details" name="details" rows="3" placeholder="Enter additional details"></textarea>
        </div>
        <button type="submit" class="add-engineer-btn">
          <i class="fas fa-plus"></i> Add Engineer
        </button>
      </form>
    </div>
  </div>

  <!-- Edit Engineer Modal -->
  <div id="editEngineerModal" class="modal">
    <div class="modal-content">
      <span class="close-modal">&times;</span>
      <h2>Edit Engineer</h2>
      <form id="editEngineerForm" class="engineer-form" enctype="multipart/form-data">
        <input type="hidden" id="edit-eng-id" name="id" />
        <div class="form-group">
          <label for="edit-eng-name">Name</label>
          <input type="text" id="edit-eng-name" name="name" placeholder="Enter engineer name" required />
        </div>
        <div class="form-group">
          <label for="edit-eng-email">Email</label>
          <input type="email" id="edit-eng-email" name="email" placeholder="Enter email address" required />
        </div>
        <div class="form-group">
          <label for="edit-eng-dob">Date of Birth</label>
          <input type="date" id="edit-eng-dob" name="dob" required />
        </div>
        <div class="form-group">
          <label for="edit-eng-mobile">Mobile Number</label>
          <input type="tel" id="edit-eng-mobile" name="mobile" placeholder="Enter mobile number" required />
        </div>
        <div class="form-group">
          <label for="edit-eng-details">Details</label>
          <textarea id="edit-eng-details" name="details" rows="3" placeholder="Enter additional details"></textarea>
        </div>
        <button type="submit" class="add-engineer-btn">
          <i class="fas fa-save"></i> Save Changes
        </button>
      </form>
    </div>
  </div>

  <script>
    const toggle = document.querySelector(".toggle");
    const sidebar = document.querySelector(".sidebar");

    toggle.addEventListener("click", () => {
      sidebar.classList.toggle("close");
    });
    $(document).ready(function () {
      $('#engineerstable').DataTable();
    });

    // Add Engineer Modal
    const addModal = document.getElementById("addEngineerModal");
    const addBtn = document.querySelector(".add-engineer-btn");
    const closeBtn = document.querySelector(".close-modal");

    addBtn.onclick = (e) => {
      e.preventDefault();
      addModal.style.display = "flex";
    };

    closeBtn.onclick = () => {
      addModal.style.display = "none";
    };

    window.onclick = (e) => {
      if (e.target == addModal) {
        addModal.style.display = "none";
      }
    };

    // AJAX call to save engineer
    $('#addEngineerForm').submit(function (e) {
      e.preventDefault();

      const formData = new FormData(this);

      $.ajax({
        url: 'backend.php',
        type: 'POST',
        data: formData,
        processData: false,
        contentType: false,
        dataType: 'json', // Ensure the response is parsed as JSON
        success: function (response) {
          if (response.success) {
            Swal.fire({
              icon: 'success',
              title: 'Success',
              text: response.message, // Display the success message
            }).then(() => {
              location.reload(); // Reload the page to reflect changes
            });
          } else {
            Swal.fire({
              icon: 'error',
              title: 'Error',
              text: response.message, // Display the error message
            });
          }
        },
        error: function (xhr, status, error) {
          Swal.fire({
            icon: 'error',
            title: 'Error',
            text: 'An error occurred: ' + error, // Display the error details
          });
        }
      });
    });

    $('#logoutBtn').click(function (e) {
      e.preventDefault();

      $.ajax({
        url: 'backend.php',
        type: 'POST',
        data: { action: "logout" },
        dataType: 'json',
        success: function (response) {
          if (response.redirect) {
            window.location.href = response.redirect;
          } else {
            alert("Logout failed. Please try again.");
          }
        },
        error: function () {
          alert("An error occurred. Please try again.");
        }
      });
    });

    // Edit Engineer Modal
    const editModal = document.getElementById("editEngineerModal");
    const closeEditBtn = editModal.querySelector(".close-modal");

    // Open modal and populate fields with existing data
    $('.edit-btn').click(function () {
      const engineerId = $(this).data('id'); // Get the engineer ID from the button's data-id attribute

      $.ajax({
        url: 'backend.php',
        type: 'GET',
        data: { action: 'get_engineer', id: engineerId },
        dataType: 'json',
        success: function (response) {
          if (response.success) {
            const engineer = response.data;
            $('#edit-eng-id').val(engineer.id);
            $('#edit-eng-name').val(engineer.name);
            $('#edit-eng-email').val(engineer.email);
            $('#edit-eng-dob').val(engineer.dob);
            $('#edit-eng-mobile').val(engineer.mobile);
            $('#edit-eng-details').val(engineer.details);
            editModal.style.display = "flex";
          } else {
            Swal.fire({
              icon: 'error',
              title: 'Error',
              text: response.message,
            });
          }
        },
        error: function () {
          Swal.fire({
            icon: 'error',
            title: 'Error',
            text: 'An error occurred while fetching engineer details.',
          });
        }
      });
    });

    closeEditBtn.onclick = () => {
      editModal.style.display = "none";
    };

    window.onclick = (e) => {
      if (e.target == editModal) {
        editModal.style.display = "none";
      }
    };

    // Submit edited engineer details
    $('#editEngineerForm').submit(function (e) {
      e.preventDefault();

      const formData = new FormData(this);

      $.ajax({
        url: 'backend.php',
        type: 'POST',
        data: formData,
        processData: false,
        contentType: false,
        dataType: 'json',
        success: function (response) {
          if (response.success) {
            Swal.fire({
              icon: 'success',
              title: 'Success',
              text: response.message,
            }).then(() => {
              location.reload(); // Reload the page to reflect changes
            });
          } else {
            Swal.fire({
              icon: 'error',
              title: 'Error',
              text: response.message,
            });
          }
        },
        error: function () {
          Swal.fire({
            icon: 'error',
            title: 'Error',
            text: 'An error occurred while saving changes.',
          });
        }
      });
    });

    // Delete Engineer functionality
    $('.delete-btn').click(function () {
      const engineerId = $(this).data('id');

      Swal.fire({
        title: 'Are you sure?',
        text: "This action cannot be undone!",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#3085d6',
        confirmButtonText: 'Yes, delete it!'
      }).then((result) => {
        if (result.isConfirmed) {
          $.ajax({
            url: 'backend.php',
            type: 'POST',
            data: {
              action: 'delete_engineer',
              id: engineerId
            },
            dataType: 'json',
            success: function (response) {
              if (response.success) {
                Swal.fire(
                  'Deleted!',
                  response.message,
                  'success'
                ).then(() => {
                  location.reload();
                });
              } else {
                Swal.fire(
                  'Error!',
                  response.message,
                  'error'
                );
              }
            },
            error: function () {
              Swal.fire(
                'Error!',
                'An error occurred while deleting the engineer.',
                'error'
              );
            }
          });
        }
      });
    });
  </script>
</body>

</html>