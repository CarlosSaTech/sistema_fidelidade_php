<style>
  .bg-vinho-escuro {
    background-color: #4a121a !important;
  }
  .logo-nav {
    max-height: 40px;
    width: auto;
  }
</style>

<nav class="navbar navbar-expand-lg navbar-dark bg-vinho-escuro mb-4 shadow-sm no-print">
  <div class="container">
    <a class="navbar-brand fw-bold d-flex align-items-center" href="index.php">
      <i class="bi bi-tag-fill text-warning fs-4 me-2"></i> 
      <span>Programa de Fidelidade</span>
    </a>
    
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
      <span class="navbar-toggler-icon"></span>
    </button>

    <div class="collapse navbar-collapse" id="navbarNav">
      <div class="navbar-nav ms-auto align-items-lg-center gap-1">
        <a class="nav-link" href="index.php"><i class="bi bi-house-door me-1"></i> Painel Geral</a>
        <a class="nav-link" href="cadastrar.php"><i class="bi bi-cart-plus me-1"></i> Registrar Pedido</a>
        <a class="nav-link fw-bold text-warning" href="campanha.php"><i class="bi bi-trophy me-1"></i> Campanhas</a>
        
        <!-- BOTÃO DE SAIR DO SISTEMA -->
        <a class="btn btn-sm btn-outline-light ms-lg-2 mt-2 mt-lg-0" href="logout.php" onclick="return confirm('Deseja realmente sair do sistema?')">
          <i class="bi bi-box-arrow-right me-1"></i> Sair
        </a>
      </div>
    </div>
  </div>
</nav>