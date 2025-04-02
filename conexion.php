<?php
$host = "localhost";
$usuario = "root";
$contrasena = "root";
$base_de_datos = "ia_talleres"; // Asegúrate de que esta base de datos exista

$conn = new mysqli($host, $usuario, $contrasena, $base_de_datos);

// Verifica la conexión
if ($conn->connect_error) {
  die("Error de conexión: " . $conn->connect_error);
}
?>
