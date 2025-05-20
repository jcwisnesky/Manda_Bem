<?php

namespace App\Services;

interface CurlClientInterface {
    public function post(string $url, array $data): string;
}