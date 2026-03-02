<?php
declare(strict_types=1);
$config = require __DIR__ . '/config.php';

try {
    $dbCfg = $config['db']; // config.php retorna array
    $dsn = "mysql:host={$dbCfg['host']};dbname={$dbCfg['name']};charset={$dbCfg['charset']};port={$dbCfg['port']}";
    $pdo = new PDO($dsn, $dbCfg['user'], $dbCfg['pass'], [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
    ]);

    echo "Conectado ao banco '{$dbCfg['name']}'.\n";

    // Tenta adicionar a coluna
    try {
        echo "Tentando adicionar coluna 'data_nascimento'...\n";
        $pdo->exec("ALTER TABLE usuarios ADD COLUMN data_nascimento DATE NOT NULL DEFAULT '2000-01-01' AFTER email");
        echo "Coluna adicionada com sucesso!\n";
    } catch (PDOException $e) {
        // Código 42S21 = Column already exists
        if ($e->getCode() == '42S21' || strpos($e->getMessage(), 'Duplicate column') !== false) {
            echo "A coluna 'data_nascimento' ja existe.\n";
        } else {
            throw $e;
        }
    }
    
    // Remove o default dummy se quiser limpar
    $pdo->exec("ALTER TABLE usuarios ALTER COLUMN data_nascimento DROP DEFAULT");
    echo "Ajuste de default concluido.\n";
    
    echo "BANCO ATUALIZADO COM SUCESSO.\n";

} catch (Throwable $e) {
    echo "ERRO: " . $e->getMessage() . "\n";
}
?>
