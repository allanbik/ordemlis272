<?php
declare(strict_types=1);
require __DIR__ . '/helpers.php';

try {
    $pdo = pdo();
    echo "Conectado ao banco.\n";

    // Lista de colunas para adicionar
    $columns = [
        "sexo CHAR(1) DEFAULT NULL",
        "registro_ueb VARCHAR(20) DEFAULT NULL",
        "naturalidade VARCHAR(100) DEFAULT NULL",
        "religiao VARCHAR(50) DEFAULT NULL",
        "rua VARCHAR(150) DEFAULT NULL",
        "numero VARCHAR(20) DEFAULT NULL",
        "complemento VARCHAR(100) DEFAULT NULL",
        "bairro VARCHAR(100) DEFAULT NULL",
        "cidade VARCHAR(100) DEFAULT NULL",
        "estado CHAR(2) DEFAULT NULL",
        "cep VARCHAR(10) DEFAULT NULL",
        "telefone_residencial VARCHAR(20) DEFAULT NULL",
        "telefone_celular VARCHAR(20) DEFAULT NULL",
        "escolaridade VARCHAR(100) DEFAULT NULL",
        "profissao VARCHAR(100) DEFAULT NULL",
        "local_trabalho VARCHAR(150) DEFAULT NULL"
    ];

    foreach ($columns as $colDef) {
        $colName = explode(" ", $colDef)[0];
        try {
            $pdo->exec("ALTER TABLE usuarios ADD COLUMN $colDef");
            echo "Coluna '$colName' adicionada.\n";
        } catch (PDOException $e) {
            if ($e->getCode() === '42S21') {
                echo "Coluna '$colName' já existe.\n";
            } else {
                echo "Erro ao adicionar '$colName': " . $e->getMessage() . "\n";
            }
        }
    }

    echo "\nBanco de dados atualizado com sucesso para a Ficha Cadastral.\n";

} catch (Exception $e) {
    die("ERRO FATAL: " . $e->getMessage());
}
