<?php
require '../session.php';

if (!is_admin()) {
    header("Location: ../login.php");
    exit;
}
?>

<?php
require '../db/config.php';
$result = mysqli_query($conn, "SELECT * FROM layanan");
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
                                <h4 class="card-title">Data Layanan</h4>
                                <?php
                                $layanan = mysqli_query($conn, "SELECT * FROM layanan");
                                $total_layanan = mysqli_num_rows($layanan);
                                ?>
                                <!-- Tombol Add Layanan -->
                                <?php if ($total_layanan < 6): ?>
                                    <div class="d-flex justify-content-between align-items-center mb-3">

                                        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addLayananModal">+ Add Layanan</button>
                                    </div>
                                <?php endif; ?>


                                <div class="table-responsive">
                                    <table id="riwayatAntrian" class="table table-striped">
                                        <thead class="table-dark">
                                            <tr>
                                                <th>Kode</th>
                                                <th>Nama</th>
                                                <th>Warna</th>
                                                <th>Suara Loket</th>
                                                <th>Aksi</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php while ($row = mysqli_fetch_assoc($result)): ?>
                                                <tr>
                                                    <td><?= $row['kode_layanan'] ?></td>
                                                    <td><?= $row['nama_layanan'] ?></td>
                                                    <td>
                                                        <div style="width: 20px; height: 20px; background: <?= $row['warna'] ?>;"></div>
                                                    </td>
                                                    <td>
                                                        <?php if (!empty($row['suara_mp3'])): ?>
                                                            <?= htmlspecialchars($row['suara_mp3']) ?><br>
                                                            <button class="btn btn-sm btn-success" onclick="document.getElementById('audio-<?= $row['id'] ?>').play()">▶️ Play</button>
                                                            <audio id="audio-<?= $row['id'] ?>" style="display:none;">
                                                                <source src="../audio/<?= htmlspecialchars($row['suara_mp3']) ?>" type="audio/mpeg">
                                                                Browser tidak mendukung pemutar audio.
                                                            </audio>
                                                        <?php else: ?>
                                                            <span class="text-muted">Tidak ada suara</span>
                                                        <?php endif; ?>
                                                    </td>


                                                    <td>
                                                        <button
                                                            class="btn btn-sm btn-warning"
                                                            data-bs-toggle="modal"
                                                            data-bs-target="#editLayananModal"
                                                            data-id="<?= $row['id'] ?>"
                                                            data-kode="<?= $row['kode_layanan'] ?>"
                                                            data-nama="<?= $row['nama_layanan'] ?>"
                                                            data-warna="<?= $row['warna'] ?>"
                                                            data-suara="<?= $row['suara_mp3'] ?>">Edit</button>
                                                        <a href="del_layanan?id=<?= $row['id'] ?>" class="btn btn-sm btn-danger" onclick="return confirm('Menghapus layanan ini juga akan menghapus semua antrian terkait. Lanjutkan?')">Hapus</a>
                                                    </td>
                                                </tr>
                                            <?php endwhile; ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>

                            <div class="modal fade" id="addLayananModal" tabindex="-1" aria-labelledby="addLayananLabel" aria-hidden="true">
                                <div class="modal-dialog">
                                    <form action="add_layanan" method="POST" enctype="multipart/form-data" class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title" id="addLayananLabel">Tambah Layanan</h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
                                        </div>
                                        <div class="modal-body">
                                            <div class="mb-2">
                                                <label>Kode Layanan</label>
                                                <input type="text" name="kode_layanan" class="form-control" required>
                                            </div>
                                            <div class="mb-2">
                                                <label>Nama Layanan</label>
                                                <input type="text" name="nama_layanan" class="form-control" required>
                                            </div>
                                            <div class="mb-2">
                                                <label>Warna</label>
                                                <input type="color" name="warna" class="form-control form-control-color" required>
                                            </div>
                                            <div class="mb-2">
                                                <label>Suara</label>
                                                <input type="file" name="suara_mp3" class="form-control" accept="audio/mpeg" required>
                                            </div>

                                        </div>
                                        <div class="modal-footer">
                                            <button type="submit" class="btn btn-secondary" name="simpan" btn-success">Simpan</button>
                                            <button type="button" class="btn btn-warning" data-bs-dismiss="modal">Batal</button>
                                        </div>
                                    </form>
                                </div>
                            </div>

                            <div class="modal fade" id="editLayananModal" tabindex="-1" aria-labelledby="editLayananLabel" aria-hidden="true">
                                <div class="modal-dialog">
                                    <form action="edit_layanan.php" method="POST" enctype="multipart/form-data" class="modal-content">

                                        <div class="modal-header">
                                            <h5 class="modal-title" id="editLayananLabel">Edit Layanan</h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
                                        </div>
                                        <div class="modal-body">
                                            <input type="hidden" name="id" id="edit-id">
                                            <div class="mb-2">
                                                <label>Kode Layanan</label>
                                                <input type="text" name="kode_layanan" id="edit-kode" class="form-control" required>
                                            </div>
                                            <div class="mb-2">
                                                <label>Nama Layanan</label>
                                                <input type="text" name="nama_layanan" id="edit-nama" class="form-control" required>
                                            </div>
                                            <div class="mb-2">
                                                <label>Warna</label>
                                                <input type="color" name="warna" id="edit-warna" class="form-control form-control-color" required>
                                            </div>
                                            <div class="mb-2">
                                                <label>Suara</label>
                                                <input type="file" name="suara_mp3" class="form-control" accept="audio/mpeg">
                                                <input type="hidden" name="suara_lama" id="edit-suara-lama">
                                                <small class="text-muted" id="nama-suara-lama">File saat ini: -</small>
                                            </div>



                                        </div>
                                        <div class="modal-footer">
                                            <button type="submit" class="btn btn-warning">Update</button>
                                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                                        </div>
                                    </form>
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
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const editModal = document.getElementById('editLayananModal');
            editModal.addEventListener('show.bs.modal', function(event) {
                const button = event.relatedTarget;
                document.getElementById('edit-id').value = button.getAttribute('data-id');
                document.getElementById('edit-kode').value = button.getAttribute('data-kode');
                document.getElementById('edit-nama').value = button.getAttribute('data-nama');
                document.getElementById('edit-warna').value = button.getAttribute('data-warna');
                document.getElementById('edit-suara-lama').value = button.getAttribute('data-suara');

                // Tampilkan nama file suara lama
                document.getElementById('nama-suara-lama').textContent = "File saat ini: " + button.getAttribute('data-suara');
            });
        });
    </script>


</body>

</html>