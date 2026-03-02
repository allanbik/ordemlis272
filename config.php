<?php
// C:\Projeto_Final_2026\config.php
declare(strict_types=1);

return [
  'db' => [
    'host' => '127.0.0.1',
    'port' => 3306,
    'name' => 'ordemdelis',
    'user' => 'root',
    'pass' => '', // <-- ajuste aqui
    'charset' => 'utf8mb4',
  ],
  'security' => [
    'token_ttl_minutes' => 30,
  ]
];
