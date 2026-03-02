<?php
declare(strict_types=1);
require __DIR__ . '/helpers.php';

$cpf = '99999999999';
$nome = 'Test User';
$email = 'testuser123@example.com';
$nasc = '1990-01-01';
$perfil = 'jovem';
$ramo = 'lobinho';
$hash = password_hash('password123', PASSWORD_BCRYPT);

try {
  $pdo = pdo();
  $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

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
  echo "Success!";
} catch (Throwable $e) {
  echo "Error: " . $e->getMessage() . "\n";
}
