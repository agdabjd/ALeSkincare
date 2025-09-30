<?php
session_start();
header('Content-Type: application/json; charset=utf-8');

if (!isset($_SESSION['user_id'])) {
    http_response_code(401);
    echo json_encode(['error' => 'Usuário não autenticado']);
    exit;
}

$pdo = require __DIR__ . '/conexao.php';
$pdo->exec("USE `aleskincare`");

$user_id = $_SESSION['user_id'];
$action = $_REQUEST['action'] ?? 'get';

function get_or_create_open_basket($pdo, $user_id) {
    $stmt = $pdo->prepare("SELECT id FROM baskets WHERE user_id = ? AND status = 'open' LIMIT 1");
    $stmt->execute([$user_id]);
    $b = $stmt->fetch();
    if ($b) return $b['id'];
    $stmt = $pdo->prepare("INSERT INTO baskets (user_id, status) VALUES (?, 'open')");
    $stmt->execute([$user_id]);
    return $pdo->lastInsertId();
}

if ($action === 'add') {
    $input = json_decode(file_get_contents('php://input'), true) ?: $_POST;
    // aceita product_id (single) ou product_ids (array)
    $product_ids = [];
    if (isset($input['product_ids']) && is_array($input['product_ids'])) {
        $product_ids = array_map('intval', $input['product_ids']);
    } elseif (isset($input['product_id'])) {
        $product_ids[] = intval($input['product_id']);
    } else {
        echo json_encode(['ok' => false, 'error' => 'product_id(s) obrigatórios']);
        exit;
    }

    $basket_id = get_or_create_open_basket($pdo, $user_id);
    $added = 0;
    foreach ($product_ids as $pid) {
        $stmt = $pdo->prepare("SELECT in_stock FROM products WHERE id = ?");
        $stmt->execute([$pid]);
        $p = $stmt->fetch();
        if (!$p) continue;
        if ((int)$p['in_stock'] === 0) continue;

        $stmt = $pdo->prepare("SELECT id FROM basket_items WHERE basket_id = ? AND product_id = ?");
        $stmt->execute([$basket_id, $pid]);
        if ($stmt->fetch()) continue;

        $ins = $pdo->prepare("INSERT INTO basket_items (basket_id, product_id) VALUES (?, ?)");
        $ins->execute([$basket_id, $pid]);
        $added++;
    }

    $stmt = $pdo->prepare("SELECT COUNT(*) as cnt FROM basket_items WHERE basket_id = ?");
    $stmt->execute([$basket_id]);
    $count = $stmt->fetch()['cnt'];

    echo json_encode(['ok' => true, 'added' => $added, 'count' => (int)$count]);
    exit;
}

if ($action === 'get') {
    $stmt = $pdo->prepare("SELECT id FROM baskets WHERE user_id = ? AND status = 'open' LIMIT 1");
    $stmt->execute([$user_id]);
    $b = $stmt->fetch();
    if (!$b) {
        echo json_encode(['items' => [], 'total' => 0, 'count' => 0]);
        exit;
    }
    $basket_id = $b['id'];
    $stmt = $pdo->prepare("
      SELECT bi.id as bi_id, p.id as product_id, p.name, p.price, p.description, p.in_stock, s.name as supplier_name
      FROM basket_items bi
      JOIN products p ON bi.product_id = p.id
      LEFT JOIN suppliers s ON p.supplier_id = s.id
      WHERE bi.basket_id = ?
    ");
    $stmt->execute([$basket_id]);
    $items = $stmt->fetchAll();
    $total = 0;
    foreach ($items as $it) $total += (float)$it['price'];
    echo json_encode(['items' => $items, 'total' => number_format($total, 2, '.', ''), 'count' => count($items)]);
    exit;
}

if ($action === 'remove') {
    $input = json_decode(file_get_contents('php://input'), true) ?: $_POST;
    $product_id = intval($input['product_id'] ?? 0);
    if (!$product_id) {
        echo json_encode(['ok' => false, 'error' => 'product_id obrigatório']);
        exit;
    }
    $stmt = $pdo->prepare("SELECT id FROM baskets WHERE user_id = ? AND status = 'open' LIMIT 1");
    $stmt->execute([$user_id]);
    $b = $stmt->fetch();
    if (!$b) {
        echo json_encode(['ok' => false, 'error' => 'Cesta não encontrada']);
        exit;
    }
    $basket_id = $b['id'];
    $del = $pdo->prepare("DELETE FROM basket_items WHERE basket_id = ? AND product_id = ?");
    $ok = $del->execute([$basket_id, $product_id]);
    echo json_encode(['ok' => (bool)$ok]);
    exit;
}

if ($action === 'clear') {
    $stmt = $pdo->prepare("SELECT id FROM baskets WHERE user_id = ? AND status = 'open' LIMIT 1");
    $stmt->execute([$user_id]);
    $b = $stmt->fetch();

    if (!$b) {
        echo json_encode(['ok' => true, 'message' => 'Nenhuma cesta aberta para finalizar']);
        exit;
    }

    $basket_id = $b['id'];

    try {
        $pdo->beginTransaction();
        $del_items = $pdo->prepare("DELETE FROM basket_items WHERE basket_id = ?");
        $del_items->execute([$basket_id]);
        $pdo->commit();
        echo json_encode(['ok' => true, 'message' => 'Compra finalizada e cesta fechada.']);
        exit;
    } catch (Exception $e) {
        $pdo->rollBack();
        http_response_code(500);
        echo json_encode(['ok' => false, 'error' => 'Erro ao finalizar a compra.']);
        exit;
    }
}

http_response_code(400);
echo json_encode(['error' => 'Ação inválida']);
