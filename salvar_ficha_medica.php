<?php
declare(strict_types=1);
require __DIR__ . '/helpers.php';

iniciar_sessao();

if ($_SERVER['REQUEST_METHOD'] !== 'POST' || empty($_SESSION['user_id'])) {
  header('Location: painel.php');
  exit;
}

$userId = (int)$_SESSION['user_id'];

// Sanitização e Preparação dos Dados
$data = [
    'usuario_id'                 => $userId,
    'altura'                     => !empty($_POST['altura']) ? (float)$_POST['altura'] : null,
    'peso'                       => !empty($_POST['peso']) ? (float)$_POST['peso'] : null,
    'tipo_sanguineo'             => $_POST['tipo_sanguineo'] ?? null,
    'fator_rh'                   => $_POST['fator_rh'] ?? null,
    
    'usa_oculos'                 => isset($_POST['usa_oculos']) ? 1 : 0,
    'usa_lentes'                 => isset($_POST['usa_lentes']) ? 1 : 0,
    'usa_aparelho_dentario'      => isset($_POST['usa_aparelho_dentario']) ? 1 : 0,
    'usa_sondas'                 => isset($_POST['usa_sondas']) ? 1 : 0,
    'usa_marcapasso'             => isset($_POST['usa_marcapasso']) ? 1 : 0,
    'usa_aparelho_audicao'       => isset($_POST['usa_aparelho_audicao']) ? 1 : 0,
    
    'asma_bronquite'             => isset($_POST['asma_bronquite']) ? 1 : 0,
    'rinite_sinusite'            => isset($_POST['rinite_sinusite']) ? 1 : 0,
    'hipertensao'                => isset($_POST['hipertensao']) ? 1 : 0,
    'diabetes'                   => isset($_POST['diabetes']) ? 1 : 0,
    'convulsoes_epilepsia'       => isset($_POST['convulsoes_epilepsia']) ? 1 : 0,
    'problemas_dermatologicos'   => isset($_POST['problemas_dermatologicos']) ? 1 : 0,
    'problemas_cardiacos'        => isset($_POST['problemas_cardiacos']) ? 1 : 0,
    'problemas_renais'           => isset($_POST['problemas_renais']) ? 1 : 0,
    'outros_problemas'           => trim($_POST['outros_problemas'] ?? ''),
    
    'usa_medicamentos'           => isset($_POST['usa_medicamentos']) ? 1 : 0,
    'autonomia_medicacao'        => isset($_POST['autonomia_medicacao']) ? 1 : 0,
    'detalhes_medicamentos'      => trim($_POST['detalhes_medicamentos'] ?? ''),
    
    'contato_emergencia_nome'    => trim($_POST['contato_emergencia_nome'] ?? ''),
    'contato_emergencia_telefone'=> trim($_POST['contato_emergencia_telefone'] ?? ''),
    'plano_saude_nome'           => trim($_POST['plano_saude_nome'] ?? ''),
    'plano_saude_carteirinha'    => trim($_POST['plano_saude_carteirinha'] ?? ''),
    'medico_preferencia_nome'    => trim($_POST['medico_preferencia_nome'] ?? ''),
    'medico_preferencia_telefone'=> trim($_POST['medico_preferencia_telefone'] ?? ''),
    'restricoes_atividades'      => trim($_POST['restricoes_atividades'] ?? ''),
    'outras_informacoes_medicas' => trim($_POST['outras_informacoes_medicas'] ?? '')
];

try {
    $pdo = pdo();
    
    // UPSERT (INSERT ... ON DUPLICATE KEY UPDATE)
    $sql = "INSERT INTO fichas_medicas (
                usuario_id, altura, peso, tipo_sanguineo, fator_rh, 
                usa_oculos, usa_lentes, usa_aparelho_dentario, usa_sondas, usa_marcapasso, usa_aparelho_audicao,
                asma_bronquite, rinite_sinusite, hipertensao, diabetes, convulsoes_epilepsia, problemas_dermatologicos, problemas_cardiacos, problemas_renais, outros_problemas,
                usa_medicamentos, autonomia_medicacao, detalhes_medicamentos,
                contato_emergencia_nome, contato_emergencia_telefone, plano_saude_nome, plano_saude_carteirinha, medico_preferencia_nome, medico_preferencia_telefone, restricoes_atividades, outras_informacoes_medicas
            ) VALUES (
                :usuario_id, :altura, :peso, :tipo_sanguineo, :fator_rh, 
                :usa_oculos, :usa_lentes, :usa_aparelho_dentario, :usa_sondas, :usa_marcapasso, :usa_aparelho_audicao,
                :asma_bronquite, :rinite_sinusite, :hipertensao, :diabetes, :convulsoes_epilepsia, :problemas_dermatologicos, :problemas_cardiacos, :problemas_renais, :outros_problemas,
                :usa_medicamentos, :autonomia_medicacao, :detalhes_medicamentos,
                :contato_emergencia_nome, :contato_emergencia_telefone, :plano_saude_nome, :plano_saude_carteirinha, :medico_preferencia_nome, :medico_preferencia_telefone, :restricoes_atividades, :outras_informacoes_medicas
            ) ON DUPLICATE KEY UPDATE 
                altura = VALUES(altura),
                peso = VALUES(peso),
                tipo_sanguineo = VALUES(tipo_sanguineo),
                fator_rh = VALUES(fator_rh),
                usa_oculos = VALUES(usa_oculos),
                usa_lentes = VALUES(usa_lentes),
                usa_aparelho_dentario = VALUES(usa_aparelho_dentario),
                usa_sondas = VALUES(usa_sondas),
                usa_marcapasso = VALUES(usa_marcapasso),
                usa_aparelho_audicao = VALUES(usa_aparelho_audicao),
                asma_bronquite = VALUES(asma_bronquite),
                rinite_sinusite = VALUES(rinite_sinusite),
                hipertensao = VALUES(hipertensao),
                diabetes = VALUES(diabetes),
                convulsoes_epilepsia = VALUES(convulsoes_epilepsia),
                problemas_dermatologicos = VALUES(problemas_dermatologicos),
                problemas_cardiacos = VALUES(problemas_cardiacos),
                problemas_renais = VALUES(problemas_renais),
                outros_problemas = VALUES(outros_problemas),
                usa_medicamentos = VALUES(usa_medicamentos),
                autonomia_medicacao = VALUES(autonomia_medicacao),
                detalhes_medicamentos = VALUES(detalhes_medicamentos),
                contato_emergencia_nome = VALUES(contato_emergencia_nome),
                contato_emergencia_telefone = VALUES(contato_emergencia_telefone),
                plano_saude_nome = VALUES(plano_saude_nome),
                plano_saude_carteirinha = VALUES(plano_saude_carteirinha),
                medico_preferencia_nome = VALUES(medico_preferencia_nome),
                medico_preferencia_telefone = VALUES(medico_preferencia_telefone),
                restricoes_atividades = VALUES(restricoes_atividades),
                outras_informacoes_medicas = VALUES(outras_informacoes_medicas);";
    
    $stmt = $pdo->prepare($sql);
    $stmt->execute($data);

    header('Location: ficha_medica.php?ok=' . rawurlencode('Ficha médica salva com sucesso!'));
    exit;

} catch (Throwable $e) {
    @file_put_contents(__DIR__ . '/_php_errors.log',
      '[' . date('Y-m-d H:i:s') . '] salvar_ficha_medica.php: ' . $e->getMessage() . "\n",
      FILE_APPEND
    );
    header('Location: ficha_medica.php?err=' . rawurlencode('Erro ao salvar os dados. ' . $e->getMessage()));
    exit;
}
