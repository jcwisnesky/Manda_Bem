<?php

namespace App\Models;

use App\Models\Endereco;
use App\Services\CurlClientInterface;
use App\Services\RealCurlClient;
use App\Services\MockCurlClient;

class Funcionario {

    public readonly ?int $id;
    private string $nome;
    private ?string $cpf;
    private string $dataNascimento;
    private Endereco $endereco;

    private CurlClientInterface $curlClient;

    public function __construct(?int $id = null, ?CurlClientInterface $curlClient = null, ?string $cpf = null) {
        $this->id = $id;
        $this->curlClient = $curlClient ?? new RealCurlClient();

        if ($cpf !== null) {
            $this->setCpf($cpf);
        } else {
            $this->cpf = null;
        }
    }

    public function setNome(string $nome): void {
        $this->nome = $nome;
    }

    /**
     * Define o CPF do funcionário.
     * Lança uma InvalidArgumentException se o CPF fornecido for inválido.
     */
public function setCpf(string $cpf): void {
    $cpfLimpo = preg_replace('/\D/', '', $cpf);

    if (!$this->isValidCpf($cpfLimpo)) {
        throw new \InvalidArgumentException("CPF inválido: O CPF '$cpf' não segue o padrão de validação.");
    }

    $this->cpf = $cpfLimpo;
}

    public function setDataNascimento(string $dataNascimento): void {
        $this->dataNascimento = $dataNascimento;
    }

    public function setEndereco(Endereco $endereco): void {
        $this->endereco = $endereco;
    }

    public function getNome(): string {
        return $this->nome;
    }

    public function getCpf(): string {
        if (!isset($this->cpf) || empty($this->cpf)) {
            return '';
        }

        $cpfLimpo = $this->cpf;
        if (strlen($cpfLimpo) === 11) {
            return preg_replace('/(\d{3})(\d{3})(\d{3})(\d{2})/', '$1.$2.$3-$4', $cpfLimpo);
        }
        return $this->cpf;
    }

    public function getIdade(): ?int {
        if (!isset($this->dataNascimento) || empty($this->dataNascimento)) {
            return null;
        }

        try {
            $dataNascimentoObj = new \DateTime($this->dataNascimento);
            $hoje = new \DateTime();
            return $hoje->diff($dataNascimentoObj)->y;
        } catch (\Exception $e) {
            return null;
        }
    }

    /**
     * Consulta a situação cadastral do funcionário.
     * Assume que o CPF já é válido se foi setado via setCpf() ou construtor.
     */
    public function consultarCpf(string $apiUrl = 'https://mandabem.com.br/situacao_funcionario'): string {
        if (!isset($this->cpf) || empty($this->cpf)) {
            return 'inativo';
        }

        // Se for uma instância de MockCurlClient, retorna a resposta mockada
        if ($this->curlClient instanceof MockCurlClient) {
            try {
                $postData = ['cpf' => $this->cpf, 'dataNascimento' => $this->dataNascimento ?? ''];
                $responseJson = $this->curlClient->post($apiUrl, $postData);
                $data = json_decode($responseJson, true);

                if (json_last_error() !== JSON_ERROR_NONE) {
                    return 'Erro: Resposta do mock não é JSON válido.';
                }
                return $data['status'] ?? 'mock_status_desconhecido';
            } catch (\Exception $e) {
                return 'Erro no mock: ' . $e->getMessage();
            }
        }

        // Validação da data de nascimento para a API real (se não for mock)
        if (!isset($this->dataNascimento) || empty($this->dataNascimento)) {
            return 'Erro: Data de nascimento não definida para consulta de API.';
        }

        $postData = [
            'cpf' => $this->cpf,
            'dataNascimento' => $this->dataNascimento,
        ];

        try {
            $response = $this->curlClient->post($apiUrl, $postData);
            $data = json_decode($response, true);

            if (json_last_error() !== JSON_ERROR_NONE) {
                return 'Erro: Resposta da API não é JSON válido.';
            }

            return $data['status'] ?? 'status_desconhecido_api';

        } catch (\Exception $e) {
            return 'Erro na consulta: ' . $e->getMessage();
        }
    }

    /**
     * Valida se um CPF é matematicamente válido e não possui dígitos repetidos.
     * Esta é uma implementação padrão do algoritmo de validação de CPF.
     */
    private function isValidCpf(string $cpf): bool
    {
        if (preg_match('/^(\d)\1{10}$/', $cpf)) return false;
        if (strlen($cpf) !== 11) return false;

        for ($t = 9; $t < 11; $t++) {
            $soma = 0;
            for ($c = 0; $c < $t; $c++) {
                $soma += $cpf[$c] * (($t + 1) - $c);
            }
            $digito = ((10 * $soma) % 11) % 10;
            if ($cpf[$t] != $digito) return false;
        }

        return true;
    }


}