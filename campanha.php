<?php 
if (session_status() === PHP_SESSION_NONE) {
    session_start(); 
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Gerenciar Campanhas - Fidelização</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
</head>
<body class="bg-light">

<?php include 'nav.php'; ?>

<div class="container py-2">
    <div class="row justify-content-center">
        <div class="col-md-9">
            
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h4 class="fw-bold mb-0"><i class="bi bi-trophy text-warning"></i> Configurar Campanha de Fidelidade</h4>
                <a href="index.php" class="btn btn-outline-secondary btn-sm"><i class="bi bi-arrow-left"></i> Voltar ao Painel</a>
            </div>

            <!-- Formulário de Nova Campanha -->
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-white py-3">
                    <h5 class="card-title mb-0 fs-6 fw-bold text-secondary">Cadastrar / Editar Campanha</h5>
                </div>
                <div class="card-body">
                    <form action="index.php" method="GET" onsubmit="alert('Campanha salva com sucesso!');">
                        
                        <!-- Nome da Campanha -->
                        <div class="row g-3 mb-3">
                            <div class="col-md-12">
                                <label class="form-label font-monospace small">Nome da Campanha</label>
                                <input type="text" class="form-control" name="nome_campanha" placeholder="Ex: Campanha Taça de Cristal 2026" required>
                            </div>
                        </div>

                        <!-- Datas de Vigência -->
                        <div class="row g-3 mb-3">
                            <div class="col-md-6">
                                <label class="form-label font-monospace small">Data de Início</label>
                                <input type="date" class="form-control" name="data_inicio" value="<?= date('Y-m-d') ?>" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label font-monospace small">Data de Término</label>
                                <input type="date" class="form-control" name="data_fim" required>
                            </div>
                        </div>

                        <!-- Meta e Prêmio -->
                        <div class="row g-3 mb-3">
                            <div class="col-md-6">
                                <label class="form-label font-monospace small">Valor de Meta em Compras (R$)</label>
                                <div class="input-group">
                                    <span class="input-group-text">R$</span>
                                    <input type="number" step="0.01" class="form-control" name="valor_meta" placeholder="1000.00" required>
                                </div>
                                <small class="text-muted">Valor acumulado necessário para ganhar o prêmio.</small>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label font-monospace small">Brinde / Prêmio de Resgate</label>
                                <input type="text" class="form-control" name="brinde" placeholder="Ex: 01 Taça de Cristal para Vinho Tinto" required>
                            </div>
                        </div>

                        <div class="d-grid gap-2 d-md-flex justify-content-md-end mt-4">
                            <button type="reset" class="btn btn-light me-md-2">Limpar</button>
                            <button type="submit" class="btn btn-success px-4"><i class="bi bi-check-circle"></i> Salvar Campanha</button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Tabela de Campanhas Cadastradas (Simulação) -->
            <div class="card shadow-sm">
                <div class="card-header bg-white py-3">
                    <h6 class="fw-bold mb-0">Campanhas Registradas</h6>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="table-dark">
                                <tr>
                                    <th>Nome</th>
                                    <th>Período</th>
                                    <th>Meta</th>
                                    <th>Brinde</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td><strong>Campanha Taça de Cristal 2026</strong></td>
                                    <td>01/08/2026 a 31/08/2026</td>
                                    <td>R$ 1.000,00</td>
                                    <td>01 Taça de Cristal</td>
                                    <td><span class="badge bg-success">Ativa</span></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>