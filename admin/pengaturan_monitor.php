<?php
session_start();
include '../db/config.php';

$id = 1; // ID pengaturan monitor selalu 1
$upload_dir = __DIR__ . '/../uploads/';

if (!is_dir($upload_dir)) {
    mkdir($upload_dir, 0755, true);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $running_text = $_POST['running_text'] ?? '';
    $ukuran_teks = $_POST['ukuran_teks'] ?? 16;
    $warna_card_cuaca = $_POST['warna_card_cuaca'] ?? '#ffffff';
    $gradient_color_start = $_POST['gradient_color_start'] ?? '#000000';
    $gradient_color_end = $_POST['gradient_color_end'] ?? '#ffffff';

    $video_file = '';

    // Ambil data lama dari database (untuk hapus file lama jika perlu)
    $oldData = $conn->query("SELECT * FROM pengaturan_monitor WHERE id=$id")->fetch_assoc();
    $oldVideo = $oldData['video_file'] ?? '';
    $oldLogos = [
        'logo' => $oldData['logo'] ?? '',
        'logo_2' => $oldData['logo_2'] ?? '',
        'logo_3' => $oldData['logo_3'] ?? '',
        'logo_4' => $oldData['logo_4'] ?? '',
    ];

    // ========== PROSES VIDEO ==========
    if (!empty($_FILES['video_file_upload']['name'])) {
        $original_name = basename($_FILES["video_file_upload"]["name"]);
        $filename_only = time() . "_" . $original_name;
        $target_file = $upload_dir . $filename_only;

        if (move_uploaded_file($_FILES["video_file_upload"]["tmp_name"], $target_file)) {
            if (!empty($oldVideo) && strpos($oldVideo, 'http') === false) {
                $old_path = __DIR__ . '/../' . $oldVideo;
                if (file_exists($old_path)) unlink($old_path);
            }
            $video_file = "uploads/" . $filename_only;
        }
    } elseif (!empty($_POST['video_file_url'])) {
        $video_file = $_POST['video_file_url'];
        if (!empty($oldVideo) && strpos($oldVideo, 'http') === false) {
            $old_path = __DIR__ . '/../' . $oldVideo;
            if (file_exists($old_path)) unlink($old_path);
        }
    } else {
        $video_file = $oldVideo;
    }

    // ========== PROSES LOGO 1 – 4 ==========
    $logo_fields = ['logo', 'logo_2', 'logo_3', 'logo_4'];
    $logo_paths = [];

    foreach ($logo_fields as $field) {
        if (!empty($_FILES[$field]['name'])) {
            $filename = time() . "_" . basename($_FILES[$field]["name"]);
            $target_path = $upload_dir . $filename;

            if (move_uploaded_file($_FILES[$field]["tmp_name"], $target_path)) {
                // Hapus logo lama
                $old_logo_path = __DIR__ . '/../' . ($oldLogos[$field] ?? '');
                if (!empty($oldLogos[$field]) && file_exists($old_logo_path)) {
                    unlink($old_logo_path);
                }

                $logo_paths[$field] = "uploads/" . $filename;
            }
        } else {
            $logo_paths[$field] = $oldLogos[$field] ?? '';
        }
    }

    // ========== UPDATE DATABASE ==========
    $stmt = $conn->prepare("UPDATE pengaturan_monitor 
        SET running_text=?, video_file=?, ukuran_teks=?, 
            logo=?, logo_2=?, logo_3=?, logo_4=?, 
            warna_card_cuaca=?, gradient_color_start=?, gradient_color_end=? 
        WHERE id=?");

    $stmt->bind_param(
        "ssisssssssi",
        $running_text,
        $video_file,
        $ukuran_teks,
        $logo_paths['logo'],
        $logo_paths['logo_2'],
        $logo_paths['logo_3'],
        $logo_paths['logo_4'],
        $warna_card_cuaca,
        $gradient_color_start,
        $gradient_color_end,
        $id
    );

    $stmt->execute();
    $stmt->close();

    $_SESSION['success'] = '✅ Pengaturan monitor berhasil diperbarui.';
    header("Location: pengaturan_monitor");
    exit;
}

// Ambil data pengaturan monitor
$data = $conn->query("SELECT * FROM pengaturan_monitor WHERE id=1")->fetch_assoc();
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
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">

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
                            <div class="card">
                                <div class="card-body">
                                    <?php if (isset($_SESSION['success'])): ?>
                                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                                            <?= $_SESSION['success'];
                                            unset($_SESSION['success']); ?>
                                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                                        </div>
                                    <?php endif; ?>

                                    <h4 class="card-title">Pengaturan Monitor</h4>
                                    <form method="POST" enctype="multipart/form-data">
                                        <div class="row">
                                            <!-- Kolom kiri -->
                                            <div class="col-md-6">
                                                <div class="mb-3">
                                                    <label>Running Text</label>
                                                    <textarea name="running_text" class="form-control" required><?= htmlspecialchars($data['running_text']) ?></textarea>
                                                </div>

                                                <div class="mb-3">
                                                    <label>Ukuran Runing Teks</label>
                                                    <input type="number" name="ukuran_teks" class="form-control" value="<?= $data['ukuran_teks'] ?>" required>
                                                </div>

                                                <div class="mb-3">
                                                    <label>Upload Video (MP4)</label>
                                                    <input type="file" name="video_file_upload" accept="video/mp4" class="form-control">
                                                    <?php if (!empty($data['video_file']) && strpos($data['video_file'], 'http') === false): ?>
                                                        <div class="mt-2">
                                                            <video src="../<?= htmlspecialchars($data['video_file']) ?>" controls style="max-width: 100%; height: auto; max-height: 200px;"></video>
                                                        </div>
                                                    <?php endif; ?>
                                                </div>

                                                <div class="mb-3">
                                                    <label>Atau Masukkan URL YouTube</label>
                                                    <input type="text" name="video_file_url" class="form-control" placeholder="https://youtube.com" value="<?= (strpos($data['video_file'], 'http') === 0) ? htmlspecialchars($data['video_file']) : '' ?>">
                                                    <div class="text-muted mt-1">
                                                        Saat ini:
                                                        <?php if (!empty($data['video_file'])): ?>
                                                            <?php if (strpos($data['video_file'], 'http') === 0): ?>
                                                                <a href="<?= htmlspecialchars($data['video_file']) ?>" target="_blank"><?= htmlspecialchars($data['video_file']) ?></a>
                                                            <?php else: ?>
                                                                <code><?= htmlspecialchars($data['video_file']) ?></code>
                                                            <?php endif; ?>
                                                        <?php else: ?>
                                                            <em>Belum ada</em>
                                                        <?php endif; ?>
                                                    </div>
                                                </div>

                                                <!-- Logo 1 -->
                                                <div class="mb-3">
                                                    <label for="logo" class="form-label">Upload Logo 1</label>
                                                    <input class="form-control" type="file" name="logo" id="logo" accept="image/*">
                                                    <?php if (!empty($data['logo'])): ?>
                                                        <div class="mt-2 d-flex align-items-center">
                                                            <img src="../<?= htmlspecialchars($data['logo']) ?>" alt="Logo 1" style="max-height: 60px; margin-right: 10px;">
                                                            <a href="del_logo.php?logo=logo" onclick="return confirm('Yakin ingin menghapus Logo 1?')" class="text-danger">
                                                                <i class="fas fa-trash-alt"></i>
                                                            </a>
                                                        </div>
                                                    <?php endif; ?>

                                                </div>

                                                <!-- Logo 2 -->
                                                <div class="mb-3">
                                                    <label for="logo_2" class="form-label">Upload Logo 2</label>
                                                    <input class="form-control" type="file" name="logo_2" id="logo_2" accept="image/*">
                                                    <?php if (!empty($data['logo_2'])): ?>
                                                        <div class="mt-2 d-flex align-items-center">
                                                            <img src="../<?= htmlspecialchars($data['logo_2']) ?>" alt="Logo 2" style="max-height: 60px; margin-right: 10px;">
                                                            <a href="del_logo.php?logo=logo_2" onclick="return confirm('Yakin ingin menghapus Logo 2?')" class="text-danger">
                                                                <i class="fas fa-trash-alt"></i>
                                                            </a>
                                                        </div>
                                                    <?php endif; ?>

                                                </div>
                                            </div>

                                            <!-- Kolom kanan -->
                                            <div class="col-md-6">
                                                <!-- Logo 3 -->
                                                <div class="mb-3">
                                                    <label for="logo_3" class="form-label">Upload Logo 3</label>
                                                    <input class="form-control" type="file" name="logo_3" id="logo_3" accept="image/*">
                                                    <?php if (!empty($data['logo_3'])): ?>
                                                        <div class="mt-2 d-flex align-items-center">
                                                            <img src="../<?= htmlspecialchars($data['logo_3']) ?>" alt="Logo 3" style="max-height: 60px; margin-right: 10px;">
                                                            <a href="del_logo.php?logo=logo_3" onclick="return confirm('Yakin ingin menghapus Logo 3?')" class="text-danger">
                                                                <i class="fas fa-trash-alt"></i>
                                                            </a>
                                                        </div>
                                                    <?php endif; ?>

                                                </div>

                                                <!-- Logo 4 -->
                                                <div class="mb-3">
                                                    <label for="logo_4" class="form-label">Upload Logo 4</label>
                                                    <input class="form-control" type="file" name="logo_4" id="logo_4" accept="image/*">
                                                    <?php if (!empty($data['logo_4'])): ?>
                                                        <div class="mt-2 d-flex align-items-center">
                                                            <img src="../<?= htmlspecialchars($data['logo_4']) ?>" alt="Logo 1" style="max-height: 60px; margin-right: 10px;">
                                                            <a href="del_logo.php?logo=logo_4" onclick="return confirm('Yakin ingin menghapus Logo 4?')" class="text-danger">
                                                                <i class="fas fa-trash-alt"></i>
                                                            </a>
                                                        </div>
                                                    <?php endif; ?>

                                                </div>

                                                <!-- Warna Card Cuaca -->
                                                <div class="mb-3">
                                                    <label for="warna_card_cuaca" class="form-label">Warna Card Cuaca</label>
                                                    <input class="form-control form-control-color" type="color" id="warna_card_cuaca" name="warna_card_cuaca" value="<?= htmlspecialchars($data['warna_card_cuaca'] ?? '#ffffff') ?>" title="Pilih warna">
                                                </div>

                                                <!-- Warna Gradasi Header -->
                                                <div class="mb-3">
                                                    <label class="form-label">Warna Gradasi Header</label>
                                                    <div class="d-flex gap-3">
                                                        <div>
                                                            <label for="gradient_color_start" class="form-label small">Warna Awal</label>
                                                            <input type="color" class="form-control form-control-color" id="gradient_color_start" name="gradient_color_start" value="<?= htmlspecialchars($data['gradient_color_start'] ?? '#000000') ?>">
                                                        </div>
                                                        <div>
                                                            <label for="gradient_color_end" class="form-label small">Warna Akhir</label>
                                                            <input type="color" class="form-control form-control-color" id="gradient_color_end" name="gradient_color_end" value="<?= htmlspecialchars($data['gradient_color_end'] ?? '#ffffff') ?>">
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Tombol Submit -->
                                        <button type="submit" class="btn btn-primary mt-3">Simpan Perubahan</button>
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
    <script src="src/assets/vendors/js/vendor.bundle.base.js"></script>
    <script src="src/assets/vendors/bootstrap-datepicker/bootstrap-datepicker.min.js"></script>
    <script src="src/assets/vendors/chart.js/chart.umd.js"></script>
    <script src="src/assets/vendors/progressbar.js/progressbar.min.js"></script>
    <script src="src/assets/js/off-canvas.js"></script>
    <script src="src/assets/js/template.js"></script>
    <script src="src/assets/js/settings.js"></script>
    <script src="src/assets/js/hoverable-collapse.js"></script>
    <script src="src/assets/js/todolist.js"></script>
    <script src="src/assets/js/jquery.cookie.js" type="text/javascript"></script>
    <script src="src/assets/js/dashboard.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
</body>

</html>