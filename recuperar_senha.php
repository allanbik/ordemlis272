<?php
// C:\Projeto_Final_2026\recuperar_senha.php
declare(strict_types=1);
require __DIR__ . '/helpers.php';

$cpf   = only_digits($_POST['cpf'] ?? '');
$email = trim((string)($_POST['email'] ?? ''));

// Por segurança: sempre responde igual (não revela se existe)
if (!cpf_is_valid($cpf) || $email === '') {
  header('Location: esqueci_senha.html');
  exit;
}

$stmt = pdo()->prepare("SELECT id, email, ativo FROM usuarios WHERE cpf = :cpf LIMIT 1");
$stmt->execute([':cpf' => $cpf]);
$u = $stmt->fetch();

if ($u && (int)$u['ativo'] === 1 && strcasecmp($u['email'], $email) === 0) {
  $token = token_generate(32);
  $hash  = token_hash($token);

  $ttl = (int)(cfg()['security']['token_ttl_minutes'] ?? 30);
  $exp = (new DateTimeImmutable("+{$ttl} minutes"))->format('Y-m-d H:i:s');

  pdo()->prepare("INSERT INTO recuperacao_senha (usuario_id, token_hash, expira_em, ip_solicitacao, user_agent)
                  VALUES (:uid, :th, :exp, :ip, :ua)")
      ->execute([
        ':uid' => $u['id'],
        ':th'  => $hash,
        ':exp' => $exp,
        ':ip'  => $_SERVER['REMOTE_ADDR'] ?? null,
        ':ua'  => substr($_SERVER['HTTP_USER_AGENT'] ?? '', 0, 255),
      ]);

  // PARA O PROJETO (modo local): em vez de email, mostramos o token na tela.
  // Em produção: enviar por email e NÃO exibir token.
  header('Content-Type: text/html; charset=utf-8');
  echo "<h3>Recuperação solicitada ✅</h3>";
  echo "<p><b>Código/Token (modo demo):</b> <code>" . htmlspecialchars($token) . "</code></p>";
  echo "<p>Abra <a href='redefinir_senha.html'>redefinir_senha.html</a> e cole o token.</p>";
  echo "<p><a href='login.html'>Voltar ao login</a></p>";
  exit;
}

// Resposta genérica
header('Location: esqueci_senha.html');
exit;
