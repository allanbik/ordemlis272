<?php
declare(strict_types=1);
require __DIR__ . '/helpers.php';

error_reporting(E_ALL);
ini_set('display_errors', '1');

function go(string $url): never {
  header('Location: ' . $url);
  exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
  go('registrar.html');
}

// DEBUG (já provou que chega)
$debugTxt = "==== " . date('Y-m-d H:i:s') . " ====\n" . print_r($_POST, true) . "\n";
@file_put_contents(__DIR__ . '/_debug_post.txt', $debugTxt, FILE_APPEND);

$nome   = trim((string)($_POST['nome'] ?? ''));
$perfil = trim((string)($_POST['perfil'] ?? ''));
$ramo   = trim((string)($_POST['ramo'] ?? ''));
$cpf    = only_digits((string)($_POST['cpf'] ?? ''));
$email  = trim((string)($_POST['email'] ?? ''));
$nasc   = trim((string)($_POST['data_nascimento'] ?? ''));

$senha1 = (string)($_POST['senha1'] ?? '');
$senha2 = (string)($_POST['senha2'] ?? '');
$lgpd   = (string)($_POST['lgpd'] ?? '');

// validações mínimas
if (mb_strlen($nome) < 3)        go('registrar.html?err=' . rawurlencode('Nome inválido.'));
if (!cpf_is_valid($cpf))         go('registrar.html?err=' . rawurlencode('CPF inválido.'));
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) go('registrar.html?err=' . rawurlencode('E-mail inválido.'));
if ($lgpd !== '1')               go('registrar.html?err=' . rawurlencode('É obrigatório aceitar LGPD.'));
if ($nasc === '')                go('registrar.html?err=' . rawurlencode('Data de nascimento é obrigatória.'));
if ($senha1 === '' || $senha1 !== $senha2) go('registrar.html?err=' . rawurlencode('As senhas não conferem.'));
if (strlen($senha1) < 6)         go('registrar.html?err=' . rawurlencode('Senha precisa ter pelo menos 6 caracteres.'));

$perfisOk = ['jovem','chefe','responsavel'];
$ramosOk  = ['lobinho','escoteiro','senior','pioneiro','chefia'];
if (!in_array($perfil, $perfisOk, true)) go('registrar.html?err=' . rawurlencode('Perfil inválido.'));
if (!in_array($ramo, $ramosOk, true))    go('registrar.html?err=' . rawurlencode('Ramo inválido.'));

$hash = password_hash($senha1, PASSWORD_BCRYPT);

try {
  $pdo = pdo();
  $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

  // duplicidade
  $chk = $pdo->prepare("SELECT id FROM usuarios WHERE cpf = :cpf OR email = :email LIMIT 1");
  $chk->execute([':cpf' => $cpf, ':email' => $email]);
  if ($chk->fetch()) {
    go('registrar.html?err=' . rawurlencode('Já existe usuário com este CPF ou e-mail.'));
  }

  // INSERT só nas colunas EXISTENTES
  $ins = $pdo->prepare("
    INSERT INTO usuarios
      (cpf, nome, email, data_nascimento, perfil, ramo, senha_hash, senha_alg, senha_updated_at, ativo, email_verificado)
    VALUES
      (:cpf, :nome, :email, :data_nascimento, :perfil, :ramo, :senha_hash, 'bcrypt', NOW(), 1, 1)
  ");
  $ins->execute([
    ':cpf' => $cpf,
    ':nome' => $nome,
    ':email' => $email,
    ':data_nascimento' => $nasc,
    ':perfil' => $perfil,
    ':ramo' => $ramo,
    ':senha_hash' => $hash,
  ]);

  go('login.html?ok=' . rawurlencode('Cadastro realizado com sucesso! Agora você pode entrar.'));

} catch (Throwable $e) {
  @file_put_contents(__DIR__ . '/_php_errors.log',
    '[' . date('Y-m-d H:i:s') . '] ' . $e->getMessage() . "\n",
    FILE_APPEND
  );
  go('registrar.html?err=' . rawurlencode('Erro no servidor. Veja _php_errors.log.'));
}
