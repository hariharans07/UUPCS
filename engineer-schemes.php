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
          <a href="engineer-profile.php">
            <i class="fas fa-user-circle"></i>
            <span class="nav-item">Profile</span>
          </a>
        </li>
        <li>
          <a href="engineer-schemes.php" class="active">
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
        <h1>My Schemes</h1>
      </div>
      <div class="user-wrapper">
        <!-- Changed notification element to a Bootstrap trigger button -->
        
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

    <div class="tasks-container">
      <div class="schemes-tabs">
        <button class="tab-btn active" data-tab="ongoing">Ongoing</button>
        <button class="tab-btn" data-tab="completed">Completed</button>
        <button class="tab-btn" data-tab="collaborated">Collaborated</button>
      </div>

      <div class="tab-content active" id="ongoing">
        <table id="ongoingSchemesTable" class="table table-striped">
          <thead>
            <tr>
              <th>Scheme ID</th>
              <th>Title</th>
              <th>Description</th>
              <th>Deadline</th>
            </tr>
          </thead>
          <tbody>
            <?php
            $userId = $_SESSION['user_id'];
            $query = "SELECT * FROM schemes WHERE assigned_engineer_id = ? AND LOWER(status) = 'ongoing'";
            $stmt = $conn->prepare($query);
            $stmt->bind_param("i", $userId);
            $stmt->execute();
            $result = $stmt->get_result();

            $schemes = [];
            while ($row = $result->fetch_assoc()) {
              $schemes[] = $row;
            }

            foreach ($schemes as $row) {
              ?>
              <tr>
                <td>
                  <a href="scheme-details.php?id=<?php echo htmlspecialchars($row['id']); ?>" class="text-decoration-none">
                    <?php echo htmlspecialchars($row['id']); ?>
                  </a>
                </td>
                <td><?php echo htmlspecialchars($row['title']); ?></td>
                <td>
                  <button class="btn btn-primary btn-sm view-description-btn" data-bs-toggle="modal" data-bs-target="#descriptionModal"
                    data-description="<?php echo htmlspecialchars($row['description']); ?>"
                    data-region="<?php echo htmlspecialchars($row['region']); ?>"
                    data-budget="<?php echo htmlspecialchars($row['budget']); ?>">
                    View
                  </button>
                </td>
                <td><?php echo htmlspecialchars($row['deadline']); ?></td>
              </tr>
              <?php
            }

            if (empty($schemes)) {
              echo '<tr><td colspan="4">No ongoing schemes found.</td></tr>';
            }
            ?>
          </tbody>
        </table>
      </div>

      <div class="tab-content" id="completed">
        <table id="completedSchemesTable" class="table table-striped">
          <thead>
            <tr>
              <th>Scheme ID</th>
              <th>Title</th>
              <th>Description</th>
              <th>Deadline</th>
            </tr>
          </thead>
          <tbody>
            <?php
            $userId = $_SESSION['user_id'];
            $query = "SELECT * FROM schemes WHERE assigned_engineer_id = ? AND LOWER(status) = 'completed'";
            $stmt = $conn->prepare($query);
            $stmt->bind_param("i", $userId);
            $stmt->execute();
            $result = $stmt->get_result();

            $schemes = [];
            while ($row = $result->fetch_assoc()) {
              $schemes[] = $row;
            }

            foreach ($schemes as $row) {
              ?>
              <tr>
                <td><?php echo htmlspecialchars($row['id']); ?></td>
                <td><?php echo htmlspecialchars($row['title']); ?></td>
                <td>
                  <button class="btn btn-primary btn-sm view-description-btn" data-bs-toggle="modal" data-bs-target="#descriptionModal"
                    data-description="<?php echo htmlspecialchars($row['description']); ?>"
                    data-region="<?php echo htmlspecialchars($row['region']); ?>"
                    data-budget="<?php echo htmlspecialchars($row['budget']); ?>">
                    View
                  </button>
                </td>
                <td><?php echo htmlspecialchars($row['deadline']); ?></td>
              </tr>
              <?php
            }

            if (empty($schemes)) {
              echo '<tr><td colspan="4">No completed schemes found.</td></tr>';
            }
            ?>
          </tbody>
        </table>
      </div>

      <div class="tab-content" id="collaborated">
        <table id="collaboratedSchemesTable" class="table table-striped">
          <thead>
            <tr>
              <th>Scheme ID</th>
              <th>Title</th>
              <th>Description</th>
              <th>Deadline</th>
              <th>Collaboration With</th>
            </tr>
          </thead>
          <tbody>
            <?php
            $collaboratedSchemes = [];
            $engineerId = $_SESSION['user_id'];
            $collabQuery = "SELECT 
                c.id AS collaboration_id, 
                c.status, 
                s1.id AS id_1, s1.title AS title_1, s1.description AS description_1, s1.deadline AS deadline_1, s1.department AS department_1, s1.region AS region_1, s1.budget AS budget_1, s1.assigned_engineer_id AS assigned_engineer_id_1,
                s2.id AS id_2, s2.title AS title_2, s2.description AS description_2, s2.deadline AS deadline_2, s2.department AS department_2, s2.region AS region_2, s2.budget AS budget_2, s2.assigned_engineer_id AS assigned_engineer_id_2
                FROM collaborations c
                JOIN schemes s1 ON c.scheme1_id = s1.id
                JOIN schemes s2 ON c.scheme2_id = s2.id
                WHERE c.status = 'approved' AND (s1.assigned_engineer_id = ? OR s2.assigned_engineer_id = ?)";
            $collabStmt = $conn->prepare($collabQuery);
            $collabStmt->bind_param('ii', $engineerId, $engineerId);
            $collabStmt->execute();
            $collabResult = $collabStmt->get_result();
            while ($row = $collabResult->fetch_assoc()) {
                // Show the scheme that the engineer is collaborating WITH, not their own
                if ($row['assigned_engineer_id_1'] == $engineerId) {
                    // Engineer owns s1, show s2 as the collaborated scheme
                    $collaboratedSchemes[] = [
                        'collaboration_id' => $row['collaboration_id'],
                        'id' => $row['id_2'],
                        'title' => $row['title_2'],
                        'description' => $row['description_2'],
                        'deadline' => $row['deadline_2'],
                        'department' => $row['department_2'],
                        'collab_with' => $row['region_1'],
                        'budget' => $row['budget_2'],
                    ];
                } else {
                    // Engineer owns s2, show s1 as the collaborated scheme
                    $collaboratedSchemes[] = [
                        'collaboration_id' => $row['collaboration_id'],
                        'id' => $row['id_1'],
                        'title' => $row['title_1'],
                        'description' => $row['description_1'],
                        'deadline' => $row['deadline_1'],
                        'department' => $row['department_1'],
                        'collab_with' => $row['region_2'],
                        'budget' => $row['budget_1'],
                    ];
                }
            }
            $collabStmt->close();

            foreach ($collaboratedSchemes as $row) {
              ?>
              <tr>
                <td>
                  <a href="scheme-details.php?id=<?php echo htmlspecialchars($row['id']); ?>&collaboration_id=<?php echo htmlspecialchars($row['collaboration_id']); ?>" class="text-decoration-none">
                    <?php echo htmlspecialchars($row['id']); ?>
                  </a>
                </td>
                <td><?php echo htmlspecialchars($row['title']); ?></td>
                <td>
                  <button class="btn btn-primary btn-sm view-description-btn" data-bs-toggle="modal" data-bs-target="#descriptionModal"
                    data-description="<?php echo htmlspecialchars($row['description']); ?>"
                    data-region="<?php echo htmlspecialchars($row['collab_with']); ?>"
                    data-budget="<?php echo htmlspecialchars($row['budget']); ?>">
                    View
                  </button>
                </td>
                <td><?php echo htmlspecialchars($row['deadline']); ?></td>
                <td><?php echo htmlspecialchars($row['collab_with']); ?></td>
              </tr>
              <?php
            }

            if (empty($collaboratedSchemes)) {
              echo '<tr><td colspan="5">No collaborated schemes found.</td></tr>';
            }
            ?>
          </tbody>
        </table>
      </div>
    </div>
  </div>

  <!-- Add Task Modal -->
  <div id="taskModal" class="modal">
    <div class="modal-content">
      <span class="close-modal">&times;</span>
      <h2>Add New Task</h2>
      <form class="scheme-form">
        <div class="form-group">
          <label for="task-description">Task Description</label>
          <input type="text" id="task-description" placeholder="Enter task description" required />
        </div>

        <div class="form-group">
          <label for="task-deadline">Deadline</label>
          <input type="date" id="task-deadline" required />
        </div>

        <button type="submit" class="announce-btn">
          <i class="fas fa-plus"></i> Add Task
        </button>
      </form>
    </div>
  </div>

  <!-- Task Details Modal -->
  <div id="taskDetailModal" class="modal">
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

  <!-- Description Modal -->
  <div class="modal fade" id="descriptionModal" tabindex="-1" aria-labelledby="descriptionModalLabel" aria-hidden="true">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="descriptionModalLabel">Scheme Details</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          <p><strong>Description:</strong> <span id="descModalDescription"></span></p>
          <p><strong>Region:</strong> <span id="descModalRegion"></span></p>
          <p><strong>Budget:</strong> <span id="descModalBudget"></span></p>
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

    const tabs = document.querySelectorAll(".tab-btn");
    const tabContents = document.querySelectorAll(".tab-content");

    tabs.forEach((tab) => {
      tab.addEventListener("click", () => {
        // Remove active class from all tabs and contents
        tabs.forEach((t) => t.classList.remove("active"));
        tabContents.forEach((content) => content.classList.remove("active"));

        // Add active class to the clicked tab and corresponding content
        tab.classList.add("active");
        const target = tab.getAttribute("data-tab");
        document.getElementById(target).classList.add("active");
      });
    });

    $(document).ready(function () {
      // Initialize DataTable
      $('#ongoingSchemesTable').DataTable();
      $('#completedSchemesTable').DataTable();
      $('#collaboratedSchemesTable').DataTable();
    });

    // Populate modal with scheme details
    document.querySelectorAll('.view-description-btn').forEach(button => {
      button.addEventListener('click', function () {
        const description = this.getAttribute('data-description');
        const region = this.getAttribute('data-region');
        const budget = this.getAttribute('data-budget');

        document.getElementById('descModalDescription').textContent = description || '';
        document.getElementById('descModalRegion').textContent = region || '';
        document.getElementById('descModalBudget').textContent = budget || '';
      });
    });
  </script>
</body>

</html>