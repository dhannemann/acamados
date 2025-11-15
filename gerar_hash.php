<?php
// gerar_hash.php

$senha_em_texto_puro = $_GET['senha'] ?? '';

if (empty($senha_em_texto_puro)) {
    echo "<h1>Gerador de HASH para senha</h1>";
    echo "<h3>O que fazer?</h3>";
    echo "<p>Para gerar o HASH da sua senha, digite na URL o seguinte formato:</p>";
    echo "<p><code>gerar_hash.php?senha=sua_senha_aqui</code></p>";
    echo "<h4>Próximos passos:</h4>";
    echo "<p>1. Copie o HASH gerado na tela.</p>";
    echo "<p>2. Vá para a sua tabela <strong>usuario</strong> no phpMyAdmin.</p>";
    echo "<p>3. Clique na aba <strong>SQL</strong> e use o seguinte comando para atualizar a senha:</p>";
    echo "<p><code>UPDATE usuario SET senha = 'COLE_O_HASH_AQUI' WHERE login = 'SEU_LOGIN';</code></p>";
    echo "<p>4. Tente fazer o login com a senha original.</p>";

} else {
    $senha_hashed = password_hash($senha_em_texto_puro, PASSWORD_DEFAULT);
    echo "<h3>O HASH da sua senha é:</h3>";
    echo "<strong>" . htmlspecialchars($senha_hashed) . "</strong>";
    echo "<hr>";
    echo "<h4>Agora, copie o HASH acima, vá para o phpMyAdmin e cole no campo de senha do seu usuário.</h4>";
}
?>