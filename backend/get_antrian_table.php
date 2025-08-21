<?php
require '../db/config.php';
session_start();

if (!isset($_SESSION['loket_id'])) {
    exit;
}

$loket_id = $_SESSION['loket_id'];
$tanggal_hari_ini = date('Y-m-d');

// Query 1: Statistik layanan
$query = "
    SELECT 
        l.id AS id_layanan,
        l.nama_layanan,
        l.kode_layanan,
        (
            SELECT CONCAT(l2.kode_layanan, LPAD(a2.nomor, 2, '0'))
            FROM antrian a2
            JOIN layanan l2 ON a2.id_layanan = l2.id
            WHERE a2.id_layanan = l.id
              AND a2.status != 'menunggu'
              AND DATE(a2.waktu_ambil) = ?
              AND a2.nomor IS NOT NULL
            ORDER BY a2.waktu_panggil DESC
            LIMIT 1
        ) AS nomor_terakhir_dipanggil,
        SUM(CASE WHEN a.status = 'menunggu' THEN 1 ELSE 0 END) AS jumlah_menunggu,
        SUM(CASE WHEN a.status != 'menunggu' THEN 1 ELSE 0 END) AS jumlah_dipanggil,
        COUNT(a.id) AS total_antrian
    FROM layanan l
    JOIN loket_layanan ll ON l.id = ll.id_layanan
    LEFT JOIN antrian a ON a.id_layanan = l.id AND DATE(a.waktu_ambil) = ?
    WHERE ll.id_pengguna = ?
    GROUP BY l.id
    ORDER BY l.nama_layanan ASC
";

$stmt = $conn->prepare($query);
$stmt->bind_param("ssi", $tanggal_hari_ini, $tanggal_hari_ini, $loket_id);
$stmt->execute();
$result = $stmt->get_result();

// Query 2: Daftar antrian yang belum terpanggil
$query_menunggu = "
    SELECT 
        a.id,
        l.nama_layanan,
        l.kode_layanan,
        a.nomor,
        a.waktu_ambil
    FROM antrian a
    JOIN layanan l ON a.id_layanan = l.id
    JOIN loket_layanan ll ON l.id = ll.id_layanan
    WHERE a.status = 'menunggu'
      AND DATE(a.waktu_ambil) = ?
      AND ll.id_pengguna = ?
    ORDER BY a.waktu_ambil ASC
";

$stmt2 = $conn->prepare($query_menunggu);
$stmt2->bind_param("si", $tanggal_hari_ini, $loket_id);
$stmt2->execute();
$result_menunggu = $stmt2->get_result();
?>

<!-- Tabel Statistik Layanan -->
<h5>Statistik Layanan</h5>
<table class="table table-striped">
    <thead class="table-dark">
        <tr>
            <th>Layanan</th>
            <th>Menunggu</th>
            <th>Dipanggil</th>
            <th>Total Antrian</th>
            <th>Nomor Terakhir Dipanggil</th>
            <th>Aksi</th>
        </tr>
    </thead>
    <tbody>
        <?php while ($row = $result->fetch_assoc()): ?>
            <tr>
                <td><?= htmlspecialchars($row['nama_layanan']) ?></td>
                <td><?= $row['jumlah_menunggu'] ?></td>
                <td><?= $row['jumlah_dipanggil'] ?></td>
                <td><?= $row['total_antrian'] ?></td>
                <td class="dipanggil"><?= $row['nomor_terakhir_dipanggil'] ?? '-' ?></td>
                <td>
                    <button class="btn btn-success panggil-btn" data-id="<?= $row['id_layanan'] ?>">🔊 Panggil</button>
                    <button class="btn btn-warning recall-btn" data-id="<?= $row['id_layanan'] ?>">🔁 Recall</button>
                </td>
            </tr>
        <?php endwhile; ?>
    </tbody>
</table>

<!-- Antrian Belum Terpanggil -->
<h5 class="mt-5">Antrian Belum Terpanggil</h5>

<div class="table-responsive" style="max-height: 400px; overflow-y: auto;">
    <table class="table table-bordered mb-0">
        <thead class="table-light sticky-top bg-light">
            <tr>
                <th>Nomor Antrian</th>
                <th>Waktu Ambil</th>
            </tr>
        </thead>
        <tbody>
            <?php if ($result_menunggu->num_rows > 0): ?>
                <?php while ($row = $result_menunggu->fetch_assoc()): ?>
                    <tr>
                        <td><?= $row['kode_layanan'] . str_pad($row['nomor'], 2, '0', STR_PAD_LEFT) ?></td>
                        <td><?= date('H:i', strtotime($row['waktu_ambil'])) ?></td>
                    </tr>
                <?php endwhile; ?>
            <?php else: ?>
                <tr>
                    <td colspan="2" class="text-center text-muted">Tidak ada antrian</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>