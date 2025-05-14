<?php
require_once 'db.php'; // Include database connection file
session_start();
if (empty($_SESSION['user_id'])||$_SESSION['role'] != 'ceo') {
    header('Location: login.php');
    exit;
}

// Fetch engineers dynamically based on session department
$department = $_SESSION['department'];
$engineers = [];

// Assuming a database connection is already established
$query = "SELECT id, name FROM users WHERE department = ? AND role = 'engineer'";
$stmt = $conn->prepare($query);
$stmt->bind_param('s', $department);
$stmt->execute();
$result = $stmt->get_result();

while ($row = $result->fetch_assoc()) {
    $engineers[] = $row;
}

$stmt->close();

// Fetch overlapping schemes for notification modal
$overlap_alerts = [];
$ongoing_query = "SELECT * FROM schemes WHERE department = ? AND status = 'ongoing'";
$ongoing_stmt = $conn->prepare($ongoing_query);
$ongoing_stmt->bind_param('s', $department);
$ongoing_stmt->execute();
$ongoing_result = $ongoing_stmt->get_result();

while ($current_scheme = $ongoing_result->fetch_assoc()) {
    $overlap_query = "SELECT * FROM schemes WHERE department != ? AND status = 'ongoing'
        AND region = ?
        AND (
            (startdate <= ? AND deadline >= ?) OR
            (startdate <= ? AND deadline >= ?) OR
            (startdate >= ? AND deadline <= ?)
        )";
    $overlap_stmt = $conn->prepare($overlap_query);
    $overlap_stmt->bind_param(
        'ssssssss',
        $department,
        $current_scheme['region'],
        $current_scheme['deadline'], $current_scheme['startdate'],
        $current_scheme['deadline'], $current_scheme['deadline'],
        $current_scheme['startdate'], $current_scheme['deadline']
    );
    $overlap_stmt->execute();
    $overlap_result = $overlap_stmt->get_result();

    while ($other_scheme = $overlap_result->fetch_assoc()) {
        // Check if this pair is already in collaborations table (any direction, any status)
        $check_collab = $conn->prepare(
            "SELECT id FROM collaborations WHERE 
                ((scheme1_id = ? AND scheme2_id = ?) OR (scheme1_id = ? AND scheme2_id = ?))"
        );
        $scheme1_id = $current_scheme['id'];
        $scheme2_id = $other_scheme['id'];
        $check_collab->bind_param('iiii', $scheme1_id, $scheme2_id, $scheme2_id, $scheme1_id);
        $check_collab->execute();
        $check_collab->store_result();
        if ($check_collab->num_rows === 0) {
            $overlap_alerts[] = [
                'current' => $current_scheme,
                'other' => $other_scheme
            ];
        }
        $check_collab->close();
    }
    $overlap_stmt->close();
}
$ongoing_stmt->close();

// Fetch collaboration requests for this CEO
$collab_requests = [];
$my_ceo_id = $_SESSION['user_id'];
$collab_req_query = "SELECT c.id, c.scheme1_id, c.scheme2_id, c.initiator_ceo_id, c.receiver_ceo_id, c.status, 
    s1.title AS scheme1_title, s2.title AS scheme2_title,
    s1.department AS scheme1_department, s2.department AS scheme2_department
    FROM collaborations c
    JOIN schemes s1 ON c.scheme1_id = s1.id
    JOIN schemes s2 ON c.scheme2_id = s2.id
    WHERE c.receiver_ceo_id = ? AND c.status = 'requested'";
