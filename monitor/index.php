<?php
require '../db/config.php';
$layanan = mysqli_query($conn, "SELECT * FROM layanan ORDER BY id ASC");
?>

<?php
$query = "SELECT * FROM pengaturan_monitor WHERE id = 1";
$result = mysqli_query($conn, $query);
$data = mysqli_fetch_assoc($result);
$gradStart = $data['gradient_color_start'];
$gradEnd = $data['gradient_color_end'];
$info = $data['warna_card_cuaca'];
$videoFile = $data['video_file'] ?? '';
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Monitor Antrian</title>
    <link rel="stylesheet" href="../css/monitor.css">
    <link href="https://fonts.cdnfonts.com/css/ds-digital" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <style>
        .header {
            margin-left: -10px;
            margin-right: -10px;
            margin-top: -10px;
            margin-bottom: 10px;
            padding: 10px;
            width: calc(100% + 20px);
            /* untuk kompensasi padding main */
            background: linear-gradient(to right, <?= $gradStart; ?>, <?= $gradEnd; ?>);
            display: flex;
            align-items: center;
            justify-content: space-between;
            box-sizing: border-box;
        }


        .weather-time-card {
            background: <?= $info; ?>;
            color: white;
            padding: 10px;
            border-radius: 10px;
            font-weight: bold;
            font-size: 16px;
            text-align: center;
            box-shadow: 2px 2px 10px rgba(0, 0, 0, 0.2);
            width: 100%;
        }
    </style>
</head>

