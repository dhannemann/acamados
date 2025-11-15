<?php
// Cole os dados que o InfinityFree te deu:

$host = 'sql211.infinityfree.com'; // <--- MySQL Hostname
$user = 'if0_40422144';            // <--- MySQL Username
$db_name = 'if0_40422144_acamados';   // <--- MySQL Database Name
$pass = 'da33626688'; 

date_default_timezone_set('America/Sao_Paulo');

// Conexão
$conn = new mysqli($host, $user, $pass, $db_name);

if ($conn->connect_error) {
    die("Erro na conexão com o banco de dados: " . $conn->connect_error);
}

$conn->set_charset("utf8mb4");

?>
