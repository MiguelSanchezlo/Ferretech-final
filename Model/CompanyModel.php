<?php
class CompanyModel
{
    private $pdo;

    public function __construct($pdo)
    {
        $this->pdo = $pdo;
    }

    public function registerCompany($nombre_empresa, $NIT, $tipo_negocio, $direccion, $ciudad, $codigo_postal, $nombre_contacto, $email_contacto, $telefono, $password, $descripcion, $latitud, $longitud) {
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);
    
        // Verificar si NIT o correo ya existen
        $check = $this->pdo->prepare("SELECT id FROM empresas WHERE NIT = ? OR email_contacto = ?");
        $check->execute([$NIT, $email_contacto]);
        if ($check->fetch()) {
            echo "❌ NIT o correo ya registrados.";
            return false;
        }
    
        // Intentar insertar
        $sql = "INSERT INTO empresas (
                    nombre_empresa, NIT, tipo_negocio, direccion, ciudad, codigo_postal,
                    nombre_contacto, email_contacto, telefono, password, descripcion, latitud, longitud
                ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
    
        $stmt = $this->pdo->prepare($sql);
        $success = $stmt->execute([
            $nombre_empresa,
            $NIT,
            $tipo_negocio,
            $direccion,
            $ciudad,
            $codigo_postal,
            $nombre_contacto,
            $email_contacto,
            $telefono,
            $hashed_password,
            $descripcion,
            $latitud,
            $longitud
        ]);
    
        if (!$success) {
            $error = $stmt->errorInfo();
            echo "❌ Error SQL: " . $error[2];
            return false;
        }
    
        return true;
    }
    

    public function loginCompany($email_contacto, $password)
    {
        $sql = "SELECT id, password FROM empresas WHERE email_contacto = ?";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$email_contacto]);
        $company = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($company && password_verify($password, $company['password'])) {
            return $company;
        }

        return false;
    }
}
