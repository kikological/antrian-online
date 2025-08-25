<?php
$conn = mysqli_connect("localhost", "root", "", "db_antrian");

// Set timezone PHP
date_default_timezone_set('Asia/Jakarta');

// Set timezone MySQL
mysqli_query($conn, "SET time_zone = '+07:00'");
