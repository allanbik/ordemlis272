<?php
// C:\Projeto_Final_2026\redefinir_senha.php
declare(strict_types=1);
require __DIR__ . '/helpers.php';

$token = trim((string)($_POST['token'] ?? ''));
$senha1 = (string)($_POST['senha1'] ?? '');
$senha2 = (string)($_POST['senha2'] ?? '');

if ($token === '' || $senha1 === '' || $senha2 === '' || $senha1 !== $senha2) {
  header('Location: redefinir_senha.html');
  exit;
}

// regra mínima simples (igual do front: 8+ e 4/5 checks)
$checks = 0;
$checks += (strlen($senha1) >= 8) ? 1 : 0;
$checks += preg_match('/[A-Z]/', $senha1) ? 1 : 0;
$checks += preg_match('/[a-z]/', $senha1) ? 1 : 0;
$checks += preg_match('/[0-9]/', $senha1) ? 1 : 0;
$checks += preg_match('/[^A-Za-z0-9]/', $senha1) ? 1 : 0;
if ($checks < 4) {
  header('Location: redefinir_senha.html');
  exit;
}

$hashToken = token_hash($token);

// busca token válido e não usado
$stmt = pdo()->prepare("
  SELECT r.id, r.usuario_id
  FROM recuperacao_senha r
  WHERE r.token_hash = :th
    AND r.usado_em IS NULL
    AND r.expira_em >= NOW()
  ORDER BY r.id DESC
  LIMIT 1
");
$stmt->execute([':th' => $hashToken]);
$row = $stmt->fetch();

if (!$row) {
  header('Location: redefinir_senha.html');
  exit;
}

$newHash = password_hash($senha1, PASSWORD_BCRYPT);

pdo()->beginTransaction();
try {
  pdo()->prepare("UPDATE usuarios
                  SET senha_hash = :sh, senha_alg='bcrypt', senha_updated_at = NOW()
                  WHERE id = :uid")
      ->execute([':sh' => $newHash, ':uid' => $row['usuario_id']]);

  pdo()->prepare("UPDATE recuperacao_senha
                  SET usado_em = NOW()
                  WHERE id = :id")
      ->execute([':id' => $row['id']]);

  pdo()->commit();
} catch (Throwable $e) {
  pdo()->rollBack();
  header('Location: redefinir_senha.html');
  exit;
}

header('Location: login.html');
exit;
