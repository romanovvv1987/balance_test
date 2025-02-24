<?php

namespace App\Infrastructure\Database;

use PDO;
use PDOException;

class TransactionRepository
{
    private PDO $db;

    public function __construct(PDO $db)
    {
        $this->db = $db;
    }

    /**
     * Показать аккаунты у которых были транзакции
     * @return array
     */
    public function getUsersWithTransactions(): array
    {
        try {
            $sql = "
                SELECT DISTINCT u.id, u.name
                FROM users u
                JOIN user_accounts ua ON u.id = ua.user_id
                JOIN transactions t ON ua.id = t.account_from
                    OR ua.id = t.account_to
                ORDER BY u.name
            ";

            $stmt = $this->db->query($sql);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            throw new PDOException("Ошибка при получении пользователей: " . $e->getMessage());
        }
    }

    /**
     * Агрегация по месяцам и пользователям
     * @param int $userId
     * @return array
     */
    public function getMonthlyBalance(int $userId): array
    {
        try {
            $sql = "
                WITH monthly_transactions AS (
                    SELECT 
                        strftime('%Y-%m', t.trdate) as month,
                        CASE 
                            WHEN ua.user_id = :user_id AND t.account_from = ua.id THEN -amount 
                            WHEN ua.user_id = :user_id AND t.account_to = ua.id THEN amount
                        END as flow
                    FROM transactions t
                    JOIN user_accounts ua ON (t.account_from = ua.id OR t.account_to = ua.id)
                    WHERE ua.user_id = :user_id
                )
                SELECT 
                    month,
                    SUM(flow) as balance
                FROM monthly_transactions
                GROUP BY month
                ORDER BY month DESC
            ";

            $stmt = $this->db->prepare($sql);
            $stmt->execute([':user_id' => $userId]);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            throw new PDOException("Ошибка при получении баланса: " . $e->getMessage());
        }
    }
}