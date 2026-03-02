<?php
declare(strict_types=1);
require __DIR__ . '/helpers.php';

try {
    $pdo = pdo();
    echo "Conectado ao banco.\n";

    $sql = "CREATE TABLE IF NOT EXISTS fichas_medicas (
        id INT AUTO_INCREMENT PRIMARY KEY,
        usuario_id INT NOT NULL,
        
        -- DADOS FÍSICOS
        altura DECIMAL(5,2) DEFAULT NULL,
        peso DECIMAL(5,2) DEFAULT NULL,
        tipo_sanguineo VARCHAR(5) DEFAULT NULL,
        fator_rh VARCHAR(5) DEFAULT NULL,
        
        -- AUXÍLIOS
        usa_oculos TINYINT(1) DEFAULT 0,
        usa_lentes TINYINT(1) DEFAULT 0,
        usa_aparelho_dentario TINYINT(1) DEFAULT 0,
        usa_sondas TINYINT(1) DEFAULT 0,
        usa_marcapasso TINYINT(1) DEFAULT 0,
        usa_aparelho_audicao TINYINT(1) DEFAULT 0,
        
        -- SAÚDE FÍSICA
        asma_bronquite TINYINT(1) DEFAULT 0,
        rinite_sinusite TINYINT(1) DEFAULT 0,
        hipertensao TINYINT(1) DEFAULT 0,
        diabetes TINYINT(1) DEFAULT 0,
        convulsoes_epilepsia TINYINT(1) DEFAULT 0,
        problemas_dermatologicos TINYINT(1) DEFAULT 0,
        problemas_cardiacos TINYINT(1) DEFAULT 0,
        problemas_renais TINYINT(1) DEFAULT 0,
        outros_problemas TEXT DEFAULT NULL,
        
        -- MEDICAMENTOS
        usa_medicamentos TINYINT(1) DEFAULT 0,
        autonomia_medicacao TINYINT(1) DEFAULT 0,
        detalhes_medicamentos TEXT DEFAULT NULL,
        
        -- EMERGÊNCIAS
        contato_emergencia_nome VARCHAR(150) DEFAULT NULL,
        contato_emergencia_telefone VARCHAR(50) DEFAULT NULL,
        plano_saude_nome VARCHAR(150) DEFAULT NULL,
        plano_saude_carteirinha VARCHAR(50) DEFAULT NULL,
        medico_preferencia_nome VARCHAR(150) DEFAULT NULL,
        medico_preferencia_telefone VARCHAR(50) DEFAULT NULL,
        restricoes_atividades TEXT DEFAULT NULL,
        outras_informacoes_medicas TEXT DEFAULT NULL,
        
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        
        FOREIGN KEY (usuario_id) REFERENCES usuarios(id) ON DELETE CASCADE,
        UNIQUE KEY unique_usuario (usuario_id)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;";

    $pdo->exec($sql);
    echo "Tabela 'fichas_medicas' criada ou já existente.\n";

} catch (Exception $e) {
    die("ERRO: " . $e->getMessage());
}
