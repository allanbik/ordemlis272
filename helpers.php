<?php
// C:\Projeto_Final_2026\helpers.php
declare(strict_types=1);

function cfg(): array {
  static $cfg = null;
  if ($cfg === null) $cfg = require __DIR__ . '/config.php';
  return $cfg;
}

function pdo(): PDO {
  static $pdo = null;
  if ($pdo !== null) return $pdo;

  $c = cfg()['db'];
  $dsn = "mysql:host={$c['host']};port={$c['port']};dbname={$c['name']};charset={$c['charset']}";
  $pdo = new PDO($dsn, $c['user'], $c['pass'], [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
  ]);
  return $pdo;
}

function only_digits(string $v): string {
  return preg_replace('/\D+/', '', $v) ?? '';
}

function cpf_is_valid(string $cpf): bool {
  $cpf = only_digits($cpf);
  if (strlen($cpf) !== 11) return false;
  if (preg_match('/^(\d)\1+$/', $cpf)) return false;

  // 1º dígito
  $sum = 0;
  for ($i = 0; $i < 9; $i++) $sum += intval($cpf[$i]) * (10 - $i);
  $d1 = ($sum * 10) % 11;
  if ($d1 === 10) $d1 = 0;
  if ($d1 !== intval($cpf[9])) return false;

  // 2º dígito
  $sum = 0;
  for ($i = 0; $i < 10; $i++) $sum += intval($cpf[$i]) * (11 - $i);
  $d2 = ($sum * 10) % 11;
  if ($d2 === 10) $d2 = 0;
  return $d2 === intval($cpf[10]);
}

function iniciar_sessao(): void {
  if (session_status() === PHP_SESSION_NONE) {
    session_start([
      'cookie_httponly' => true,
      'use_strict_mode' => true,
    ]);
  }
}

function require_login(): void {
  iniciar_sessao();
  if (empty($_SESSION['user_id'])) {
    header('Location: login.html');
    exit;
  }
}

function token_generate(int $bytes = 32): string {
  return bin2hex(random_bytes($bytes)); // 64 chars (32 bytes)
}

function token_hash(string $token): string {
  // Guardar hash (nunca token puro)
  return hash('sha256', $token);
}

function now_dt(): DateTimeImmutable {
  return new DateTimeImmutable('now');
}
