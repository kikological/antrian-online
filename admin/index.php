<?php
require '../session.php';

if (!is_admin()) {
    header("Location: ../login.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>Dashboard Administartor </title>
    <link rel="stylesheet" href="src/assets/vendors/feather/feather.css">
    <link rel="stylesheet" href="src/assets/vendors/mdi/css/materialdesignicons.min.css">
    <link rel="stylesheet" href="src/assets/vendors/ti-icons/css/themify-icons.css">
    <link rel="stylesheet" href="src/assets/vendors/font-awesome/css/font-awesome.min.css">
    <link rel="stylesheet" href="src/assets/vendors/typicons/typicons.css">
    <link rel="stylesheet" href="src/assets/vendors/simple-line-icons/css/simple-line-icons.css">
    <link rel="stylesheet" href="src/assets/vendors/css/vendor.bundle.base.css">
    <link rel="stylesheet" href="src/assets/vendors/bootstrap-datepicker/bootstrap-datepicker.min.css">
    <link rel="stylesheet" href="src/assets/vendors/datatables.net-bs4/dataTables.bootstrap4.css">
    <link rel="stylesheet" type="text/css" href="src/assets/js/select.dataTables.min.css">
    <link rel="stylesheet" href="src/assets/css/style.css">
    <link rel="shortcut icon" href="src/assets/images/favicon.png" />
</head>
<?php
require '../db/config.php';

// statistik antrian 7 hari terakhir
// Statistik 30 hari terakhir
$tanggal30 = [];
$total30 = [];

for ($i = 29; $i >= 0; $i--) {
    $tgl = date('Y-m-d', strtotime("-$i days"));
    $label = date('d M', strtotime($tgl));

    $query = mysqli_query($conn, "
        SELECT COUNT(*) AS total 
        FROM history_antrian 
        WHERE DATE(waktu_ambil) = '$tgl'
    ");
    $row = mysqli_fetch_assoc($query);

    $tanggal30[] = $label;
    $total30[] = (int)$row['total'];
}

// Gabungkan jadi satu array agar mudah dipakai di JS
$data7 = [];
for ($i = 6; $i >= 0; $i--) {
    $tgl = date('Y-m-d', strtotime("-$i days"));
    $label = date('d M', strtotime($tgl));

    $query = mysqli_query($conn, "
        SELECT COUNT(*) AS total 
        FROM history_antrian 
        WHERE DATE(waktu_ambil) = '$tgl'
    ");
    $row = mysqli_fetch_assoc($query);

    $data7[] = [
        'label' => $label,
        'total' => (int)$row['total']
    ];
}

$data30 = [];
for ($i = 29; $i >= 0; $i--) {
    $tgl = date('Y-m-d', strtotime("-$i days"));
    $label = date('d M', strtotime($tgl));

    $query = mysqli_query($conn, "
        SELECT COUNT(*) AS total 
        FROM history_antrian 
        WHERE DATE(waktu_ambil) = '$tgl'
    ");
    $row = mysqli_fetch_assoc($query);

    $data30[] = [
        'label' => $label,
        'total' => (int)$row['total']
    ];
}

?>

<body class="with-welcome-text">
    <div class="container-scroller">
        <?php include 'navbar.php' ?>
        <div class="container-fluid page-body-wrapper">
            <?php include 'sidebar.php' ?>
            <div class="main-panel">
                <div class="content-wrapper">
                    <div class="row">
                        <div class="col-sm-12">
                            <div class="home-tab">

                                <div class="tab-content tab-content-basic">
                                    <div class="tab-pane fade show active" id="overview" role="tabpanel" aria-labelledby="overview">
                                        <ul class="navbar-nav">
                                            <li class="nav-item fw-semibold d-none d-lg-block ms-0">
                                                <h1 class="welcome-text">Selamat Datang, <span class="text-black fw-bold"><?= 									$_SESSION['nama']; ?></span></h1>
                                             	<h3 class="welcome-sub-text" id="weather"></h3> 
                                                <h3 class="welcome-sub-text" id="clock"></h3>
                                                <div class="row mt-4">
                                                    <div class="col-md-12">
                                                        <div class="card">
                                                            <div class="card-body">
                                                                <h4 class="card-title d-flex justify-content-between align-items-center">
                                                                    Statistik Data Antrian
                                                                    <select id="filterHari" class="form-select form-select-sm w-auto">
                                                                        <option value="7" selected>7 Hari</option>
                                                                        <option value="30">30 Hari</option>
                                                                    </select>
                                                                </h4>

                                                                <canvas id="chartAntrian" style="max-height: 300px;"></canvas>

                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>

                                            </li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <?php include 'footer.php' ?>
            </div>
        </div>
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
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

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

        function updateClock() {
            let now = new Date();
            let timeString = now.toLocaleTimeString("id-ID", {
                hour: '2-digit',
                minute: '2-digit',
                second: '2-digit'
            });
            document.getElementById("clock").innerHTML = `${timeString}`;
        }

        fetchWeather();
        updateClock();
        setInterval(updateClock, 1000); // Update waktu setiap detik
    </script>
    <script>
        const data7 = <?= json_encode($data7) ?>;
        const data30 = <?= json_encode($data30) ?>;

        const ctx = document.getElementById('chartAntrian').getContext('2d');

        let chartAntrian = new Chart(ctx, {
            type: 'bar',
            data: {
                labels: data7.map(item => item.label),
                datasets: [{
                    label: 'Jumlah Antrian',
                    data: data7.map(item => item.total),
                    backgroundColor: 'rgba(54, 162, 235, 0.6)',
                    borderColor: 'rgba(54, 162, 235, 1)',
                    borderWidth: 1,
                    borderRadius: 6
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            stepSize: 1
                        }
                    }
                }
            }
        });

        document.getElementById('filterHari').addEventListener('change', function() {
            const selected = this.value;
            const data = selected === '30' ? data30 : data7;

            chartAntrian.data.labels = data.map(item => item.label);
            chartAntrian.data.datasets[0].data = data.map(item => item.total);
            chartAntrian.update();
        });
    </script>

</body>

</html>