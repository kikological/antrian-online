<?php
require '../session.php';
if (!is_loket()) {
    header("Location: ../login.php");
    exit;
}

require '../db/config.php';
$tanggal_hari_ini = date('Y-m-d');
$loket_id = $_SESSION['loket_id'];
$loket_nama = $_SESSION['nama'];

// Ambil layanan yang ditangani oleh loket ini
$query = "SELECT l.* FROM layanan l
          JOIN loket_layanan ll ON l.id = ll.id_layanan
          WHERE ll.id_pengguna = ?
          ORDER BY l.id ASC";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $loket_id);
$stmt->execute();
$layanan = $stmt->get_result();
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
        .dipanggil {
            font-weight: bold;
            color: #e53935;
            animation: smooth-blink 2s ease-in-out 3;
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
                                <h4 class="card-title">Antrian Aktif</h4>
                                <div class="alert mb-1 border">
                                    <strong>Tanggal:</strong> <?= $tanggal_hari_ini; ?>
                                </div>

                                <!-- Tabel Data Antrian -->
                                <div class="table-responsive" id="antrian-table-container">

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
        let isProcessing = false; // Untuk mencegah konflik saat sedang proses klik tombol

        function loadAntrianTable() {
            if (isProcessing) return; // Skip refresh saat sedang proses panggil/recall

            fetch("../backend/get_antrian_table.php")
                .then(res => res.text())
                .then(html => {
                    document.getElementById("antrian-table-container").innerHTML = html;
                    attachButtonHandlers(); // Re-bind setelah data ter-refresh
                })
                .catch(err => console.error("Gagal memuat data antrian:", err));
        }

        function attachButtonHandlers() {
            document.querySelectorAll(".panggil-btn").forEach(button => {
                button.addEventListener("click", async function() {
                    const idLayanan = this.dataset.id;
                    const loket = "<?= $_SESSION['loket_id'] ?>";

                    const formData = new URLSearchParams();
                    formData.append('id_layanan', idLayanan);
                    formData.append('loket', loket);

                    isProcessing = true; // Tandai sedang proses
                    try {
                        const res = await fetch("../backend/panggil_antrian.php", {
                            method: "POST",
                            headers: {
                                "Content-Type": "application/x-www-form-urlencoded"
                            },
                            body: formData
                        });

                        const data = await res.json();
                        if (data.success) {
                            loadAntrianTable();
                        } else {
                            alert("Opps: " + data.message);
                        }
                    } catch (err) {
                        console.error("ERROR:", err);
                        alert("Terjadi kesalahan saat memanggil antrian.");
                    } finally {
                        isProcessing = false;
                    }
                });
            });

            document.querySelectorAll(".recall-btn").forEach(button => {
                button.addEventListener("click", async function() {
                    const idLayanan = this.dataset.id;
                    const loket = "<?= $_SESSION['loket_id'] ?>";

                    isProcessing = true;
                    try {
                        const res = await fetch(`../backend/recall.php?id_layanan=${idLayanan}&loket=${loket}`);
                        const data = await res.json();

                        if (data.success) {
                            alert("Recall berhasil");
                            loadAntrianTable();
                        } else {
                            alert("Recall gagal: " + data.message);
                        }
                    } catch (err) {
                        console.error("Recall error:", err);
                        alert("Terjadi kesalahan saat recall.");
                    } finally {
                        isProcessing = false;
                    }
                });
            });
        }

        // Inisialisasi saat halaman pertama kali dimuat
        document.addEventListener("DOMContentLoaded", function() {
            loadAntrianTable();
            setInterval(loadAntrianTable, 5000); // Auto-refresh tiap 5 detik
        });
    </script>

</body>

</html>



<!DOCTYPE html>
<html>

<head>
    <title>Panel Loket</title>
</head>

<body>
    <div class="panel">

    </div>

    <script>
        document.addEventListener("DOMContentLoaded", function() {
            const buttons = document.querySelectorAll(".panggil-btn");

            buttons.forEach(button => {
                button.addEventListener("click", async function() {
                    const idLayanan = this.dataset.id;
                    const loket = "<?= $_SESSION['loket_id'] ?>";

                    const formData = new URLSearchParams();
                    formData.append('id_layanan', idLayanan);
                    formData.append('loket', loket);

                    try {
                        const res = await fetch("../backend/panggil_antrian.php", {
                            method: "POST",
                            headers: {
                                "Content-Type": "application/x-www-form-urlencoded"
                            },
                            body: formData
                        });

                        const data = await res.json();
                        console.log("RESPON DARI SERVER:", data);

                        if (data.success) {
                            alert("Berhasil memanggil " + data.kode + data.nomor);
                        } else {
                            alert("Gagal: " + data.message);
                        }

                    } catch (err) {
                        console.error("ERROR:", err);
                        alert("Terjadi kesalahan saat memanggil antrian.");
                    }
                });
            });

            const recallButtons = document.querySelectorAll(".recall-btn");
            recallButtons.forEach(button => {
                button.addEventListener("click", async function() {
                    const idLayanan = this.dataset.id;
                    const loket = "<?= $_SESSION['loket_id'] ?>";

                    try {
                        const res = await fetch(`../backend/recall.php?id_layanan=${idLayanan}&loket=${loket}`);
                        const data = await res.json();

                        if (data.success) {
                            alert("Antrian berhasil dipanggil ulang.");
                        } else {
                            alert("Recall gagal: " + data.message);
                        }
                    } catch (err) {
                        console.error("Recall error:", err);
                        alert("Terjadi kesalahan saat recall.");
                    }
                });
            });
        });
    </script>
</body>

</html>