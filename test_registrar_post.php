<?php
declare(strict_types=1);

$url = 'http://localhost/ordemdelis/registrar.php';
$data = [
    'nome' => 'Teste Cadastro Novo',
    'perfil' => 'jovem',
    'ramo' => 'lobinho',
    'cpf' => '000.111.222-33',
    'email' => 'teste.novo@example.com',
    'data_nascimento' => '2010-05-15',
    'senha1' => 'Senha@123',
    'senha2' => 'Senha@123',
    'lgpd' => '1'
];

$options = [
    'http' => [
        'header'  => "Content-type: application/x-www-form-urlencoded\r\n",
        'method'  => 'POST',
        'content' => http_build_query($data),
        'ignore_errors' => true // to get response even if it's 400 or 500
    ]
];

$context  = stream_context_create($options);
$result = file_get_contents($url, false, $context);

echo "Response headers:\n";
print_r($http_response_header);
echo "\nResponse body:\n";
echo $result;
