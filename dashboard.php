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
                <!-- Changed notification element to a Bootstrap trigger button -->
                <button type="button" class="btn notification-btn" data-bs-toggle="modal"
                    data-bs-target="#notificationModal">
                    <i class="fas fa-bell"></i>
                    <span class="notification-badge">3</span>
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
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="notificationModalLabel">Scheme Overlap Alert</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body text-danger">
                    <p><strong>Scheme ID: S007 - Road Development Project</strong> overlaps with:</p>
                    <p style="margin-top: 15px">
                        Water Department's Scheme:
                        <strong>Water Pipeline Installation (WD-003)</strong>
                    </p>
                    <p style="margin-top: 10px">
                        <i class="fas fa-calendar-alt"></i> Same time period: March 2024 - June 2024
                    </p>
                    <p>
                        <i class="fas fa-map-marker-alt"></i> Same region: Sector 7, Civil Lines
                    </p>
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
    </script>
</body>
</html>