<?php
/**
 * config.php
 * Conexão com o banco SQLite e criação da tabela de entrega.
 *
 * Disciplina: Desenvolvimento de Aplicações Distribuídas e Móveis
 * Microsserviço: ENTREGA
 *
 * OBS: O microsserviço de Pagamento NÃO é mais mockado aqui.
 * A validação/consulta do pagamento deve ser feita via chamada de API
 * (ex: file_get_contents, cURL ou Guzzle) ao serviço real de Pagamento.
 */

header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization");

// Responde rapidamente a requisições de preflight (CORS)
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

$dbFile = __DIR__ . '/database.sqlite';

try {
    $pdo = new PDO('sqlite:' . $dbFile);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(["erro" => "Falha na conexão com o banco de dados.", "detalhes" => $e->getMessage()]);
    exit;
}

/**
 * URL base do microsserviço de Pagamento (API real).
 * Ajuste conforme o endpoint disponibilizado pelo grupo responsável.
 */
const PAGAMENTO_API_URL = "http://localhost/pagamento/api/pagamentos.php";

/**
 * Consulta um pagamento pelo ID diretamente na API do microsserviço de Pagamento.
 * Retorna o array decodificado do pagamento ou null se não encontrado/erro.
 */
function consultarPagamentoApi($idPagamento) {
    $url = PAGAMENTO_API_URL . "?id=" . urlencode($idPagamento);

    $ctx = stream_context_create([
        "http" => [
            "method"  => "GET",
            "timeout" => 5
        ]
    ]);

    $resposta = @file_get_contents($url, false, $ctx);
    if ($resposta === false) {
        return null;
    }

    $dados = json_decode($resposta, true);
    return is_array($dados) ? $dados : null;
}

/**
 * Cria a tabela "entrega" conforme o modelo especificado pelo professor.
 * identrega INTEGER UNSIGNED AUTO_INCREMENT
 * pagamento INTEGER UNSIGNED NOT NULL (referência ao ID retornado pela API de Pagamento)
 * endereco VARCHAR(200) NULL
 * entregador VARCHAR(100) NULL
 */
$pdo->exec("
    CREATE TABLE IF NOT EXISTS entrega (
        identrega INTEGER PRIMARY KEY AUTOINCREMENT,
        pagamento INTEGER NOT NULL,
        endereco VARCHAR(200) NULL,
        entregador VARCHAR(100) NULL
    );
");

/**
 * Função utilitária para enviar respostas JSON padronizadas.
 */
function responder($status, $dados) {
    http_response_code($status);
    echo json_encode($dados, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
    exit;
}

/**
 * Lê o corpo JSON da requisição (POST/PUT) e devolve como array associativo.
 */
function lerCorpoJson() {
    $raw = file_get_contents("php://input");
    $dados = json_decode($raw, true);
    return is_array($dados) ? $dados : [];
}