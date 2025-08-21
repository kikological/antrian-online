<?php
require '../session.php';
if (!is_admin()) {
    header("Location: ../login.php");
    exit;
}

include '../db/config.php';

// Tambah/Edit pengguna
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = isset($_POST['id']) ? intval($_POST['id']) : 0;
    $username = trim($_POST['username']);
    $nama = trim($_POST['nama']);
    $password = $_POST['password'];
    $role = $_POST['role'];

    if (!$username || !$nama || !$role || !in_array($role, ['admin', 'loket'])) {
        echo "<div style='color: red;'>Lengkapi semua kolom.</div>";
    } else {
        if ($id > 0) {
            // EDIT
            if (!empty($password)) {
                $hashed = password_hash($password, PASSWORD_DEFAULT);
                $stmt = $conn->prepare("UPDATE pengguna SET username = ?, nama = ?, password = ?, role = ? WHERE id = ?");
                $stmt->bind_param("ssssi", $username, $nama, $hashed, $role, $id);
            } else {
                $stmt = $conn->prepare("UPDATE pengguna SET username = ?, nama = ?, role = ? WHERE id = ?");
                $stmt->bind_param("sssi", $username, $nama, $role, $id);
            }
            $stmt->execute();
            echo "<div style='color: green;'>Pengguna berhasil diubah.</div>";
            $stmt->close();
        } else {
            // TAMBAH
            $check = $conn->prepare("SELECT id FROM pengguna WHERE username = ?");
            $check->bind_param("s", $username);
            $check->execute();
            $check->store_result();

            if ($check->num_rows > 0) {
                echo "<div style='color: red;'>Username sudah digunakan.</div>";
            } else {
                $hashed = password_hash($password, PASSWORD_DEFAULT);
                $stmt = $conn->prepare("INSERT INTO pengguna (username, nama, password, role) VALUES (?, ?, ?, ?)");
                $stmt->bind_param("ssss", $username, $nama, $hashed, $role);
                $stmt->execute();
                echo "<div style='color: green;'>Pengguna berhasil ditambahkan.</div>";
                $stmt->close();
            }
            $check->close();
        }
    }
}

