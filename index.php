<?php
// Configurações e Sessão
date_default_timezone_set('America/Sao_Paulo');
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Configurações da Campanha Ativa
$campanha_nome = "Taça de Cristal 2026";
$campanha_meta_valor = 1000.00; // A cada R$ 1.000,00 ganha 1 taça
$campanha_validade = "31/12/2026";

// Dados Fictícios de Clientes na Sessão
if (!isset($_SESSION['clientes_fidelidade'])) {
    $_SESSION['clientes_fidelidade'] = [
        1 => [
            'id' => 1,
            'nome' => 'Adega São João Ltda',
            'documento' => '12.345.678/0001-90',
            'resgatados' => 1,
            'compras' => [
                ['data' => '2026-01-10', 'pedido' => 'PED-1001', 'valor' => 1000.00],
                ['data' => '2026-01-20', 'pedido' => 'PED-1042', 'valor' => 1000.00],
                ['data' => '2026-02-05', 'pedido' => 'PED-1105', 'valor' => 500.00],
            ],
            'resgates_historico' => [
                ['data' => '2026-01-22', 'qtd' => 1, 'responsavel' => 'Atendente João']
            ]
        ],
        2 => [
            'id' => 2,
            'nome' => 'Carlos Eduardo Silva',
            'documento' => '123.456.789-00',
            'resgatados' => 0,
            'compras' => [
                ['data' => '2026-02-01', 'pedido' => 'PED-1080', 'valor' => 750.00],
            ],
            'resgates_historico' => []
        ],
        3 => [
            'id' => 3,
            'nome' => 'Restaurante Bella Italia',
            'documento' => '98.765.432/0001-11',
            'resgatados' => 3,
            'compras' => [
                ['data' => '2026-01-05', 'pedido' => 'PED-0990', 'valor' => 1500.00],
                ['data' => '2026-01-18', 'pedido' => 'PED-1025', 'valor' => 1500.00],
                ['data' => '2026-02-10', 'pedido' => 'PED-1120', 'valor' => 500.00],
            ],
            'resgates_historico' => [
                ['data' => '2026-01-20', 'qtd' => 3, 'responsavel' => 'Gerente Maria']
            ]
        ]
    ];
}

// Processar Resgate via POST
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['acao']) && $_POST['acao'] === 'resgatar') {
    $cliente_id = (int)$_POST['cliente_id'];
    if (isset($_SESSION['clientes_fidelidade'][$cliente_id])) {
        $_SESSION['clientes_fidelidade'][$cliente_id]['resgatados'] += 1;
        $_SESSION['clientes_fidelidade'][$cliente_id]['resgates_historico'][] = [
            'data' => date('Y-m-d H:i:s'),
            'qtd' => 1,
            'responsavel' => 'Operador Sistema'
        ];
        header("Location: " . $_SERVER['PHP_SELF'] . "?msg=resgate_sucesso");
        exit;
    }
}

// Cálculos Globais de KPI
$kpi_clientes_ativos = count($_SESSION['clientes_fidelidade']);
$kpi_premios_a_retirar = 0;
$kpi_premios_resgatados = 0;
$kpi_total_acumulado = 0;

foreach ($_SESSION['clientes_fidelidade'] as $cli) {
    $total_compras = array_sum(array_column($cli['compras'], 'valor'));
    $premios_conquistados = floor($total_compras / $campanha_meta_valor);
    $a_retirar = max(0, $premios_conquistados - $cli['resgatados']);
    
    $kpi_premios_a_retirar += $a_retirar;
    $kpi_premios_resgatados += $cli['resgatados'];
    $kpi_total_acumulado += $total_compras;
}

