<?php

namespace App\Models;

class Endereco {
    public function __construct(
        public string $rua,
        public string $numero,
        public string $bairro,
        public string $cidade,
        public string $estado,
        public ?string $complemento = null
    ) {}
}