<?php
session_start();

/*
// Verifica se a sessão está ativa usando as variáveis corretas
if (!isset($_SESSION['usuario_id']) || !isset($_SESSION['nome_usuario'])) {
    header("Location: /acamados/index.php");
    exit();
}*/
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cadastro - GDS</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary">
        <div class="container-fluid">
            <a class="navbar-brand" href="/acamados/dashboard/dashboard.php">Acamados</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav me-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="/acamados/dashboard/dashboard.php">Cadastro</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="/acamados/pacientes/exibir_dados.php">Exibir Dados</a>
                    </li>
                </ul>
                <!--<span class="navbar-text me-2">Bem-vindo, <?php //echo htmlspecialchars($_SESSION['nome_usuario']); ?></span>
                <a href="/acamados/logout.php" class="btn btn-outline-light">Sair</a>-->
            </div>
        </div>
    </nav>
    <div class="container mt-4">

