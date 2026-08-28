<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Limpa as variáveis de sessão
unset($_SESSION['usuario_logado']);
session_unset();
session_destroy();

// Redireciona para o login (ou mude para 'index.php' se preferir)
header('Location: login.php');
exit;
?>