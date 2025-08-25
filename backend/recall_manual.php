<?php
include "../db/config.php";   // ✅ arahkan ke folder db

header('Content-Type: application/json');

if (isset($_GET['nomor'])) {
    $nomor = intval($_GET['nomor']);
    $cek = mysqli_query($conn, "SELECT * FROM antrian WHERE nomor='$nomor' AND DATE(waktu_ambil)=CURDATE() LIMIT 1");

    if (mysqli_num_rows($cek) > 0) {
        mysqli_query($conn, "UPDATE antrian 
                             SET status='dipanggil', waktu_panggil=NOW() 
                             WHERE nomor='$nomor' AND DATE(waktu_ambil)=CURDATE()");
        echo json_encode(["success" => true]);
    } else {
        echo json_encode(["success" => false, "message" => "Nomor tidak ditemukan"]);
    }
} else {
    echo json_encode(["success" => false, "message" => "Parameter nomor kosong"]);
}