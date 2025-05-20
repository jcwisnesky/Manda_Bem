<?php

namespace Tests;

use PHPUnit\Framework\TestCase;
use App\Models\Funcionario;
use App\Models\Endereco;
use App\Services\MockCurlClient;

class FuncionarioTest extends TestCase
{
    private Endereco $endereco;

    protected function setUp(): void
    {
        parent::setUp();
        $this->endereco = new Endereco(
            rua: 'Rua de Teste',
            numero: '99',
            bairro: 'Bairro Teste',
            cidade: 'Cidade Teste',
            estado: 'TS',
            complemento: 'Apto Teste'
        );
    }

    public function testFuncionarioSemCpfRetornaInativo(): void
    {
        $funcionario = new Funcionario(id: 1);
        $funcionario->setNome('Teste Sem CPF');
        $funcionario->setDataNascimento('1995-01-01');
        $funcionario->setEndereco($this->endereco);

        $this->assertEquals('inativo', $funcionario->consultarCpf());
    }

    public function testSetCpfComCpfInvalidoLancaExcecao(): void
    {
        $funcionario = new Funcionario(id: 2);
        $funcionario->setNome('Teste CPF Inválido');
        $funcionario->setDataNascimento('1990-01-01');
        $funcionario->setEndereco($this->endereco);

        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage("CPF inválido: O CPF '123.456.789-99' não segue o padrão de validação.");

        $funcionario->setCpf('123.456.789-99');
    }

    public function testFuncionarioComCpfValidoEMockAtivoRetornaAtivo(): void
    {
        $mockCurlClientAtivo = new MockCurlClient([['status' => 'ativo']]);
        $funcionario = new Funcionario(id: 3, curlClient: $mockCurlClientAtivo);
        $funcionario->setNome('Teste Mock Ativo');
        $funcionario->setCpf('113.062.997-05'); // CPF válido
        $funcionario->setDataNascimento('1980-01-01');
        $funcionario->setEndereco($this->endereco);

        $this->assertEquals('ativo', $funcionario->consultarCpf());
    }

    public function testFuncionarioComCpfValidoEMockInativoRetornaInativo(): void
    {
        $mockCurlClientInativo = new MockCurlClient([['status' => 'inativo']]);
        $funcionario = new Funcionario(id: 4, curlClient: $mockCurlClientInativo);
        $funcionario->setNome('Teste Mock Inativo');
        $funcionario->setCpf('987.654.321-00'); // CPF válido
        $funcionario->setDataNascimento('1992-11-11');
        $funcionario->setEndereco($this->endereco);

        $this->assertEquals('inativo', $funcionario->consultarCpf());
    }

    public function testFuncionarioComCpfValidoApiLocalAtivo(): void
    {
        // Este teste depende de um servidor HTTP rodando `test_api_local.php`
        // Pode ser desabilitado ou marcado como "skipped" se não quiser a dependência.

        $this->markTestSkipped('Este teste requer que o servidor local test_api_local.php esteja rodando em http://localhost:8000.');

        $funcionario = new Funcionario(id: 5);
        $funcionario->setNome('Teste API Local Ativo');
        $funcionario->setCpf('113.062.997-05');
        $funcionario->setDataNascimento('2000-03-10');
        $funcionario->setEndereco($this->endereco);

        $localApiUrl = 'http://localhost:8000/test_api_local.php';
        $this->assertEquals('ativo', $funcionario->consultarCpf($localApiUrl));
    }

    // Testes para getCpf(), getIdade(), etc.
    public function testGetCpfRetornaCpfFormatado(): void
    {
        $funcionario = new Funcionario();
        $funcionario->setCpf('11306299705');
        $this->assertEquals('113.062.997-05', $funcionario->getCpf());

        $funcionarioComPontos = new Funcionario();
        $funcionarioComPontos->setCpf('113.062.997-05');
        $this->assertEquals('113.062.997-05', $funcionarioComPontos->getCpf());
    }

    public function testGetIdadeCalculaCorretamente(): void
    {
        $funcionario = new Funcionario();
        $funcionario->setDataNascimento('2000-05-20');
        $this->assertEquals(25, $funcionario->getIdade());

        $funcionarioNoLimite = new Funcionario();
        $funcionarioNoLimite->setDataNascimento('2000-05-21');
        $this->assertEquals(24, $funcionarioNoLimite->getIdade());

        $funcionarioSemData = new Funcionario();
        $this->assertNull($funcionarioSemData->getIdade());
    }
}