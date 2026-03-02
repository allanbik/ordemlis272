<?php
declare(strict_types=1);
require __DIR__ . '/helpers.php';

iniciar_sessao();

if (empty($_SESSION['user_id'])) {
  header('Location: login.html?err=' . rawurlencode('Faça login para continuar.'));
  exit;
}

$userId = (int)$_SESSION['user_id'];
$pdo = pdo();

// Busca dados atuais
$stmt = $pdo->prepare("SELECT * FROM usuarios WHERE id = :id LIMIT 1");
$stmt->execute([':id' => $userId]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$user) {
  session_destroy();
  header('Location: login.html');
  exit;
}
?>
<!doctype html>
<html lang="pt-br">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Ficha Cadastral — Ordem de Lis 272/PR</title>
  <link rel="icon" type="image/jpeg" href="imagens/logo.jpg">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
  <link href="css/style.css" rel="stylesheet">
</head>
<body class="bg-scout-bg">

  <!-- Navbar -->
  <nav class="navbar navbar-expand-lg navbar-dark bg-scout-blue mb-4 shadow-sm">
    <div class="container">
      <a class="navbar-brand d-flex align-items-center gap-2" href="painel.php">
        <img src="imagens/logo.jpg" alt="Logo" width="30" height="30" class="rounded d-inline-block align-text-top border border-white">
        Ordem de Lis 272/PR
      </a>
      <div class="collapse navbar-collapse justify-content-end" id="navbarNav">
        <ul class="navbar-nav align-items-center">
          <li class="nav-item me-3 text-white">
            Olá, <strong><?= htmlspecialchars($user['nome']) ?></strong>
          </li>
          <li class="nav-item">
            <a class="btn btn-sm btn-outline-light text-uppercase fw-bold" href="painel.php"><i class="bi bi-arrow-left"></i> Voltar</a>
          </li>
        </ul>
      </div>
    </div>
  </nav>

  <div class="container mb-5">
    <div class="row justify-content-center">
      <div class="col-lg-10">
        
        <div class="card shadow border-0">
          <div class="card-header bg-scout-blue text-white py-3">
            <h4 class="mb-0 fw-bold"><i class="bi bi-person-vcard me-2"></i> Ficha Cadastral Detalhada</h4>
          </div>
          <div class="card-body p-4">
            
            <?php if (isset($_GET['ok'])): ?>
              <div class="alert alert-success border-0 shadow-sm mb-4">
                <i class="bi bi-check-circle-fill me-2"></i> <?= htmlspecialchars($_GET['ok']) ?>
              </div>
            <?php endif; ?>

            <?php if (isset($_GET['err'])): ?>
              <div class="alert alert-danger border-0 shadow-sm mb-4">
                <i class="bi bi-exclamation-triangle-fill me-2"></i> <?= htmlspecialchars($_GET['err']) ?>
              </div>
            <?php endif; ?>

            <form action="salvar_ficha.php" method="POST">
              
              <!-- SEÇÃO: DADOS PESSOAIS -->
              <h5 class="text-scout-blue fw-bold border-bottom pb-2 mb-3">1. Dados Pessoais</h5>
              <div class="row g-3 mb-4">
                <div class="col-md-6">
                  <label class="form-label fw-bold">Nome Completo</label>
                  <input type="text" class="form-control" value="<?= htmlspecialchars($user['nome']) ?>" disabled>
                  <div class="form-text">Para alterar o nome, entre em contato com a secretaria.</div>
                </div>
                <div class="col-md-3">
                  <label class="form-label fw-bold">CPF</label>
                  <input type="text" class="form-control" value="<?= htmlspecialchars($user['cpf']) ?>" disabled>
                </div>
                <div class="col-md-3">
                  <label class="form-label fw-bold">Data de Nascimento</label>
                  <input type="date" name="data_nascimento" class="form-control" value="<?= htmlspecialchars($user['data_nascimento'] ?? '') ?>" required>
                </div>
                
                <div class="col-md-3">
                  <label class="form-label fw-bold">Sexo</label>
                  <select name="sexo" class="form-select">
                    <option value="">Selecione...</option>
                    <option value="M" <?= ($user['sexo'] ?? '') === 'M' ? 'selected' : '' ?>>Masculino</option>
                    <option value="F" <?= ($user['sexo'] ?? '') === 'F' ? 'selected' : '' ?>>Feminino</option>
                    <option value="O" <?= ($user['sexo'] ?? '') === 'O' ? 'selected' : '' ?>>Outro</option>
                  </select>
                </div>
                <div class="col-md-3">
                  <label class="form-label fw-bold">Registro UEB</label>
                  <input type="text" name="registro_ueb" class="form-control" value="<?= htmlspecialchars($user['registro_ueb'] ?? '') ?>" placeholder="000000-0">
                </div>
                <div class="col-md-3">
                  <label class="form-label fw-bold">Naturalidade (Cidade/UF)</label>
                  <input type="text" name="naturalidade" class="form-control" value="<?= htmlspecialchars($user['naturalidade'] ?? '') ?>">
                </div>
                <div class="col-md-3">
                  <label class="form-label fw-bold">Religião</label>
                  <input type="text" name="religiao" class="form-control" value="<?= htmlspecialchars($user['religiao'] ?? '') ?>">
                </div>
              </div>

              <!-- SEÇÃO: ENDEREÇO -->
              <h5 class="text-scout-blue fw-bold border-bottom pb-2 mb-3">2. Endereço e Contato</h5>
              <div class="row g-3 mb-4">
                <div class="col-md-6">
                  <label class="form-label fw-bold">Rua / Logradouro</label>
                  <input type="text" name="rua" class="form-control" value="<?= htmlspecialchars($user['rua'] ?? '') ?>">
                </div>
                <div class="col-md-2">
                  <label class="form-label fw-bold">Número</label>
                  <input type="text" name="numero" class="form-control" value="<?= htmlspecialchars($user['numero'] ?? '') ?>">
                </div>
                <div class="col-md-4">
                  <label class="form-label fw-bold">Complemento</label>
                  <input type="text" name="complemento" class="form-control" value="<?= htmlspecialchars($user['complemento'] ?? '') ?>">
                </div>
                
                <div class="col-md-4">
                  <label class="form-label fw-bold">Bairro</label>
                  <input type="text" name="bairro" class="form-control" value="<?= htmlspecialchars($user['bairro'] ?? '') ?>">
                </div>
                <div class="col-md-4">
                  <label class="form-label fw-bold">Cidade</label>
                  <input type="text" name="cidade" class="form-control" value="<?= htmlspecialchars($user['cidade'] ?? '') ?>">
                </div>
                <div class="col-md-2">
                  <label class="form-label fw-bold">Estado (UF)</label>
                  <input type="text" name="estado" class="form-control" maxlength="2" value="<?= htmlspecialchars($user['estado'] ?? '') ?>" placeholder="PR">
                </div>
                <div class="col-md-2">
                  <label class="form-label fw-bold">CEP</label>
                  <input type="text" name="cep" class="form-control" value="<?= htmlspecialchars($user['cep'] ?? '') ?>" placeholder="00000-000">
                </div>

                <div class="col-md-6">
                  <label class="form-label fw-bold">Telefone Residencial</label>
                  <input type="text" name="telefone_residencial" class="form-control" value="<?= htmlspecialchars($user['telefone_residencial'] ?? '') ?>" placeholder="(00) 0000-0000">
                </div>
                <div class="col-md-6">
                  <label class="form-label fw-bold">Telefone Celular / WhatsApp</label>
                  <input type="text" name="telefone_celular" class="form-control" value="<?= htmlspecialchars($user['telefone_celular'] ?? '') ?>" placeholder="(00) 00000-0000">
                </div>
              </div>

              <!-- SEÇÃO: ESCOLARIDADE / PROFISSÃO -->
              <h5 class="text-scout-blue fw-bold border-bottom pb-2 mb-3">3. Profissional e Escolar</h5>
              <div class="row g-3 mb-4">
                <div class="col-md-4">
                  <label class="form-label fw-bold">Escolaridade</label>
                  <select name="escolaridade" class="form-select">
                    <option value="">Selecione...</option>
                    <?php
                    $niveis = ["Fundamental Incompleto", "Fundamental Completo", "Médio Incompleto", "Médio Completo", "Superior Incompleto", "Superior Completo", "Pós-Graduação"];
                    foreach($niveis as $nivel) {
                      $sel = ($user['escolaridade'] === $nivel) ? 'selected' : '';
                      echo "<option value=\"$nivel\" $sel>$nivel</option>";
                    }
                    ?>
                  </select>
                </div>
                <div class="col-md-4">
                  <label class="form-label fw-bold">Profissão</label>
                  <input type="text" name="profissao" class="form-control" value="<?= htmlspecialchars($user['profissao'] ?? '') ?>">
                </div>
                <div class="col-md-4">
                  <label class="form-label fw-bold">Local de Trabalho / Escola</label>
                  <input type="text" name="local_trabalho" class="form-control" value="<?= htmlspecialchars($user['local_trabalho'] ?? '') ?>">
                </div>
              </div>

              <hr class="my-4">

              <div class="d-flex justify-content-between align-items-center">
                <a href="painel.php" class="btn btn-light"><i class="bi bi-x-lg"></i> Cancelar</a>
                <button type="submit" class="btn btn-scout-blue px-5 fw-bold">
                  <i class="bi bi-save me-2"></i> Salvar Alterações
                </button>
              </div>

            </form>

          </div>
        </div>

      </div>
    </div>
    
    <div class="text-center text-muted small mt-4">
      © <?= date('Y') ?> Grupo Escoteiro Ordem de Lis 272/PR • Sempre Alerta!
    </div>
  </div>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
