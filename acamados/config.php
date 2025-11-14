<?php
// A SOLUÇÃO MAIS SIMPLES: Ler 1 variável (DATABASE_URL)
$db_url = $_ENV['DATABASE_URL'] ?? null;

// Se a variável existir (estamos no Railway)
if (!empty($db_url)) {
    $url_parts = parse_url($db_url);

    $host = $url_parts['host'];
    $user = $url_parts['user'];
    $pass = $url_parts['pass'];
    $db_name = ltrim($url_parts['path'], '/'); // Remove a barra "/" do início
    $port = $url_parts['port'];

} else {
    // Se não (estamos no PC/localhost), usa o plano B
    $host = 'localhost';
    $user = 'root';
    $pass = '';
    $db_name = 'acamados';
    $port = 3307;
}

date_default_timezone_set('America/Sao_Paulo');

// Conexão
$conn = new mysqli($host, $user, $pass, $db_name, (int)$port);

if ($conn->connect_error) {
    // Se falhar, MOSTRA O ERRO para depuração
    die("Erro na conexão com o banco de dados: " . $conn->connect_error);
}

$conn->set_charset("utf8mb4");

?>
