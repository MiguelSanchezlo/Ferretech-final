<?php

class UsuarioModel
{
  private $pdo;

  public function __construct($pdo)
  {
    $this->pdo = $pdo;
  }

  public function obtenerUsuarioPorId($id)
  {
    $stmt = $this->pdo->prepare("SELECT nombre, apellido, email FROM usuarios WHERE id = ?");
    $stmt->execute([$id]);
    return $stmt->fetch(PDO::FETCH_ASSOC);
  }

  public function actualizarNombreApellido($id, $nombre, $apellido)
  {
    $stmt = $this->pdo->prepare("UPDATE usuarios SET nombre = ?, apellido = ? WHERE id = ?");
    return $stmt->execute([$nombre, $apellido, $id]);
  }

  public function verificarPassword($id, $password)
  {
    $stmt = $this->pdo->prepare("SELECT password FROM usuarios WHERE id = ?");
    $stmt->execute([$id]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    return $user && password_verify($password, $user['password']);
  }

  public function actualizarPassword($id, $nuevaPassword)
  {
    $hash = password_hash($nuevaPassword, PASSWORD_DEFAULT);
    $stmt = $this->pdo->prepare("UPDATE usuarios SET password = ? WHERE id = ?");
    
    return $stmt->execute([$hash, $id]);
  }
}
