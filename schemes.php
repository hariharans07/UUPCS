<?php
require_once 'db.php';
session_start();
if (empty($_SESSION['user_id']) || $_SESSION['role'] != 'ceo') {
  header('Location: login.php');
  exit;
}

// Fetch ongoing schemes for the session department
$department = $_SESSION['department'];
$ongoingSchemes = [];
$completedSchemes = [];

$query = "SELECT * FROM schemes WHERE department = ? AND status = 'Ongoing'";
$stmt = $conn->prepare($query);
$stmt->bind_param('s', $department);
$stmt->execute();
$result = $stmt->get_result();

while ($row = $result->fetch_assoc()) {
  $ongoingSchemes[] = $row;
}
$stmt->close();

// Fetch completed schemes for the session department
$query = "SELECT * FROM schemes WHERE department = ? AND status = 'Completed'";
$stmt = $conn->prepare($query);
$stmt->bind_param('s', $department);
$stmt->execute();
$result = $stmt->get_result();

while ($row = $result->fetch_assoc()) {
  $completedSchemes[] = $row;
}
$stmt->close();
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>UUPCS Schemes</title>
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
          <a href="dashboard.php">
            <i class="fas fa-chart-bar"></i>
            <span class="nav-item">Dashboard</span>
          </a>
        </li>
        <li>
          <a href="schemes.php" class="active">
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
        <h1>Schemes</h1>


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

    <div class="schemes-list-container">
      <div class="schemes-header">
        <h2>Schemes</h2>
        <a href="dashboard.php" class="add-scheme-btn">
          <i class="fas fa-plus"></i> New Scheme
        </a>
      </div>

      <div class="schemes-tabs">
        <button class="tab-btn active" data-tab="ongoing">Ongoing</button>
        <button class="tab-btn" data-tab="completed">Completed</button>
        <button class="tab-btn" data-tab="collaborated">Collaborated</button>
      </div>

      <div class="tab-content active" id="ongoing">
        <div class="schemes-table">
          <table id="ongoingTable" class="table table-striped">
            <thead>
              <tr>
                <th>SID</th>
                <th>Scheme Title</th>
                <th>Budget</th>
                <th>Deadline</th>
                <th>Action</th>
              </tr>
            </thead>
            <tbody>
              <?php foreach ($ongoingSchemes as $index => $scheme): ?>
                <tr>
                  <td><?php echo $index + 1; ?></td>
                  <td><?php echo htmlspecialchars($scheme['title']); ?></td>
                  <td>₹<?php echo htmlspecialchars(number_format($scheme['budget'])); ?></td>
                  <td><?php echo htmlspecialchars($scheme['deadline']); ?></td>
                  <td>
                    <button class="btn btn-primary" data-scheme-id="<?php echo $scheme['id']; ?>" data-bs-toggle="modal"
                      data-bs-target="#schemeModal">
                      <i class="fas fa-eye"></i> View
                    </button>
                  </td>
                </tr>
              <?php endforeach; ?>
            </tbody>
          </table>
        </div>
      </div>

      <div class="tab-content" id="completed">
        <div class="schemes-table">
          <table id="completedTable" class="table table-striped">
            <thead>
              <tr>
                <th>SID</th>
                <th>Scheme Title</th>
                <th>Budget</th>
                <th>Deadline</th>
                <th>Action</th>
              </tr>
            </thead>
            <tbody>
              <?php foreach ($completedSchemes as $index => $scheme): ?>
                <tr>
                  <td><?php echo $index + 1; ?></td>
                  <td><?php echo htmlspecialchars($scheme['title']); ?></td>
                  <td>₹<?php echo htmlspecialchars(number_format($scheme['budget'])); ?></td>
                  <td><?php echo htmlspecialchars($scheme['deadline']); ?></td>
                  <td>
                    <button class="btn btn-primary" data-scheme-id="<?php echo $scheme['id']; ?>" data-bs-toggle="modal"
                      data-bs-target="#schemeModal">
                      <i class="fas fa-eye"></i> View
                    </button>
                  </td>
                </tr>
              <?php endforeach; ?>
            </tbody>
          </table>
        </div>
      </div>

      <div class="tab-content" id="collaborated">
        <div class="schemes-table">
          <table>
            <thead>
              <tr>
                <th>SID</th>
                <th>Scheme Title</th>
                <th>Budget</th>
                <th>Deadline</th>
                <th>Action</th>
              </tr>
            </thead>
            <tbody>
              <tr>
                <td onclick="window.location.href='tasks.php'">006</td>
                <td>Smart Traffic System</td>
                <td>₹75,00,000</td>
                <td>2024-09-01</td>
                <td>
                  <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#schemeModal">
                    <i class="fas fa-eye"></i> View
                  </button>
                </td>
              </tr>
              <tr>
                <td onclick="window.location.href='tasks.php'">007</td>
                <td>Waste Management</td>
                <td>₹60,00,000</td>
                <td>2024-07-30</td>
                <td>
                  <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#schemeModal">
                    <i class="fas fa-eye"></i> View
                  </button>
                </td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>
    </div>

    <!-- Updated Modal Structure to Include Description, Start Date, and Region -->
    <div class="modal fade" id="schemeModal" tabindex="-1" aria-labelledby="schemeModalLabel" aria-hidden="true">
      <div class="modal-dialog">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title" id="schemeModalLabel">Scheme Details</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>
          <div class="modal-body">
            <h2 id="modalTitle"></h2>
            <p><strong>Budget:</strong> <span id="modalBudget"></span></p>
            <p><strong>Deadline:</strong> <span id="modalDeadline"></span></p>
            <p><strong>Start Date:</strong> <span id="modalStartDate"></span></p>
            <p><strong>Region:</strong> <span id="modalRegion"></span></p>
            <p><strong>Engineer:</strong> <span id="modalEngineer"></span></p>
            <div class="modal-description">
              <h3>Description</h3>
              <p id="modalDescription"></p>
            </div>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
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

      // Add this after existing toggle script
      const tabBtns = document.querySelectorAll('.tab-btn');
      const tabContents = document.querySelectorAll('.tab-content');

      tabBtns.forEach(btn => {
        btn.addEventListener('click', () => {
          const tabId = btn.dataset.tab;

          tabBtns.forEach(b => b.classList.remove('active'));
          tabContents.forEach(c => c.classList.remove('active'));

          btn.classList.add('active');
          document.getElementById(tabId).classList.add('active');
        });
      });

      // Replace existing modal script with this


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

      $(document).ready(function () {
        $('#ongoingTable').DataTable();
        $('#completedTable').DataTable();
      });

      $(document).ready(function () {
        $('#ongoingTable, #completedTable').on('click', '.btn-primary', function () {
          const schemeId = $(this).data('scheme-id');

          $.ajax({
            url: 'backend.php',
            type: 'GET',
            data: { action: 'get_scheme', scheme_id: schemeId },
            dataType: 'json',
            success: function (response) {
              if (response.success) {
                $('#modalTitle').text(response.data.title);
                $('#modalBudget').text(`₹${response.data.budget.toLocaleString()}`);
                $('#modalDeadline').text(response.data.deadline);
                $('#modalStartDate').text(response.data.startdate);
                $('#modalRegion').text(response.data.region);
                $('#modalEngineer').text(response.data.engineer); // Corrected key to match backend response
                $('#modalDescription').text(response.data.description);
                $('#schemeModal').modal('show');
              } else {
                alert(response.message);
              }
            },
            error: function () {
              alert('An error occurred while fetching scheme details.');
            }
          });
        });
      });
    </script>
</body>

</html>