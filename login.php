<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>UUPCS Login</title>
  <link rel="stylesheet" href="style.css" />
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" />
  <!-- Added Bootstrap 5.3.2 CSS -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
  <!-- Added Bootstrap Bundle JS -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</head>

<body class="login-body">
  <div class="container">
    <div class="login-container">
      <div class="scheme-form-container login-unified-card" style="justify-content: center;">
        <div class="main-header">
          <div class="logo-section">
            <i class="fas fa-city"></i>
            <h1>UUPCS Login</h1>
          </div>
        </div>
          <div class="login-section ceo-section">
            <form id="loginForm" class="login-form" onsubmit="handleCEOLogin(event)">
              <div class="form-group">
                <label for="ceo-username">Username/Email:</label>
                <input type="text" id="ceo-username" name="ceo-username" required placeholder="Enter CEO username" />
              </div>
              <div class="form-group">
                <label for="ceo-password">Password</label>
                <div class="password-input">
                  <input type="password" id="ceo-password" name="ceo-password" required placeholder="Enter password" />
                  <i class="fas fa-eye toggle-password" onclick="togglePassword('ceo-password')"></i>
                </div>
              </div>
              <button type="submit" id="loginBtn" class="announce-btn">
                <i class="fas fa-sign-in-alt"></i> Login
              </button>
            </form>
          </div>
      </div>
    </div>
  </div>

  <!-- Loading Overlay -->
  <div class="loading-overlay">
    <div class="loader"></div>
  </div>

  <script>
    function togglePassword(inputId) {
      const passwordInput = document.getElementById(inputId);
      const icon = passwordInput.nextElementSibling;
      if (passwordInput.type === "password") {
        passwordInput.type = "text";
        icon.classList.remove("fa-eye");
        icon.classList.add("fa-eye-slash");
      } else {
        passwordInput.type = "password";
        icon.classList.remove("fa-eye-slash");
        icon.classList.add("fa-eye");
      }
    }

    // Add tab switching functionality
    const tabBtns = document.querySelectorAll(".tab-btn");
    const tabContents = document.querySelectorAll(".tab-content");

    tabBtns.forEach((btn) => {
      btn.addEventListener("click", () => {
        const tabId = btn.dataset.tab;

        tabBtns.forEach((b) => b.classList.remove("active"));
        tabContents.forEach((c) => c.classList.remove("active"));

        btn.classList.add("active");
        document.getElementById(tabId).classList.add("active");
      });
    });

    function handleCEOLogin(event) {
      event.preventDefault();
      document.querySelector(".loading-overlay").style.display = "flex";
      setTimeout(() => {
        document.querySelector(".loading-overlay").style.display = "none";
        window.location.href = "dashboard.php";
      }, 1500);
    }

    function handleEngineerLogin(event) {
      event.preventDefault();
      document.querySelector(".loading-overlay").style.display = "flex";
      setTimeout(() => {
        document.querySelector(".loading-overlay").style.display = "none";
        window.location.href = "engineer-profile.php";
      }, 1500);
    }

    // jQuery triggers an ajax call to backend for login
    $(document).ready(function () {
      $('#loginForm').submit(function (e) {
        e.preventDefault();

        let formData = $(this).serializeArray(); // Serialize form inputs
        formData.push({ name: "action", value: "ceo_login" }); // Add unique action

        $.ajax({
          url: 'backend.php',
          type: 'POST',
          data: $.param(formData), // Convert to URL-encoded string
          dataType: 'json',
          success: function (response) {
            if (response.redirect) {
              window.location.href = response.redirect;
            } else {
              alert("Login failed. Please try again.");
            }
          },
          error: function () {
            alert("An error occurred. Please try again.");
          }
        });
      });
    });

  </script>
</body>

</html>