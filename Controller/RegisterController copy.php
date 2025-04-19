<?php
require __DIR__ . '/../Model/UserModel.php';

class RegisterController {
    private $userModel;

    public function __construct() {
        $this->userModel = new UserModel();
    }

    public function register() {
        if ($_SERVER["REQUEST_METHOD"] == "POST") {
            $nombre = trim($_POST['nombre']);
            $apellido = trim($_POST['apellido']);
            $email = trim($_POST['email']);
            $contrasena = $_POST['contrasena'];
            $confirmarContrasena = $_POST['confirmar-contrasena'];

            if (empty($nombre) || empty($apellido) || empty($email) || empty($contrasena) || empty($confirmarContrasena)) {
                echo "Todos los campos son obligatorios.";
                return;
            }

            if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                echo "Correo inválido.";
                return;
            }

            if ($contrasena !== $confirmarContrasena) {
                echo "Las contraseñas no coinciden.";
                return;
            }

            $resultado = $this->userModel->registerUser($nombre, $apellido, $email, $contrasena);
            if ($resultado === true) {
                header("Location: login.php?registro=exitoso");
                exit();
            } else {
                echo $resultado;
            }
        }
    }
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $controller = new RegisterController();
    $controller->register();
}
?>