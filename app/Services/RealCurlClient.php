<?php

namespace App\Services;

use Exception;

class RealCurlClient implements CurlClientInterface {
    public function post(string $url, array $data): string {
        $ch = curl_init($url);
        if ($ch === false) {
            throw new Exception('Falha ao inicializar cURL.');
        }

        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));
        curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/x-www-form-urlencoded']);

        $response = curl_exec($ch);

        if (curl_errno($ch)) {
            $error = curl_error($ch);
            curl_close($ch);
            throw new Exception('Erro cURL: ' . $error);
        }

        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        if ($httpCode !== 200) {
            curl_close($ch);
            // Lançar uma exceção para o caller lidar com o erro da API
            throw new Exception('Erro na API: Status HTTP ' . $httpCode . ' - Resposta: ' . ($response ?: 'Vazio'));
        }

        curl_close($ch);
        return $response;
    }
}