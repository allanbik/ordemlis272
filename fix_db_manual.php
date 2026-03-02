<?php
declare(strict_types=1);

// Aumenta reporte de erros para visualização
ini_set('display_errors', '1');
ini_set('display_startup_errors', '1');
error_reporting(E_ALL);

echo "<h1>Correção de Banco de Dados</h1>";

// Caminho para config.php
$configFile = __DIR__ . '/config.php';

if (!file_exists($configFile)) {
    die("ERRO: config.php nao encontrado em $configFile");
}

try {
    $config = require $configFile;
    $dbCfg = $config['db'];

    $dsn = "mysql:host={$dbCfg['host']};port={$dbCfg['port']};charset={$dbCfg['charset']}";
    $pdo = new PDO($dsn, $dbCfg['user'], $dbCfg['pass'], [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
    ]);

    // Seleciona banco
    $pdo->exec("USE `{$dbCfg['name']}`");
    echo "<p>Conectado ao banco '{$dbCfg['name']}'.</p>";

    // Tenta adicionar a coluna
    try {
        echo "<p>Tentando adicionar coluna 'data_nascimento'...</p>";
        $pdo->exec("ALTER TABLE usuarios ADD COLUMN data_nascimento DATE NOT NULL DEFAULT '2000-01-01' AFTER email");
        echo "<p style='color:green'><strong>SUCESSO:</strong> Coluna 'data_nascimento' adicionada.</p>";
        
        // Remove default
        $pdo->exec("ALTER TABLE usuarios ALTER COLUMN data_nascimento DROP DEFAULT");
        
    } catch (PDOException $e) {
        // 42S21 ou 1060 = Duplicate column
        if ($e->getCode() == '42S21' || $e->errorInfo[1] == 1060) {
             echo "<p style='color:blue'>AVISO: A coluna 'data_nascimento' JÁ EXISTE. Tudo certo.</p>";
        } else {
             throw $e;
        }
    }
    
    echo "<hr>";
    echo "<p>Agora você pode tentar registrar novamente:</p>";
    echo "<a href='registrar.html'>Ir para Registrar</a>";

} catch (Throwable $e) {
    echo "<p style='color:red'><strong>ERRO:</strong> " . $e->getMessage() . "</p>";
}
?>
