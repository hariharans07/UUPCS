<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>UUPCS Resources</title>
    <link rel="stylesheet" href="style.css" />
    <link
      rel="stylesheet"
      href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css"
    />
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
            <a href="tasks.php">
              <i class="fas fa-tasks"></i>
              <span class="nav-item">Tasks</span>
            </a>
          </li>
          <li>
            <a href="#" class="active">
              <i class="fas fa-box"></i>
              <span class="nav-item">Resources</span>
            </a>
          </li>
          <li>
            <a href="request.php">
              <i class="fas fa-paper-plane"></i>
              <span class="nav-item">Request</span>
            </a>
          </li>
        </ul>
      </nav>
      <div class="sidebar-footer">
        <a href="schemes.html" class="back-btn">
          <i class="fas fa-arrow-left"></i>
          <span class="nav-item">Back to Schemes</span>
        </a>
      </div>
    </div>

    <div class="main-content">
      <header>
        <div class="header-content">
          <div class="toggle">
            <i class="fas fa-bars menu-icon"></i>
          </div>
          <h1>Resources</h1>
        </div>
        <div class="user-wrapper">
          <img src="assets/profile-logo.png" alt="Admin Profile" />
          <div>
            <h4>John Doe</h4>
            <small>Admin</small>
          </div>
        </div>
      </header>

      <div class="resources-container">
        <div class="resources-header">
          <h2>Available Resources</h2>
          <button class="add-resource-btn">
            <i class="fas fa-plus"></i> Add Resource
          </button>
        </div>

        <div class="schemes-table">
          <table>
            <thead>
              <tr>
                <th>RID</th>
                <th>Name</th>
                <th>Quantity</th>
                <th>Image</th>
              </tr>
            </thead>
            <tbody>
              <tr>
                <td>R001</td>
                <td>Cement Bags</td>
                <td>500</td>
                <td>
                  <img
                    src="assets/cement.jpg"
                    alt="Cement"
                    class="resource-image"
                  />
                </td>
              </tr>
              <tr>
                <td>R002</td>
                <td>Steel Rods</td>
                <td>1000</td>
                <td>
                  <img
                    src="assets/steel.jpg"
                    alt="Steel"
                    class="resource-image"
                  />
                </td>
              </tr>
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
    </script>
  </body>
</html>