if (file_exists('nav.php')) {
    include 'nav.php';
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Programa de Fidelidade - Painel Geral</title>
  
  <!-- Bootstrap 5 CSS -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <!-- Bootstrap Icons -->
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
  
  <style>
    .bg-vinho { background-color: #4a121a; color: white; }
    .btn-vinho { background-color: #4a121a; color: white; }
    .btn-vinho:hover { background-color: #360c13; color: white; }
    .text-vinho { color: #4a121a; }
    
    .fs-7 { font-size: 0.85rem; }
    .fs-8 { font-size: 0.75rem; }
    
    .table-modern thead th {
      background-color: #f8f9fa;
      color: #6c757d;
      font-weight: 600;
      text-transform: uppercase;
      font-size: 0.75rem;
      letter-spacing: 0.5px;
      border-bottom: 2px solid #e9ecef;
      padding-top: 12px;
      padding-bottom: 12px;
    }
    .table-modern tbody td {
      padding-top: 14px;
      padding-bottom: 14px;
    }

    .kpi-card {
      border-left: 4px solid #4a121a;
      transition: transform 0.2s;
    }
    .kpi-card:hover {
      transform: translateY(-2px);
    }

    @media print {
      .no-print { display: none !important; }
      .card { border: none !important; box-shadow: none !important; }
    }
  </style>
</head>
<body class="bg-light">

<div class="container my-4">

  <?php if (isset($_GET['msg']) && $_GET['msg'] === 'resgate_sucesso'): ?>
    <div class="alert alert-success alert-dismissible fade show no-print mb-4" role="alert">
      <i class="bi bi-check-circle-fill me-2"></i> <strong>Sucesso!</strong> O resgate do prêmio foi registrado com sucesso.
      <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Fechar"></button>
    </div>
  <?php endif; ?>

  <!-- Banner da Campanha Ativa -->
  <div class="card border-0 shadow-sm mb-4 border-start border-4 border-warning">
    <div class="card-body p-4 d-flex flex-wrap align-items-center justify-content-between gap-3">
      <div>
        <div class="d-flex align-items-center gap-2 mb-1">
          <span class="badge bg-warning-subtle text-warning-emphasis border border-warning-subtle rounded-pill px-2 py-1 fs-8 fw-semibold">
            Campanha Ativa
          </span>
          <h4 class="fw-bold mb-0 text-dark"><?= htmlspecialchars($campanha_nome) ?></h4>
        </div>
        <p class="text-secondary mb-0">Regra: A cada <strong>R$ <?= number_format($campanha_meta_valor, 2, ',', '.') ?></strong> em compras acumuladas, o cliente ganha <strong>01 Taça de Cristal</strong>.</p>
      </div>
      <div class="text-md-end">
        <span class="badge bg-light text-dark border px-3 py-2 fs-7 d-inline-flex align-items-center gap-2 shadow-sm">
          <i class="bi bi-calendar-event text-warning fs-6"></i> 
          <span>Válido até <strong><?= $campanha_validade ?></strong></span>
        </span>
      </div>
    </div>
  </div>

  <!-- KPIs Globais -->
  <div class="row g-3 mb-4">
    <div class="col-12 col-sm-6 col-xl-3">
      <div class="card border-0 shadow-sm kpi-card p-3">
        <div class="d-flex align-items-center justify-content-between">
          <div>
            <span class="text-muted fs-7 fw-semibold">Clientes Ativos</span>
            <h3 class="fw-bold mb-0 text-dark"><?= number_format($kpi_clientes_ativos, 0, ',', '.') ?></h3>
          </div>
          <div class="bg-light rounded-circle p-3 text-vinho">
            <i class="bi bi-people fs-4"></i>
          </div>
        </div>
      </div>
    </div>
    
    <div class="col-12 col-sm-6 col-xl-3">
      <div class="card border-0 shadow-sm kpi-card p-3" style="border-left-color: #198754;">
        <div class="d-flex align-items-center justify-content-between">
          <div>
            <span class="text-muted fs-7 fw-semibold">Prêmios a Retirar</span>
            <h3 class="fw-bold mb-0 text-success"><?= $kpi_premios_a_retirar ?></h3>
          </div>
          <div class="bg-success-subtle rounded-circle p-3 text-success">
            <i class="bi bi-gift fs-4"></i>
          </div>
        </div>
      </div>
    </div>

    <div class="col-12 col-sm-6 col-xl-3">
      <div class="card border-0 shadow-sm kpi-card p-3" style="border-left-color: #0dcaf0;">
        <div class="d-flex align-items-center justify-content-between">
          <div>
            <span class="text-muted fs-7 fw-semibold">Prêmios Resgatados</span>
            <h3 class="fw-bold mb-0 text-info"><?= $kpi_premios_resgatados ?></h3>
          </div>
          <div class="bg-info-subtle rounded-circle p-3 text-info">
            <i class="bi bi-check-circle fs-4"></i>
          </div>
        </div>
      </div>
    </div>

    <div class="col-12 col-sm-6 col-xl-3">
      <div class="card border-0 shadow-sm kpi-card p-3" style="border-left-color: #ffc107;">
        <div class="d-flex align-items-center justify-content-between">
          <div>
            <span class="text-muted fs-7 fw-semibold">Total Acumulado</span>
            <h3 class="fw-bold mb-0 text-dark">R$ <?= number_format($kpi_total_acumulado, 2, ',', '.') ?></h3>
          </div>
          <div class="bg-warning-subtle rounded-circle p-3 text-warning-emphasis">
            <i class="bi bi-currency-dollar fs-4"></i>
          </div>
        </div>
      </div>
    </div>
  </div>

  <!-- Tabela de Clientes -->
  <div class="card border-0 shadow-sm">
    <div class="card-body p-4">
      
      <div class="row align-items-center g-3 mb-4 no-print">
        <div class="col-md-4">
          <h5 class="fw-bold m-0 text-dark d-flex align-items-center gap-2">
            <i class="bi bi-person-vcard text-vinho"></i> Acompanhamento de Clientes
          </h5>
        </div>
        
        <div class="col-md-4">
          <div class="input-group input-group-sm">
            <span class="input-group-text bg-white border-end-0 text-muted"><i class="bi bi-search"></i></span>
            <input type="text" id="inputBusca" class="form-control border-start-0 ps-0" placeholder="Buscar por cliente ou CPF/CNPJ..." onkeyup="filtrarTabela()">
          </div>
        </div>

        <div class="col-md-4 text-md-end d-flex gap-2 justify-content-md-end">
          <button onclick="exportarExcel()" class="btn btn-sm btn-outline-secondary" title="Exportar para Excel">
            <i class="bi bi-file-earmark-excel me-1 text-success"></i> Excel
          </button>
          <button onclick="window.print()" class="btn btn-sm btn-outline-secondary" title="Imprimir Relatório">
            <i class="bi bi-printer"></i>
          </button>
        </div>
      </div>

      <div class="mb-4 d-flex justify-content-between align-items-center flex-wrap gap-2 no-print">
        <div class="btn-group btn-group-sm" role="group">
          <button type="button" class="btn btn-secondary active" onclick="filtrarStatus('todos', this)">Todos</button>
          <button type="button" class="btn btn-outline-secondary" onclick="filtrarStatus('retirar', this)">
            <i class="bi bi-gift-fill text-success me-1"></i> Com Prêmio A Retirar
          </button>
          <button type="button" class="btn btn-outline-secondary" onclick="filtrarStatus('andamento', this)">
            <i class="bi bi-hourglass-split text-warning me-1"></i> Em Andamento
          </button>
        </div>
      </div>

      <div class="table-responsive">
        <table class="table table-hover align-middle table-modern mb-0" id="tabelaClientes">
          <thead>
            <tr>
              <th>Cliente</th>
              <th>Acumulado na Campanha</th>
              <th style="width: 260px;">Progresso do Próximo Prêmio</th>
              <th>Situação do Prêmio</th>
              <th class="text-end no-print">Ações</th>
            </tr>
          </thead>
          <tbody>
            <?php foreach ($_SESSION['clientes_fidelidade'] as $cli): 
                $total_compras = array_sum(array_column($cli['compras'], 'valor'));
                $premios_conquistados = floor($total_compras / $campanha_meta_valor);
                $resgatados = $cli['resgatados'];
                $a_retirar = max(0, $premios_conquistados - $resgatados);
                
                $valor_resto = fmod($total_compras, $campanha_meta_valor);
                $porcentagem = ($valor_resto / $campanha_meta_valor) * 100;
                $falta_valor = $campanha_meta_valor - $valor_resto;
                $proximo_numero_taca = $premios_conquistados + 1;

                $status_filtro = ($a_retirar > 0) ? 'retirar' : 'andamento';
            ?>
              <tr data-status="<?= $status_filtro ?>">
                <td>
                  <div class="fw-bold text-dark cli-nome"><?= htmlspecialchars($cli['nome']) ?></div>
                  <div class="text-muted fs-8 cli-doc"><?= htmlspecialchars($cli['documento']) ?></div>
                </td>
                <td>
                  <span class="fw-bold text-dark">R$ <?= number_format($total_compras, 2, ',', '.') ?></span>
                  <div class="fs-8 text-muted"><?= $premios_conquistados ?> prêmio(s) conquistado(s)</div>
                </td>
                <td>
                  <div class="d-flex justify-content-between fs-8 fw-semibold mb-1">
                    <span>Próximo prêmio</span>
                    <span class="text-info"><?= round($porcentagem) ?>%</span>
                  </div>
                  <div class="progress mb-1" style="height: 6px;">
                    <div class="progress-bar bg-info" style="width: <?= $porcentagem ?>%"></div>
                  </div>
                  <div class="text-muted fs-8">
                    Falta <strong>R$ <?= number_format($falta_valor, 2, ',', '.') ?></strong> para a <?= $proximo_numero_taca ?>ª Taça
                  </div>
                </td>
                <td>
                  <?php if ($a_retirar > 0): ?>
                    <span class="badge bg-success-subtle text-success border border-success-subtle rounded-pill px-3 py-2 fs-7">
                      <i class="bi bi-gift-fill me-1"></i> <?= $a_retirar ?> prêmio(s) a retirar
                    </span>
                  <?php elseif ($resgatados > 0 && $a_retirar == 0): ?>
                    <span class="badge bg-secondary-subtle text-secondary border rounded-pill px-3 py-2 fs-7">
                      <i class="bi bi-check-all me-1"></i> <?= $resgatados ?> prêmio(s) retirado(s)
                    </span>
                  <?php else: ?>
                    <span class="badge bg-light text-secondary border rounded-pill px-3 py-2 fs-7">
                      <i class="bi bi-hourglass-split me-1"></i> Em andamento
                    </span>
                  <?php endif; ?>
                </td>
                <td class="text-end no-print">
                  <?php if ($a_retirar > 0): ?>
                    <form method="POST" class="d-inline">
                      <input type="hidden" name="acao" value="resgatar">
                      <input type="hidden" name="cliente_id" value="<?= $cli['id'] ?>">
                      <button type="submit" class="btn btn-sm btn-success me-1">
                        <i class="bi bi-check2-circle me-1"></i> Resgatar
                      </button>
                    </form>
                  <?php endif; ?>

                  <div class="btn-group">
                    <button class="btn btn-sm btn-light border dropdown-toggle" data-bs-toggle="dropdown" aria-label="Opções">
                      <i class="bi bi-three-dots-vertical"></i>
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end shadow-sm">
                      <li>
                        <a class="dropdown-item fs-7" href="#" onclick='abrirModalCompras(<?= json_encode($cli) ?>)'>
                          <i class="bi bi-clock-history me-2 text-muted"></i>Histórico de Compras
                        </a>
                      </li>
                      <li>
                        <a class="dropdown-item fs-7" href="#" onclick='abrirModalExtrato(<?= json_encode($cli) ?>)'>
                          <i class="bi bi-file-text me-2 text-muted"></i>Extrato de Prêmios
                        </a>
                      </li>
                    </ul>
                  </div>
                </td>
              </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      </div>

    </div>
  </div>

</div>

<!-- MODAL 1: Histórico de Compras e Resgates -->
<div class="modal fade" id="modalCompras" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered modal-lg">
    <div class="modal-content" id="areaImpressaoCompras">
      <div class="modal-header">
        <h5 class="modal-title fw-bold" id="modalComprasTitulo">
          <i class="bi bi-cart me-2 text-vinho"></i> Histórico do Cliente
        </h5>
        <button type="button" class="btn-close no-print" data-bs-dismiss="modal" aria-label="Fechar"></button>
      </div>
      <div class="modal-body">
        <!-- Tabela de Compras -->
        <h6 class="fw-bold text-dark mb-2"><i class="bi bi-bag-check me-1"></i> Compras Realizadas</h6>
        <table class="table table-sm align-middle mb-4">
          <thead>
            <tr>
              <th>Data</th>
              <th>Pedido</th>
              <th class="text-end">Valor</th>
            </tr>
          </thead>
          <tbody id="modalComprasCorpo"></tbody>
        </table>

        <!-- Tabela de Resgates -->
        <h6 class="fw-bold text-dark mb-2"><i class="bi bi-gift me-1"></i> Histórico de Resgates</h6>
        <table class="table table-sm align-middle">
          <thead>
            <tr>
              <th>Data/Hora</th>
              <th>Qtd. Resgatada</th>
              <th>Responsável</th>
            </tr>
          </thead>
          <tbody id="modalResgatesCorpo"></tbody>
        </table>
      </div>
      <div class="modal-footer no-print">
        <button type="button" class="btn btn-sm btn-outline-secondary" data-bs-dismiss="modal">Fechar</button>
        <button type="button" class="btn btn-sm btn-vinho" onclick="imprimirHistoricoCompras()">
          <i class="bi bi-printer me-1"></i> Imprimir
        </button>
      </div>
    </div>
  </div>
</div>

<!-- MODAL 2: Extrato de Prêmios (Cartão Fidelidade Imprimível) -->
<div class="modal fade" id="modalExtrato" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered modal-md">
    <div class="modal-content border-0 shadow-lg rounded-4 overflow-hidden" id="areaImpressaoExtrato">
      
      <!-- Cabeçalho Estilo Cartão Vinho -->
      <div class="bg-vinho text-white text-center p-4 position-relative">
        <button type="button" class="btn-close btn-close-white position-absolute top-0 end-0 m-3 no-print" data-bs-dismiss="modal" aria-label="Fechar"></button>
        
        <div class="mb-2">
          <span class="badge bg-warning text-dark fw-bold px-3 py-1 rounded-pill" id="extratoTagBadge">
            <i class="bi bi-star-fill me-1"></i> Saldo Múltiplo
          </span>
        </div>
        
        <div class="display-6 text-warning mb-1">
          <i class="bi bi-trophy-fill"></i>
        </div>
        
        <h5 class="fw-bold tracking-wide text-uppercase mb-0">Extrato de Fidelidade</h5>
        <small class="text-white-50 fs-8" id="extratoNomeCampanha">Campanha <?= htmlspecialchars($campanha_nome) ?></small>
      </div>

      <!-- Corpo do Cartão -->
      <div class="modal-body p-4 bg-white">
        
        <!-- Cliente Titular & Prêmios -->
        <div class="d-flex align-items-center justify-content-between mb-3">
          <div>
            <div class="text-uppercase text-muted fw-bold fs-8">Cliente Titular</div>
            <div class="fw-bold fs-6 text-dark" id="extratoClienteNome">Adega São João Ltda</div>
            <div class="text-muted fs-8" id="extratoClienteDoc">Doc: 12.345.678/0001-90</div>
          </div>
          <span class="badge bg-warning-subtle text-warning-emphasis border border-warning-subtle px-3 py-2 rounded-pill fs-8 fw-bold">
            <i class="bi bi-gift-fill me-1"></i> <span id="extratoQtdPremios">0</span> Prêmio(s) Conquistado(s)
          </span>
        </div>

        <!-- Box de Progresso -->
        <div class="card border rounded-3 bg-light p-3 mb-3">
          <div class="d-flex justify-content-between align-items-center mb-2">
            <span class="fw-bold fs-7 text-dark">
              <i class="bi bi-rocket-takeoff-fill me-1 text-vinho"></i> Progresso do Próximo Prêmio
            </span>
            <span class="badge bg-vinho text-white px-2 py-1 fs-8" id="extratoPorcentagem">0% Concluído</span>
          </div>

          <div class="progress mb-2" style="height: 14px;">
            <div id="extratoProgressBar" class="progress-bar progress-bar-striped progress-bar-animated bg-success" role="progressbar" style="width: 0%;"></div>
          </div>

          <div class="d-flex justify-content-between fs-7">
            <span class="text-muted">Total Comprado: <strong class="text-dark" id="extratoTotalComprado">R$ 0,00</strong></span>
            <span class="text-danger fw-bold" id="extratoFaltaTexto">Falta apenas R$ 0,00</span>
          </div>
        </div>

        <!-- Box Próximo Prêmio -->
        <div class="p-3 text-center rounded-3 mb-3" style="background-color: #fff9e6; border: 1.5px dashed #ffc107;">
          <div class="text-uppercase fw-bold text-secondary fs-8 mb-1">Seu Próximo Prêmio:</div>
          <div class="fs-5 fw-bold text-dark d-flex align-items-center justify-content-center gap-2">
            <i class="bi bi-gem text-warning fs-4"></i> <span>01 Taça de Cristal</span>
          </div>
        </div>

        <!-- Validade -->
        <div class="p-2 text-center rounded bg-warning-subtle text-warning-emphasis fs-8 mb-3">
          <i class="bi bi-clock me-1"></i> Campanha válida até <strong><?= $campanha_validade ?></strong>. Aproveite!
        </div>

        <!-- Botões de Ação do Modal -->
        <div class="d-flex justify-content-end gap-2 no-print">
          <button type="button" class="btn btn-sm btn-outline-secondary" data-bs-dismiss="modal">Fechar</button>
          <button type="button" class="btn btn-sm btn-vinho" onclick="imprimirExtrato()">
            <i class="bi bi-printer me-1"></i> Imprimir Extrato
          </button>
        </div>

      </div>
    </div>
  </div>
</div>

<!-- Bootstrap 5 JS Bundle -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

<script>
  // Inicialização dos Modais Bootstrap
  const modalComprasBS = new bootstrap.Modal(document.getElementById('modalCompras'));
  const modalExtratoBS = new bootstrap.Modal(document.getElementById('modalExtrato'));

  // 1. Filtrar Tabela por Busca (Texto)
  function filtrarTabela() {
    const busca = document.getElementById('inputBusca').value.toLowerCase();
    const linhas = document.querySelectorAll('#tabelaClientes tbody tr');

    linhas.forEach(linha => {
      const nome = linha.querySelector('.cli-nome').innerText.toLowerCase();
      const doc = linha.querySelector('.cli-doc').innerText.toLowerCase();
      linha.style.display = (nome.includes(busca) || doc.includes(busca)) ? '' : 'none';
    });
  }

  // 2. Filtrar Tabela por Status
  function filtrarStatus(status, botao) {
    document.querySelectorAll('.btn-group .btn').forEach(btn => {
      btn.classList.remove('btn-secondary', 'active');
      btn.classList.add('btn-outline-secondary');
    });
    botao.classList.remove('btn-outline-secondary');
    botao.classList.add('btn-secondary', 'active');

    const linhas = document.querySelectorAll('#tabelaClientes tbody tr');
    linhas.forEach(linha => {
      linha.style.display = (status === 'todos' || linha.dataset.status === status) ? '' : 'none';
    });
  }

  // 3. Abrir Modal de Compras e Resgates
function abrirModalCompras(cliente) {
  document.getElementById('modalComprasTitulo').innerHTML = `<i class="bi bi-cart me-2 text-vinho"></i> Histórico: ${cliente.nome}`;
  
  // Preencher Compras
  const corpoCompras = document.getElementById('modalComprasCorpo');
  corpoCompras.innerHTML = '';
  if (!cliente.compras || cliente.compras.length === 0) {
    corpoCompras.innerHTML = '<tr><td colspan="3" class="text-center text-muted">Nenhuma compra registrada.</td></tr>';
  } else {
    cliente.compras.forEach(c => {
      const valorFmt = new Intl.NumberFormat('pt-BR', { style: 'currency', currency: 'BRL' }).format(c.valor);
      const dataFmt = c.data.split('-').reverse().join('/');
      corpoCompras.innerHTML += `
        <tr>
          <td>${dataFmt}</td>
          <td><span class="badge bg-light text-dark border">${c.pedido}</span></td>
          <td class="text-end fw-bold">${valorFmt}</td>
        </tr>
      `;
    });
  }

  // Preencher Resgates
  const corpoResgates = document.getElementById('modalResgatesCorpo');
  corpoResgates.innerHTML = '';
  if (!cliente.resgates_historico || cliente.resgates_historico.length === 0) {
    corpoResgates.innerHTML = '<tr><td colspan="3" class="text-center text-muted">Nenhum resgate efetuado até o momento.</td></tr>';
  } else {
    cliente.resgates_historico.forEach(r => {
      // Formata data caso tenha horário ou apenas ano-mês-dia
      const dataFmt = r.data.includes(' ') 
        ? r.data.split(' ')[0].split('-').reverse().join('/') + ' ' + r.data.split(' ')[1]
        : r.data.split('-').reverse().join('/');
      
      corpoResgates.innerHTML += `
        <tr>
          <td>${dataFmt}</td>
          <td><span class="badge bg-success-subtle text-success border border-success-subtle">${r.qtd} prêmio(s)</span></td>
          <td>${r.responsavel || '-'}</td>
        </tr>
      `;
    });
  }

  modalComprasBS.show();
}

// Função para Imprimir o Modal de Compras/Resgates
function imprimirHistoricoCompras() {
  const conteudo = document.getElementById('areaImpressaoCompras').outerHTML;
  const janelaImpressao = window.open('', '', 'height=700,width=900');
  
  janelaImpressao.document.write('<html><head><title>Histórico de Compras e Resgates</title>');
  janelaImpressao.document.write('<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">');
  janelaImpressao.document.write('<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">');
  janelaImpressao.document.write(`
    <style>
      .text-vinho { color: #4a121a !important; }
      .no-print { display: none !important; }
      body { padding: 20px; background-color: #fff; }
    </style>
  `);
  janelaImpressao.document.write('</head><body>');
  janelaImpressao.document.write(conteudo);
  janelaImpressao.document.write('</body></html>');
  janelaImpressao.document.close();
  
  setTimeout(() => {
    janelaImpressao.focus();
    janelaImpressao.print();
    janelaImpressao.close();
  }, 500);
}
  // 4. Abrir Modal de Extrato (Cartão Fidelidade)
  function abrirModalExtrato(cliente) {
    const metaValor = 1000.00;
    const totalCompras = cliente.compras ? cliente.compras.reduce((acc, c) => acc + parseFloat(c.valor), 0) : 0;
    const conquistados = Math.floor(totalCompras / metaValor);
    
    const valorResto = totalCompras % metaValor;
    const porcentagem = Math.min(100, Math.round((valorResto / metaValor) * 100));
    const faltaValor = metaValor - valorResto;

    document.getElementById('extratoClienteNome').innerText = cliente.nome;
    document.getElementById('extratoClienteDoc').innerText = `Doc: ${cliente.documento}`;
    document.getElementById('extratoQtdPremios').innerText = conquistados;
    
    const tagBadge = document.getElementById('extratoTagBadge');
    if (conquistados > 1) {
      tagBadge.innerHTML = '<i class="bi bi-star-fill me-1"></i> Saldo Múltiplo';
      tagBadge.classList.remove('d-none');
    } else {
      tagBadge.classList.add('d-none');
    }

    document.getElementById('extratoPorcentagem').innerText = `${porcentagem}% Concluído`;
    document.getElementById('extratoProgressBar').style.width = `${porcentagem}%`;
    
    const fmtMoeda = new Intl.NumberFormat('pt-BR', { style: 'currency', currency: 'BRL' });
    document.getElementById('extratoTotalComprado').innerText = fmtMoeda.format(totalCompras);
    document.getElementById('extratoFaltaTexto').innerText = `Falta apenas ${fmtMoeda.format(faltaValor)}`;

    modalExtratoBS.show();
  }

  // 5. Imprimir Extrato (com cores forçadas)
function imprimirExtrato() {
  const conteudo = document.getElementById('areaImpressaoExtrato').outerHTML;
  const janelaImpressao = window.open('', '', 'height=700,width=900');
  
  janelaImpressao.document.write('<html><head><title>Extrato de Fidelidade</title>');
  janelaImpressao.document.write('<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">');
  janelaImpressao.document.write('<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">');
  janelaImpressao.document.write(`
    <style>
      /* Força a impressão de cores de fundo e bordas em todos os navegadores */
      * {
        -webkit-print-color-adjust: exact !important;
        print-color-adjust: exact !important;
        color-adjust: exact !important;
      }
      .bg-vinho { background-color: #4a121a !important; color: white !important; }
      .text-vinho { color: #4a121a !important; }
      .no-print { display: none !important; }
      body { padding: 20px; display: flex; justify-content: center; background-color: #fff; }
      .modal-content { max-width: 500px; border: 1px solid #ccc !important; }
    </style>
  `);
  janelaImpressao.document.write('</head><body>');
  janelaImpressao.document.write(conteudo);
  janelaImpressao.document.write('</body></html>');
  janelaImpressao.document.close();
  
  setTimeout(() => {
    janelaImpressao.focus();
    janelaImpressao.print();
    janelaImpressao.close();
  }, 500);
}

  // 6. Exportar Excel (CSV)
  function exportarExcel() {
    let csv = '\uFEFFCliente;Documento;Status\n';
    const linhas = document.querySelectorAll('#tabelaClientes tbody tr');
    linhas.forEach(l => {
      const nome = l.querySelector('.cli-nome').innerText.replace(';', '');
      const doc = l.querySelector('.cli-doc').innerText.replace(';', '');
      const status = l.dataset.status;
      csv += `"${nome}";"${doc}";"${status}"\n`;
    });

    const blob = new Blob([csv], { type: 'text/csv;charset=utf-8;' });
    const link = document.createElement('a');
    link.href = URL.createObjectURL(blob);
    link.setAttribute('download', 'relatorio_fidelidade.csv');
    document.body.appendChild(link);
    link.click();
    document.body.removeChild(link);
  }
</script>

</body>
</html>