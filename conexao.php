<?php
$host = 'localhost';
$dbname = 'databasetcc';
$username = 'root';
$password = 'usbw';

$conn = new mysqli($host, $username, 
$password, $dbname);


if ($conn->connect_error) {
    die("Falha na conexão: " . 
    $conn->connect_error);
} else {
   /* echo "Banco de dados Conectado com 
    Sucesso!!!"; */
}
?>