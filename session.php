<?php
session_start();

// Cek apakah user sudah login
if (!isset($_SESSION['user_id']) || !isset($_SESSION['role'])) {
    header("Location: ../login.php");
    exit;
}

// Akses admin-only
function is_admin()
{
    return $_SESSION['role'] === 'admin';
}

// Akses loket-only
function is_loket()
{
    return $_SESSION['role'] === 'loket';
}
