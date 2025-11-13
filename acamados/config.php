<?php
$conn = new mysqli('localhost', 'root', '', 'acamados', 3307);
//$conn = new MySQLi('localhost', 'biometria', 'Biometria@T12s14r2', 'biometria');
date_default_timezone_set('America/Sao_Paulo');

if ($conn->connect_error) {
    die("Erro na conexão com o banco de dados: " . $conn->connect_error);
}
?>