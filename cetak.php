<?php
require __DIR__ . '/vendor/autoload.php';
require __DIR__ . '/db/config.php';

use Mike42\Escpos\Printer;
use Mike42\Escpos\PrintConnectors\WindowsPrintConnector;
use Mike42\Escpos\EscposImage;

header('Content-Type: application/json');
date_default_timezone_set("Asia/Jakarta");

// Logging
$logFile = __DIR__ . '/debug_log.txt';
function logDebug($message)
{
    global $logFile;
    file_put_contents($logFile, "[" . date("Y-m-d H:i:s") . "] " . $message . PHP_EOL, FILE_APPEND);
}

// Ambil data dari frontend
$input = file_get_contents('php://input');
logDebug("RAW POST: " . $input);

$data = json_decode($input, true);
logDebug("PARSED DATA: " . json_encode($data));

$id_layanan = intval($data['id_layanan'] ?? 0);
$layanan    = strtoupper($data['layanan'] ?? 'LAYANAN');
$tanggal    = date('l, d-m-Y H:i:s');

if ($id_layanan <= 0) {
    logDebug("❌ ID layanan tidak valid.");
    echo json_encode(["success" => false, "message" => "ID layanan tidak valid."]);
    exit;
}

// Ambil data layanan dari DB
$queryLayanan = mysqli_query($conn, "SELECT kode_layanan, nama_layanan FROM layanan WHERE id = $id_layanan");
$layananData = mysqli_fetch_assoc($queryLayanan);
$kodeLayanan = $layananData['kode_layanan'] ?? 'A';

// Ambil nomor antrian terakhir
$stmt = mysqli_prepare($conn, "SELECT MAX(nomor) FROM antrian WHERE id_layanan = ?");
mysqli_stmt_bind_param($stmt, "i", $id_layanan);
mysqli_stmt_execute($stmt);
mysqli_stmt_bind_result($stmt, $lastNomor);
mysqli_stmt_fetch($stmt);
mysqli_stmt_close($stmt);

$nextNomor = intval($lastNomor) + 1;

// Simpan antrian ke DB
$stmt = mysqli_prepare($conn, "INSERT INTO antrian (id_layanan, nomor) VALUES (?, ?)");
mysqli_stmt_bind_param($stmt, "ii", $id_layanan, $nextNomor);
$insertSuccess = mysqli_stmt_execute($stmt);
mysqli_stmt_close($stmt);

if (!$insertSuccess) {
    logDebug("❌ Gagal menyimpan ke database.");
    echo json_encode(["success" => false, "message" => "Gagal menyimpan ke database."]);
    exit;
}

$kode = sprintf("%s-%d", $kodeLayanan, $nextNomor);
logDebug("✅ Data antrian disimpan: $kode | $layanan");

// Ambil pengaturan printer
$query = "SELECT * FROM pengaturan_printer WHERE aktif = 1 LIMIT 1";
$result = mysqli_query($conn, $query);
$setting = mysqli_fetch_assoc($result);

if (!$setting) {
    logDebug("Pengaturan printer tidak ditemukan");
    echo json_encode(["success" => false, "message" => "Pengaturan printer tidak ditemukan."]);
    exit;
}

$mode = $setting['mode_printer'] ?? 'escpos';

if ($mode === 'escpos') {
    // ESC/POS
    $printerName = $setting['nama_printer'];
    logDebug("Mode printer ESC/POS - printer: $printerName");

    try {
        $connector = new WindowsPrintConnector($printerName);
        $printer = new Printer($connector);
        $printer->setJustification(Printer::JUSTIFY_CENTER);

        // Logo
        try {
            $logoPath = realpath(__DIR__ . '/' . $setting['path_logo']);
            if ($logoPath && file_exists($logoPath)) {
                $logo = EscposImage::load($logoPath, false);
                $printer->bitImage($logo);
            } else {
                logDebug("Logo tidak ditemukan di: $logoPath");
            }
        } catch (Exception $e) {
            logDebug("Gagal load logo: " . $e->getMessage());
        }

        // Header
        $printer->setTextSize(2, 2);
        $printer->text($setting['judul_cadangan'] . "\n");

        $printer->setTextSize(1, 1);
        $printer->setEmphasis(true);
        $printer->text($setting['alamat_header'] . "\n\n");

        $printer->text(strtoupper($setting['label_antrian']) . "\n");
        $printer->setEmphasis(false);
        $printer->text("------------------------------\n");

        $printer->setTextSize(2, 2);
        $printer->text($kode . "\n");

        $printer->setTextSize(1, 1);
        $printer->text("------------------------------\n");
        $printer->setEmphasis(true);
        $printer->text($layanan . "\n\n");

        $printer->setEmphasis(false);
        $printer->text($setting['teks_footer'] . "\n\n");
        $printer->text($tanggal . "\n");

        $printer->cut();
        $printer->close();

        logDebug("✅ Cetak sukses (ESC/POS)");
        echo json_encode(["success" => true]);
    } catch (Exception $e) {
        logDebug("❌ ERROR CETAK: " . $e->getMessage());
        echo json_encode(["success" => false, "message" => $e->getMessage()]);
    }
} else {
    // HTML print
    logDebug("Mode printer HTML biasa");

    $html = "
<html>
<head>
    <title>Cetak Antrian</title>
    <style>
        @media print {
            @page { size: 58mm auto; margin: 0; }
            body { width: 58mm; margin: 0; padding: 0; text-align: center; font-family: Arial; }
        }
        h1 { font-size: 60px; margin: 0; }
        h2 { font-size: 24px; margin: 0; }
        p { font-size: 16px; margin: 0; }
        img.logo { max-width: 100px; margin-top: 5px; }
    </style>
</head>
<body onload='window.print(); setTimeout(() => window.close(), 1500);'>
    <img src='{$setting['path_logo']}' class='logo' />
    <h2>{$setting['judul_cadangan']}</h2>
    <p>{$setting['alamat_header']}</p>
    <hr />
    <h1>$kode</h1>
    <h2>$layanan</h2>
    <hr />
    <p>{$setting['teks_footer']}</p>
    <p><small>$tanggal</small></p>
</body>
</html>
";
    $fileName = 'print_temp.html';
    file_put_contents($fileName, $html);
    logDebug("✅ HTML cetak disiapkan: $fileName");

    echo json_encode([
        "success" => true,
        "htmlMode" => true,
        "file" => $fileName
    ]);
}
