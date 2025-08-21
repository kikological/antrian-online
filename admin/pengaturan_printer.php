<?php
require '../db/config.php';
require '../session.php';

if (!is_admin()) {
    header("Location: ../login.php");
    exit;
}

$query = "SELECT * FROM pengaturan_printer WHERE aktif = 1 LIMIT 1";
$result = $conn->query($query);
$setting = $result->fetch_assoc();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $judul_cadangan = $_POST['judul_cadangan'];
    $alamat_header = $_POST['alamat_header'];
    $label_antrian = $_POST['label_antrian'];
    $teks_footer = $_POST['teks_footer'];
    $nama_printer = $_POST['nama_printer'];
    $mode_printer = $_POST['mode_printer']; // Tambahan

    if (!empty($_FILES['logo']['name'])) {
        $allowedTypes = ['image/png'];
        $maxWidth = 300;

        $fileType = mime_content_type($_FILES['logo']['tmp_name']);

        if (!in_array($fileType, $allowedTypes)) {
            $_SESSION['error'] = "Logo harus berformat PNG.";
            header("Location: pengaturan_printer");
            exit();
        }

        list($width, $height) = getimagesize($_FILES['logo']['tmp_name']);
        $aspectRatio = $height / $width;

        if ($width > $maxWidth) {
            $newWidth = $maxWidth;
            $newHeight = intval($maxWidth * $aspectRatio);
        } else {
            $newWidth = $width;
            $newHeight = $height;
        }

        $srcImage = imagecreatefrompng($_FILES['logo']['tmp_name']);
        $resizedImage = imagecreatetruecolor($newWidth, $newHeight);

        imagealphablending($resizedImage, false);
        imagesavealpha($resizedImage, true);
        $transparent = imagecolorallocatealpha($resizedImage, 0, 0, 0, 127);
        imagefill($resizedImage, 0, 0, $transparent);

        imagecopyresampled($resizedImage, $srcImage, 0, 0, 0, 0, $newWidth, $newHeight, $width, $height);
        imagefilter($resizedImage, IMG_FILTER_GRAYSCALE);

        $uploadDir = __DIR__ . '/../uploads/';
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0755, true);
        }

        $uploadName = 'logo_' . time() . '.png';
        $uploadPath = $uploadDir . $uploadName;

        $path_logo = 'uploads/' . $uploadName;

        if (!empty($setting['path_logo']) && strpos($setting['path_logo'], 'uploads/') === 0) {
            $oldLogoPath = realpath(__DIR__ . '/../' . $setting['path_logo']);
            if ($oldLogoPath && file_exists($oldLogoPath)) {
                unlink($oldLogoPath);
            }
        }

        if (!imagepng($resizedImage, $uploadPath)) {
            $_SESSION['error'] = "Gagal menyimpan logo.";
            header("Location: pengaturan_printer");
            exit();
        }

        imagedestroy($srcImage);
        imagedestroy($resizedImage);
    } else {
        $path_logo = $setting['path_logo'];
    }

    // Update dengan mode_printer
    $stmt = $conn->prepare("UPDATE pengaturan_printer SET 
        path_logo = ?, 
        judul_cadangan = ?, 
        alamat_header = ?, 
        label_antrian = ?, 
        teks_footer = ?, 
        nama_printer = ?,
        mode_printer = ?
        WHERE id = ?");

    $stmt->bind_param("sssssssi", $path_logo, $judul_cadangan, $alamat_header, $label_antrian, $teks_footer, $nama_printer, $mode_printer, $setting['id']);

    if (!$stmt->execute()) {
        $_SESSION['error'] = "Terjadi kesalahan: " . $stmt->error;
    } elseif ($stmt->affected_rows == 0) {
        $_SESSION['error'] = "Tidak ada perubahan data!";
    } else {
        $_SESSION['success'] = "Pengaturan berhasil diperbarui!";
        unset($_SESSION['form_data']);
    }

    header("Location: pengaturan_printer");
    exit();
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
                                <h4 class="card-title">Pengaturan Printer Thermal</h4>
                                <hr>
                                </p>
                                <?php if (isset($_SESSION['success'])): ?>
                                    <div class="alert alert-success">
                                        <?= $_SESSION['success'];
                                        unset($_SESSION['success']); ?>
                                    </div>
                                <?php endif; ?>

                                <?php if (isset($_SESSION['error'])): ?>
                                    <div class="alert alert-danger">
                                        <?= $_SESSION['error'];
                                        unset($_SESSION['error']); ?>
                                    </div>
                                <?php endif; ?>

                                <form method="POST" enctype="multipart/form-data">
                                    <div class="mb-3">
                                        <label class="form-label">Mode Printer</label>
                                        <select name="mode_printer" id="mode_printer" class="form-control">
                                            <option value="biasa" <?= $setting['mode_printer'] === 'biasa' ? 'selected' : '' ?>>Biasa (Cetak ke printer Windows)</option>
                                            <option value="escpos" <?= $setting['mode_printer'] === 'escpos' ? 'selected' : '' ?>>ESC/POS (Cetak langsung via PHP)</option>
                                        </select>
                                    </div>

                                    <div class="mb-3" id="printer_name_container" style="display: none;">
                                        <label class="form-label">Nama Printer Thermal</label>
                                        <textarea name="nama_printer" rows="3" class="form-control"><?= htmlspecialchars($setting['nama_printer']) ?></textarea>
                                        <small class="text-muted">
                                            Contoh: <code>RONGTA 58mm Series Printer</code>,<code>EPOS58D</code>,<code>POS58</code> (Sesuaikan nama Printer Anda yang sudah dishare).<br>
                                            <strong>Catatan:</strong> Pastikan driver printer sudah di install dan berfungsi, kemudian di-*share* terlebih dahulu agar bisa diakses oleh sistem.
                                            <br><a href="https://youtu.be/ijBrAsxdhyw?si=m0gU1fuYe5NBDEHM" target="_blank">Cara share printer di Windows</a>
                                        </small>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label">Judul</label>
                                        <input type="text" name="judul_cadangan" class="form-control" value="<?= htmlspecialchars($setting['judul_cadangan']) ?>">
                                    </div>

                                    <div class="mb-3">
                                        <label class="form-label">Alamat</label>
                                        <textarea name="alamat_header" class="form-control" rows="3"><?= htmlspecialchars($setting['alamat_header']) ?></textarea>
                                    </div>

                                    <div class="mb-3">
                                        <label class="form-label">Label Antrian</label>
                                        <input type="text" name="label_antrian" class="form-control" value="<?= htmlspecialchars($setting['label_antrian']) ?>">
                                    </div>

                                    <div class="mb-3">
                                        <label class="form-label">Teks Footer</label>
                                        <textarea name="teks_footer" rows="3" class="form-control"><?= htmlspecialchars($setting['teks_footer']) ?></textarea>
                                    </div>

                                    <div class="mb-3">
                                        <label class="form-label">Logo</label>
                                        <input type="file" class="form-control" name="logo">

                                        <?php if (!empty($setting['path_logo'])): ?>
                                            <p><img src="../<?= $setting['path_logo'] ?>" alt="Logo" style="max-height: 100px;"></p>
                                        <?php endif; ?>
                                        <small class="text-muted">
                                            Format PNG (Transparan). Disarankan ukuran (265x130) dan hitam putih untuk hasil cetak terbaik.
                                        </small>

                                    </div>
                                    <button type="submit" class="btn btn-primary">Simpan Pengaturan</button>
                                </form>
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
    <script>
        const modeSelect = document.getElementById('mode_printer');
        const printerNameContainer = document.getElementById('printer_name_container');

        function togglePrinterName() {
            if (modeSelect.value === 'escpos') {
                printerNameContainer.style.display = 'block';
            } else {
                printerNameContainer.style.display = 'none';
            }
        }

        // Jalankan sekali saat page load
        togglePrinterName();

        // Pasang event listener untuk perubahan select
        modeSelect.addEventListener('change', togglePrinterName);
    </script>
</body>

</html>