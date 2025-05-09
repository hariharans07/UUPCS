<?php
include 'db.php';
session_start();
if (empty($_SESSION['user_id'])) {
  header('Location: login.php');
  exit;
}

// Fetch user details from the database
$user_id = $_SESSION['user_id'];
$query = "SELECT name, email, mobile, created_at FROM users WHERE id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();
$stmt->close();

$user['created_at'] = date('Y-m-d', strtotime($user['created_at']));
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
          <a href="engineer-dashboard.php">
            <i class="fas fa-chart-bar"></i>
            <span class="nav-item">Dashboard</span>
          </a>
        </li>
        <li>
          <a href="engineer-profile.php" class="active">
            <i class="fas fa-user-circle"></i>
            <span class="nav-item">Profile</span>
          </a>
        </li>
        <li>
          <a href="engineer-schemes.php">
            <i class="fas fa-file-contract"></i>
            <span class="nav-item">My Schemes</span>
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
        <h1>My Profile</h1>
      </div>
      <div class="user-wrapper">
        <!-- Changed notification element to a Bootstrap trigger button -->
        <button type="button" class="btn notification-btn" data-bs-toggle="modal" data-bs-target="#notificationModal">
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
      <div class="profile-header">
        <div class="profile-image">
          <img src="assets/profile-logo.png" alt="Profile Picture" />
        </div>
        <div class="profile-info">
          <h2><?php echo htmlspecialchars($user['name']); ?></h2>
          <p>EID: <?php echo htmlspecialchars($user_id); ?></p>
          <span class="status-badge active">Active</span>
        </div>
      </div>

      <form class="engineer-form profile-form">
        <div class="form-row">
          <div class="form-group">
            <label for="full-name">Full Name</label>
            <input type="text" id="full-name" value="<?php echo htmlspecialchars($user['name']); ?>" />
          </div>
          <div class="form-group">
            <label for="email">Email</label>
            <input type="email" id="email" value="<?php echo htmlspecialchars($user['email']); ?>" />
          </div>
        </div>

        <div class="form-row">
          <div class="form-group">
            <label for="phone">Phone Number</label>
            <input type="tel" id="phone" value="<?php echo htmlspecialchars($user['mobile']); ?>" />
          </div>
          <div class="form-group">
            <label for="joining-date">Joining Date</label>
            <input type="date" id="joining-date" value="<?php echo htmlspecialchars($user['created_at']); ?>" readonly />
          </div>
        </div>

        <div class="form-row">
          <div class="form-group">
            <label for="current-password">Current Password</label>
            <div class="password-input">
              <input type="password" id="current-password" placeholder="Enter current password" />
              <i class="fas fa-eye toggle-password"></i>
            </div>
          </div>
          <div class="form-group">
            <label for="new-password">New Password</label>
            <div class="password-input">
              <input type="password" id="new-password" placeholder="Enter new password" />
              <i class="fas fa-eye toggle-password"></i>
            </div>
          </div>
        </div>

        <button type="submit" class="announce-btn">
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

    function handleSubmit(event) {
      event.preventDefault();
      document.querySelector(".loading-overlay").style.display = "flex";

      // Simulate form submission
      setTimeout(() => {
        document.querySelector(".loading-overlay").style.display = "none";
        // Add success message or redirect
      }, 1500);
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
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
  $(document).ready(function () {
    // Handle form submission
    $('.profile-form').on('submit', function (e) {
      e.preventDefault();

      const formData = {
        action: 'update_profile',
        name: $('#full-name').val(),
        email: $('#email').val(),
        phone: $('#phone').val(),
        current_password: $('#current-password').val() || null,
        new_password: $('#new-password').val() || null
      };

      $.ajax({
        url: 'backend.php',
        type: 'POST',
        data: formData,
        dataType: 'json',
        success: function (response) {
          if (response.success) {
            Swal.fire({
              icon: 'success',
              title: 'Success',
              text: response.message || 'Profile updated successfully.',
            }).then(() => {
              location.reload();
            });
          } else {
            Swal.fire({
              icon: 'error',
              title: 'Error',
              text: response.message || 'Failed to update profile.',
            });
          }
        },
        error: function () {
          Swal.fire({
            icon: 'error',
            title: 'Error',
            text: 'An error occurred. Please try again.',
          });
        }
      });
    });

    // Toggle password visibility
    $('.toggle-password').on('click', function () {
      const input = $(this).siblings('input');
      const type = input.attr('type') === 'password' ? 'text' : 'password';
      input.attr('type', type);
      $(this).toggleClass('fa-eye fa-eye-slash');
    });
  });
</script>
</body>
</html>