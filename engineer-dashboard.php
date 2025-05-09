<?php
include 'db.php';
session_start();
if (empty($_SESSION['user_id'])) {
  header('Location: login.php');
  exit;
}
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
          <a href="engineer-dashboard.php" class="active">
            <i class="fas fa-chart-bar"></i>
            <span class="nav-item">Dashboard</span>
          </a>
        </li>
        <li>
          <a href="engineer-profile.php">
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
        <h1>Engineer Dashboard</h1>
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

    <div class="dashboard-stats">
      <div class="stat-card">
        <i class="fas fa-tasks"></i>
        <div class="stat-info">
          <h3>Active Tasks</h3>
          <p>5</p>
        </div>
      </div>
      <div class="stat-card">
        <i class="fas fa-project-diagram"></i>
        <div class="stat-info">
          <h3>Current Projects</h3>
          <p>2</p>
        </div>
      </div>
      <div class="stat-card">
        <i class="fas fa-check-circle"></i>
        <div class="stat-info">
          <h3>Completed Tasks</h3>
          <p>12</p>
        </div>
      </div>
    </div>

    <div class="scheme-form-container">
      <h2>Recent Activities</h2>
      <div class="activity-list">
        <!-- Activity items will go here -->
      </div>
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
</body>

</html>