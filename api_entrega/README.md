# API Entrega — Microsserviço (PHP + SQLite)

Projeto desenvolvido para a disciplina **Desenvolvimento de Aplicações Distribuídas e Móveis** (PUC Minas).

## Estrutura

```
api_entrega/
├── config.php          # Conexão SQLite + criação das tabelas (entrega e pagamento mock)
├── entregas.php         # API REST principal (CRUD de Entrega)
├── pagamentos.php       # MOCK do microsserviço de Pagamento (somente leitura)
├── .htaccess            # Configuração de CORS / preflight
├── database.sqlite      # Criado automaticamente na primeira execução
└── frontend/
    ├── index.html        # Tela AdminLTE 3 (CRUD de entregas)
    └── js/app.js          # Lógica JS que consome a API via fetch/AJAX
```

## Requisitos

- PHP 7.4+ com extensão `pdo_sqlite` habilitada
- Servidor web (Apache, ou o servidor embutido do PHP)

## Como executar

### Opção 1 — Servidor embutido do PHP (mais simples)

```bash
cd api_entrega
php -S localhost:8000
```

A API ficará disponível em:
- `http://localhost:8000/entregas.php`
- `http://localhost:8000/pagamentos.php`

O front-end pode ser aberto direto no navegador (`frontend/index.html`) ou servido por outro `php -S` em outra porta.

> ⚠️ Se usar o servidor embutido para o front-end também, ajuste a constante
> `API_BASE` em `frontend/js/app.js` para apontar para a porta correta da API.

### Opção 2 — Apache / XAMPP / WAMP

1. Copie a pasta `api_entrega` para o diretório `htdocs` (ou equivalente).
2. Acesse `http://localhost/api_entrega/frontend/index.html`.
3. Ajuste `API_BASE` em `app.js` se necessário (ex.: `http://localhost/api_entrega`).

O banco SQLite (`database.sqlite`) e as tabelas são criados automaticamente
na primeira requisição.

## Endpoints da API de Entrega

| Método | Endpoint                  | Descrição                          |
|--------|---------------------------|-------------------------------------|
| GET    | /entregas.php              | Lista todas as entregas             |
| GET    | /entregas.php?id=1         | Retorna uma entrega específica      |
| POST   | /entregas.php               | Cria uma nova entrega               |
| PUT    | /entregas.php?id=1         | Atualiza uma entrega existente      |
| DELETE | /entregas.php?id=1         | Remove uma entrega                  |

### Corpo esperado (POST / PUT)

```json
{
  "pagamento": 1,
  "endereco": "Rua das Flores, 123",
  "entregador": "João da Silva"
}
```

## Endpoint mock de Pagamento

| Método | Endpoint                    | Descrição                       |
|--------|------------------------------|----------------------------------|
| GET    | /pagamentos.php               | Lista todos os pagamentos (mock) |
| GET    | /pagamentos.php?id=1          | Retorna um pagamento específico  |

> Este endpoint **simula** o microsserviço de Pagamento de outro grupo.
> Quando a API real de Pagamento estiver disponível, basta substituir as
> chamadas em `frontend/js/app.js` (função `carregarPagamentos`) e a
> validação em `entregas.php` (função `validarDados`) pela URL real do
> serviço — por exemplo, via `file_get_contents()` ou `curl` consumindo
> o endpoint HTTP do outro grupo.

## Observações de arquitetura de microsserviços

- O serviço de Entrega é totalmente independente: possui seu próprio
  banco de dados (SQLite) e sua própria API HTTP.
- A FK para `pagamento` é resolvida via chamada (mockada) a outro serviço,
  simulando a comunicação entre microsserviços via endpoints HTTP/JSON.
- O front-end (AdminLTE 3) consome somente a API HTTP, sem acesso direto
  ao banco de dados — respeitando a separação de responsabilidades.
