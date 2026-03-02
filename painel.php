<?php
declare(strict_types=1);
require __DIR__ . '/helpers.php';

iniciar_sessao();

function go(string $url): never {
  header('Location: ' . $url);
  exit;
}

if (empty($_SESSION['user_id'])) {
  go('login.html?err=' . rawurlencode('Faça login para continuar.'));
}

$userId = (int)$_SESSION['user_id'];

try {
  $pdo = pdo();
  $stmt = $pdo->prepare("SELECT id, nome, cpf, email, perfil, ramo FROM usuarios WHERE id = :id LIMIT 1");
  $stmt->execute([':id' => $userId]);
  $user = $stmt->fetch(PDO::FETCH_ASSOC);

  if (!$user) {
    session_destroy();
    go('login.html?err=' . rawurlencode('Sessão inválida. Entre novamente.'));
  }
} catch (Throwable $e) {
  @file_put_contents(__DIR__ . '/_php_errors.log',
    '[' . date('Y-m-d H:i:s') . '] painel.php: ' . $e->getMessage() . "\n",
    FILE_APPEND
  );
  go('login.html?err=' . rawurlencode('Erro no servidor.'));
}
?>
<!doctype html>
<html lang="pt-br">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Painel — Ordem de Lis 272/PR</title>
  <link rel="icon" type="image/jpeg" href="imagens/logo.jpg">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
  <link href="css/style.css" rel="stylesheet">
</head>
<body class="bg-scout-bg">

  <!-- Navbar -->
  <nav class="navbar navbar-expand-lg navbar-dark bg-scout-blue mb-4 shadow-sm">
    <div class="container">
      <a class="navbar-brand d-flex align-items-center gap-2" href="#">
        <img src="imagens/logo.jpg" alt="Logo" width="30" height="30" class="rounded d-inline-block align-text-top border border-white">
        Ordem de Lis 272/PR
      </a>
      <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
        <span class="navbar-toggler-icon"></span>
      </button>
      <div class="collapse navbar-collapse justify-content-end" id="navbarNav">
        <ul class="navbar-nav align-items-center">
          <li class="nav-item me-3 text-white">
            Olá, <strong><?= htmlspecialchars($user['nome']) ?></strong>
          </li>
          <li class="nav-item">
            <a class="btn btn-sm btn-outline-light text-uppercase fw-bold" href="logout.php">Sair <i class="bi bi-box-arrow-right"></i></a>
          </li>
        </ul>
      </div>
    </div>
  </nav>

  <div class="container">
    
    <!-- Welcome Header -->
    <div class="row mb-4">
      <div class="col-12">
        <div class="card bg-white p-4 shadow-sm border-0">
          <div class="d-flex align-items-center gap-3">
            <div class="bg-scout-blue text-white rounded-circle d-flex align-items-center justify-content-center" style="width: 60px; height: 60px; font-size: 1.5rem;">
               <?= strtoupper(substr($user['nome'], 0, 1)) ?>
            </div>
            <div>
              <h2 class="h4 fw-bold mb-0 text-scout-blue">Painel do Associado</h2>
              <p class="text-muted mb-0">Ramo: <span class="badge bg-scout-green"><?= ucfirst($user['ramo']) ?></span> • Perfil: <?= ucfirst($user['perfil']) ?></p>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Actions Grid -->
    <div class="row g-4">
      
      <!-- Ficha Cadastral -->
      <div class="col-12 col-md-6 col-lg-4">
        <div class="card h-100 border-0 shadow-sm">
          <div class="card-body text-center p-4">
            <div class="mb-3 text-scout-blue" style="font-size: 3rem;"><i class="bi bi-person-lines-fill"></i></div>
            <h5 class="fw-bold">Ficha Cadastral</h5>
            <p class="text-muted small">Mantenha seus dados pessoais, endereço e contatos sempre atualizados.</p>
            <a href="ficha.php" class="btn btn-outline-primary w-100">Atualizar Dados</a>
          </div>
        </div>
      </div>

      <!-- Ficha Médica -->
      <div class="col-12 col-md-6 col-lg-4">
        <div class="card h-100 border-0 shadow-sm">
          <div class="card-body text-center p-4">
            <div class="mb-3 text-danger" style="font-size: 3rem;"><i class="bi bi-heart-pulse-fill"></i></div>
            <h5 class="fw-bold">Ficha Médica</h5>
            <p class="text-muted small">Informações vitais de saúde, alergias e contatos de emergência.</p>
            <a href="ficha_medica.php" class="btn btn-outline-primary w-100">Preencher Ficha</a>
          </div>
        </div>
      </div>

      <!-- Atividades -->
      <div class="col-12 col-md-6 col-lg-4">
        <div class="card h-100 border-0 shadow-sm">
          <div class="card-body text-center p-4">
            <div class="mb-3 text-scout-yellow" style="font-size: 3rem;"><i class="bi bi-calendar-event-fill"></i></div>
            <h5 class="fw-bold">Minhas Atividades</h5>
            <p class="text-muted small">Inscreva-se em acampamentos, cursos e atividades do grupo.</p>
            <a href="#" class="btn btn-outline-primary w-100">Ver Agenda</a>
          </div>
        </div>
      </div>

    </div>

    <!-- Footer -->
    <div class="text-center text-muted small mt-5 mb-4">
      © <?= date('Y') ?> Grupo Escoteiro Ordem de Lis 272/PR • Sempre Alerta!
    </div>

  </div>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
