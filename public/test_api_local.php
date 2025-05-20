<?php
// Este script simula a API "situacao_funcionario" em um servidor local.

// Garante que é uma requisição POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405); // Método Não Permitido
    header('Allow: POST');
    echo json_encode(['error' => 'Apenas requisições POST são permitidas.']);
    exit();
}

// Garante que o Content-Type é application/x-www-form-urlencoded
if (strpos($_SERVER['CONTENT_TYPE'] ?? '', 'application/x-www-form-urlencoded') === false) {
    http_response_code(400); // Bad Request
    echo json_encode(['error' => 'Content-Type deve ser application/x-www-form-urlencoded.']);
    exit();
}

// Captura os dados do POST
$cpf = $_POST['cpf'] ?? null;
$dataNascimento = $_POST['dataNascimento'] ?? null;

// Lógica de simulação da API
// Você pode variar as respostas com base no CPF ou data de nascimento para testes mais complexos.
if ($cpf && $dataNascimento) {
    // Exemplo: CPF terminando em '00' é ativo, outros inativos.
    // Ou CPF '123.456.789-00' é ativo, o resto é inativo.
    if ($cpf === '123.456.789-00' || substr($cpf, -2) === '00') {
        $status = 'ativo';
    } elseif ($cpf === '555.444.333-22') { // CPF específico para o teste local no index.php
        $status = 'ativo_local';
    }
    else {
        $status = 'inativo_local';
    }

    http_response_code(200); // OK
    header('Content-Type: application/json');
    echo json_encode(['status' => $status, 'received_cpf' => $cpf, 'received_date' => $dataNascimento]);
} else {
    http_response_code(400); // Bad Request
    header('Content-Type: application/json');
    echo json_encode(['error' => 'CPF e Data de Nascimento são obrigatórios.', 'received_data' => $_POST]);
}

exit();