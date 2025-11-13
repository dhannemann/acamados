<?php
// login.php

session_start();
include("config.php");

// Protege contra requisições diretas sem dados de formulário
if (empty($_POST["login"]) || empty($_POST["senha"])) {
    $_SESSION["erro_login"] = "Preencha todos os campos.";
    header("Location: /acamados/index.php"); // Corrigido
    exit();
}

$login = $conn->real_escape_string($_POST["login"]);
$senha = $_POST["senha"];

// Busca pelo usuário no banco
$sql = "SELECT * FROM usuario WHERE login = '{$login}' LIMIT 1";
$res = $conn->query($sql);

if ($res->num_rows > 0) {
    $row = $res->fetch_object();

    // Verifica a senha com hash
    if (password_verify($senha, $row->senha)) {
        $_SESSION["usuario_id"] = $row->id;
        $_SESSION["nome_usuario"] = $row->nome_completo;
        
        // Redirecionamento corrigido para o caminho completo
        header("Location: /acamados/dashboard/dashboard.php");
        exit();
    }
}

// Falha no login
$_SESSION["erro_login"] = "Usuário ou senha incorretos!";
header("Location: /acamados/index.php"); // Corrigido
exit();
?>