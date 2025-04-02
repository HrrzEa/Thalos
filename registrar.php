<?php
// Incluir la conexión a la base de datos
include("conexion.php");

// Verificamos que el formulario fue enviado por método POST
if ($_SERVER["REQUEST_METHOD"] == "POST") {
  // Recoger y limpiar datos del formulario
  $correo = trim($_POST['correo']);
  $contrasena = password_hash($_POST['contrasena'], PASSWORD_BCRYPT); // Encriptamos la contraseña
  $nombre = trim($_POST['nombre']);
  $taller = trim($_POST['taller']);

  // Comprobamos si ya existe un usuario con ese correo
  $verificar = $conn->prepare("SELECT id FROM usuario WHERE correo = ?");
  if (!$verificar) {
    header("Location: index.html?registro=error");
    exit;
  }

  $verificar->bind_param("s", $correo);
  $verificar->execute();
  $verificar->store_result();

  if ($verificar->num_rows > 0) {
    // Ya existe un usuario con ese correo
    $verificar->close();
    header("Location: index.html?registro=correo_repetido");
    exit;
  }
  $verificar->close();

  // Insertamos el nuevo usuario
  $sql = "INSERT INTO usuario (correo, contrasena, nombre, nombre_taller) VALUES (?, ?, ?, ?)";
  $stmt = $conn->prepare($sql);
  if (!$stmt) {
    header("Location: index.html?registro=error");
    exit;
  }

  $stmt->bind_param("ssss", $correo, $contrasena, $nombre, $taller);

  if ($stmt->execute()) {
    // Registro exitoso, redirige al index con mensaje
    $stmt->close();
    $conn->close();
    header("Location: index.html?registro=exito");
    exit;
  } else {
    $stmt->close();
    $conn->close();
    header("Location: index.html?registro=error");
    exit;
  }
}
?>
