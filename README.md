# Microsserviço de Entrega

`Tecnologia em Análise e Desenvolvimento de Sistemas`
`Desenvolvimento de Aplicações Distribuídas e Móveis`
`1º Semestre de 2026`

O projeto consiste no desenvolvimento de um microsserviço para gerenciamento de **Entregas**, dentro de um sistema distribuído composto por diversas APIs independentes desenvolvidas pelos grupos da turma. O serviço expõe uma API REST em PHP, com persistência em banco de dados SQLite, responsável por cadastrar, consultar, atualizar e remover registros de entrega, relacionando cada entrega a um pagamento (referência de chave estrangeira consumida de outro microsserviço da arquitetura). O front-end foi construído com o template AdminLTE 3.0, consumindo exclusivamente os endpoints HTTP/JSON da API, sem acesso direto ao banco de dados, respeitando o princípio de separação entre serviços previsto na arquitetura de microsserviços.

## Integrantes

* Italo Gabriel
* Maíra Mendes
* Guilherme Monteiro

## Orientador

* Paulo Henrique Rodrigues

## Instruções de Instalação, Execução e Acesso

### Acesso rápido (produção)

* URL da aplicação: http://localhost/api_entrega/frontend/index.php (Colocar no HTDocs e dar start no xamp primeiro)

* Status: em desenvolvimento 
* Ambiente: homologação (ambiente de testes em sala de aula)

### Usuário(s) de teste (se houver)

* Não há autenticação implementada nesta versão. A API e o front-end são de acesso livre no ambiente local.

### Pré-requisitos

* XAMPP (ou outro servidor com Apache + PHP 7.4 ou superior, com extensão `pdo_sqlite` habilitada)
* Navegador web atualizado

### Passo a passo de instalação

1. Extrair o arquivo `api_entrega.zip` em uma pasta local.
2. Copiar a pasta `api_entrega` (descompactada, **não** o `.zip`) para o diretório `C:\xampp\htdocs\`.
3. Abrir o **XAMPP Control Panel** e clicar em **Start** no módulo **Apache**.
4. Verificar/ajustar a constante `API_BASE` no arquivo `frontend/js/app.js` para o caminho correspondente, por exemplo:
   ```js
   const API_BASE = "http://localhost/api_entrega";
   ```

### Execução e acesso

* **API (testar isoladamente):**
  * `http://localhost/api_entrega/entregas.php` — lista as entregas cadastradas
  * `http://localhost/api_entrega/pagamentos.php` — lista os pagamentos (mock do microsserviço de Pagamento)
* **Front-end (AdminLTE 3):**
  * `http://localhost/api_entrega/frontend/index.php`

O banco de dados (`database.sqlite`) é criado e populado automaticamente pela própria API na primeira requisição, não sendo necessária nenhuma configuração manual de banco de dados.