// Hapus pengguna
if (isset($_GET['hapus'])) {
    $id = intval($_GET['hapus']);
    $stmt = $conn->prepare("DELETE FROM pengguna WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    echo "<div style='color: green;'>Pengguna berhasil dihapus.</div>";
    $stmt->close();
}

// Ambil data pengguna
$sql = "SELECT * FROM pengguna";
$result = $conn->query($sql);
if (!$result) {
    die("Query error: " . $conn->error);
}
?>


<!DOCTYPE html>
<html lang="en">

<head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>Dashboard Administartor </title>
    <!-- plugins:css -->
    <link rel="stylesheet" href="src/assets/vendors/feather/feather.css">
    <link rel="stylesheet" href="src/assets/vendors/mdi/css/materialdesignicons.min.css">
    <link rel="stylesheet" href="src/assets/vendors/ti-icons/css/themify-icons.css">
    <link rel="stylesheet" href="src/assets/vendors/font-awesome/css/font-awesome.min.css">
    <link rel="stylesheet" href="src/assets/vendors/typicons/typicons.css">
    <link rel="stylesheet" href="src/assets/vendors/simple-line-icons/css/simple-line-icons.css">
    <link rel="stylesheet" href="src/assets/vendors/css/vendor.bundle.base.css">
    <link rel="stylesheet" href="src/assets/vendors/bootstrap-datepicker/bootstrap-datepicker.min.css">
    <!-- endinject -->
    <!-- Plugin css for this page -->
    <link rel="stylesheet" href="src/assets/vendors/datatables.net-bs4/dataTables.bootstrap4.css">
    <link rel="stylesheet" type="text/css" href="src/assets/js/select.dataTables.min.css">
    <!-- End plugin css for this page -->
    <!-- inject:css -->
    <link rel="stylesheet" href="src/assets/css/style.css">
    <!-- endinject -->
    <link rel="shortcut icon" href="src/assets/images/favicon.png" />
</head>

<body class="with-welcome-text">
    <div class="container-scroller">
        <!-- partial:partials/_navbar.html -->
        <?php include 'navbar.php' ?>
        <!-- partial -->
        <div class="container-fluid page-body-wrapper">
            <!-- partial:partials/_sidebar.html -->
            <?php include 'sidebar.php' ?>
            <!-- partial -->
            <div class="main-panel">
                <div class="content-wrapper">
                    <div class="col-lg-12 grid-margin stretch-card">
                        <div class="card">
                            <div class="card-body">
                                <h4 class="card-title">Kelola Pengguna</h4>
                                <hr>
                                </p>
                                <div class="table-responsive">

                                    <?php if (isset($_SESSION['success'])): ?>
                                        <div class="alert alert-success"> <?= $_SESSION['success'];
                                                                            unset($_SESSION['success']); ?> </div>
                                    <?php endif; ?>

                                    <?php if (isset($_SESSION['error'])): ?>
                                        <div class="alert alert-danger"> <?= $_SESSION['error'];
                                                                            unset($_SESSION['error']); ?> </div>
                                    <?php endif; ?>

                                    <button class="btn btn-primary mb-3" data-bs-toggle="modal" data-bs-target="#modalTambah">Tambah</button>

                                    <table class="table table-striped">
                                        <thead class="table-dark">
                                            <tr>
                                                <th>No</th>
                                                <th>Nama</th>
                                                <th>Username</th>
                                                <th>Aksi</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php
                                            $no = 1;
                                            while ($row = $result->fetch_assoc()): ?>
                                                <tr>
                                                    <td><?= $no++ ?></td>
                                                    <td><?= $row['nama']; ?></td>
                                                    <td><?= $row['username']; ?></td>
                                                    <td>
                                                        <button class="btn btn-warning btn-sm" data-bs-toggle="modal" data-bs-target="#modalEdit<?= $row['id']; ?>">Edit</button>
                                                        <a href="?hapus=<?= $row['id']; ?>" class="btn btn-danger btn-sm" onclick="return confirm('Yakin ingin menghapus?');">Hapus</a>
                                                    </td>
                                                </tr>

                                                <!-- Modal Edit -->
                                                <div class="modal fade" id="modalEdit<?= $row['id']; ?>" tabindex="-1">
                                                    <div class="modal-dialog">
                                                        <div class="modal-content">
                                                            <div class="modal-header">
                                                                <h5 class="modal-title">Edit Pengguna</h5>
                                                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                                            </div>
                                                            <div class="modal-body">
                                                                <form method="POST">
                                                                    <!-- hidden ID untuk identifikasi edit -->
                                                                    <input type="hidden" name="id" value="<?= $row['id']; ?>">
                                                                    <div class="mb-3">
                                                                        <label>Nama</label>
                                                                        <input type="text" name="nama" class="form-control" value="<?= $row['nama']; ?>" required>
                                                                    </div>
                                                                    <div class="mb-3">
                                                                        <label>Username</label>
                                                                        <input type="text" name="username" class="form-control" value="<?= $row['username']; ?>" required>
                                                                    </div>
                                                                    <div class="mb-3">
                                                                        <label>Password <small>(kosongkan jika tidak diubah)</small></label>
                                                                        <input type="password" name="password" class="form-control">
                                                                    </div>
                                                                    <div class="mb-3">
                                                                        <label>Role</label>
                                                                        <select name="role" class="form-control" required>
                                                                            <option value="loket" <?= $row['role'] === 'loket' ? 'selected' : ''; ?>>Loket</option>
                                                                            <option value="admin" <?= $row['role'] === 'admin' ? 'selected' : ''; ?>>Admin</option>
                                                                        </select>
                                                                    </div>
                                                                    <button type="submit" class="btn btn-primary">Simpan</button>
                                                                </form>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>

                                            <?php endwhile; ?>
                                        </tbody>
                                    </table>
                                    <!-- Modal Tambah -->
                                    <div class="modal fade" id="modalTambah" tabindex="-1">
                                        <div class="modal-dialog">
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <h5 class="modal-title">Tambah Pengguna</h5>
                                                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                                </div>
                                                <div class="modal-body">
                                                    <form method="POST">
                                                        <!-- hidden ID = 0 berarti TAMBAH -->
                                                        <input type="hidden" name="id" value="0">
                                                        <div class="mb-3">
                                                            <label>Nama</label>
                                                            <input type="text" name="nama" class="form-control" required>
                                                        </div>
                                                        <div class="mb-3">
                                                            <label>Username</label>
                                                            <input type="text" name="username" class="form-control" required>
                                                        </div>
                                                        <div class="mb-3">
                                                            <label>Password</label>
                                                            <input type="password" name="password" class="form-control" required>
                                                        </div>
                                                        <div class="mb-3">
                                                            <label>Role</label>
                                                            <select name="role" class="form-control" required>
                                                                <option value="">-- Pilih Role --</option>
                                                                <option value="loket">Loket</option>
                                                                <option value="admin">Admin</option>
                                                            </select>
                                                        </div>
                                                        <button type="submit" class="btn btn-primary">Simpan</button>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- content-wrapper ends -->
                <!-- partial:partials/_footer.html -->
                <?php include 'footer.php' ?>
                <!-- partial -->
            </div>
            <!-- main-panel ends -->
        </div>
        <!-- page-body-wrapper ends -->
    </div>
    <!-- container-scroller -->
    <!-- plugins:js -->
    <script src="src/assets/vendors/js/vendor.bundle.base.js"></script>
    <script src="src/assets/vendors/bootstrap-datepicker/bootstrap-datepicker.min.js"></script>
    <!-- endinject -->
    <!-- Plugin js for this page -->
    <script src="src/assets/vendors/chart.js/chart.umd.js"></script>
    <script src="src/assets/vendors/progressbar.js/progressbar.min.js"></script>
    <!-- End plugin js for this page -->
    <!-- inject:js -->
    <script src="src/assets/js/off-canvas.js"></script>
    <script src="src/assets/js/template.js"></script>
    <script src="src/assets/js/settings.js"></script>
    <script src="src/assets/js/hoverable-collapse.js"></script>
    <script src="src/assets/js/todolist.js"></script>
    <!-- endinject -->
    <!-- Custom js for this page-->
    <script src="src/assets/js/jquery.cookie.js" type="text/javascript"></script>
    <script src="src/assets/js/dashboard.js"></script>
    <!-- <script src="src/assets/js/Chart.roundedBarCharts.js"></script> -->
    <!-- End custom js for this page-->
</body>

</html>