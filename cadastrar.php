<?php
date_default_timezone_set('America/Sao_Paulo');
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Inicializar lista de clientes caso não exista
if (!isset($_SESSION['clientes'])) {
    $_SESSION['clientes'] = [
        [
            'id' => 1,
            'nome' => 'Adega São João Ltda',
            'documento' => '12.345.678/0001-90',
            'premios_retirados' => 1,
            'compras' => [
                ['data' => '10/01/2026', 'pedido' => 'PED-1001', 'valor' => 1000.00],
                ['data' => '15/02/2026', 'pedido' => 'PED-1045', 'valor' => 1000.00],
                ['data' => '01/03/2026', 'pedido' => 'PED-1120', 'valor' => 500.00],
            ],
            'resgates' => [
                ['data' => '16/02/2026', 'premio' => '01 Taça de Cristal']
            ]
        ],
        [
            'id' => 2,
            'nome' => 'Carlos Eduardo Silva',
            'documento' => '123.456.789-00',
            'premios_retirados' => 0,
            'compras' => [
                ['data' => '20/02/2026', 'pedido' => 'PED-1088', 'valor' => 750.00]
            ],
            'resgates' => []
        ],
        [
            'id' => 3,
            'nome' => 'Restaurante Bella Italia',
            'documento' => '98.765.432/0001-11',
            'premios_retirados' => 2,
            'compras' => [
                ['data' => '05/01/2026', 'pedido' => 'PED-0990', 'valor' => 1000.00],
                ['data' => '28/01/2026', 'pedido' => 'PED-1022', 'valor' => 1000.00]
            ],
            'resgates' => [
                ['data' => '06/01/2026', 'premio' => '01 Taça de Cristal'],
                ['data' => '29/01/2026', 'premio' => '01 Taça de Cristal']
            ]
        ]
    ];
}

$mensagem = '';

