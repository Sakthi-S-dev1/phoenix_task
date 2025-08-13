<?php
require '../auth_session.php';
$user_name = $_SESSION['user_name'];
$role = $_SESSION['role'];

require '../db.php';


$sql = "SELECT * FROM warehouse";
$result = mysqli_query($conn, $sql);

// this is the dlt handleing
if (isset($_GET['delete']) && $role === 'admin') {
  $D_id = intval($_GET['delete']);
  $sql = "DELETE FROM warehouse WHERE id = $D_id";
  $D_result = mysqli_query($conn, $sql);
  if ($D_result)
    header("location: warehouse_list.php");
  exit;
}

//edit 
$editData;
if (isset($_GET['edit']) && $role === 'admin') {
  $E_id = intval($_GET['edit']);
  $sql = "SELECT * FROM warehouse WHERE id = $E_id";
  $E_result = mysqli_query($conn, $sql);
  if (mysqli_num_rows($E_result) > 0) {
    $editData = mysqli_fetch_assoc($E_result);
  }
}

?>
<?php

if ($_SERVER['REQUEST_METHOD'] === 'POST' && $_SESSION['role'] === 'admin') {
  foreach ($_POST['prefix'] as $index => $prefix) {
    $warehouse = trim($_POST['warehouse'][$index]);
    $stock = intval($_POST['stock'][$index]);

    if ($prefix && $warehouse && $stock >= 0) {
      $sql = "INSERT INTO warehouse (prefix, warehouse_name, stock) VALUES ('$prefix','$warehouse','$stock')";
      $result = mysqli_query($conn, $sql);
    }
  }

  header("Location: warehouse_list.php");
  exit;
}
?>

<head>
  <meta charset="UTF-8" />
  <title>Dashboard</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <!-- Bootstrap CSS -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" />
  <!-- Bootstrap JS Bundle (includes Popper) -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
  <!-- Font Awesome -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" />
  <!-- bootstrap icons -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
  <link rel="stylesheet" href="../assets2/styles.css" />
</head>

