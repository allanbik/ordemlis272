<?php
declare(strict_types=1);
require __DIR__ . '/helpers.php';

iniciar_sessao();

if ($_SERVER['REQUEST_METHOD'] !== 'POST' || empty($_SESSION['user_id'])) {
  header('Location: painel.php');
  exit;
}

$userId = (int)$_SESSION['user_id'];

// Sanitização básica
$data = [
    'data_nascimento'      => $_POST['data_nascimento'] ?? null,
    'sexo'                 => $_POST['sexo'] ?? null,
    'registro_ueb'         => trim($_POST['registro_ueb'] ?? ''),
    'naturalidade'         => trim($_POST['naturalidade'] ?? ''),
    'religiao'             => trim($_POST['religiao'] ?? ''),
    'rua'                  => trim($_POST['rua'] ?? ''),
    'numero'               => trim($_POST['numero'] ?? ''),
    'complemento'          => trim($_POST['complemento'] ?? ''),
    'bairro'               => trim($_POST['bairro'] ?? ''),
    'cidade'               => trim($_POST['cidade'] ?? ''),
    'estado'               => strtoupper(substr(trim($_POST['estado'] ?? ''), 0, 2)),
    'cep'                  => trim($_POST['cep'] ?? ''),
    'telefone_residencial' => trim($_POST['telefone_residencial'] ?? ''),
    'telefone_celular'     => trim($_POST['telefone_celular'] ?? ''),
    'escolaridade'         => $_POST['escolaridade'] ?? '',
    'profissao'            => trim($_POST['profissao'] ?? ''),
    'local_trabalho'       => trim($_POST['local_trabalho'] ?? ''),
    'id'                   => $userId
];

try {
    $pdo = pdo();
    $sql = "UPDATE usuarios SET 
                data_nascimento = :data_nascimento,
                sexo = :sexo,
                registro_ueb = :registro_ueb,
                naturalidade = :naturalidade,
                religiao = :religiao,
                rua = :rua,
                numero = :numero,
                complemento = :complemento,
                bairro = :bairro,
                cidade = :cidade,
                estado = :estado,
                cep = :cep,
                telefone_residencial = :telefone_residencial,
                telefone_celular = :telefone_celular,
                escolaridade = :escolaridade,
                profissao = :profissao,
                local_trabalho = :local_trabalho
            WHERE id = :id";
    
    $stmt = $pdo->prepare($sql);
    $stmt->execute($data);

    header('Location: ficha.php?ok=' . rawurlencode('Dados atualizados com sucesso!'));
    exit;

} catch (Throwable $e) {
    @file_put_contents(__DIR__ . '/_php_errors.log',
      '[' . date('Y-m-d H:i:s') . '] salvar_ficha.php: ' . $e->getMessage() . "\n",
      FILE_APPEND
    );
    header('Location: ficha.php?err=' . rawurlencode('Erro ao salvar os dados. Tente novamente mais tarde.'));
    exit;
}
