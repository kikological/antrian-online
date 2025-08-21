<?php
require 'db/config.php';
// Ambil data layanan
$layanan = mysqli_query($conn, "SELECT * FROM layanan ORDER BY id ASC");
?>
<?php
$query = "SELECT * FROM pengaturan_monitor WHERE id = 1";
$result = mysqli_query($conn, $query);
$data = mysqli_fetch_assoc($result);
$gradStart = $data['gradient_color_start'];
$gradEnd = $data['gradient_color_end'];
?>
<?php
$query = "SELECT * FROM pengaturan_printer WHERE aktif = 1 LIMIT 1";
$result = $conn->query($query);
$setting = $result->fetch_assoc();
?>
<!DOCTYPE html>
<html>
<link rel="stylesheet" href="css/daftar.css">

<head>
    <title>Ambil Antrian</title>
    <style>
        body {
            margin: 0;
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            display: flex;
            flex-direction: column;
            min-height: 100vh;
            position: relative;
            z-index: 0;
        }

        body::before {
            content: "";
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-image: url('<?= !empty($data['logo']) ? '' . $data['logo'] : '' ?>');
            background-repeat: no-repeat;
            background-position: center;
            background-size: contain;
            opacity: 0.08;
            pointer-events: none;
            z-index: -1;
        }

        .header {
            background: linear-gradient(to right, <?= $gradStart; ?>, <?= $gradEnd; ?>);
            color: white;
            padding: 20px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            box-sizing: border-box;
        }

        #modal-cetak {
            display: none;
            transition: all 0.3s ease;
        }

        #modal-cetak.show {
            display: flex;
        }
    </style>
</head>

<body>
    <!-- Modal "Sedang Mencetak" -->
    <div id="modal-cetak" style="
    display: none;
    position: fixed;
    top: 0; left: 0;
    width: 100%; height: 100%;
    background-color: rgba(0, 0, 0, 0.5);
    z-index: 9999;
    align-items: center;
    justify-content: center;
">
        <div style="
        background: white;
        padding: 20px 30px;
        border-radius: 10px;
        font-size: 18px;
        box-shadow: 0 0 10px rgba(0,0,0,0.3);
        text-align: center;
    ">
            ⏳ Sedang mencetak, mohon tunggu...
        </div>
    </div>


    <div class="header">
        <div class="logo-group d-flex align-items-center gap-3">
            <?php if (!empty($data['logo'])): ?>
                <img src="<?= $data['logo']; ?>" alt="Logo 1" class="logo" />
            <?php endif; ?>
            <?php if (!empty($data['logo_2'])): ?>
                <img src="<?= $data['logo_2']; ?>" alt="Logo 2" class="logo" />
            <?php endif; ?>
            <?php if (!empty($data['logo_3'])): ?>
                <img src="<?= $data['logo_3']; ?>" alt="Logo 3" class="logo" />
            <?php endif; ?>
            <?php if (!empty($data['logo_4'])): ?>
                <img src="<?= $data['logo_4']; ?>" alt="Logo 4" class="logo" />
            <?php endif; ?>
        </div>
    </div>
    <h2 style="text-align:center; margin-top: 40px;">Silakan Pilih Layanan</h2>
    <div class="button-container">
        <?php while ($row = mysqli_fetch_assoc($layanan)): ?>
            <form class="ambil-form" data-layanan="<?= (int)$row['id'] ?>">
                <button type="submit" class="layanan-button">
                    <?= htmlspecialchars($row['nama_layanan']) ?>
                </button>
            </form>
        <?php endwhile; ?>
    </div>

    <footer>
        &copy; <?= date('Y') ?> <?= $setting['judul_cadangan']; ?>. Semua Hak Dilindungi.
    </footer>

    <script>
        document.querySelectorAll('.ambil-form').forEach(form => {
            form.addEventListener('submit', function(e) {
                e.preventDefault();

                const idLayanan = this.getAttribute('data-layanan');

                fetch('backend/ambil_antrian.php', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/x-www-form-urlencoded',
                        },
                        body: 'layanan=' + encodeURIComponent(idLayanan)
                    })
                    .then(response => response.json())
                    .then(data => {
                        console.log("Respon dari ambil_antrian.php:", data);

                        if (data.success && data.nomor && data.layanan && data.kode && data.id_layanan) {
                            const kodeNama = `${data.kode}-${data.nomor}`;
                            kirimKePrinter(kodeNama, data.layanan, data.id_layanan);
                        } else {
                            alert('❌ Gagal mengambil antrian.');
                        }
                    })
                    .catch(error => {
                        console.error("❗ Kesalahan jaringan:", error);
                        alert('⚠️ Terjadi kesalahan jaringan.');
                    });
            });
        });

        function showModal() {
            document.getElementById('modal-cetak').style.display = 'flex';
        }

        function hideModal() {
            document.getElementById('modal-cetak').style.display = 'none';
        }

        function kirimKePrinter(kodeNama, namaLayanan, idLayanan) {
            showModal(); // tampilkan modal

            fetch('cetak.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({
                        kode: kodeNama,
                        layanan: namaLayanan,
                        id_layanan: idLayanan
                    })
                })
                .then(response => {
                    return response.json().catch(() => {
                        throw new Error("Respon bukan JSON. Mungkin ada error di cetak.php");
                    });
                })
                .then(result => {
                    setTimeout(() => {
                        hideModal(); // sembunyikan modal setelah 2 detik

                        if (result.success) {
                            console.log("✅ Berhasil cetak antrian!");

                            if (result.htmlMode && result.file) {
                                window.open(result.file, '_blank');
                            }
                        } else {
                            alert("❌ Gagal mencetak: " + result.message);
                        }
                    }, 2000);
                })
                .catch(error => {
                    setTimeout(() => {
                        hideModal();
                        console.error("❗ Kesalahan saat kirimKePrinter:", error);
                        alert("⚠️ Terjadi kesalahan saat mencetak: " + error.message);
                    }, 1500);
                });
        }
    </script>




</body>

</html>