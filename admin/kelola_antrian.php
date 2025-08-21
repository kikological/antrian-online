<?php
require '../db/config.php';

$tanggal_hari_ini = date('Y-m-d');

// Cek apakah ada antrian yang belum direset
$cekAntrianKemarin = mysqli_query($conn, "
    SELECT COUNT(*) AS jumlah FROM antrian 
    WHERE DATE(waktu_ambil) < '$tanggal_hari_ini'
");

$dataCek = mysqli_fetch_assoc($cekAntrianKemarin);
$adaAntrianBelumReset = $dataCek['jumlah'] > 0;

// Proses reset antrian
if (isset($_POST['reset_antrian'])) {
    $ambil_antrian = mysqli_query($conn, "SELECT * FROM antrian WHERE DATE(waktu_ambil) < '$tanggal_hari_ini'");
    $jumlah_diproses = 0;

    while ($data = mysqli_fetch_assoc($ambil_antrian)) {
        $jumlah_diproses++;
        $id_layanan = $data['id_layanan'];
        $nomor = $data['nomor'];
        $status = $data['status'];
        $waktu_ambil = $data['waktu_ambil'];
        $waktu_panggil = $data['waktu_panggil'] ?? null;

        mysqli_query($conn, "INSERT INTO history_antrian (id_layanan, nomor, status, waktu_ambil, waktu_panggil)
            VALUES ('$id_layanan', '$nomor', '$status', '$waktu_ambil', " . ($waktu_panggil ? "'$waktu_panggil'" : "NULL") . ")");
    }

    if ($jumlah_diproses > 0) {
        mysqli_query($conn, "DELETE FROM antrian WHERE DATE(waktu_ambil) < '$tanggal_hari_ini'");
        header("Location: " . $_SERVER['PHP_SELF'] . "?reset=success&jumlah=$jumlah_diproses");
    } else {
        header("Location: " . $_SERVER['PHP_SELF'] . "?reset=none");
    }
    exit;
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
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

</head>

<body class="with-welcome-text">
    <?php if ($adaAntrianBelumReset): ?>
        <!-- Modal -->
        <div class="modal fade" id="resetModal" tabindex="-1" aria-labelledby="resetModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content border-danger">
                    <div class="modal-header bg-danger text-white">
                        <h5 class="modal-title" id="resetModalLabel">Reset Antrian Diperlukan</h5>
                    </div>
                    <div class="modal-body">
                        Terdapat antrian dari hari sebelumnya yang belum direset. Apakah Anda ingin melakukan reset sekarang?
                    </div>
                    <div class="modal-footer">
                        <form method="post">
                            <button type="submit" name="reset_antrian" class="btn btn-danger">Reset Sekarang</button>
                        </form>
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                    </div>
                </div>
            </div>
        </div>

        <script>
            // Tampilkan modal setelah halaman dimuat
            document.addEventListener('DOMContentLoaded', function() {
                var resetModal = new bootstrap.Modal(document.getElementById('resetModal'));
                resetModal.show();
            });
        </script>
    <?php endif; ?>

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
                                <?php if (isset($_GET['reset'])): ?>
                                    <?php if ($_GET['reset'] === 'success'): ?>
                                        <div class="alert alert-success">
                                            Berhasil mereset <?= htmlspecialchars($_GET['jumlah']) ?> antrian ke riwayat.
                                        </div>
                                    <?php elseif ($_GET['reset'] === 'none'): ?>
                                        <div class="alert alert-warning">
                                            Tidak ada antrian sebelum hari ini yang bisa di-reset.
                                        </div>
                                    <?php endif; ?>
                                <?php endif; ?>

                                <h4 class="card-title">Data Antrian</h4>
                                <form method="POST" onsubmit="return confirm('Yakin ingin mereset semua antrian?')">
                                    <button type="submit" name="reset_antrian" class="btn btn-danger mb-3">Reset Antrian</button>
                                </form>

                                <div class="alert mb-1 border">
                                    <strong>Tanggal:</strong> <?= $tanggal_hari_ini; ?>
                                </div>

                                <!-- Tabel Data Antrian -->
                                <div class="table-responsive">
                                    <?php
                                    $query = "
                        SELECT 
                            l.kode_layanan,
                            GROUP_CONCAT(DISTINCT l.nama_layanan SEPARATOR ', ') AS nama_layanan,
                            SUM(CASE WHEN a.status = 'menunggu' THEN 1 ELSE 0 END) AS jumlah_menunggu,
                            SUM(CASE WHEN a.status != 'menunggu' THEN 1 ELSE 0 END) AS jumlah_dipanggil,
                            COUNT(a.id) AS total_antrian
                        FROM layanan l
                        LEFT JOIN antrian a ON a.id_layanan = l.id AND DATE(a.waktu_ambil) = '$tanggal_hari_ini'
                        GROUP BY l.kode_layanan
                        ORDER BY nama_layanan ASC
                    ";
                                    $result = mysqli_query($conn, $query);

                                    if (!$result) {
                                        die("Query error: " . mysqli_error($conn));
                                    }
                                    ?>

                                    <table class="table table-striped">
                                        <thead class="table-dark">
                                            <tr>
                                                <th>Layanan</th>
                                                <th>Menunggu Dipanggil</th>
                                                <th>Sudah Dipanggil</th>
                                                <th>Total</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php while ($row = mysqli_fetch_assoc($result)): ?>
                                                <tr>
                                                    <td><?= htmlspecialchars($row['nama_layanan']) ?></td>
                                                    <td><?= $row['jumlah_menunggu'] ?></td>
                                                    <td><?= $row['jumlah_dipanggil'] ?></td>
                                                    <td><?= $row['total_antrian'] ?></td>
                                                </tr>
                                            <?php endwhile; ?>
                                        </tbody>
                                    </table>
                                </div>

                                <!-- Tabel Riwayat Antrian Hari Ini -->
                                <h4 class="card-title mt-5">Riwayat Antrian Per Tanggal</h4>
                                <?php
                                $query_history = "
                                                SELECT 
                                                    DATE(waktu_ambil) AS tanggal,
                                                    COUNT(*) AS total_antrian,
                                                    SUM(CASE WHEN status = 'menunggu' THEN 1 ELSE 0 END) AS jumlah_menunggu,
                                                    SUM(CASE WHEN status != 'menunggu' THEN 1 ELSE 0 END) AS jumlah_dipanggil
                                                FROM history_antrian
                                                GROUP BY DATE(waktu_ambil)
                                                ORDER BY tanggal DESC
                                            ";
                                $result_history = mysqli_query($conn, $query_history);

                                if (!$result_history) {
                                    echo "<div class='alert alert-danger'>Gagal mengambil data riwayat: " . mysqli_error($conn) . "</div>";
                                } elseif (mysqli_num_rows($result_history) === 0) {
                                    echo "<div class='alert alert-info'>Belum ada riwayat antrian.</div>";
                                } else {
                                ?>
                                    <div class="table-responsive mt-3">
                                        <table id="riwayatAntrian" class="table table-bordered table-striped">
                                            <thead class="table-dark">
                                                <tr>
                                                    <th>Tanggal</th>
                                                    <th>Total Antrian</th>
                                                    <th>Menunggu</th>
                                                    <th>Sudah Dipanggil</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php while ($row = mysqli_fetch_assoc($result_history)): ?>
                                                    <tr>
                                                        <td><?= htmlspecialchars($row['tanggal']) ?></td>
                                                        <td><?= $row['total_antrian'] ?></td>
                                                        <td><?= $row['jumlah_menunggu'] ?></td>
                                                        <td><?= $row['jumlah_dipanggil'] ?></td>
                                                    </tr>
                                                <?php endwhile; ?>
                                            </tbody>
                                        </table>
                                    </div>
                                <?php } ?>

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
</body>

</html>