// PROCESSAR FORMULÁRIO DE LANÇAMENTO DE PEDIDO
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $clienteId = isset($_POST['cliente_id']) ? intval($_POST['cliente_id']) : 0;
    $nomeCliente = trim($_POST['nome_cliente'] ?? '');
    $documento = trim($_POST['documento'] ?? '');
    $numeroPedido = trim($_POST['numero_pedido'] ?? '');
    $dataPedido = trim($_POST['data_pedido'] ?? date('d/m/Y'));
    $valorPedido = floatval(str_replace(['.', ','], ['', '.'], $_POST['valor_pedido'] ?? '0'));

    if (!empty($nomeCliente) && $valorPedido > 0) {
        $clienteEncontrado = false;

        // Tentar localizar cliente existente por ID ou por Nome/Documento
        foreach ($_SESSION['clientes'] as &$c) {
            if (($clienteId > 0 && $c['id'] === $clienteId) || 
                (strcasecmp($c['nome'], $nomeCliente) === 0) ||
                (!empty($documento) && $c['documento'] === $documento)) {
                
                $c['compras'][] = [
                    'data' => date('d/m/Y', strtotime($dataPedido)),
                    'pedido' => $numeroPedido ?: 'PED-' . rand(1000, 9999),
                    'valor' => $valorPedido
                ];
                $clienteEncontrado = true;
                break;
            }
        }

        // Se for um novo cliente, cadastra ele no sistema
        if (!$clienteEncontrado) {
            $novoId = count($_SESSION['clientes']) + 1;
            $_SESSION['clientes'][] = [
                'id' => $novoId,
                'nome' => $nomeCliente,
                'documento' => $documento,
                'premios_retirados' => 0,
                'compras' => [
                    [
                        'data' => date('d/m/Y', strtotime($dataPedido)),
                        'pedido' => $numeroPedido ?: 'PED-' . rand(1000, 9999),
                        'valor' => $valorPedido
                    ]
                ],
                'resgates' => []
            ];
        }

        header('Location: index.php');
        exit;
    } else {
        $mensagem = '<div class="alert alert-danger mb-4">Por favor, preencha o nome do cliente e o valor do pedido corretamente.</div>';
    }
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Lançar Pedido - Fidelidade</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <style>
        .text-vinho { color: #4a121a !important; }
        .btn-vinho { background-color: #4a121a !important; color: #fff !important; }
        .btn-vinho:hover { background-color: #330b12 !important; color: #fff !important; }
    </style>
</head>
<body class="bg-light py-5">

<div class="container" style="max-width: 650px;">
    
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h3 class="fw-bold text-vinho mb-0"><i class="bi bi-cart-plus-fill me-2"></i>Lançar Novo Pedido</h3>
        <a href="index.php" class="btn btn-outline-secondary btn-sm"><i class="bi bi-arrow-left me-1"></i> Voltar</a>
    </div>

    <?= $mensagem ?>

    <div class="card shadow-sm border-0">
        <div class="card-body p-4">
            
            <form method="POST">
                
                <!-- CAMPO DE BUSCA DE CLIENTES EXISTENTES -->
                <div class="mb-4 bg-light p-3 rounded border">
                    <label for="selectCliente" class="form-label fw-bold text-secondary">
                        <i class="bi bi-search me-1"></i> Buscar Cliente Cadastrado
                    </label>
                    <select class="form-select" id="selectCliente" onchange="selecionarCliente(this)">
                        <option value="">-- Clique aqui para pesquisar um cliente existente --</option>
                        <?php foreach($_SESSION['clientes'] as $cli): ?>
                            <option value="<?= $cli['id'] ?>" 
                                    data-nome="<?= htmlspecialchars($cli['nome']) ?>" 
                                    data-doc="<?= htmlspecialchars($cli['documento']) ?>">
                                <?= htmlspecialchars($cli['nome']) ?> (<?= $cli['documento'] ? $cli['documento'] : 'Sem CPF/CNPJ' ?>)
                            </option>
                        <?php endforeach; ?>
                    </select>
                    <div class="form-text">Ao selecionar um cliente acima, os dados serão preenchidos automaticamente. Se for um cliente novo, deixe em branco e digite abaixo.</div>
                </div>

                <input type="hidden" name="cliente_id" id="cliente_id" value="0">

                <div class="mb-3">
                    <label for="nome_cliente" class="form-label fw-semibold">Nome / Razão Social <span class="text-danger">*</span></label>
                    <input type="text" class="form-control" id="nome_cliente" name="nome_cliente" placeholder="Ex: Adega São João Ltda" required>
                </div>

                <div class="mb-3">
                    <label for="documento" class="form-label fw-semibold">CPF / CNPJ</label>
                    <input type="text" class="form-control" id="documento" name="documento" placeholder="00.000.000/0000-00">
                </div>

                <div class="row g-3 mb-3">
                    <div class="col-md-6">
                        <label for="numero_pedido" class="form-label fw-semibold">Nº do Pedido</label>
                        <input type="text" class="form-control" id="numero_pedido" name="numero_pedido" placeholder="Ex: PED-1050">
                    </div>
                    <div class="col-md-6">
                        <label for="data_pedido" class="form-label fw-semibold">Data do Pedido</label>
                        <input type="date" class="form-control" id="data_pedido" name="data_pedido" value="<?= date('Y-m-d') ?>">
                    </div>
                </div>

                <div class="mb-4">
                    <label for="valor_pedido" class="form-label fw-semibold">Valor do Pedido (R$) <span class="text-danger">*</span></label>
                    <div class="input-group">
                        <span class="input-group-text">R$</span>
                        <input type="text" class="form-control" id="valor_pedido" name="valor_pedido" placeholder="0,00" required>
                    </div>
                </div>

                <div class="d-grid gap-2">
                    <button type="submit" class="btn btn-vinho btn-lg fw-bold"><i class="bi bi-check-circle-fill me-2"></i>Salvar e Lançar Pedido</button>
                </div>

            </form>

        </div>
    </div>
</div>

<script>
    function selecionarCliente(select) {
        const option = select.options[select.selectedIndex];
        
        if (select.value !== "") {
            document.getElementById('cliente_id').value = select.value;
            document.getElementById('nome_cliente').value = option.getAttribute('data-nome');
            document.getElementById('documento').value = option.getAttribute('data-doc');
        } else {
            document.getElementById('cliente_id').value = "0";
            document.getElementById('nome_cliente').value = "";
            document.getElementById('documento').value = "";
        }
    }
</script>

</body>
</html>