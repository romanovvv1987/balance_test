<?php
require_once __DIR__ . '/../vendor/autoload.php';

use App\Infrastructure\Database\DatabaseConnection;
use App\Infrastructure\Database\TransactionRepository;
use App\Presentation\BalanceController;

try {
    $db = DatabaseConnection::getConnection();
    $transactionRepository = new TransactionRepository($db);
    $controller = new BalanceController($transactionRepository);

    if ($_SERVER['REQUEST_URI'] === '/api/users') {
        header('Content-Type: application/json');
        echo $controller->getUsers();
    } elseif (preg_match('/^\/api\/balance\/(\d+)$/', $_SERVER['REQUEST_URI'], $matches)) {
        header('Content-Type: application/json');
        echo $controller->getBalance((int)$matches[1]);
    }
} catch (Exception $e) {
    http_response_code(500);
    echo 'Внутренняя ошибка сервера: ' . $e->getMessage();
}