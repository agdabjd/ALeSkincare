<?php
$host = 'localhost';
$db   = 'aleskincare';
$user = 'root';
$pass = '';
$charset = 'utf8mb4';

$dsn = "mysql:host=$host;charset=$charset";

$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION, // erros em forma de exceção
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,       // resultados como array associativo
    PDO::ATTR_EMULATE_PREPARES   => false,                  // usar prepares nativos do MySQL
];

try {
    $pdo = new PDO($dsn, $user, $pass, $options);

    // Seleciona o banco (alguns ambientes exigem)
    $pdo->exec("CREATE DATABASE IF NOT EXISTS `$db` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
    $pdo->exec("USE `$db`");

    $pdo->exec("
        CREATE TABLE IF NOT EXISTS `suppliers` (
            `id` int(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
            `name` varchar(150) NOT NULL,
            `contact` varchar(150) DEFAULT NULL,
            `address` varchar(255) DEFAULT NULL,
            `created_at` timestamp NOT NULL DEFAULT current_timestamp()
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
    ");

    $stmt = $pdo->query("SELECT COUNT(*) FROM suppliers");
    $supplier_count = $stmt->fetchColumn();

    if ($supplier_count === 0) { 
        $pdo->exec("
            INSERT INTO `suppliers` (`id`, `name`, `contact`, `address`, `created_at`) VALUES
                (1, 'Beauty Labs', 'teste@gmail.com', 'Rua dos testes, 1234, Curitiba/PR', '2025-09-28 03:30:53'),
                (2, 'Skincare Pro', '44912341234', 'Avenida industrial, 1360, Guarulhos/SP', '2025-09-29 03:00:16');
        ");
    }

    $pdo->exec("
        CREATE TABLE IF NOT EXISTS `users` (
            `id` int(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
            `name` varchar(100) NOT NULL,
            `email` varchar(150) NOT NULL,
            `password_hash` varchar(255) NOT NULL,
            `created_at` timestamp NOT NULL DEFAULT current_timestamp()
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
    ");

    $stmt = $pdo->query("SELECT COUNT(*) FROM users");
    $user_count = $stmt->fetchColumn();

    if ($user_count === 0) {
        $pdo->exec("
            INSERT INTO `users` (`id`, `name`, `email`, `password_hash`, `created_at`) VALUES
                (1, 'Usuário teste', 'teste@gmail.com', '5905b442112a3c7b7af83255a1667224:f372d76e255309ee6c2ca44999a5cfcc4be152873dd05b7e37448d28b3bec478', '2025-09-28 03:29:20');
        ");
    }

        $pdo->exec("
        CREATE TABLE IF NOT EXISTS `products` (
            `id` int(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
            `name` varchar(150) NOT NULL,
            `price` decimal(10,2) NOT NULL DEFAULT 0.00,
            `supplier_id` int(11) DEFAULT NULL,
            `description` text DEFAULT NULL,
            `in_stock` tinyint(1) DEFAULT 1,
            `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
             -- ADICIONANDO A FOREIGN KEY AQUI!
            FOREIGN KEY (`supplier_id`) REFERENCES `suppliers` (`id`) ON DELETE SET NULL
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
    ");

    $stmt = $pdo->query("SELECT COUNT(*) FROM products");
    $product_count = $stmt->fetchColumn();

    if ($product_count === 0) {
        $pdo->exec("
            INSERT INTO `products` (`id`, `name`, `price`, `supplier_id`, `description`, `in_stock`, `created_at`) VALUES
                (1, 'Sérum Vitamina C', 79.99, 2, 'Um sérum de vitamina C formulado para aumentar o viço e melhorar a textura da pele.', 1, '2025-09-28 03:31:32'),
                (2, 'Creme para os olhos', 150.00, 2, 'Um creme hidratante para área dos olhos, diminui o inchaço e as manchas escuras.', 0, '2025-09-30 16:59:20'),
                (3, 'Protetor solar FPS 50', 55.00, 2, 'Protetor solar de textura leve, ideal para uso diário, indicado para todos os tipos de pele, efeito matte.', 1, '2025-09-30 18:07:50');
        ");
    }

    $pdo->exec("
        CREATE TABLE IF NOT EXISTS `baskets` (
            `id` int(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
            `user_id` int(11) NOT NULL,
            `status` enum('open','closed') DEFAULT 'open',
            `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
            FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
    ");

    $pdo->exec("
        CREATE TABLE IF NOT EXISTS `basket_items` (
            `id` int(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
            `basket_id` int(11) NOT NULL,
            `product_id` int(11) NOT NULL,
            `quantity` int(11) DEFAULT 1,
            `added_at` timestamp NOT NULL DEFAULT current_timestamp(),
            -- ADICIONANDO AS FOREIGN KEYS AQUI!
            FOREIGN KEY (`basket_id`) REFERENCES `baskets` (`id`) ON DELETE CASCADE,
            FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
    ");

    return $pdo;
} catch (PDOException $e) {
    die('Erro de conexão: ' . $e->getMessage());
}
