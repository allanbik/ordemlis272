<?php
// C:\xampp\htdocs\ordemdelis\setup_database.php
declare(strict_types=1);

echo "<h1>Setup do Banco de Dados</h1>";
echo "<pre>";

try {
    // 1. Ler configuração
    if (!file_exists(__DIR__ . '/config.php')) {
        throw new Exception("Arquivo config.php não encontrado.");
    }
    $cfg = require __DIR__ . '/config.php';
    $dbCfg = $cfg['db'];

    // 2. Conectar ao MySQL (sem selecionar banco ainda, para poder criar se não existir)
    echo "Conectando ao MySQL em {$dbCfg['host']}... ";
    $dsn = "mysql:host={$dbCfg['host']};port={$dbCfg['port']};charset={$dbCfg['charset']}";
    $pdo = new PDO($dsn, $dbCfg['user'], $dbCfg['pass'], [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
    ]);
    echo "OK.\n";

    // 3. Executar scripts SQL
    $sqlFiles = glob(__DIR__ . '/banco/*.sql');
    if (empty($sqlFiles)) {
        echo "Nenhum arquivo SQL encontrado em banco/.\n";
    } else {
        foreach ($sqlFiles as $file) {
            echo "Executando " . basename($file) . "... ";
            $sql = file_get_contents($file);
            
            // O PDO não executa múltiplos comandos de uma vez muito bem se houver DELIMITER, mas scripts simples ok.
            // Para garantir, vamos tentar executar o script inteiro.
            // Se houver problemas com DELIMITER ou múltiplos comandos complexos, teríamos que separar.
            // Como os scripts parecem ser padrão (CREATE DATABASE, CREATE TABLE), deve funcionar.

            $pdo->exec($sql);
            echo "OK.\n";
        }
    }

    echo "\nSetup concluído com sucesso! \n";
    echo "Agora você pode acessar <a href='registrar.html'>Criar Conta</a> ou <a href='login.html'>Login</a>.";

} catch (Throwable $e) {
    echo "ERRO: " . $e->getMessage() . "\n";
    echo "Verifique se o MySQL está rodando e se as credenciais em config.php estão corretas.\n";
}

echo "</pre>";
