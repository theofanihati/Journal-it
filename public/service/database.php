<?php
$host = "localhost";
$user = "root";
$password = "";
$database = "journal_it";


$db = mysqli_connect($host, $user, $password, $database);

if ($db->connect_error) {
    die("Koneksi gagal: " . $conn->connect_error);
}
?>
