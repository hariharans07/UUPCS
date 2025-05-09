<?php
include 'db.php';
session_start();
if (empty($_SESSION['user_id'])||$_SESSION['role'] != 'ceo') {
  header('Location: login.php');
  exit;
}
?>
<?php // Converted to PHP ?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>UUPCS Inventory</title>
  <link rel="stylesheet" href="style.css" />
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet" />
  <!-- Added Bootstrap 5.3.2 CSS -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <!-- Added jQuery and Bootstrap Bundle JS -->
  <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
  <!-- Added DataTables CSS and JS -->
  <link rel="stylesheet" href="https://cdn.datatables.net/1.11.5/css/jquery.dataTables.min.css">
  <script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
  <!-- Add SweetAlert2 -->
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
          <a href="inventory.php" class="active">
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
        <h1>Inventory</h1>
      
        
      
      <!-- Added dropdown button -->
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

    <div class="resources-container">
      <div class="resources-header">
        <h2>Inventory Items</h2>
        <button class="add-resource-btn" data-bs-toggle="modal" data-bs-target="#addItemModal">
          <i class="fas fa-plus"></i> Add Item
        </button>
      </div>

      <div class="schemes-table">
        <table id="inventorytable" class="table table-striped">
          <thead>
            <tr>
              <th>SNO</th>
              <th>Item Name</th>
              <th>Quantity</th>
              <th>Status</th>
              <th>Action</th>
            </tr>
          </thead>
          <tbody>
            <?php
            // Fetch resources from database
            $dept = $_SESSION['department'];
            $query = "SELECT * FROM resources WHERE department = ?";
            $stmt = $conn->prepare($query);
            $stmt->bind_param("s", $dept);
            $stmt->execute();
            $result = $stmt->get_result();

            $sno = 1; // Initialize counter
            while ($row = $result->fetch_assoc()) {
              $status = ($row['total_quantity'] > 30) ? 'In Stock' : 'Low Stock';
              $statusClass = ($row['total_quantity'] > 30) ? 'active' : 'inactive';
              echo "<tr>
                      <td>{$sno}</td>
                      <td>{$row['name']}</td>
                      <td>{$row['total_quantity']}</td>
                      <td><span class='status-badge {$statusClass}'>{$status}</span></td>
                      <td class='action-buttons'>
                        <button class='edit-btn' data-id='{$row['id']}' title='Increase Quantity'>
                          <i class='fas fa-plus-circle'></i>
                        </button>
                      </td>
                    </tr>";
              $sno++; // Increment counter
            }
            ?>
          </tbody>
        </table>
      </div>
    </div>
  </div>

  <!-- Add Item Modal -->
  <div class="modal fade" id="addItemModal" tabindex="-1" aria-labelledby="addItemModalLabel" aria-hidden="true">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="addItemModalLabel">Add New Item</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          <form id="addItemForm">
            <div class="mb-3">
              <label for="itemName" class="form-label">Item Name</label>
              <input type="text" class="form-control" id="itemName" name="itemName" required>
            </div>
            <div class="mb-3">
              <label for="itemQuantity" class="form-label">Quantity</label>
              <input type="number" class="form-control" id="itemQuantity" name="itemQuantity" required min="1">
            </div>
          </form>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
          <button type="button" class="btn btn-primary" id="saveItemBtn">Save Item</button>
        </div>
      </div>
    </div>
  </div>

  <!-- Increase Quantity Modal -->
  <div class="modal fade" id="increaseQuantityModal" tabindex="-1" aria-labelledby="increaseQuantityModalLabel" aria-hidden="true">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="increaseQuantityModalLabel">Increase Quantity</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          <form id="increaseQuantityForm">
            <input type="hidden" id="increaseItemId" name="itemId">
            <div class="mb-3">
              <label for="increaseQuantity" class="form-label">Enter Quantity to Add</label>
              <input type="number" class="form-control" id="increaseQuantity" name="quantity" required min="1">
            </div>
          </form>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
          <button type="button" class="btn btn-primary" id="saveIncreaseQuantityBtn">Save</button>
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

    $(document).ready(function () {
      $('#inventorytable').DataTable();
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

    $('#saveItemBtn').click(function() {
      const itemName = $('#itemName').val();
      const itemQuantity = $('#itemQuantity').val();

      if (!itemName || !itemQuantity) {
        Swal.fire({
          icon: 'warning',
          title: 'Validation Error',
          text: 'Please fill all fields'
        });
        return;
      }

      $.ajax({
        url: 'backend.php',
        type: 'POST',
        data: {
          action: 'add_inventory_item',
          name: itemName,
          quantity: itemQuantity
        },
        dataType: 'json',
        success: function(response) {
          if (response.success) {
            Swal.fire({
              icon: 'success',
              title: 'Success!',
              text: 'Item added successfully',
              timer: 1500
            }).then(() => {
              location.reload();
            });
          } else {
            Swal.fire({
              icon: 'error',
              title: 'Error!',
              text: response.message || 'Failed to add item'
            });
          }
          $('#addItemModal').modal('hide');
        },
        error: function() {
          Swal.fire({
            icon: 'error',
            title: 'Error!',
            text: 'An error occurred while processing your request'
          });
        }
      });
    });

    // Handle Increase Quantity Button Click
    $('.edit-btn').click(function () {
      const itemId = $(this).data('id'); // Get the item ID from the button's data-id attribute
      $('#increaseItemId').val(itemId); // Set the item ID in the hidden input
      $('#increaseQuantityModal').modal('show'); // Show the modal
    });

    // Handle Save Button in Increase Quantity Modal
    $('#saveIncreaseQuantityBtn').click(function () {
      const itemId = $('#increaseItemId').val();
      const quantityToAdd = $('#increaseQuantity').val();

      if (!quantityToAdd || quantityToAdd <= 0) {
        Swal.fire({
          icon: 'warning',
          title: 'Validation Error',
          text: 'Please enter a valid quantity'
        });
        return;
      }

      $.ajax({
        url: 'backend.php',
        type: 'POST',
        data: {
          action: 'increase_inventory_quantity',
          itemId: itemId,
          quantity: quantityToAdd
        },
        dataType: 'json',
        success: function (response) {
          if (response.success) {
            Swal.fire({
              icon: 'success',
              title: 'Success!',
              text: 'Quantity updated successfully',
              timer: 1500
            }).then(() => {
              location.reload();
            });
          } else {
            Swal.fire({
              icon: 'error',
              title: 'Error!',
              text: response.message || 'Failed to update quantity'
            });
          }
          $('#increaseQuantityModal').modal('hide');
        },
        error: function () {
          Swal.fire({
            icon: 'error',
            title: 'Error!',
            text: 'An error occurred while processing your request'
          });
        }
      });
    });
  </script>
</body>

</html>