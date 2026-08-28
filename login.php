<?php
date_default_timezone_set('America/Sao_Paulo');
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// 1. USUÁRIOS DE TESTE EM SESSÃO (Enquanto não criamos o banco de dados)
if (!isset($_SESSION['usuarios'])) {
    $_SESSION['usuarios'] = [
        [
            'id' => 1,
            'nome' => 'Carlos (Gestor)',
            'email' => 'admin@adega.com',
            'senha' => '123456',
            'perfil' => 'gestor',
            'permissoes' => [] // Acesso total
        ],
        [
            'id' => 2,
            'nome' => 'João (Funcionário)',
            'email' => 'joao@adega.com',
            'senha' => '123456',
            'perfil' => 'funcionario',
            'permissoes' => ['lancar_pedido', 'ver_historico']
        ]
    ];
}

$erro = '';

// Se já estiver logado, redireciona para o painel principal
if (isset($_SESSION['usuario_logado'])) {
    header('Location: index.php');
    exit;
}

// 2. PROCESSAR SUBMISSÃO DO FORMULÁRIO DE LOGIN
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    $senha = trim($_POST['senha'] ?? '');

    if (!empty($email) && !empty($senha)) {
        $usuarioEncontrado = null;

        foreach ($_SESSION['usuarios'] as $user) {
            if (strcasecmp($user['email'], $email) === 0 && $user['senha'] === $senha) {
                $usuarioEncontrado = $user;
                break;
            }
        }

        if ($usuarioEncontrado) {
            // Salva os dados do usuário logado na sessão
            $_SESSION['usuario_logado'] = $usuarioEncontrado;
            header('Location: index.php');
            exit;
        } else {
            $erro = 'E-mail ou senha incorretos. Tente novamente.';
        }
    } else {
        $erro = 'Por favor, preencha todos os campos.';
    }
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Login - Programa de Fidelidade</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <style>
        body {
            background-color: #f8f9fa;
            height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .text-vinho { color: #4a121a !important; }
        .bg-vinho { background-color: #4a121a !important; color: #fff; }
        .btn-vinho { background-color: #4a121a !important; color: #fff !important; }
        .btn-vinho:hover { background-color: #330b12 !important; color: #fff !important; }
        .card-login {
            width: 100%;
            max-width: 420px;
            border-radius: 12px;
        }
    </style>
</head>
<body>

<div class="card card-login shadow-lg border-0">
    <div class="card-body p-4 p-sm-5">
        
        <!-- LOGO E TÍTULO -->
        <div class="text-center mb-4">
            <div class="d-inline-flex align-items-center justify-content-center bg-vinho rounded-circle mb-3" style="width: 60px; height: 60px;">
                <i class="bi bi-trophy fs-2 text-warning"></i>
            </div>
            <h4 class="fw-bold text-vinho mb-1">Programa de Fidelidade</h4>
            <p class="text-muted small">Acesse com suas credenciais</p>
        </div>

        <!-- MENSAGEM DE ERRO -->
        <?php if($erro): ?>
            <div class="alert alert-danger alert-dismissible fade show text-center py-2" role="alert">
                <small class="fw-semibold"><i class="bi bi-exclamation-triangle-fill me-1"></i> <?= $erro ?></small>
                <button type="button" class="btn-close py-2" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>

        <!-- FORMULÁRIO DE LOGIN -->
        <form method="POST" action="login.php">
            <div class="mb-3">
                <label for="email" class="form-label fw-semibold text-secondary">E-mail</label>
                <div class="input-group">
                    <span class="input-group-text bg-light text-muted"><i class="bi bi-envelope"></i></span>
                    <input type="email" class="form-control" id="email" name="email" placeholder="seu@email.com" required autofocus>
                </div>
            </div>

            <div class="mb-4">
                <div class="d-flex justify-content-between align-items-center mb-1">
                    <label for="senha" class="form-label fw-semibold text-secondary mb-0">Senha</label>
                </div>
                <div class="input-group">
                    <span class="input-group-text bg-light text-muted"><i class="bi bi-lock"></i></span>
                    <input type="password" class="form-control" id="senha" name="senha" placeholder="******" required>
                </div>
            </div>

            <button type="submit" class="btn btn-vinho btn-lg w-100 fw-bold shadow-sm mb-3">
                <i class="bi bi-box-arrow-in-right me-2"></i> Entrar no Sistema
            </button>
        </form>

        <!-- CONTAS DE TESTE (Para agilizar durante a estruturação) -->
        <div class="mt-4 pt-3 border-top text-center">
            <small class="text-muted d-block mb-2 fw-semibold">Credenciais de Teste:</small>
            <div class="d-flex justify-content-center gap-2">
                <button type="button" class="btn btn-sm btn-outline-danger" onclick="preencher('admin@adega.com', '123456')">
                    Gestor
                </button>
                <button type="button" class="btn btn-sm btn-outline-primary" onclick="preencher('joao@adega.com', '123456')">
                    Funcionário
                </button>
            </div>
        </div>

    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
    function preencher(email, senha) {
        document.getElementById('email').value = email;
        document.getElementById('senha').value = senha;
    }
</script>
</body>
</html>