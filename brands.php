<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
require '../auth_session.php';
$user_name = $_SESSION['user_name'];
$role = $_SESSION['role'];

require '../db.php';
require "../actions.php";

/* this is the crud handling section using the ['action'] */
if ($_SERVER['REQUEST_METHOD'] === "POST" && isset($_POST['action']) && $role === "admin") {

    /* this is for the add action */
    if ($_POST['action'] === "add") {
        $brand = $_POST['brand'];

        if (!empty($brand)) {
            $check_sql = "SELECT * FROM brands";
            if ($r = mysqli_query($conn, $check_sql)) {
                while ($row = mysqli_fetch_assoc($r)) {
                    if ($row['brand_name'] == $brand) {
                        echo "<script>alert('existing brand name not allowed');window.history.back();</script>";
                        exit;
                    }
                }
            }

            $sql = "INSERT INTO brands(brand_name,added_at) VALUES('$brand',NOW())";

            if (mysqli_query($conn, $sql)) {
                $action = "new brand added:$brand";
                log_action($conn, $_SESSION['user_id'], $user_name, $action, $role);

                header("location:brands.php");
            }
        }
    }

    /* this for update */
    if ($_POST['action'] === "update") {
        $id = $_GET['update'];
        $brand = $_POST['brand'];
        if (!empty($brand)) {
            $sql = "UPDATE brands SET brand_name = '$brand',updated_at=NOW() WHERE id='$id'";
            $result = mysqli_query($conn, $sql);

            if ($result) {
                /* var_dump($id);
                exit; */
                $action = "brand updated: $brand";
                log_action($conn, $_SESSION['user_id'], $user_name, $action, $role);
                header("location: brands.php");
            } else {
                echo "Qerr";
                exit;
            }
        }
    }
}
/* showing current data in update form */
$currentRow = null;
if (isset($_GET['update']) && $role === "admin") {
    $update_id = $_GET['update'];
    $sql = "SELECT * FROM brands WHERE id='$update_id'";
    $result = mysqli_query($conn, $sql);
    if (mysqli_num_rows($result) > 0) {
        $currentRow = mysqli_fetch_assoc($result);
    }
}
/* for the delete operation */
if (isset($_GET['delete']) && $role === "admin") {
    $id = $_GET['delete'];
    $sql = "DELETE FROM brands WHERE id='$id'";
    if (mysqli_query($conn, $sql)) {
        header("location: brands.php");
    }
}
$sql = "SELECT * FROM brands";
$result = mysqli_query($conn, $sql);

$head_var = $_GET['val'] ?? "create";
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <title>Brands</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" />
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
    <!-- Bootstrap JS Bundle (includes Popper) -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <!-- bootstrap icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
    <link rel="stylesheet" href="../assets2/styles.css" />
</head>

<body>
    <!-- Sidebar will be injected here -->
    <div class="toggle-btn" id="toggleBtn">
        <i class="fas fa-bars"></i>
    </div>
    <?php include "../includes/sidebar.php"; ?>
    <!-- Main Content -->
    <div class="main-content" id="mainContent">
        <div id="mainHeader" class="header d-flex justify-content-between align-items-center px-4 py-2 bg-white border-bottom shadow-sm  w-100">
            <h4 class="fw-bold mb-0">Add Brands</h4>
            <?php include "../users/nav_profile.php"; ?>
        </div>

        <!-- Push content below fixed header -->
        <div style="padding-top: 30px;"></div>
        <div class="container-fluid px-3">
            <div class="card shadow-sm rounded-4 mt-4">
                <div class="card-body">
                    <!-- add brand -->
                    <?php if ($role == "admin" && $head_var == "create") : ?>
                        <form action="" method="post" id="addForm">
                            <!-- Row with two columns: Input & Button -->
                            <div class="row g-3">
                                <!-- Brand Name -->
                                <label for="brandNameAdd" class="form-label">Brand Name</label>
                                <div class="col-md-10">
                                    <input type="text" id="brandNameAdd" name="brand" class="form-control" placeholder="Enter brand name">
                                    <div id="nameErrAdd" class="invalid-feedback text-start"></div>
                                    <input type="hidden" name="action" value="add">
                                </div>
                                <!-- Add Button -->
                                <div class="col-md-2 d-flex justify-content-center">
                                    <div class="">
                                        <button type="submit" class="btn custom-orange-btn">Add</button>
                                    </div>
                                </div>
                            </div>
                        </form>
                    <?php endif; ?>
                    <!-- update brand -->
                    <?php if ($role == "admin" && $head_var == "update") : ?>
                        <form action="" method="post" id="updateForm" class="">
                            <!-- Row with two columns: Input & Button -->
                            <div class="row g-3">
                                <!-- Brand Name -->
                                <label for="brandNameUpdate" class="form-label">update Brand Name</label>
                                <div class="col-md-9">
                                    <input type="text" id="brandNameUpdate" name="brand" class="form-control" value="<?php echo $currentRow['brand_name'] ?>">
                                    <div id="nameErrUpdate" class="invalid-feedback text-start"></div>
                                    <input type="hidden" name="action" value="update">
                                </div>
                                <!-- Add Button -->
                                <div class="col-md-2 d-flex ">
                                    <div class=" d-flex">
                                        <button type="submit" class="btn btn-success mx-2">Update</button>
                                        <a href="brands.php" class="btn btn-danger" style="text-decoration: none;">Cancel</a>
                                    </div>
                                </div>

                            </div>
                        </form>
                    <?php endif; ?>
                    <!-- to show alert msg -->
                    <div id="msg" class="mt-3"></div>

                    <!-- Brand Table -->
                    <div class="table-responsive mt-4">
                        <table class="table table-hover text-center align-middle table-bordered rounded overflow-hidden">
                            <thead class="custom-thead text-center">
                                <tr>
                                    <th style="width: 20%;">S.No</th>
                                    <th style="width: 50%;">Brand Name</th>

                                    <?php if ($role === 'admin') : ?>
                                        <th>ACTIONS</th>
                                    <?php endif; ?>
                                </tr>
                            </thead>
                            <tbody id="brandTableBody">
                                <?php $serial = 1; ?>
                                <?php while ($row = mysqli_fetch_assoc($result)) : ?>
                                    <tr>
                                        <td><?php echo $serial++; ?></td>
                                        <td><?php echo $row['brand_name']; ?></td>
                                        <!-- actions columns only visible for admin  -->
                                        <?php if ($role === 'admin') : ?>
                                            <td class="text-center">
                                                <a href="?update=<?php echo $row['id']; ?>&val=update" class="btn btn-sm btn-outline-primary me-1" title="Edit">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                                <a href="?delete=<?php echo $row['id']; ?>" class="btn btn-sm btn-outline-danger" title="Delete" onclick="return confirm('wanna delete this Brand?');">
                                                    <i class="fas fa-trash-alt"></i>
                                                </a>
                                            </td>
                                        <?php endif; ?>
                                    </tr>
                                <?php endwhile; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- jQuery (must be before your JS files) -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <!--  scripts -->
    <script src="../assets2/script.js"></script>
    <script src="../assets2/brands.js"></script>
</body>

</html>