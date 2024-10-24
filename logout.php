<?php
session_start();

// Menghapus semua variabel sesi
session_unset();

// Menghancurkan sesi
session_destroy();

// Mengarahkan pengguna ke halaman login atau halaman lain
header("Location: login.php");
exit();
?>
