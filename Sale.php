<?php
require_once __DIR__ . '/../config/Database.php';

class Sale {
    private PDO $db;

    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
    }

    public function getAll(): array {
        $stmt = $this->db->query(
            "SELECT s.*, m.name AS medicine_name, m.price AS unit_price,
                    c.name AS customer_name, u.name AS user_name
             FROM sales s
             LEFT JOIN medicines m ON s.medicine_id = m.id
             LEFT JOIN customers c ON s.customer_id = c.id
             LEFT JOIN users u ON s.user_id = u.id
             ORDER BY s.sale_date DESC"
        );
        return $stmt->fetchAll();
    }

    public function getById(int $id): array|false {
        $stmt = $this->db->prepare(
            "SELECT s.*, m.name AS medicine_name, c.name AS customer_name, u.name AS user_name
             FROM sales s
             LEFT JOIN medicines m ON s.medicine_id = m.id
             LEFT JOIN customers c ON s.customer_id = c.id
             LEFT JOIN users u ON s.user_id = u.id
             WHERE s.id = ?"
        );
        $stmt->execute([$id]);
        return $stmt->fetch();
    }

    public function create(int $medicine_id, int $customer_id, int $user_id, int $quantity): array {
        $stmt = $this->db->prepare("SELECT * FROM medicines WHERE id = ?");
        $stmt->execute([$medicine_id]);
        $medicine = $stmt->fetch();

        if (!$medicine) {
            return ['success' => false, 'message' => 'Medicine not found.'];
        }
        if ($medicine['quantity'] < $quantity) {
            return ['success' => false, 'message' => "Insufficient stock. Available: {$medicine['quantity']}"];
        }

        $total_price = $medicine['price'] * $quantity;

        $stmt = $this->db->prepare(
            "INSERT INTO sales (medicine_id, customer_id, user_id, quantity, total_price) VALUES (?, ?, ?, ?, ?)"
        );
        $stmt->execute([$medicine_id, $customer_id, $user_id, $quantity, $total_price]);

        $stmt = $this->db->prepare("UPDATE medicines SET quantity = quantity - ? WHERE id = ?");
        $stmt->execute([$quantity, $medicine_id]);

        $stmt = $this->db->prepare(
            "INSERT INTO stock (medicine_id, change_type, quantity, note) VALUES (?, 'OUT', ?, ?)"
        );
        $stmt->execute([$medicine_id, $quantity, "Sale #" . $this->db->lastInsertId()]);

        return ['success' => true, 'message' => 'Sale recorded successfully.', 'total' => $total_price];
    }

    public function getTodayRevenue(): float {
        $stmt = $this->db->query(
            "SELECT COALESCE(SUM(total_price), 0) as revenue FROM sales WHERE DATE(sale_date) = CURDATE()"
        );
        return (float)$stmt->fetch()['revenue'];
    }

    public function getTotalRevenue(): float {
        $stmt = $this->db->query("SELECT COALESCE(SUM(total_price), 0) as revenue FROM sales");
        return (float)$stmt->fetch()['revenue'];
    }

    public function getCount(): int {
        $stmt = $this->db->query("SELECT COUNT(*) as cnt FROM sales");
        return (int)$stmt->fetch()['cnt'];
    }

    public function getRecentSales(int $limit = 5): array {
        $stmt = $this->db->prepare(
            "SELECT s.*, m.name AS medicine_name, c.name AS customer_name
             FROM sales s
             LEFT JOIN medicines m ON s.medicine_id = m.id
             LEFT JOIN customers c ON s.customer_id = c.id
             ORDER BY s.sale_date DESC LIMIT ?"
        );
        $stmt->execute([$limit]);
        return $stmt->fetchAll();
    }

    public function getDailySales(int $days = 7): array {
        $stmt = $this->db->prepare(
            "SELECT DATE(sale_date) as day, COUNT(*) as count, SUM(total_price) as revenue
             FROM sales
             WHERE sale_date >= DATE_SUB(CURDATE(), INTERVAL ? DAY)
             GROUP BY DATE(sale_date)
             ORDER BY day ASC"
        );
        $stmt->execute([$days]);
        return $stmt->fetchAll();
    }

    public function getTopMedicines(int $limit = 5): array {
        $stmt = $this->db->prepare(
            "SELECT m.name, SUM(s.quantity) as total_sold, SUM(s.total_price) as revenue
             FROM sales s
             JOIN medicines m ON s.medicine_id = m.id
             GROUP BY s.medicine_id
             ORDER BY total_sold DESC LIMIT ?"
        );
        $stmt->execute([$limit]);
        return $stmt->fetchAll();
    }
}
