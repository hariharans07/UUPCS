<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>UUPCS Requests</title>
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
            <a href="resources.php">
              <i class="fas fa-box"></i>
              <span class="nav-item">Resources</span>
            </a>
          </li>
          <li>
            <a href="#" class="active">
              <i class="fas fa-paper-plane"></i>
              <span class="nav-item">Request</span>
            </a>
          </li>
        </ul>
      </nav>
      <div class="sidebar-footer">
        <a href="schemes.php" class="back-btn">
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
          <h1>Requests</h1>
        </div>
        <div class="user-wrapper">
          <img src="assets/profile-logo.png" alt="Profile" />
          <div>
            <h4>John Doe</h4>
            <small>Admin</small>
          </div>
        </div>
      </header>

      <div class="tasks-container">
        <div class="tasks-header">
          <h2>Resource Requests</h2>
        </div>

        <div class="schemes-table">
          <table>
            <thead>
              <tr>
                <th>ReqID</th>
                <th>Description</th>
                <th>Time</th>
                <th>Action</th>
              </tr>
            </thead>
            <tbody>
              <tr>
                <td>REQ001</td>
                <td>Additional cement bags required for foundation work</td>
                <td>2024-02-20 10:30 AM</td>
                <td class="action-buttons">
                  <button
                    class="edit-btn"
                    onclick="handleAction('grant', 'REQ001')"
                  >
                    <i class="fas fa-check"></i> Grant
                  </button>
                  <button
                    class="delete-btn"
                    onclick="handleAction('reject', 'REQ001')"
                  >
                    <i class="fas fa-times"></i> Reject
                  </button>
                </td>
              </tr>
              <tr>
                <td>REQ002</td>
                <td>Steel rods needed for reinforcement</td>
                <td>2024-02-20 11:45 AM</td>
                <td class="action-buttons">
                  <button
                    class="edit-btn"
                    onclick="handleAction('grant', 'REQ002')"
                  >
                    <i class="fas fa-check"></i> Grant
                  </button>
                  <button
                    class="delete-btn"
                    onclick="handleAction('reject', 'REQ002')"
                  >
                    <i class="fas fa-times"></i> Reject
                  </button>
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

      function handleAction(action, reqId) {
        const message =
          action === "grant"
            ? `Request ${reqId} has been granted`
            : `Request ${reqId} has been rejected`;
        alert(message);
        // Here you can add logic to remove the row or update its status
      }
    </script>
  </body>
</html>