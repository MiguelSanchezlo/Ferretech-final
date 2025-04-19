<?php
require_once 'database.php';

class Order {
    private $db;

    public function __construct($db) {
        $this->db = $db;
    }

    public function createOrder($user_id, $total_price) {
        $stmt = $this->db->prepare("INSERT INTO orders (user_id, total_price) VALUES (?, ?)");
        $stmt->bind_param("id", $user_id, $total_price);
        return $stmt->execute();
    }

    public function getOrdersByUser($user_id) {
        $stmt = $this->db->prepare("SELECT * FROM orders WHERE user_id = ?");
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }
}
?>
