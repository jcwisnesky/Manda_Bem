# Teste Programação Manda Bem

Este projeto implementa a classe `Funcionario` e seus métodos conforme as especificações do teste.

## Requisitos

-   PHP >= 8.1
-   Composer

## Estrutura do Projeto

-   `app/Models/`: Contém as classes de modelo (`Funcionario`, `Endereco`).
-   `app/Services/`: Contém as classes de serviço (`CurlClientInterface`, `RealCurlClient`, `MockCurlClient`) para a comunicação com APIs.
-   `public/`: Contém o script `index.php` (exemplo de uso da aplicação) e `test_api_local.php` (simulação de API local).
-   `tests/`: Contém os testes de unidade e integração utilizando PHPUnit.
-   `vendor/`: Pasta de dependências gerada pelo Composer.
-   `composer.json`: Arquivo de configuração do Composer.
-   `phpunit.xml`: Arquivo de configuração do PHPUnit.

## Instalação

1.  Clone este repositório.
2.  Navegue até a raiz do projeto no terminal.
3.  Instale as dependências via Composer:
    ```bash
    composer install
    ```

## Como Executar a Aplicação (Exemplo)

Para ver um exemplo básico de uso da classe `Funcionario`, execute o script `index.php`:
```bash
php -S localhost:8000 -t public/
```

Em outro terminal
```bash
php public/index.php
```
## Como Executar os testes (PHPUnit)
```bash
vendor/bin/phpunit
```
### Saída esperada:

- Testes de CPF válido/inválido

- Simulações com Mock da API

- Teste da getIdade() e formatação de CPF

- Um teste com markTestSkipped() para uso com servidor local

## Decisões de Design e Boas Práticas
- O método consultarCpf() utiliza cURL com injeção de dependência, permitindo mocks durante testes.

- O projeto foi feito sem frameworks PHP, respeitando a proposta do teste.

- Os testes foram organizados para cobrir casos esperados, exceções e integração com mocks/API.
