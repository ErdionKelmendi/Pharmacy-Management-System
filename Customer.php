<?php
require_once __DIR__ . '/../config/Database.php';

class Customer {
    private PDO $db;

    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
    }

    public function getAll(): array {
        $stmt = $this->db->query("SELECT * FROM customers ORDER BY name ASC");
        return $stmt->fetchAll();
    }

    public function getById(int $id): array|false {
        $stmt = $this->db->prepare("SELECT * FROM customers WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch();
    }

    public function create(string $name, string $phone): bool {
        $stmt = $this->db->prepare("INSERT INTO customers (name, phone) VALUES (?, ?)");
        return $stmt->execute([$name, $phone]);
    }

    public function delete(int $id): bool {
        $stmt = $this->db->prepare("DELETE FROM customers WHERE id = ?");
        return $stmt->execute([$id]);
    }

    public function getCount(): int {
        $stmt = $this->db->query("SELECT COUNT(*) as cnt FROM customers");
        return (int)$stmt->fetch()['cnt'];
    }

    public function getWithSaleCount(): array {
        $stmt = $this->db->query(
            "SELECT c.*, COUNT(s.id) as sale_count, COALESCE(SUM(s.total_price),0) as total_spent
             FROM customers c
             LEFT JOIN sales s ON c.id = s.customer_id
             GROUP BY c.id
             ORDER BY c.name ASC"
        );
        return $stmt->fetchAll();
    }
}
