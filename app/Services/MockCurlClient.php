<?php

namespace App\Services;

class MockCurlClient implements CurlClientInterface {
    private array $mockResponses;
    private int $callCount = 0;

    // Pode receber um array de respostas para simular chamadas sequenciais
    public function __construct(array $mockResponses = []) {
        $this->mockResponses = $mockResponses;
    }

    public function post(string $url, array $data): string {
        // Retorna a próxima resposta mockada na fila ou uma resposta padrão
        if (isset($this->mockResponses[$this->callCount])) {
            $response = $this->mockResponses[$this->callCount];
            $this->callCount++;
            return json_encode($response);
        }

        // Resposta padrão se não houver mais mocks definidos
        return json_encode(['status' => 'mock_default_status']);
    }

    public function resetCallCount(): void {
        $this->callCount = 0;
    }
}