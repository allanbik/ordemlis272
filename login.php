<?php
declare(strict_types=1);
require __DIR__ . '/helpers.php';

error_reporting(E_ALL);
ini_set('display_errors', '1');

function go(string $url): never {
  header('Location: ' . $url);
  exit;
}

iniciar_sessao();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
  go('login.html');
}

$cpf  = only_digits((string)($_POST['cpf'] ?? ''));
$pass = (string)($_POST['senha'] ?? '');

if (!cpf_is_valid($cpf) || trim($pass) === '') {
  go('login.html?err=' . rawurlencode('Informe CPF e senha válidos.'));
}

try {
  $pdo = pdo();
  $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

  // tabela/colunas que você tem
  $stmt = $pdo->prepare("
    SELECT id, nome, senha_hash, ativo, perfil, ramo
    FROM usuarios
    WHERE cpf = :cpf
    LIMIT 1
  ");
  $stmt->execute([':cpf' => $cpf]);
  $user = $stmt->fetch(PDO::FETCH_ASSOC);

  if (!$user) {
    go('login.html?err=' . rawurlencode('Usuário não encontrado.'));
  }

  if ((int)$user['ativo'] !== 1) {
    go('login.html?err=' . rawurlencode('Usuário inativo.'));
  }

  if (!password_verify($pass, (string)$user['senha_hash'])) {
    go('login.html?err=' . rawurlencode('CPF ou senha incorretos.'));
  }

  // sucesso: cria sessão
  session_regenerate_id(true);
  $_SESSION['user_id'] = (int)$user['id'];

  go('painel.php');

} catch (Throwable $e) {
  @file_put_contents(__DIR__ . '/_php_errors.log',
    '[' . date('Y-m-d H:i:s') . '] login.php: ' . $e->getMessage() . "\n",
    FILE_APPEND
  );
  go('login.html?err=' . rawurlencode('Erro no servidor. Veja _php_errors.log.'));
}
