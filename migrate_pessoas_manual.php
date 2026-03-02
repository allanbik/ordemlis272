<?php
declare(strict_types=1);

// Aumenta reporte de erros
ini_set('display_errors', '1');
ini_set('display_startup_errors', '1');
error_reporting(E_ALL);

echo "<h1>Migração: Usuários -> Pessoas</h1>";

$configFile = __DIR__ . '/config.php';
if (!file_exists($configFile)) {
    die("ERRO: config.php nao encontrado.");
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

    // 1. Criar tabela 'pessoas' se não existir
    echo "<p>Criando tabela 'pessoas'...</p>";
    $sqlCreate = "
    CREATE TABLE IF NOT EXISTS pessoas (
        id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
        usuario_id BIGINT UNSIGNED NOT NULL,
        
        -- Dados básicos migrados de usuarios
        nome VARCHAR(150) NOT NULL,
        cpf CHAR(11) NOT NULL, 
        email VARCHAR(190) NULL,
        data_nascimento DATE NOT NULL DEFAULT '2000-01-01',
        
        -- Novos dados complementares
        rg VARCHAR(20) NULL,
        orgao_emissor VARCHAR(20) NULL,
        sexo ENUM('M','F') NULL,
        nome_pai VARCHAR(150) NULL,
        nome_mae VARCHAR(150) NULL,
        
        -- Contato
        telefone_fixo VARCHAR(20) NULL,
        celular VARCHAR(20) NULL,
        
        -- Endereço
        cep VARCHAR(9) NULL,
        endereco VARCHAR(255) NULL,
        numero VARCHAR(20) NULL,
        complemento VARCHAR(100) NULL,
        bairro VARCHAR(100) NULL,
        cidade VARCHAR(100) NULL,
        estado CHAR(2) NULL,
        
        created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
        updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,

        PRIMARY KEY (id),
        UNIQUE KEY uq_pessoas_usuario_id (usuario_id),
        KEY ix_pessoas_cpf (cpf),
        CONSTRAINT fk_pessoas_usuarios FOREIGN KEY (usuario_id) REFERENCES usuarios (id) ON DELETE CASCADE
    ) ENGINE=InnoDB;
    ";
    
    $pdo->exec($sqlCreate);
    echo "<p style='color:green'>Tabela 'pessoas' criada/verificada com sucesso.</p>";

    // 2. Migrar dados
    echo "<p>Migrando dados de 'usuarios' para 'pessoas'...</p>";
    
    // Inserir apenas se não existir (IGNORE) baseado na chave única usuario_id
    $sqlMigrate = "
    INSERT IGNORE INTO pessoas (usuario_id, nome, cpf, email, data_nascimento, created_at)
    SELECT id, nome, cpf, email, data_nascimento, created_at
    FROM usuarios
    ";
    
    $stmt = $pdo->prepare($sqlMigrate);
    $stmt->execute();
    $count = $stmt->rowCount();
    
    echo "<p style='color:green'><strong>SUCESSO:</strong> $count registros migrados/atualizados.</p>";
    
    echo "<hr>";
    echo "<p>Tabela Pronta! <a href='painel.php'>Voltar ao Painel</a></p>";

} catch (Throwable $e) {
    echo "<p style='color:red'><strong>ERRO CRÍTICO:</strong> " . $e->getMessage() . "</p>";
}
?>
