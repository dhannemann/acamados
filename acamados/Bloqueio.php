<?php //Verifica se o usuário está logado e se é administrador (tipo 1)
if ($_SESSION['tipo'] != '1') {
  header("Location: index.php");
  exit();
}