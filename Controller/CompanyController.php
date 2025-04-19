<?php
require_once __DIR__ . '/../Model/CompanyModel.php';
require_once __DIR__ . '/../Config/connection.php'; // Aquí debe estar definido $pdo

class CompanyController
{
    private $model;

    public function __construct($pdo)
    {
        $this->model = new CompanyModel($pdo);
    }

    public function register()
    {
        if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['registerCompany'])) {
            $nombre_empresa    = $_POST['nombre_empresa'];
            $NIT               = $_POST['nit'];
            $tipo_negocio      = $_POST['tipo_negocio'];
            $direccion         = $_POST['direccion'];
            $ciudad            = $_POST['ciudad'];
            $codigo_postal     = $_POST['codigo_postal'];
            $nombre_contacto   = $_POST['nombre_contacto'];
            $email_contacto    = $_POST['email_contacto'];
            $telefono          = $_POST['telefono'];
            $password          = $_POST['password'];
            $confirm_password  = $_POST['confirm_password'];
            $descripcion       = $_POST['descripcion'] ?? "";
            $latitud           = $_POST['latitud'] ?? null;
            $longitud          = $_POST['longitud'] ?? null;


            // Validaciones básicas
            if ($password !== $confirm_password) {
                die("Error: Las contraseñas no coinciden.");
            }

            if (!filter_var($email_contacto, FILTER_VALIDATE_EMAIL)) {
                die("Error: El correo no es válido.");
            }

            $registro = $this->model->registerCompany(
                $nombre_empresa,
                $NIT,
                $tipo_negocio,
                $direccion,
                $ciudad,
                $codigo_postal,
                $nombre_contacto,
                $email_contacto,
                $telefono,
                $password,
                $descripcion,
                $latitud,
                $longitud
            );

            if ($registro) {
                header("Location: registercompany.php?success=1");
                exit();
            } else {
                echo "Error: Al registrar controller.";
            }
        }
    }
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $controller = new CompanyController($pdo);
    $controller->register();
}
