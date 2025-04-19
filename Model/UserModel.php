<?php
require __DIR__ . "/../Config/connection.php";

class UserModel {
    private $db;

    public function __construct() {
        global $conn;

        if (!$conn) {
            die("Error: No se pudo conectar a la base de datos.");
        }

        $this->db = $conn;
    }

    public function registerUser($nombre, $apellido, $email, $contrasena) {
        global $conn;
        $hashed_password = password_hash($contrasena, PASSWORD_DEFAULT);
        $stmt = $conn->prepare("INSERT INTO usuarios (nombre, apellido, email, contrasena) VALUES (?, ?, ?, ?)");
        if ($stmt) {
            $stmt->bind_param("ssss", $nombre, $apellido, $email, $hashed_password);
            $stmt->execute();
            $stmt->close();
        }
    }
}
?>