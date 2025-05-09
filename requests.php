<?php
require_once 'db.php'; // Include database connection file
session_start();
if (empty($_SESSION['user_id'])||$_SESSION['role'] != 'ceo') {
    header('Location: login.php');
    exit;
}

// Fetch pending resource requests with engineer and scheme info
$department = $_SESSION['department'];
$query = "
    SELECT rr.id, rr.type, rr.requested_quantity, rr.status, u.name AS engineer_name, s.title AS scheme_title
    FROM resource_requests rr
    LEFT JOIN users u ON rr.engineer_id = u.id
    LEFT JOIN schemes s ON rr.scheme_id = s.id
    WHERE rr.status = 'pending'
      AND (s.department = ? OR s.department = 'common')
    ORDER BY rr.id DESC
";
$stmt = $conn->prepare($query);
$stmt->bind_param('s', $department);
$stmt->execute();
$result = $stmt->get_result();
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>UUPCS Requests</title>
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
                    <a href="requests.php" class="active">
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
                <h1>Requests</h1>
                <div class="dropdown" style="position:absolute; right:25px;">
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
        <div class="container mt-4">
            <h2>Resource Requests</h2>
            <div class="table-responsive">
                <table class="table table-striped" id="requestsTable">
                    <thead>
                        <tr>
                            <th>Request ID</th>
                            <th>Type</th>
                            <th>Requested Quantity</th>
                            <th>Engineer</th>
                            <th>Scheme</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($row = $result->fetch_assoc()): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($row['id']); ?></td>
                            <td><?php echo htmlspecialchars($row['type']); ?></td>
                            <td><?php echo htmlspecialchars($row['requested_quantity']); ?></td>
                            <td><?php echo htmlspecialchars($row['engineer_name']); ?></td>
                            <td><?php echo htmlspecialchars($row['scheme_title']); ?></td>
                            <td>
                                <button class="btn btn-success btn-sm" onclick="approveRequest(<?php echo $row['id']; ?>)">Approve</button>
                                <button class="btn btn-danger btn-sm" onclick="rejectRequest(<?php echo $row['id']; ?>)">Reject</button>
                            </td>
                        </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <script>
        const toggle = document.querySelector(".toggle");
        const sidebar = document.querySelector(".sidebar");

        toggle.addEventListener("click", () => {
            sidebar.classList.toggle("close");
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

        let requestsTable;
        $(document).ready(function () {
            requestsTable = $('#requestsTable').DataTable();
        });

        function approveRequest(requestId) {
            Swal.fire({
                title: 'Approve Request?',
                text: "Are you sure you want to approve this resource request?",
                icon: 'question',
                showCancelButton: true,
                confirmButtonText: 'Approve',
                cancelButtonText: 'Cancel'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: 'backend.php',
                        type: 'POST',
                        data: {
                            action: 'update_resource_request_status',
                            request_id: requestId,
                            status: 'approved'
                        },
                        dataType: 'json',
                        success: function (response) {
                            if (response.success) {
                                Swal.fire('Approved!', response.message, 'success').then(() => {
                                    location.reload();
                                });
                            } else {
                                Swal.fire('Error', response.message || 'Failed to approve request.', 'error');
                            }
                        },
                        error: function () {
                            Swal.fire('Error', 'An error occurred while processing the request.', 'error');
                        }
                    });
                }
            });
        }

        function rejectRequest(requestId) {
            Swal.fire({
                title: 'Reject Request?',
                text: "Are you sure you want to reject this resource request?",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Reject',
                cancelButtonText: 'Cancel'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: 'backend.php',
                        type: 'POST',
                        data: {
                            action: 'update_resource_request_status',
                            request_id: requestId,
                            status: 'rejected'
                        },
                        dataType: 'json',
                        success: function (response) {
                            if (response.success) {
                                Swal.fire('Rejected!', response.message, 'success').then(() => {
                                    location.reload();
                                });
                            } else {
                                Swal.fire('Error', response.message || 'Failed to reject request.', 'error');
                            }
                        },
                        error: function () {
                            Swal.fire('Error', 'An error occurred while processing the request.', 'error');
                        }
                    });
                }
            });
        }
    </script>
</body>
</html>
