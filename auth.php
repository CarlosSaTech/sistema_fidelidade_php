<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// 1. VERIFICAR SE O USUÁRIO ESTÁ LOGADO
if (!isset($_SESSION['usuario_logado'])) {
    header('Location: login.php');
    exit;
}

// Retorna o usuário logado atualmente
function getUsuarioLogado() {
    return $_SESSION['usuario_logado'];
}

// 2. VERIFICAÇÃO DE PERMISSÃO
function temPermissao($chavePermissao) {
    $usuario = getUsuarioLogado();

    // Gestor tem acesso livre a tudo
    if ($usuario['perfil'] === 'gestor') {
        return true;
    }

    // Funcionário verifica se a permissão foi concedida
    return in_array($chavePermissao, $usuario['permissoes'] ?? []);
}
?>