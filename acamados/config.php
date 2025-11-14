<?php
// Tenta pegar as variáveis de ambiente do Railway
$host = getenv('MYSQLHOST');
$user = getenv('MYSQLUSER');
$pass = getenv('MYSQLPASSWORD');
$db_name = getenv('MYSQLDATABASE');
$port = (int)getenv('MYSQLPORT'); // Converte a porta para número

// Se não achar (para rodar no seu PC/localhost), use os valores antigos
if (empty($host)) {
    $host = 'localhost';
    $user = 'root';
    $pass = '';
    $db_name = 'acamados';
    $port = 3307;
}

date_default_timezone_set('America/Sao_Paulo');

// Conexão
$conn = new mysqli($host, $user, $pass, $db_name, $port);

if ($conn->connect_error) {
    die("Erro na conexão com o banco de dados: " . $conn->connect_error);
}

// Boa prática: Definir o charset para evitar problemas com acentos
$conn->set_charset("utf8mb4");

?>