<body>
  <!-- Sidebar will be injected here -->
  <div class="toggle-btn" id="toggleBtn">
    <i class="fas fa-bars"></i>
  </div>
  <?php include '../includes/sidebar.php'; ?>
  <!-- Main Content -->
  <div class="main-content" id="mainContent">
    <!-- Header -->
    <div id="mainHeader" class="header d-flex justify-content-between align-items-center px-4 py-2 bg-white border-bottom shadow-sm w-100">
      <h4 class="fw-bold mb-0">Warehouse List</h4>

      <?php include "../users/nav_profile.php"; ?>

      <!-- Notification Panel -->
      <div id="notificationPanel" class="position-fixed top-0 end-0 bg-white border shadow z-3 p-3" style="display: none; width: 300px; height: 100vh;">
        <div class="d-flex justify-content-between align-items-center border-bottom pb-2 mb-2">
          <h5 class="mb-0">Notifications</h5>
          <button id="closeNotification" class="btn-close"></button>
        </div>
        <div class="notification-body px-3 py-2">
        </div>

      </div>
    </div>

    <!-- Push content below fixed header -->
    <div style="padding-top: 30px;"></div>
    <div class="container-fluid px-3">
      <div class="card shadow-sm rounded-4 mt-4">
        <div class="card-body">

          <!-- Header Button -->
          <div class="row">
            <?php if ($role === 'admin') : ?>
              <div class="col text-end mb-3">
                <button class="btn custom-orange-btn text-white" onclick="toggleAddForm()" type="button">
                  <i class="fas fa-plus-circle me-2"></i> Add Warehouse
                </button>

              </div>
            <?php endif; ?>
          </div>

          <div id="addWarehouseForm" style="display: none;">
            <div class="mb-3 text-start">ADD NEW WAREHOUSE</div>
            <!-- Add Form -->
            <form id="warehouseForm" method="POST" action="">

              <div id="warehouseRowTemplate" class="row g-3 warehouse-entry mb-3 d-none">
                <div class="col-md-4">
                  <input type="text" class="form-control prefix" placeholder="Warehouse Code ex,[ACT-1243]" name="prefix[]" disabled>
                  <div class="invalid-feedback prefixErr"></div>
                </div>
                <div class="col-md-4">
                  <input type="text" class="form-control warehouse" placeholder="Enter Warehouse" name="warehouse[]" disabled>
                  <div class="invalid-feedback"></div>
                </div>
                <div class="col-md-2">
                  <input type="number" class="form-control stock" placeholder="Stock" name="stock[]" disabled>
                  <div class="invalid-feedback"></div>
                </div>
                <div class="col-md-2 d-flex gap-2">
                  <div>
                    <button type="button" class="btn btn-outline-primary"><i class="fas fa-plus"></i></button>
                    <button type="button" class="btn btn-danger"><i class="fas fa-minus"></i></button>
                  </div>
                </div>
              </div>


              <div id="warehouseStockFields">
                <div class="row g-3 warehouse-entry mb-3">
                  <div class="col-md-4">
                    <input type="text" id="prefix" class="form-control prefix" placeholder="Warehouse Code ex,[ACT-1243]" name="prefix[]">
                    <div id="prefixErr" class="invalid-feedback prefixErr"></div>
                  </div>
                  <div class="col-md-4">
                    <input type="text" id="warehouse" class="form-control warehouse" placeholder="Enter Warehouse" name="warehouse[]">
                    <div id="warehouseErr" class="invalid-feedback warehouseErr"></div>
                  </div>
                  <div class="col-md-2">
                    <input type="number" id="stock" class="form-control stock" placeholder="Stock" name="stock[]">
                    <div id="stockErr" class="invalid-feedback stockErr"></div>
                  </div>
                  <div class="col-md-2 d-flex gap-2">
                    <div>
                      <button type="button" class="btn btn-outline-primary " onclick="addField()"><i class="fas fa-plus"></i></button>
                      <button type="button" class="btn btn-danger" onclick="this.closest('.warehouse-entry').remove()"><i class="fas fa-minus"></i></button>
                    </div>
                  </div>
                </div>
              </div>
              <div class="">
                <button type="submit" class="btn btn-sm btn-success btn-lg mt-2 mx-3" style="width: 70px;">Save</button>
                <a href="warehouse_list.php" class="btn btn-sm btn-secondary btn-lg mt-2 mx-3" style="width: 70px;">Cancel</a>
              </div>
              <div class="mt-3 add_msg"></div>
            </form>
          </div>

          <!-- Responsive Table -->
          <div class="table-responsive">
            <table id="customerTable" class="table table-hover table-bordered align-middle">
              <thead class="custom-thead text-center">
                <tr>
                  <th scope="col">S.NO</th>
                  <th scope="col">INVOICE PREFIX</th>
                  <th scope="col">WAREHOUSE</th>
                  <th scope="col">STOCKS</th>

                  <?php if ($role === 'admin') : ?>
                    <th scope="col">ACTIONS</th>
                  <?php endif; ?>

                </tr>
              </thead>
              <tbody>
                <?php $serial = 1; ?>
                <?php while ($row = mysqli_fetch_assoc($result)) : ?>
                  <tr data-id="<?php echo $row['id']; ?>">
                    <td><?php echo $serial++; ?></td>
                    <td class="prefix"><?php echo htmlspecialchars($row['prefix']); ?></td>
                    <td class="warehouse_name"><?php echo htmlspecialchars($row['warehouse_name']); ?></td>
                    <td class="stock"><?php echo htmlspecialchars($row['stock']); ?></td>
                    <?php if ($role === 'admin') : ?>

                      <td class="actions text-center">

                        <a href="?edit=<?php echo $row['id']; ?>" class="btn btn-sm btn-outline-primary">
                          <i class="fas fa-edit"></i>
                        </a>

                        <a href="?delete=<?php echo $row['id']; ?>" class="btn btn-sm btn-outline-danger ms-1"
                          onclick="return confirm('wanna delete this warhause ?');">
                          <i class="fas fa-trash-alt"></i>
                        </a>

                      </td>
                    <?php endif; ?>
                  </tr>
                <?php endwhile; ?>
              </tbody>


            </table>
            <?php if ($editData): ?>

              <div id="message" class="my-2 w-75 mx-auto"></div>

              <form id="warehouseEditForm" method="POST" class="my-2">
                <h5>EDIT Warehouse</h5>
                <input type="hidden" name="id" value="<?php echo $editData['id']; ?>">

                <div class="row gy-2 gx-3  flex-wrap">
                  <div class="col-md-3">
                    <label for="prefix" class="form-label">Prefix</label>
                    <input type="text" class="form-control prefix" id="prefix" name="prefix"
                      value="<?php echo htmlspecialchars($editData['prefix']); ?>" required />
                    <div class="invalid-feedback prefixErr"></div>
                  </div>

                  <div class="col-md-auto">
                    <label for="warehouse_name" class="form-label">Warehouse Name</label>
                    <input type="text" class="form-control warehouse" id="warehouse_name" name="warehouse_name"
                      value="<?php echo htmlspecialchars($editData['warehouse_name']); ?>" required />
                    <div class="invalid-feedback"></div>
                  </div>

                  <div class="col-md-auto">
                    <label for="stock" class="form-label">Stock</label>
                    <input type="number" class="form-control stock" id="stock" name="stock" min="0"
                      value="<?php echo htmlspecialchars($editData['stock']); ?>" required />
                    <div class="invalid-feedback"></div>
                  </div>

                  <div class="col-md-auto mt-md-4">

                    <div class="mt-3 edit_msg"></div>

                    <div class="mt-3 d-flex gap-2 justify-content-between">
                      <button type="submit" class="btn btn-success">Update</button>
                      <a href="warehouse_list.php" class="btn btn-secondary">Cancel</a>
                    </div>
                  </div>
                </div>
              </form>
            <?php endif; ?>

          </div>

        </div>
      </div>
    </div>

    <!-- Your Sidebar Script -->
    <script src="../assets2/script.js"></script>
    <script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
    <script src="../assets2/warehouse_list.js"></script>
    <!--     <script src="../assets2/add_warehouse.js"></script> -->
    <script>
      function toggleAddForm() {
        const form = document.getElementById("addWarehouseForm");
        form.style.display = (form.style.display === "none" || form.style.display === "") ? "block" : "none";
      }
      window.addField = function() {
        const container = document.getElementById("warehouseStockFields");
        const template = document.getElementById("warehouseRowTemplate");

        const newEntry = template.cloneNode(true);
        newEntry.removeAttribute("id");
        newEntry.classList.remove("d-none"); // make it visible
        newEntry.style.display = "flex";

        // Clear and enable inputs
        $(newEntry).find("input").val("").removeAttr("disabled");

        // Attach "+" button
        const addBtn = newEntry.querySelector(".btn-outline-primary");
        addBtn.onclick = addField;

        // Attach "–" button
        const removeBtn = newEntry.querySelector(".btn-danger");
        removeBtn.onclick = function() {
          this.closest(".warehouse-entry").remove();
        };
        container.appendChild(newEntry);
      };
    </script>
</body>

</html>