$collab_req_stmt = $conn->prepare($collab_req_query);
$collab_req_stmt->bind_param('i', $my_ceo_id);
$collab_req_stmt->execute();
$collab_req_result = $collab_req_stmt->get_result();
while ($row = $collab_req_result->fetch_assoc()) {
    $collab_requests[] = $row;
}
$collab_req_stmt->close();
error_log("Collab requests count: " . count($collab_requests)); // Debug line
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>UUPCS Dashboard</title>
    <link rel="stylesheet" href="style.css" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" />
    <!-- Added Bootstrap 5.3.2 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Added jQuery and Bootstrap Bundle JS -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <!-- Added SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
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
                    <a href="dashboard.php" class="active">
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
                    <a href="cengineers.php">
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
                <h1>Dashboard</h1>
            </div>
            <div class="user-wrapper">
                <!-- Notification bell button: show badge only if overlaps exist -->
                <button type="button" class="btn notification-btn" data-bs-toggle="modal"
                    data-bs-target="#notificationModal">
                    <i class="fas fa-bell"></i>
                    <?php if (count($overlap_alerts) > 0): ?>
                        <span class="notification-badge"><?php echo count($overlap_alerts); ?></span>
                    <?php endif; ?>
                </button>
                <div class="dropdown">
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

        <div class="scheme-form-container">
            <h2>New Scheme Details</h2>
            <form class="scheme-form" onsubmit="handleSubmit(event)">
                <div class="form-group">
                    <label for="scheme-name">Scheme Name</label>
                    <input type="text" id="scheme-name" placeholder="Enter scheme name" />
                </div>

                <div class="form-group">
                    <label for="scheme-region">Region</label>
                    <input type="text" id="scheme-region" placeholder="Enter region" required />
                </div>

                <div class="form-group">
                    <label for="scheme-budget">Scheme Budget (in Rs.)</label>
                    <input type="number" id="scheme-budget" placeholder="Enter budget amount" />
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label for="start-date">Start Date</label>
                        <input type="date" id="start-date" />
                    </div>

                    <div class="form-group">
                        <label for="deadline">Deadline</label>
                        <input type="date" id="deadline" />
                    </div>
                </div>

                <div class="form-group">
                    <label for="engineer">Assign Engineer</label>
                    <select id="engineer" required>
                        <option value="">Select Engineer</option>
                        <?php foreach ($engineers as $engineer): ?>
                            <option value="<?php echo htmlspecialchars($engineer['id']); ?>">
                                <?php echo htmlspecialchars($engineer['name']); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="form-group">
                    <label for="description">Description</label>
                    <textarea id="description" rows="4" placeholder="Enter scheme description"></textarea>
                </div>

                <button type="submit" class="announce-btn">
                    <i class="fas fa-bullhorn"></i> Announce Scheme
                </button>
            </form>
        </div>
    </div>

    <!-- Updated Bootstrap Notification Modal -->
    <div class="modal fade" id="notificationModal" tabindex="-1" aria-labelledby="notificationModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="notificationModalLabel">Scheme Overlap Alert</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body text-danger">
                    <?php if (!empty($collab_requests)): ?>
                        <div class="mb-4">
                            <h6>Collaboration Requests</h6>
                            <?php foreach ($collab_requests as $req): ?>
                                <div class="border p-2 mb-2">
                                    <p>
                                        <strong>From:</strong> <?php echo htmlspecialchars($req['scheme1_department']); ?> <br>
                                        <strong>Scheme:</strong> <?php echo htmlspecialchars($req['scheme1_title']); ?> <br>
                                        <strong>With your scheme:</strong> <?php echo htmlspecialchars($req['scheme2_title']); ?>
                                    </p>
                                    <button class="btn btn-success btn-sm collab-action-btn" data-collab-id="<?php echo $req['id']; ?>" data-action="approved">Accept</button>
                                    <button class="btn btn-danger btn-sm collab-action-btn" data-collab-id="<?php echo $req['id']; ?>" data-action="rejected">Reject</button>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                    <?php if (empty($overlap_alerts) && empty($collab_requests)): ?>
                        <p>No overlapping schemes or collaboration requests found.</p>
                    <?php endif; ?>
                    <?php if (!empty($overlap_alerts)): ?>
                        <?php foreach ($overlap_alerts as $alert): ?>
                            <div class="mb-4 border-bottom pb-3">
                                <h6>Your Department's Scheme:</h6>
                                <ul>
                                    <li><strong>ID:</strong> <?php echo htmlspecialchars($alert['current']['id']); ?></li>
                                    <li><strong>Title:</strong> <?php echo htmlspecialchars($alert['current']['title']); ?></li>
                                    <li><strong>Department:</strong> <?php echo htmlspecialchars($alert['current']['department']); ?></li>
                                    <li><strong>Description:</strong> <?php echo htmlspecialchars($alert['current']['description']); ?></li>
                                    <li><strong>Region:</strong> <?php echo htmlspecialchars($alert['current']['region']); ?></li>
                                    <li><strong>Assigned Engineer ID:</strong> <?php echo htmlspecialchars($alert['current']['assigned_engineer_id']); ?></li>
                                    <li><strong>Start Date:</strong> <?php echo htmlspecialchars($alert['current']['startdate']); ?></li>
                                    <li><strong>Deadline:</strong> <?php echo htmlspecialchars($alert['current']['deadline']); ?></li>
                                    <li><strong>Budget:</strong> <?php echo htmlspecialchars($alert['current']['budget']); ?></li>
                                    <li><strong>Status:</strong> <?php echo htmlspecialchars($alert['current']['status']); ?></li>
                                    <li><strong>Created By CEO ID:</strong> <?php echo htmlspecialchars($alert['current']['created_by_ceo_id']); ?></li>
                                </ul>
                                <h6>Overlapping Scheme from Another Department:</h6>
                                <ul>
                                    <li><strong>ID:</strong> <?php echo htmlspecialchars($alert['other']['id']); ?></li>
                                    <li><strong>Title:</strong> <?php echo htmlspecialchars($alert['other']['title']); ?></li>
                                    <li><strong>Department:</strong> <?php echo htmlspecialchars($alert['other']['department']); ?></li>
                                    <li><strong>Description:</strong> <?php echo htmlspecialchars($alert['other']['description']); ?></li>
                                    <li><strong>Region:</strong> <?php echo htmlspecialchars($alert['other']['region']); ?></li>
                                    <li><strong>Assigned Engineer ID:</strong> <?php echo htmlspecialchars($alert['other']['assigned_engineer_id']); ?></li>
                                    <li><strong>Start Date:</strong> <?php echo htmlspecialchars($alert['other']['startdate']); ?></li>
                                    <li><strong>Deadline:</strong> <?php echo htmlspecialchars($alert['other']['deadline']); ?></li>
                                    <li><strong>Budget:</strong> <?php echo htmlspecialchars($alert['other']['budget']); ?></li>
                                    <li><strong>Status:</strong> <?php echo htmlspecialchars($alert['other']['status']); ?></li>
                                    <li><strong>Created By CEO ID:</strong> <?php echo htmlspecialchars($alert['other']['created_by_ceo_id']); ?></li>
                                </ul>
                                <button class="btn btn-primary collaborate-btn mt-2"
                                    data-scheme1="<?php echo htmlspecialchars($alert['current']['id']); ?>"
                                    data-scheme2="<?php echo htmlspecialchars($alert['other']['id']); ?>"
                                    data-initiator="<?php echo htmlspecialchars($alert['current']['created_by_ceo_id']); ?>"
                                    data-receiver="<?php echo htmlspecialchars($alert['other']['created_by_ceo_id']); ?>">
                                    Collaborate
                                </button>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <!-- Add Loading Overlay -->
    <div class="loading-overlay">
        <div class="loader"></div>
    </div>

    <script>
        const toggle = document.querySelector(".toggle");
        const sidebar = document.querySelector(".sidebar");

        toggle.addEventListener("click", () => {
            sidebar.classList.toggle("close");
        });

        function handleSubmit(event) {
            event.preventDefault();
            document.querySelector(".loading-overlay").style.display = "flex";

            const formData = {
                action: "announce_scheme",
                title: document.getElementById("scheme-name").value,
                description: document.getElementById("description").value,
                region: document.getElementById("scheme-region").value,
                assigned_engineer_id: document.getElementById("engineer").value,
                deadline: document.getElementById("deadline").value,
                budget: document.getElementById("scheme-budget").value,
                startdate: document.getElementById("start-date").value
            };

            $.ajax({
                url: 'backend.php',
                type: 'POST',
                data: formData,
                dataType: 'json',
                success: function(response) {
                    document.querySelector(".loading-overlay").style.display = "none";
                    if (response.success) {
                        Swal.fire({
                            title: 'Success!',
                            text: `${formData.title} is announced`,
                            icon: 'success',
                            confirmButtonText: 'OK'
                        }).then(() => {
                            location.reload();
                        });
                    } else {
                        alert(response.message);
                    }
                },
                error: function() {
                    document.querySelector(".loading-overlay").style.display = "none";
                    alert("An error occurred. Please try again.");
                }
            });
        }

        // Logout script
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

        // Collaborate button handler
        $(document).on('click', '.collaborate-btn', function () {
            var scheme1_id = $(this).data('scheme1');
            var scheme2_id = $(this).data('scheme2');
            var initiator_ceo_id = $(this).data('initiator');
            var receiver_ceo_id = $(this).data('receiver');
            var btn = $(this);

            btn.prop('disabled', true).text('Requesting...');

            $.ajax({
                url: 'backend.php',
                type: 'POST',
                data: {
                    action: 'collaborate',
                    scheme1_id: scheme1_id,
                    scheme2_id: scheme2_id,
                    initiator_ceo_id: initiator_ceo_id,
                    receiver_ceo_id: receiver_ceo_id
                },
                dataType: 'json',
                success: function (response) {
                    if (response.success) {
                        btn.removeClass('btn-primary').addClass('btn-success').text('Requested');
                    } else {
                        btn.prop('disabled', false).text('Collaborate');
                        alert(response.message || 'Collaboration request failed.');
                    }
                },
                error: function () {
                    btn.prop('disabled', false).text('Collaborate');
                    alert('An error occurred. Please try again.');
                }
            });
        });

        // Accept/Reject collaboration request
        $(document).on('click', '.collab-action-btn', function () {
            var collabId = $(this).data('collab-id');
            var action = $(this).data('action');
            var btn = $(this);
            btn.prop('disabled', true);

            $.ajax({
                url: 'backend.php',
                type: 'POST',
                data: {
                    action: 'collab_update_status',
                    collab_id: collabId,
                    status: action
                },
                dataType: 'json',
                success: function (response) {
                    if (response.success) {
                        btn.closest('.border').fadeOut();
                        if (response.swal) {
                            Swal.fire({
                                icon: response.swal.icon,
                                title: response.swal.title,
                                text: response.swal.text
                            });
                        }
                    } else {
                        btn.prop('disabled', false);
                        alert(response.message || 'Failed to update collaboration status.');
                    }
                },
                error: function () {
                    btn.prop('disabled', false);
                    alert('An error occurred. Please try again.');
                }
            });
        });
    </script>
</body>
</html>