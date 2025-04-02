<?php
include 'conexion.php';

$correo = $_POST['correo'];
$contrasena = $_POST['contrasena'];

// Validar formato de contraseña (mínimo 6 caracteres seguros)
if (!preg_match('/^[a-zA-Z0-9!@#$%^&*()_\-+=\[\]{}|\\:;"\'<>,.?\/~`]{6,}$/', $contrasena)) {
    header("Location: index.php?login=contrasena_invalida");
    exit();
}

$stmt = $conn->prepare("SELECT * FROM usuario WHERE correo = ?");
$stmt->bind_param("s", $correo);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    // Usuario no encontrado
    header("Location: index.php?login=usuario_no_existe");
    exit();
}

$user = $result->fetch_assoc();
$hashAlmacenado = $user['contrasena'];

if (password_verify($contrasena, $hashAlmacenado)) {
    setcookie("usuario_logueado", $correo, time() + (30 * 24 * 60 * 60), "/");
    header("Location: area_personal.php");
    exit();
}

// Contraseña incorrecta
header("Location: index.php?login=error");
exit();
?>
