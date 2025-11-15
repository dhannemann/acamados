<?php
// Cole os dados que o 000webhost te deu:
$host = 'sql123.000webhost.com'; // <--- O HOST que você anotou
$user = 'id12345_usuario_acamados'; // <--- O USUÁRIO que você anotou
$pass = 'SuaSenhaQueVoceCriou'; // <--- A SENHA que você anotou
$db_name = 'id12345_acamados'; // <--- O NOME DO BANCO que você anotou

date_default_timezone_set('America/Sao_Paulo');

// Conexão
$conn = new mysqli($host, $user, $pass, $db_name);

if ($conn->connect_error) {
    die("Erro na conexão com o banco de dados: " . $conn->connect_error);
}

$conn->set_charset("utf8mb4");

?>
