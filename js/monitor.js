const source = new EventSource('../backend/stream.php');
let lastCalled = {};
let audioAktif = false;

// Status Audio
function mulaiPemantauan() {
    audioAktif = true;
    console.log("Audio diaktifkan");
}

// Sistem antrian audio
let audioQueue = [];
let isPlaying = false;

function enqueueAudio(files) {
    audioQueue.push(files);
    if (!isPlaying) {
        playNextInQueue();
    }
}

async function playNextInQueue() {
    if (audioQueue.length === 0) {
        isPlaying = false;
        return;
    }

    isPlaying = true;
    const files = audioQueue.shift();
    await playAudioSequence(files);
    playNextInQueue();
}

// SSE: menerima data dari backend
source.onmessage = function (event) {
    if (!event.data) return;

    const list = JSON.parse(event.data);

    // Reset tampilan
    document.querySelectorAll('[id^="queue"]').forEach(el => {
        el.innerText = '--';
    });

    list.forEach(item => {
        const el = document.getElementById('queue' + item.kode);
        if (el) {
            el.innerText = item.nomor ? item.kode + item.nomor : '--';
        }

        const key = item.kode;
        const current = item.kode + item.nomor;


        // Audio diputar jika nomor baru ATAU recall aktif
        if (lastCalled[key] !== current || item.recall === 1) {
            lastCalled[key] = current;

            if (!audioAktif) return;

            const loketAudioFile = item.loket_audio ? `../audio/${item.loket_audio}` : '../audio/teller.mp3';

            const files = [
                '../audio/Bell.wav',
                '../audio/Nomor antrian.mp3',
                `../audio/${item.kode}.mp3`,
                ...getNumberAudioFiles(item.nomor),
                '../audio/Silahkan Menuju Loket.mp3',
                loketAudioFile,
            ];


            enqueueAudio(files);

            // Reset flag recall setelah diputar
            if (item.recall === 1 && item.id) {
                fetch(`../backend/reset_recall.php?id=${item.id}`)
                    .then(res => res.ok && console.log("Recall reset untuk ID", item.id))
                    .catch(err => console.warn("Gagal reset recall:", err));
            }
        }
    });
};

// Fungsi memutar audio berurutan
async function playAudioSequence(files) {
    for (const file of files) {
        await new Promise((resolve) => {
            const audio = new Audio(file);
            audio.onended = resolve;
            audio.onerror = () => {
                console.warn("Gagal memuat file audio:", file);
                resolve(); // Lanjut walaupun gagal
            };
            audio.play();
        });
    }
}

// Fungsi menyusun audio angka
function getNumberAudioFiles(number) {
    const num = parseInt(number, 10);
    const files = [];

    if (isNaN(num) || num < 0 || num > 500) {
        console.warn("Nomor antrian tidak valid: " + number);
        return files;
    }

    if (num === 0) {
        files.push(`../audio/0.mp3`);
        return files;
    }

    if (num <= 20) {
        files.push(`../audio/${num}.mp3`);
        return files;
    }

    if (num < 100) {
        const puluhan = Math.floor(num / 10) * 10;
        const satuan = num % 10;
        files.push(`../audio/${puluhan}.mp3`);
        if (satuan !== 0) {
            files.push(`../audio/${satuan}.mp3`);
        }
        return files;
    }

    if (num % 100 === 0) {
        files.push(`../audio/${num}.mp3`);
        return files;
    }

    const ratusan = Math.floor(num / 100) * 100;
    const sisa = num % 100;

    files.push(`../audio/${ratusan}.mp3`);

    if (sisa <= 20) {
        if (sisa !== 0) {
            files.push(`../audio/${sisa}.mp3`);
        }
    } else {
        const puluhan = Math.floor(sisa / 10) * 10;
        const satuan = sisa % 10;
        files.push(`../audio/${puluhan}.mp3`);
        if (satuan !== 0) {
            files.push(`../audio/${satuan}.mp3`);
        }
    }

    return files;
}

// Tampilkan modal saat halaman dimuat
window.addEventListener('DOMContentLoaded', () => {
    document.getElementById('audioModal').style.display = 'flex';
});

function tutupModal() {
    document.getElementById('audioModal').style.display = 'none';
}

function masukFullscreen() {
    const elem = document.documentElement;
    if (elem.requestFullscreen) {
        elem.requestFullscreen();
    } else if (elem.mozRequestFullScreen) { // Firefox
        elem.mozRequestFullScreen();
    } else if (elem.webkitRequestFullscreen) { // Chrome, Safari & Opera
        elem.webkitRequestFullscreen();
    } else if (elem.msRequestFullscreen) { // IE/Edge
        elem.msRequestFullscreen();
    }
}

// Jam dinamis
function updateClock() {
    const now = new Date();
    const jam = document.getElementById("jam");
    jam.textContent = now.toLocaleTimeString('id-ID', {
        hour12: false
    });
}
setInterval(updateClock, 1000);
updateClock();



