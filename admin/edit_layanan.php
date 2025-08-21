<?php
require '../session.php';

if (!is_admin()) {
    header("Location: ../login.php");
    exit;
}

require '../db/config.php';

// Validasi ID
if (!isset($_POST['id']) || !is_numeric($_POST['id'])) {
    die('ID layanan tidak valid.');
}
$id = intval($_POST['id']);

// Ambil dan filter input dari form
$nama  = trim(mysqli_real_escape_string($conn, $_POST['nama_layanan']));
$kode  = trim(mysqli_real_escape_string($conn, $_POST['kode_layanan']));
$warna = trim(mysqli_real_escape_string($conn, $_POST['warna']));
$suara = isset($_POST['suara_lama']) ? $_POST['suara_lama'] : ''; // Ambil suara lama dari input hidden

// Handle upload MP3 jika ada file baru
if (isset($_FILES['suara_mp3']) && $_FILES['suara_mp3']['error'] === UPLOAD_ERR_OK) {
    $file_tmp  = $_FILES['suara_mp3']['tmp_name'];
    $file_type = mime_content_type($file_tmp);
    $file_name = basename($_FILES['suara_mp3']['name']);
    $ext       = pathinfo($file_name, PATHINFO_EXTENSION);

    if ($file_type === 'audio/mpeg' && strtolower($ext) === 'mp3') {
        $target_dir = '../audio/';
        if (!is_dir($target_dir)) {
            mkdir($target_dir, 0755, true);
        }

        // Bersihkan nama file untuk mencegah karakter berbahaya
        $clean_file_name = preg_replace('/[^a-zA-Z0-9_\-\.]/', '_', strtolower($file_name));
        $target_file = $target_dir . $clean_file_name;

        // Pindahkan file ke folder audio
        if (move_uploaded_file($file_tmp, $target_file)) {
            // Hapus file lama jika berbeda
            if ($suara !== $clean_file_name && file_exists($target_dir . $suara)) {
                unlink($target_dir . $suara);
            }
            $suara = $clean_file_name;
        } else {
            die("Gagal mengunggah file MP3.");
        }
    } else {
        die("File harus berupa MP3.");
    }
}

// Update data layanan ke database
$update = $conn->prepare("UPDATE layanan SET nama_layanan=?, kode_layanan=?, warna=?, suara_mp3=? WHERE id=?");
$update->bind_param("ssssi", $nama, $kode, $warna, $suara, $id);

if ($update->execute()) {
    header("Location: layanan.php?updated=1");
    exit;
} else {
    die("Gagal memperbarui data.");
}


$update->close();
$conn->close();
