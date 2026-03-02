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

// Busca dados básicos do usuário
$stmt = $pdo->prepare("SELECT nome, ramo, perfil FROM usuarios WHERE id = :id LIMIT 1");
$stmt->execute([':id' => $userId]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

// Busca dados médicos
$stmtM = $pdo->prepare("SELECT * FROM fichas_medicas WHERE usuario_id = :id LIMIT 1");
$stmtM->execute([':id' => $userId]);
$med = $stmtM->fetch(PDO::FETCH_ASSOC) ?: [];

?>
<!doctype html>
<html lang="pt-br">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Ficha Médica — Ordem de Lis 272/PR</title>
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
            Olá, <strong><?= htmlspecialchars($user['nome'] ?? '') ?></strong>
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
          <div class="card-header bg-danger text-white py-3">
            <h4 class="mb-0 fw-bold"><i class="bi bi-heart-pulse-fill me-2"></i> Ficha Médica Individual</h4>
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

            <form action="salvar_ficha_medica.php" method="POST">
              
              <!-- 1. DADOS FÍSICOS -->
              <h5 class="text-danger fw-bold border-bottom pb-2 mb-3">1. Dados Físicos</h5>
              <div class="row g-3 mb-4">
                <div class="col-md-3">
                  <label class="form-label fw-bold">Altura (cm)</label>
                  <input type="number" step="0.01" name="altura" class="form-control" value="<?= $med['altura'] ?? '' ?>" placeholder="Ex: 1.70">
                </div>
                <div class="col-md-3">
                  <label class="form-label fw-bold">Peso (kg)</label>
                  <input type="number" step="0.01" name="peso" class="form-control" value="<?= $med['peso'] ?? '' ?>" placeholder="Ex: 65.5">
                </div>
                <div class="col-md-3">
                  <label class="form-label fw-bold">Tipo Sanguíneo</label>
                  <select name="tipo_sanguineo" class="form-select">
                    <option value="">Selecione...</option>
                    <?php foreach(['A','B','AB','O','Não sabe'] as $t): ?>
                      <option value="<?= $t ?>" <?= ($med['tipo_sanguineo'] ?? '') === $t ? 'selected' : '' ?>><?= $t ?></option>
                    <?php endforeach; ?>
                  </select>
                </div>
                <div class="col-md-3">
                  <label class="form-label fw-bold">Fator RH</label>
                  <select name="fator_rh" class="form-select">
                    <option value="">Selecione...</option>
                    <option value="+" <?= ($med['fator_rh'] ?? '') === '+' ? 'selected' : '' ?>>Positivo (+)</option>
                    <option value="-" <?= ($med['fator_rh'] ?? '') === '-' ? 'selected' : '' ?>>Negativo (-)</option>
                  </select>
                </div>
              </div>

              <!-- 2. AUXÍLIOS -->
              <h5 class="text-danger fw-bold border-bottom pb-2 mb-3">2. Uso de Auxílios</h5>
              <div class="row g-3 mb-4">
                <div class="col-12">
                  <div class="d-flex flex-wrap gap-4">
                    <div class="form-check">
                      <input class="form-check-input" type="checkbox" name="usa_oculos" value="1" id="oculos" <?= ($med['usa_oculos'] ?? 0) ? 'checked' : '' ?>>
                      <label class="form-check-label" for="oculos">Óculos</label>
                    </div>
                    <div class="form-check">
                      <input class="form-check-input" type="checkbox" name="usa_lentes" value="1" id="lentes" <?= ($med['usa_lentes'] ?? 0) ? 'checked' : '' ?>>
                      <label class="form-check-label" for="lentes">Lentes de contato</label>
                    </div>
                    <div class="form-check">
                      <input class="form-check-input" type="checkbox" name="usa_aparelho_dentario" value="1" id="dentario" <?= ($med['usa_aparelho_dentario'] ?? 0) ? 'checked' : '' ?>>
                      <label class="form-check-label" for="dentario">Aparelho dentário</label>
                    </div>
                    <div class="form-check">
                      <input class="form-check-input" type="checkbox" name="usa_sondas" value="1" id="sondas" <?= ($med['usa_sondas'] ?? 0) ? 'checked' : '' ?>>
                      <label class="form-check-label" for="sondas">Sondas</label>
                    </div>
                    <div class="form-check">
                      <input class="form-check-input" type="checkbox" name="usa_marcapasso" value="1" id="marcapasso" <?= ($med['usa_marcapasso'] ?? 0) ? 'checked' : '' ?>>
                      <label class="form-check-label" for="marcapasso">Marcapasso</label>
                    </div>
                    <div class="form-check">
                      <input class="form-check-input" type="checkbox" name="usa_aparelho_audicao" value="1" id="audicao" <?= ($med['usa_aparelho_audicao'] ?? 0) ? 'checked' : '' ?>>
                      <label class="form-check-label" for="audicao">Aparelho de audição</label>
                    </div>
                  </div>
                </div>
              </div>

              <!-- 3. SAÚDE FÍSICA -->
              <h5 class="text-danger fw-bold border-bottom pb-2 mb-3">3. Saúde e Alergias</h5>
              <div class="row g-3 mb-4">
                <div class="col-12 mb-2">
                  <p class="small text-muted mb-3">Marque se você apresenta ou já apresentou algum dos problemas abaixo:</p>
                  <div class="row">
                    <div class="col-md-4 mb-2">
                      <div class="form-check">
                        <input class="form-check-input" type="checkbox" name="asma_bronquite" value="1" id="asma" <?= ($med['asma_bronquite'] ?? 0) ? 'checked' : '' ?>>
                        <label class="form-check-label" for="asma">Asma ou Bronquite</label>
                      </div>
                    </div>
                    <div class="col-md-4 mb-2">
                      <div class="form-check">
                        <input class="form-check-input" type="checkbox" name="rinite_sinusite" value="1" id="rinite" <?= ($med['rinite_sinusite'] ?? 0) ? 'checked' : '' ?>>
                        <label class="form-check-label" for="rinite">Rinite ou Sinusite</label>
                      </div>
                    </div>
                    <div class="col-md-4 mb-2">
                      <div class="form-check">
                        <input class="form-check-input" type="checkbox" name="hipertensao" value="1" id="hiper" <?= ($med['hipertensao'] ?? 0) ? 'checked' : '' ?>>
                        <label class="form-check-label" for="hiper">Hipertensão</label>
                      </div>
                    </div>
                    <div class="col-md-4 mb-2">
                      <div class="form-check">
                        <input class="form-check-input" type="checkbox" name="diabetes" value="1" id="diab" <?= ($med['diabetes'] ?? 0) ? 'checked' : '' ?>>
                        <label class="form-check-label" for="diab">Diabetes</label>
                      </div>
                    </div>
                    <div class="col-md-4 mb-2">
                      <div class="form-check">
                        <input class="form-check-input" type="checkbox" name="convulsoes_epilepsia" value="1" id="conv" <?= ($med['convulsoes_epilepsia'] ?? 0) ? 'checked' : '' ?>>
                        <label class="form-check-label" for="conv">Convulsões ou Epilepsia</label>
                      </div>
                    </div>
                    <div class="col-md-4 mb-2">
                      <div class="form-check">
                        <input class="form-check-input" type="checkbox" name="problemas_dermatologicos" value="1" id="derm" <?= ($med['problemas_dermatologicos'] ?? 0) ? 'checked' : '' ?>>
                        <label class="form-check-label" for="derm">Problemas Dermatológicos</label>
                      </div>
                    </div>
                    <div class="col-md-4 mb-2">
                      <div class="form-check">
                        <input class="form-check-input" type="checkbox" name="problemas_cardiacos" value="1" id="card" <?= ($med['problemas_cardiacos'] ?? 0) ? 'checked' : '' ?>>
                        <label class="form-check-label" for="card">Problemas Cardíacos</label>
                      </div>
                    </div>
                    <div class="col-md-4 mb-2">
                      <div class="form-check">
                        <input class="form-check-input" type="checkbox" name="problemas_renais" value="1" id="ren" <?= ($med['problemas_renais'] ?? 0) ? 'checked' : '' ?>>
                        <label class="form-check-label" for="ren">Problemas Renais</label>
                      </div>
                    </div>
                  </div>
                </div>
                <div class="col-12">
                  <label class="form-label fw-bold">Outros Problemas / Alergias Específicas</label>
                  <textarea name="outros_problemas" class="form-control" rows="2" placeholder="Cite aqui outras condições ou alergias..."><?= htmlspecialchars($med['outros_problemas'] ?? '') ?></textarea>
                </div>
              </div>

              <!-- 4. MEDICAMENTOS -->
              <h5 class="text-danger fw-bold border-bottom pb-2 mb-3">4. Medicamentos</h5>
              <div class="row g-3 mb-4">
                <div class="col-md-6">
                  <div class="form-check mb-2">
                    <input class="form-check-input" type="checkbox" name="usa_medicamentos" value="1" id="med_uso" <?= ($med['usa_medicamentos'] ?? 0) ? 'checked' : '' ?>>
                    <label class="form-check-label fw-bold" for="med_uso">Faz uso de medicamento contínuo ou temporário?</label>
                  </div>
                  <div class="form-check">
                    <input class="form-check-input" type="checkbox" name="autonomia_medicacao" value="1" id="autonomia" <?= ($med['autonomia_medicacao'] ?? 0) ? 'checked' : '' ?>>
                    <label class="form-check-label" for="autonomia">Tem autonomia para administrar a medicação sozinho?</label>
                  </div>
                </div>
                <div class="col-md-6">
                  <label class="form-label fw-bold">Detalhes dos Medicamentos (Nome, Dose, Horário)</label>
                  <textarea name="detalhes_medicamentos" class="form-control" rows="3"><?= htmlspecialchars($med['detalhes_medicamentos'] ?? '') ?></textarea>
                </div>
              </div>

              <!-- 5. EMERGÊNCIAS -->
              <h5 class="text-danger fw-bold border-bottom pb-2 mb-3">5. Contatos e Emergências</h5>
              <div class="row g-3 mb-4">
                <div class="col-md-6">
                  <label class="form-label fw-bold">Avisar em Emergência (Nome)</label>
                  <input type="text" name="contato_emergencia_nome" class="form-control" value="<?= htmlspecialchars($med['contato_emergencia_nome'] ?? '') ?>" placeholder="Ex: Pai / Mãe / Cônjuge">
                </div>
                <div class="col-md-6">
                  <label class="form-label fw-bold">Telefone de Emergência</label>
                  <input type="text" name="contato_emergencia_telefone" class="form-control" value="<?= htmlspecialchars($med['contato_emergencia_telefone'] ?? '') ?>" placeholder="(00) 00000-0000">
                </div>
                <div class="col-md-6">
                  <label class="form-label fw-bold">Plano de Saúde (Operadora)</label>
                  <input type="text" name="plano_saude_nome" class="form-control" value="<?= htmlspecialchars($med['plano_saude_nome'] ?? '') ?>" placeholder="Ex: Unimed, SUS, etc">
                </div>
                <div class="col-md-6">
                  <label class="form-label fw-bold">Número da Carteirinha</label>
                  <input type="text" name="plano_saude_carteirinha" class="form-control" value="<?= htmlspecialchars($med['plano_saude_carteirinha'] ?? '') ?>">
                </div>
                <div class="col-md-6">
                  <label class="form-label fw-bold">Médico de Preferência (Opcional)</label>
                  <input type="text" name="medico_preferencia_nome" class="form-control" value="<?= htmlspecialchars($med['medico_preferencia_nome'] ?? '') ?>">
                </div>
                <div class="col-md-6">
                  <label class="form-label fw-bold">Telefone do Médico</label>
                  <input type="text" name="medico_preferencia_telefone" class="form-control" value="<?= htmlspecialchars($med['medico_preferencia_telefone'] ?? '') ?>">
                </div>
                <div class="col-12">
                  <label class="form-label fw-bold">Restrições para Atividades Físicas</label>
                  <textarea name="restricoes_atividades" class="form-control" rows="2"><?= htmlspecialchars($med['restricoes_atividades'] ?? '') ?></textarea>
                </div>
                <div class="col-12">
                  <label class="form-label fw-bold">Outras Informações Médicas Relevantes</label>
                  <textarea name="outras_informacoes_medicas" class="form-control" rows="2"><?= htmlspecialchars($med['outras_informacoes_medicas'] ?? '') ?></textarea>
                </div>
              </div>

              <hr class="my-4">

              <div class="d-flex justify-content-between align-items-center">
                <a href="painel.php" class="btn btn-light"><i class="bi bi-x-lg"></i> Cancelar</a>
                <button type="submit" class="btn btn-danger px-5 fw-bold shadow-sm">
                  <i class="bi bi-heart-pulse me-2"></i> Salvar Ficha Médica
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
