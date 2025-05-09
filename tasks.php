<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>UUPCS Tasks</title>
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
            <a href="#" class="active">
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
            <a href="request.php">
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
          <h1>Tasks</h1>
        </div>
        <div class="user-wrapper">
          <img src="https://via.placeholder.com/40" alt="profile" />
          <div>
            <h4>John Doe</h4>
            <small>Admin</small>
          </div>
        </div>
      </header>

      <div class="tasks-container">
        <div class="tasks-header">
          <h2>Scheme Tasks</h2>
        </div>

        <div class="schemes-tabs">  <!-- Changed from tasks-tabs to schemes-tabs -->
          <button class="tab-btn active" data-tab="ongoing">Ongoing</button>
          <button class="tab-btn" data-tab="completed">Completed</button>
        </div>

        <div class="tab-content active" id="ongoing">
          <div class="schemes-table"> <!-- Added proper spacing with existing class -->
            <table>
              <thead>
                <tr>
                  <th>TID</th>
                  <th>Description</th>
                  <th>Deadline</th>
                  <th>Status</th>
                </tr>
              </thead>
              <tbody>
                <tr>
                  <td>T001</td>
                  <td>Initial Survey</td>
                  <td>2024-03-01</td>
                  <td><span class="task-status pending">Pending</span></td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>

        <div class="tab-content" id="completed">
          <div class="schemes-table">
            <table>
              <thead>
                <tr>
                  <th>TID</th>
                  <th>Description</th>
                  <th>Deadline</th>
                  <th>Status</th>
                </tr>
              </thead>
              <tbody>
                <tr>
                  <td>T003</td>
                  <td>Site Preparation</td>
                  <td>2024-02-15</td>
                  <td><span class="task-status completed">Completed</span></td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>

        <!-- Add Modal Structure -->
        <div id="taskModal" class="modal">
          <div class="modal-content">
            <span class="close-modal">&times;</span>
            <h2 id="modalTitle"></h2>
            <div class="modal-info">
              <p><strong>Status:</strong> <span id="modalStatus"></span></p>
              <p><strong>Deadline:</strong> <span id="modalDeadline"></span></p>
              <div class="modal-description">
                <h3>Description</h3>
                <p id="modalDescription"></p>
              </div>
            </div>
          </div>
        </div>

        <script>
          const toggle = document.querySelector(".toggle");
          const sidebar = document.querySelector(".sidebar");

          toggle.addEventListener("click", () => {
            sidebar.classList.toggle("close");
          });

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

          // Add Modal functionality
          const modal = document.getElementById("taskModal");
          const closeBtn = document.querySelector(".close-modal");

          function openModal(btn) {
            const row = btn.closest('tr');
            const title = row.querySelector('td:nth-child(2)').textContent;
            const status = row.querySelector('td:nth-child(3)').textContent;
            const deadline = row.querySelector('td:nth-child(4)').textContent;
            
            const descriptions = {
              'Initial Survey': 'Conduct comprehensive survey of the project area and document findings.',
              'Resource Allocation': 'Plan and allocate resources for different project phases.',
              'Site Preparation': 'Prepare the construction site for upcoming work phases.'
            };

            document.getElementById('modalTitle').textContent = title;
            document.getElementById('modalStatus').textContent = status;
            document.getElementById('modalDeadline').textContent = deadline;
            document.getElementById('modalDescription').textContent = descriptions[title] || 'Description not available';
            
            modal.style.display = "block";
          }

          closeBtn.onclick = () => modal.style.display = "none";
          window.onclick = (e) => {
            if (e.target == modal) modal.style.display = "none";
          };
        </script>
  </body>
</html>