<body>
    <div id="audioModal" class="modal-overlay">
        <div class="modal-content">
            <p>Klik tombol Aktifkan Audio + Fullscreen untuk </p>
            <p>Mengaktifkan audio di halaman ini dan membuat halaman menjadi fullscreen</p>
            <button class="btn-suara" onclick="handleAktifkanAudio()">🔊 Aktifkan Audio + Fullscreen</button>
        </div>
    </div>


    <main>
        <div class="header">
            <div class="logo-group d-flex align-items-center gap-3">
                <?php if (!empty($data['logo'])): ?>
                    <img src="../<?= $data['logo']; ?>" alt="Logo 1" class="logo" />
                <?php endif; ?>
                <?php if (!empty($data['logo_2'])): ?>
                    <img src="../<?= $data['logo_2']; ?>" alt="Logo 2" class="logo" />
                <?php endif; ?>
                <?php if (!empty($data['logo_3'])): ?>
                    <img src="../<?= $data['logo_3']; ?>" alt="Logo 3" class="logo" />
                <?php endif; ?>
                <?php if (!empty($data['logo_4'])): ?>
                    <img src="../<?= $data['logo_4']; ?>" alt="Logo 4" class="logo" />
                <?php endif; ?>
            </div>
        </div>

        <div class="top-section">
            <div class="video-section">
                <?php if (isYoutubeUrl($videoFile)): ?>
                    <div class="youtube-wrapper">
                        <iframe
                            id="ytPlayer"
                            src="<?= embedYoutube($videoFile) ?>&enablejsapi=1"
                            frameborder="0"
                            allow="autoplay; fullscreen; encrypted-media"
                            allowfullscreen>
                        </iframe>
                    </div>
                <?php elseif (!empty($videoFile)): ?>
                    <video id="myVideo" autoplay muted loop>
                        <source src="../<?= htmlspecialchars($videoFile) ?>" type="video/mp4">
                        Browser Anda tidak mendukung video.
                    </video>
                <?php else: ?>
                    <p style="text-align:center;">Video tidak tersedia</p>
                <?php endif; ?>
            </div>


            <?php
            // Fungsi bantu
            function isYoutubeUrl($url)
            {
                return strpos($url, 'youtube.com') !== false || strpos($url, 'youtu.be') !== false;
            }

            function embedYoutube($url)
            {
                if (
                    preg_match('/youtu\.be\/([^\?&]+)/', $url, $matches) ||
                    preg_match('/youtube\.com.*[?&]v=([^\?&]+)/', $url, $matches)
                ) {
                    $videoId = $matches[1];
                    return "https://www.youtube.com/embed/$videoId?autoplay=1&mute=1&loop=1&playlist=$videoId";
                }
                return $url;
            }
            ?>
            <div class="right-section">
                <div class="weather-time-card">
                    <div class="jam" id="jam" style="font-size: 36px;">--:--:--</div>
                    <marquee behavior="scroll" direction="left" scrollamount="5" style="font-size: <?= $data['ukuran_teks'] ?>px; color: white;">
                        <?= htmlspecialchars($data['running_text']); ?>
                    </marquee>
                    <p id="weather"> Memuat cuaca...</p>
                    <p id="date-time"> Memuat waktu...</p>
                </div>
                <div class="queue" id="mainCard1"></div>
            </div>
        </div>

        <div class="bottom-queues" id="bottomCards">
            <!-- Antrian tambahan -->
        </div>
    </main>
    <script>
        // API Cuaca (Gunakan API Key dari https://www.weatherapi.com/)
        async function fetchWeather() {
			const apiKey = "af3b303bcc114cbd88c132602253107"; // ganti dengan API Key dari weatherapi.com
			const city = "Kudus";
			const url = `https://api.weatherapi.com/v1/current.json?key=${apiKey}&q=${city}&lang=id`;

		try {
			const res = await fetch(url);
			const data = await res.json();

			const weatherEl = document.getElementById("weather");
			if (weatherEl) {
					weatherEl.innerHTML = `🌡️ ${data.current.temp_c}°C | ${data.location.name}`;
				}
			} catch (err) {
				console.error("Gagal ambil cuaca:", err);
				const weatherEl = document.getElementById("weather");
			if (weatherEl) {
				weatherEl.innerText = "Cuaca tidak tersedia";
				}
			}
		}


        // Fungsi untuk menampilkan waktu real-time
        document.addEventListener("DOMContentLoaded", function() {
            function updateTime() {
                const dateTimeElement = document.getElementById("date-time");
                if (!dateTimeElement) {
                    console.error("Elemen dengan ID 'date-time' tidak ditemukan!");
                    return;
                }

                const now = new Date();
                const options = {
                    weekday: 'long',
                    year: 'numeric',
                    month: 'long',
                    day: 'numeric'
                };

                dateTimeElement.innerText = now.toLocaleDateString('id-ID', options);
            }
            updateTime();
            setInterval(updateTime, 1000);
        });
        // Jalankan fungsi
        fetchWeather();
        updateTime();
        setInterval(updateClock, 1000); // Update waktu setiap detik
    </script>
    <script>
        // Ambil data layanan dari PHP
        const layananData = <?php
                            $cards = [];
                            while ($row = mysqli_fetch_assoc($layanan)) {
                                $cards[] = $row;
                            }
                            echo json_encode($cards);
                            ?>;

        const bottomCardsContainer = document.getElementById('bottomCards');
        const mainCardContainer = document.getElementById('mainCard1');

        // Render kartu awal
        layananData.forEach((layanan, index) => {
            const id = `queue${layanan.kode_layanan}`;
            const displayNama = `${layanan.nama_layanan.toUpperCase()}`;

            if (index === 0) {
                const mainCard = document.createElement('div');
                mainCard.className = 'main-queue-card';
                mainCard.style.backgroundColor = layanan.warna;
                mainCard.innerHTML = `
                <div class="queue-header">NOMOR ANTRIAN</div>
                <div class="queue-number" id="${id}">--</div>
                <div class="queue-footer">${displayNama}</div>
            `;
                mainCardContainer.appendChild(mainCard);
            } else {
                const card = document.createElement('div');
                card.className = 'queue';
                card.style.backgroundColor = layanan.warna;
                card.innerHTML = `
                <div>NOMOR ANTRIAN</div>
                <h1 id="${id}">--</h1>
                <div>${displayNama}</div>
            `;
                bottomCardsContainer.appendChild(card);
            }
        });
    </script>
    <script src="../js/monitor.js"></script>




<script src="https://www.youtube.com/iframe_api"></script>
<script>
var player;
function onYouTubeIframeAPIReady() {
    window.player = new YT.Player('ytPlayer', {
        events: {
            'onReady': function (event) {
                event.target.mute();
                event.target.playVideo();
                event.target.setVolume(30); // 🔑 paten 30%
            }
        }
    });
}

function handleAktifkanAudio() {
    if (player && typeof player.unMute === "function") {
        // Mulai ulang video (opsional, biar audio jelas)
        player.seekTo(0);
        player.unMute();
        player.playVideo();
    }

    // Fullscreen halaman (bukan iframe saja)
    setTimeout(() => {
        let elem = document.documentElement; // seluruh layout
        if (elem.requestFullscreen) {
            elem.requestFullscreen();
        } else if (elem.webkitRequestFullscreen) {
            elem.webkitRequestFullscreen();
        } else if (elem.msRequestFullscreen) {
            elem.msRequestFullscreen();
        }
    }, 100);

    // Fungsi lama tetap dijalankan
    if (typeof mulaiPemantauan === "function") mulaiPemantauan();
    if (typeof tutupModal === "function") tutupModal();
}

</script>

</body>

</html>