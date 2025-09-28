<?php
// php/api_suppliers.php
session_start();
header('Content-Type: application/json; charset=utf-8');
$pdo = require __DIR__ . '/conexao.php';
$pdo->exec("USE `aleskincare`");

$action = $_REQUEST['action'] ?? 'list';

if ($action === 'list') {
    $stmt = $pdo->query("SELECT * FROM suppliers ORDER BY id DESC");
    echo json_encode($stmt->fetchAll());
    exit;
}

if ($action === 'create') {
    $data = $_POST;
    if (empty($data)) {
        $data = json_decode(file_get_contents('php://input'), true) ?: [];
    }
    $name = $data['name'] ?? null;
    $contact = $data['contact'] ?? null;
    $address = $data['address'] ?? null;

    if (!$name) {
        echo json_encode(['ok' => false, 'error' => 'Nome obrigatório']);
        exit;
    }
    $stmt = $pdo->prepare("INSERT INTO suppliers (name, contact, address) VALUES (?, ?, ?)");
    $ok = $stmt->execute([$name, $contact, $address]);
    echo json_encode(['ok' => (bool)$ok]);
    exit;
}

if ($action === 'delete') {
    $id = intval($_GET['id'] ?? 0);
    $stmt = $pdo->prepare("DELETE FROM suppliers WHERE id = ?");
    $ok = $stmt->execute([$id]);
    echo json_encode(['ok' => (bool)$ok]);
    exit;
}

http_response_code(400);
echo json_encode(['error' => 'Ação inválida']);
