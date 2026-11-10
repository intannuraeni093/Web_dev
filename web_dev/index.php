<?php
// Memulai session PHP untuk mengamankan halaman login
session_start();

$login_error = '';

// Proses Autentikasi Login di sisi Server (PHP)
if (isset($_POST['login'])) {
    $username = trim($_POST['username']);
    $password = $_POST['password'];

    // Validasi sederhana kredensial default
    if ($username === 'mahasiswa123' && $password === 'rahasia') {
        $_SESSION['user'] = $username;
        $_SESSION['login_time'] = time();
        header("Location: index.php");
        exit;
    } else {
        $login_error = 'Nama pengguna atau kata sandi salah!';
    }
}

// Proses Logout / Keluar
if (isset($_GET['action']) && $_GET['action'] === 'logout') {
    session_unset();
    session_destroy();
    header("Location: index.php");
    exit;
}

// Cek apakah user sudah masuk atau belum
$is_logged_in = isset($_SESSION['user']);
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>WebDev Project Hub - Portofolio Proyek Web</title>
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    <!-- Font Awesome untuk Icon -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <!-- Hubungkan ke CSS Terpisah -->
    <link rel="stylesheet" href="style.css">
</head>
<body class="text-slate-800 transition-colors duration-300">

    <!-- AUDIO BACKROUND (Lofi Belajar) -->
    <audio id="bgAudio" loop>
        <source src="assets/audio/Death Bed.mp3" type="audio/mpeg">
        Browser Anda tidak mendukung elemen audio.
    </audio>

    <!-- NOTIFIKASI TOAST CUSTOM -->
    <div id="toast" class="fixed top-5 right-5 z-50 flex items-center w-full max-w-xs p-4 space-x-3 text-gray-500 bg-white rounded-xl shadow-2xl border border-slate-100 transform translate-x-full transition-transform duration-300 ease-out" role="alert">
        <div id="toastIcon" class="inline-flex items-center justify-center flex-shrink-0 w-8 h-8 text-blue-500 bg-blue-100 rounded-lg">
            <i class="fas fa-info-circle"></i>
        </div>
        <div class="text-sm font-semibold text-slate-800" id="toastMessage">Pesan Notifikasi</div>
    </div>

    <!-- KONTROL ALUR HALAMAN BERBASIS SESSION PHP -->
    <?php if (!$is_logged_in): ?>
        <!-- Tampilkan Halaman Login jika belum masuk -->
        <?php include 'login.php'; ?>
    <?php else: ?>
        <!-- Tampilkan Antarmuka Utama Dashboard jika sudah masuk -->
        <div id="dashboardPage" class="min-h-screen flex flex-col">
            <?php include 'header.php'; ?>
            
            <main class="flex-grow max-w-7xl w-full mx-auto px-4 sm:px-6 lg:px-8 py-8 space-y-8">
                <?php include 'dashboard.php'; ?>
            </main>

            <?php include 'modals.php'; ?>
            <?php include 'footer.php'; ?>
        </div>
    <?php endif; ?>

</body>
</html>