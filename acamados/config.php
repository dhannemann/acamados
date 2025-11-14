<?php
// Tenta pegar as variáveis de ambiente do Railway (forma moderna)
$host = $_ENV['MYSQLHOST'] ?? null;
$user = $_ENV['MYSQLUSER'] ?? null;
$pass = $_ENV['MYSQLPASSWORD'] ?? null;
$db_name = $_ENV['MYSQLDATABASE'] ?? null;
$port = $_ENV['MYSQLPORT'] ?? null; // Pega a porta do Railway

// Se não achar (para rodar no seu PC/localhost), use os valores antigos
if (empty($host)) {
    $host = 'localhost';
    $user = 'root';
    $pass = '';
    $db_name = 'acamados';
    $port = 3307; // A porta do seu localhost
}

date_default_timezone_set('America/Sao_Paulo');

// Conexão
// A linha 21 (agora) converte a porta para número
$conn = new mysqli($host, $user, $pass, $db_name, (int)$port);

if ($conn->connect_error) {
    // Se falhar, MOSTRA O ERRO para depuração
    die("Erro na conexão com o banco de dados: " . $conn->connect_error);
}

// Boa prática: Definir o charset para evitar problemas com acentos
$conn->set_charset("utf8mb4");

?>
