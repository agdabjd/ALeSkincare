<?php
// php/api_products.php
session_start();
header('Content-Type: application/json; charset=utf-8');
$pdo = require __DIR__ . '/conexao.php';

// Garante que usamos o DB (o conexao.php conecta sem selecionar DB em alguns ambientes; seleciona caso necessário)
$pdo->exec("USE `aleskincare`");

$action = $_REQUEST['action'] ?? 'list';

if ($action === 'list') {
    $stmt = $pdo->query("SELECT p.*, s.name AS supplier_name FROM products p LEFT JOIN suppliers s ON p.supplier_id = s.id ORDER BY p.id DESC");
    echo json_encode($stmt->fetchAll());
    exit;
}

if ($action === 'create') {
    // aceita JSON ou form data
    $data = $_POST;
    if (empty($data)) {
        $input = json_decode(file_get_contents('php://input'), true);
        $data = $input ?: [];
    }

    $id          = $data['id'] ?? null;
    $name        = $data['name'] ?? null;
    $price       = $data['price'] ?? 0;
    $supplier_id = $data['supplier_id'] ?: null;
    $description = $data['description'] ?? '';
    $in_stock    = isset($data['in_stock']) ? (int)$data['in_stock'] : 1;

    if (!$name) {
        echo json_encode(['ok' => false, 'error' => 'Nome obrigatório']);
        exit;
    }

    if ($id) {
        // UPDATE
        $stmt = $pdo->prepare("UPDATE products 
                               SET name = ?, price = ?, supplier_id = ?, description = ?, in_stock = ?
                               WHERE id = ?");
        $ok = $stmt->execute([$name, $price, $supplier_id, $description, $in_stock, $id]);
    } else {
        // INSERT
        $stmt = $pdo->prepare("INSERT INTO products (name, price, supplier_id, description, in_stock) 
                               VALUES (?, ?, ?, ?, ?)");
        $ok = $stmt->execute([$name, $price, $supplier_id, $description, $in_stock]);
    }

    echo json_encode(['ok' => (bool)$ok]);
    exit;
}

if ($action === 'get') {
    $id = intval($_GET['id'] ?? 0);
    $stmt = $pdo->prepare("SELECT * FROM products WHERE id = ?");
    $stmt->execute([$id]);
    $product = $stmt->fetch(PDO::FETCH_ASSOC);
    echo json_encode($product ?: null);
    exit;
}

if ($action === 'update') {
    $data = $_POST;
    if (empty($data)) {
        $input = json_decode(file_get_contents('php://input'), true);
        $data = $input ?: [];
    }

    $id = $data['id'] ?? null;
    $name = $data['name'] ?? null;
    $price = $data['price'] ?? 0;
    $supplier_id = $data['supplier_id'] ?: null;
    $description = $data['description'] ?? '';
    $in_stock = isset($data['in_stock']) ? (int)$data['in_stock'] : 1;

    if (!$id || !$name) {
        echo json_encode(['ok' => false, 'error' => 'Dados inválidos']);
        exit;
    }

    $stmt = $pdo->prepare("UPDATE products SET name=?, price=?, supplier_id=?, description=?, in_stock=? WHERE id=?");
    $ok = $stmt->execute([$name, $price, $supplier_id, $description, $in_stock, $id]);

    echo json_encode(['ok' => (bool)$ok]);
    exit;
}


if ($action === 'delete') {
    $id = intval($_GET['id'] ?? 0);
    $stmt = $pdo->prepare("DELETE FROM products WHERE id = ?");
    $ok = $stmt->execute([$id]);
    echo json_encode(['ok' => (bool)$ok]);
    exit;
}

http_response_code(400);
echo json_encode(['error' => 'Ação inválida']);
