<?php
require '../session.php';

if (!is_admin()) {
    header("Location: ../login.php");
    exit;
}

include '../db/config.php';

// Inisialisasi
$selected_loket = isset($_GET['loket']) ? intval($_GET['loket']) : 0;
$alert = "";

// Proses simpan jika POST
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['loket'])) {
    $loket = intval($_POST['loket']);
    $layanan = $_POST['layanan'] ?? [];

    // Hapus layanan lama
    $conn->query("DELETE FROM loket_layanan WHERE id_pengguna = $loket");

    // Simpan layanan baru
    $stmt = $conn->prepare("INSERT INTO loket_layanan (id_pengguna, id_layanan) VALUES (?, ?)");
    foreach ($layanan as $id_layanan) {
        $stmt->bind_param("ii", $loket, $id_layanan);
        $stmt->execute();
    }
    $stmt->close();

    // Redirect agar tidak simpan ulang saat refresh
    header("Location: ?loket=$loket&success=1");
    exit;
}

// Tampilkan alert jika redirect sukses
if (isset($_GET['success']) && $_GET['success'] == 1) {
    $alert = "<div class='alert alert-success'>Berhasil disimpan!</div>";
}

// Ambil layanan yang sudah ditetapkan
$layanan_terpilih = [];
if ($selected_loket) {
    $q = $conn->prepare("SELECT id_layanan FROM loket_layanan WHERE id_pengguna = ?");
    $q->bind_param("i", $selected_loket);
    $q->execute();
    $res = $q->get_result();
    while ($r = $res->fetch_assoc()) {
        $layanan_terpilih[] = $r['id_layanan'];
    }
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
    <!--DataTables CSS -->
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css">
    <!-- endinject -->
    <!-- Plugin css for this page -->
    <link rel="stylesheet" href="src/assets/vendors/datatables.net-bs4/dataTables.bootstrap4.css">
    <link rel="stylesheet" type="text/css" href="src/assets/js/select.dataTables.min.css">
    <!-- End plugin css for this page -->
    <!-- inject:css -->
    <link rel="stylesheet" href="src/assets/css/style.css">
    <!-- endinject -->
    <link rel="shortcut icon" href="src/assets/images/favicon.png" />
    <style>
        select {
            color: black !important;
            background-color: white;
            /* jika ingin latar terang */
        }
    </style>

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
                                <div class="mb-4">
                                    <?= $alert ?>
                                    <!-- Bagian Form -->
                                    <h5 class="mb-3">Pengaturan Layanan per Loket</h5>
                                    <form method="POST">
                                        <div class="mb-3">
                                            <label for="loket" class="form-label">Pilih Loket:</label>
                                            <select name="loket" id="loket" class="form-select" required>
                                                <option value="">-- Pilih Loket --</option>
                                                <?php
                                                $result = $conn->query("SELECT id, nama FROM pengguna WHERE role = 'loket'");
                                                while ($row = $result->fetch_assoc()):
                                                    $selected = ($row['id'] == $selected_loket) ? 'selected' : '';
                                                ?>
                                                    <option value="<?= $row['id'] ?>" <?= $selected ?>><?= htmlspecialchars($row['nama']) ?></option>
                                                <?php endwhile; ?>
                                            </select>
                                        </div>

                                        <div class="mb-3">
                                            <label class="form-label">Layanan yang Ditangani:</label>
                                            <div class="row">
                                                <?php
                                                $result = $conn->query("SELECT * FROM layanan");
                                                while ($row = $result->fetch_assoc()):
                                                    $checked = in_array($row['id'], $layanan_terpilih) ? 'checked' : '';
                                                ?>
                                                    <div class="col-md-6">
                                                        <div class="form-check">
                                                            <input class="form-check-input" style="margin-left: 0.5em;" type="checkbox" name="layanan[]" value="<?= $row['id'] ?>" id="layanan<?= $row['id'] ?>" <?= $checked ?>>
                                                            <label class="form-check-label" for="layanan<?= $row['id'] ?>">
                                                                <?= htmlspecialchars($row['nama_layanan']) ?>
                                                            </label>
                                                        </div>
                                                    </div>
                                                <?php endwhile; ?>
                                            </div>
                                        </div>

                                        <button type="submit" class="btn btn-primary">Simpan</button>
                                    </form>
                                </div>

                                <hr>

                                <div class="mt-4">
                                    <h5 class="mb-3">Daftar Loket Layanan</h5>
                                    <div class="table-responsive">
                                        <table id="riwayatAntrian" class="table table-striped table-bordered">
                                            <thead class="table-dark">
                                                <tr>
                                                    <th>Loket</th>
                                                    <th>Layanan yang ditangani</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php
                                                $query = "SELECT 
                                                            p.nama AS nama_loket,
                                                            GROUP_CONCAT(CONCAT(l.nama_layanan) SEPARATOR ', ') AS layanan_dengan_loket
                                                        FROM loket_layanan ll
                                                        JOIN pengguna p ON ll.id_pengguna = p.id
                                                        JOIN layanan l ON ll.id_layanan = l.id
                                                        GROUP BY ll.id_pengguna";

                                                $res = $conn->query($query);
                                                while ($row = $res->fetch_assoc()):
                                                ?>
                                                    <tr>
                                                        <td><?= htmlspecialchars($row['nama_loket']) ?></td>
                                                        <td><?= htmlspecialchars($row['layanan_dengan_loket']) ?></td>
                                                    </tr>
                                                <?php endwhile; ?>
                                            </tbody>

                                        </table>
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
    <!-- Tambahkan jQuery & DataTables JS -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>

    <!-- Inisialisasi DataTables -->
    <script>
        $(document).ready(function() {
            $('#riwayatAntrian').DataTable({
                "paging": true,
                "searching": true,
                "ordering": true,
                "info": true,
                "language": {
                    "lengthMenu": "Tampilkan _MENU_ entri per halaman",
                    "zeroRecords": "Tidak ada data ditemukan",
                    "info": "Menampilkan _START_ sampai _END_ dari _TOTAL_ entri",
                    "infoEmpty": "Tidak ada data",
                    "search": "Cari:",
                    "paginate": {
                        "first": "Awal",
                        "last": "Akhir",
                        "next": "Selanjutnya",
                        "previous": "Sebelumnya"
                    }
                }
            });
        });
    </script>
    <!-- Script reload saat pilih loket -->
    <script>
        document.getElementById('loket').addEventListener('change', function() {
            const selectedLoket = this.value;
            if (selectedLoket !== "") {
                window.location.href = '?loket=' + selectedLoket;
            }
        });
    </script>
</body>

</html>