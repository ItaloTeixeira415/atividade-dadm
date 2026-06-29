<?php
/**
 * pagamentos.php
 * MOCK do microsserviço de PAGAMENTO.
 *
 * Em um cenário real, este serviço pertenceria a outro grupo da turma
 * e seria acessado via HTTP em outra URL/porta. Aqui ele é simulado
 * localmente apenas para permitir o funcionamento e testes da API de ENTREGA
 * (respeitando a FK "pagamento" da tabela "entrega").
 *
 * Endpoints:
 *   GET /pagamentos.php          -> lista todos os pagamentos
 *   GET /pagamentos.php?id=1     -> retorna um pagamento específico
 */

require __DIR__ . '/config.php';

$metodo = $_SERVER['REQUEST_METHOD'];

if ($metodo !== 'GET') {
    responder(405, ["erro" => "Método não permitido. Este mock só aceita GET."]);
}

if (isset($_GET['id'])) {
    $id = (int) $_GET['id'];
    $stmt = $pdo->prepare("SELECT * FROM pagamento WHERE id = :id");
    $stmt->execute([':id' => $id]);
    $pagamento = $stmt->fetch();

    if (!$pagamento) {
        responder(404, ["erro" => "Pagamento não encontrado."]);
    }

    responder(200, $pagamento);
} else {
    $pagamentos = $pdo->query("SELECT * FROM pagamento ORDER BY id")->fetchAll();
    responder(200, $pagamentos);
}
