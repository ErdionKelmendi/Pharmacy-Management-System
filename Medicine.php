<?php
require_once __DIR__ . '/../config/Database.php';

class Medicine {
    private PDO $db;

    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
    }

    public function getAll(): array {
        $stmt = $this->db->query("SELECT * FROM medicines ORDER BY name ASC");
        return $stmt->fetchAll();
    }

    public function getById(int $id): array|false {
        $stmt = $this->db->prepare("SELECT * FROM medicines WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch();
    }

    public function create(string $name, float $price, int $quantity, string $expiry_date): bool {
        $stmt = $this->db->prepare(
            "INSERT INTO medicines (name, price, quantity, expiry_date) VALUES (?, ?, ?, ?)"
        );
        $result = $stmt->execute([$name, $price, $quantity, $expiry_date]);

        if ($result) {
            $medId = $this->db->lastInsertId();
            $this->logStock((int)$medId, 'IN', $quantity, 'Initial stock on creation');
        }
        return $result;
    }

    public function update(int $id, string $name, float $price, int $quantity, string $expiry_date): bool {
        $old = $this->getById($id);
        $diff = $quantity - (int)$old['quantity'];

        $stmt = $this->db->prepare(
            "UPDATE medicines SET name=?, price=?, quantity=?, expiry_date=? WHERE id=?"
        );
        $result = $stmt->execute([$name, $price, $quantity, $expiry_date, $id]);

        if ($result && $diff !== 0) {
            $type = $diff > 0 ? 'IN' : 'OUT';
            $this->logStock($id, $type, abs($diff), 'Manual stock adjustment');
        }
        return $result;
    }

    public function delete(int $id): bool {
        $stmt = $this->db->prepare("DELETE FROM medicines WHERE id = ?");
        return $stmt->execute([$id]);
    }

    public function search(string $query): array {
        $stmt = $this->db->prepare("SELECT * FROM medicines WHERE name LIKE ? ORDER BY name ASC");
        $stmt->execute(["%$query%"]);
        return $stmt->fetchAll();
    }

    public function getLowStock(int $threshold = 10): array {
        $stmt = $this->db->prepare("SELECT * FROM medicines WHERE quantity <= ? ORDER BY quantity ASC");
        $stmt->execute([$threshold]);
        return $stmt->fetchAll();
    }

    public function getExpiringSoon(int $days = 30): array {
        $stmt = $this->db->prepare(
            "SELECT * FROM medicines WHERE expiry_date <= DATE_ADD(CURDATE(), INTERVAL ? DAY) AND expiry_date >= CURDATE() ORDER BY expiry_date ASC"
        );
        $stmt->execute([$days]);
        return $stmt->fetchAll();
    }

    public function getTotalValue(): float {
        $stmt = $this->db->query("SELECT SUM(price * quantity) as total FROM medicines");
        $row = $stmt->fetch();
        return (float)($row['total'] ?? 0);
    }

    public function getCount(): int {
        $stmt = $this->db->query("SELECT COUNT(*) as cnt FROM medicines");
        return (int)$stmt->fetch()['cnt'];
    }

    private function logStock(int $medicineId, string $type, int $quantity, string $note): void {
        $stmt = $this->db->prepare(
            "INSERT INTO stock (medicine_id, change_type, quantity, note) VALUES (?, ?, ?, ?)"
        );
        $stmt->execute([$medicineId, $type, $quantity, $note]);
    }
}
