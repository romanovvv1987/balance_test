<?php

namespace App\Presentation;

use App\Infrastructure\Database\TransactionRepository;

class BalanceController
{
    private TransactionRepository $transactionRepository;

    public function __construct(TransactionRepository $transactionRepository)
    {
        $this->transactionRepository = $transactionRepository;
    }

    public function getUsers(): string
    {
        try {
            $users = $this->transactionRepository->getUsersWithTransactions();
            return json_encode(['success' => true, 'data' => $users]);
        } catch (\Exception $e) {
            return json_encode(['success' => false, 'error' => $e->getMessage()]);
        }
    }

    public function getBalance(int $userId): string
    {
        try {
            $balance = $this->transactionRepository->getMonthlyBalance($userId);
            return json_encode(['success' => true, 'data' => $balance]);
        } catch (\Exception $e) {
            return json_encode(['success' => false, 'error' => $e->getMessage()]);
        }
    }
}