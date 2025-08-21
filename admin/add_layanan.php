<?php
require '../session.php';

if (!is_admin()) {
    header("Location: ../login.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    require '../db/config.php';

    // Ambil input
    $kode  = trim(mysqli_real_escape_string($conn, $_POST['kode_layanan']));
    $nama  = trim(mysqli_real_escape_string($conn, $_POST['nama_layanan']));
    $warna = trim(mysqli_real_escape_string($conn, $_POST['warna']));

    $suara_filename = null;

    if (isset($_FILES['suara_mp3']) && $_FILES['suara_mp3']['error'] === UPLOAD_ERR_OK) {
        $file_tmp  = $_FILES['suara_mp3']['tmp_name'];
        $file_type = mime_content_type($file_tmp);
        $original_name = $_FILES['suara_mp3']['name'];
        $ext = pathinfo($original_name, PATHINFO_EXTENSION);

        // Validasi hanya file MP3
        if ($file_type === 'audio/mpeg' && strtolower($ext) === 'mp3') {
            $target_dir = '../audio/';
            if (!is_dir($target_dir)) {
                mkdir($target_dir, 0755, true);
            }

            // Gunakan nama asli, tapi disanitasi agar aman
            $safe_name = preg_replace('/[^a-zA-Z0-9_\-\.]/', '_', strtolower($original_name));
            $target_file = $target_dir . $safe_name;

            // Simpan nama file ke DB
            $suara_filename = $safe_name;

            if (!move_uploaded_file($file_tmp, $target_file)) {
                die("Gagal mengunggah file suara.");
            }
        } else {
            die("File harus berupa MP3.");
        }
    }

    // Simpan data ke database
    $stmt = $conn->prepare("INSERT INTO layanan (kode_layanan, nama_layanan, warna, suara_mp3) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("ssss", $kode, $nama, $warna, $suara_filename);

    if ($stmt->execute()) {
        header('Location: layanan.php');
        exit;
    } else {
        die("Gagal menyimpan data layanan: " . $stmt->error);
    }

    $stmt->close();
    $conn->close();
} else {
    header('Location: layanan.php');
    exit;
}
