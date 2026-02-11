<?php
$conn = mysqli_connect("localhost", "root", "usbw", "databasetcc");

mysqli_set_charset($conn, "utf8");

if (!$conn) {
    die("Erro na conexão!");
}
?>
