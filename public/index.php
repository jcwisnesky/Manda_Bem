<?php


require_once __DIR__ . '/../vendor/autoload.php';

use App\Models\Funcionario;
use App\Models\Endereco;
use App\Services\RealCurlClient;

echo "=== Demonstração da Classe Funcionário - Manda Bem ===" . PHP_EOL . PHP_EOL;

$endereco = new Endereco(
    rua: 'Rua Principal',
    numero: '456',
    bairro: 'Centro',
    cidade: 'Rio de Janeiro',
    estado: 'RJ',
    complemento: 'Sala 10'
);

echo "--- Demonstração 1: Funcionário com CPF Válido ---" . PHP_EOL;
try {
    $funcionarioValido = new Funcionario(id: 10, curlClient: new RealCurlClient());
    $funcionarioValido->setNome('João da Silva');
    $funcionarioValido->setCpf('113.062.997-05'); // CPF válido
    $funcionarioValido->setDataNascimento('1985-10-20');
    $funcionarioValido->setEndereco($endereco);

    echo "Nome: " . $funcionarioValido->getNome() . PHP_EOL;
    echo "CPF: " . $funcionarioValido->getCpf() . PHP_EOL;
    echo "Idade: " . $funcionarioValido->getIdade() . " anos" . PHP_EOL;
    echo "Endereço: " . $endereco->rua . ", " . $endereco->numero . PHP_EOL;

    $localApiUrl = 'http://localhost:8000/test_api_local.php';
    echo "Status cadastral (API local): " . $funcionarioValido->consultarCpf($localApiUrl) . PHP_EOL;

} catch (\InvalidArgumentException $e) {
    echo "Erro ao criar funcionário com CPF válido: " . $e->getMessage() . PHP_EOL;
} catch (\Exception $e) {
    echo "Ocorreu um erro inesperado: " . $e->getMessage() . PHP_EOL;
}

echo PHP_EOL . "--- Demonstração 2: Tentativa de Criar Funcionário com CPF Inválido ---" . PHP_EOL;
try {
    $funcionarioInvalido = new Funcionario(id: 11, curlClient: new RealCurlClient());
    $funcionarioInvalido->setNome('Maria Teste');
    // Esta linha irá lançar uma InvalidArgumentException porque o CPF é inválido
    $funcionarioInvalido->setCpf('123.456.789-99'); // CPF inválido
    $funcionarioInvalido->setDataNascimento('1992-03-15');
    $funcionarioInvalido->setEndereco($endereco);

    echo "Funcionário com CPF inválido criado com sucesso (NÃO DEVERIA ACONTECER)." . PHP_EOL; // Esta linha não deve ser executada

} catch (\InvalidArgumentException $e) {
    // Captura a exceção e exibe uma mensagem
    echo "Erro esperado ao definir CPF inválido: " . $e->getMessage() . PHP_EOL;
} catch (\Exception $e) {
    echo "Ocorreu um erro inesperado: " . $e->getMessage() . PHP_EOL;
}


echo PHP_EOL . "=== Demonstração Finalizada ===" . PHP_EOL;