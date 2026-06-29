<?php
/**
 * entregas.php
 * API REST do microsserviço de ENTREGA.
 *
 * Endpoints:
 *   GET    /entregas.php             -> lista todas as entregas
 *   GET    /entregas.php?id=1        -> retorna uma entrega específica
 *   POST   /entregas.php             -> cria uma nova entrega (JSON no body)
 *   PUT    /entregas.php?id=1        -> atualiza uma entrega existente (JSON no body)
 *   DELETE /entregas.php?id=1        -> remove uma entrega
 *
 * Corpo esperado (POST/PUT) - JSON:
 * {
 *   "pagamento": 1,
 *   "endereco": "Rua das Flores, 123",
 *   "entregador": "João da Silva"
 * }
 */

require __DIR__ . '/config.php';

$metodo = $_SERVER['REQUEST_METHOD'];

switch ($metodo) {

    case 'GET':
        if (isset($_GET['id'])) {
            buscarUmaEntrega($pdo, (int) $_GET['id']);
        } else {
            listarEntregas($pdo);
        }
        break;

    case 'POST':
        criarEntrega($pdo);
        break;

    case 'PUT':
        if (!isset($_GET['id'])) {
            responder(400, ["erro" => "Informe o id da entrega via ?id= para atualizar."]);
        }
        atualizarEntrega($pdo, (int) $_GET['id']);
        break;

    case 'DELETE':
        if (!isset($_GET['id'])) {
            responder(400, ["erro" => "Informe o id da entrega via ?id= para remover."]);
        }
        removerEntrega($pdo, (int) $_GET['id']);
        break;

    default:
        responder(405, ["erro" => "Método não permitido."]);
}

/* ----------------------------------------------------------------------- */

function listarEntregas($pdo) {
    $sql = "SELECT e.identrega, e.pagamento, e.endereco, e.entregador,
                   p.forma as pagamento_forma, p.valor as pagamento_valor, p.status as pagamento_status
            FROM entrega e
            LEFT JOIN pagamento p ON p.id = e.pagamento
            ORDER BY e.identrega DESC";
    $entregas = $pdo->query($sql)->fetchAll();
    responder(200, $entregas);
}

function buscarUmaEntrega($pdo, $id) {
    $sql = "SELECT e.identrega, e.pagamento, e.endereco, e.entregador,
                   p.forma as pagamento_forma, p.valor as pagamento_valor, p.status as pagamento_status
            FROM entrega e
            LEFT JOIN pagamento p ON p.id = e.pagamento
            WHERE e.identrega = :id";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([':id' => $id]);
    $entrega = $stmt->fetch();

    if (!$entrega) {
        responder(404, ["erro" => "Entrega não encontrada."]);
    }

    responder(200, $entrega);
}

function criarEntrega($pdo) {
    $dados = lerCorpoJson();

    $erros = validarDados($dados, $pdo);
    if (!empty($erros)) {
        responder(422, ["erro" => "Dados inválidos.", "detalhes" => $erros]);
    }

    $sql = "INSERT INTO entrega (pagamento, endereco, entregador) VALUES (:pagamento, :endereco, :entregador)";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([
        ':pagamento'  => $dados['pagamento'],
        ':endereco'   => $dados['endereco'] ?? null,
        ':entregador' => $dados['entregador'] ?? null,
    ]);

    $novoId = $pdo->lastInsertId();
    buscarUmaEntrega($pdo, (int) $novoId);
}

function atualizarEntrega($pdo, $id) {
    $stmt = $pdo->prepare("SELECT * FROM entrega WHERE identrega = :id");
    $stmt->execute([':id' => $id]);
    $existente = $stmt->fetch();

    if (!$existente) {
        responder(404, ["erro" => "Entrega não encontrada."]);
    }

    $dados = lerCorpoJson();
    $erros = validarDados($dados, $pdo);
    if (!empty($erros)) {
        responder(422, ["erro" => "Dados inválidos.", "detalhes" => $erros]);
    }

    $sql = "UPDATE entrega
            SET pagamento = :pagamento, endereco = :endereco, entregador = :entregador
            WHERE identrega = :id";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([
        ':pagamento'  => $dados['pagamento'],
        ':endereco'   => $dados['endereco'] ?? null,
        ':entregador' => $dados['entregador'] ?? null,
        ':id'         => $id,
    ]);

    buscarUmaEntrega($pdo, $id);
}

function removerEntrega($pdo, $id) {
    $stmt = $pdo->prepare("SELECT * FROM entrega WHERE identrega = :id");
    $stmt->execute([':id' => $id]);
    $existente = $stmt->fetch();

    if (!$existente) {
        responder(404, ["erro" => "Entrega não encontrada."]);
    }

    $stmt = $pdo->prepare("DELETE FROM entrega WHERE identrega = :id");
    $stmt->execute([':id' => $id]);

    responder(200, ["mensagem" => "Entrega removida com sucesso.", "identrega" => $id]);
}

/**
 * Valida os dados recebidos no corpo da requisição.
 * Garante que o campo "pagamento" exista na tabela (simulando a checagem de FK,
 * já que no mundo real seria uma chamada à API de pagamento de outro grupo).
 */
function validarDados($dados, $pdo) {
    $erros = [];

    if (empty($dados['pagamento'])) {
        $erros[] = "O campo 'pagamento' (id do pagamento) é obrigatório.";
    } else {
        $stmt = $pdo->prepare("SELECT COUNT(*) as total FROM pagamento WHERE id = :id");
        $stmt->execute([':id' => $dados['pagamento']]);
        if ($stmt->fetch()['total'] == 0) {
            $erros[] = "O 'pagamento' informado (id={$dados['pagamento']}) não existe.";
        }
    }

    if (isset($dados['endereco']) && strlen($dados['endereco']) > 200) {
        $erros[] = "O campo 'endereco' deve ter no máximo 200 caracteres.";
    }

    if (isset($dados['entregador']) && strlen($dados['entregador']) > 100) {
        $erros[] = "O campo 'entregador' deve ter no máximo 100 caracteres.";
    }

    return $erros;
}
