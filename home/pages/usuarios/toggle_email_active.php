<?php
session_start();
include("../../../conexion.php");

// Solo permitir petición POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
  echo json_encode(['success' => false, 'message' => 'Método no permitido']);
  exit;
}

$codigo = isset($_POST['Codigo']) ? intval($_POST['Codigo']) : 0;
$email_active = isset($_POST['email_active']) ? intval($_POST['email_active']) : 0;

if ($codigo <= 0) {
  echo json_encode(['success' => false, 'message' => 'Código inválido']);
  exit;
}

// Obtener email del usuario
$stmt = $conn->prepare("SELECT email FROM `Usuarios` WHERE Codigo = ?");
$stmt->bind_param("i", $codigo);
$stmt->execute();
$res = $stmt->get_result();
if ($res->num_rows === 0) {
  echo json_encode(['success' => false, 'message' => 'Usuario no encontrado']);
  exit;
}
$row = $res->fetch_assoc();
$email = $row['email'];

// Actualizar campo email_active
$upd = $conn->prepare("UPDATE `Usuarios` SET email_active = ? WHERE Codigo = ?");
$upd->bind_param("ii", $email_active, $codigo);
if (!$upd->execute()) {
  echo json_encode(['success' => false, 'message' => 'Error al actualizar: '. $conn->error]);
  exit;
}

// Si se activó, ejemplo: enviar correo (ajusta cabeceras/plantilla)
// Nota: mail() requiere servidor SMTP configurado; reemplazar por tu servicio real si corresponde.
if ($email_active === 1 && !empty($email)) {
  $subject = "Notificaciones activadas";
  $message = "Hola,\n\nSe han activado las notificaciones por email para su cuenta.\n\nSaludos.";
  $headers = "From: no-reply@tudominio.com\r\n";
  // intentar enviar (no obligatorio)
  @mail($email, $subject, $message, $headers);
  $msg = 'Email activado. Se intentó enviar notificación.';
} else {
  $msg = 'Email desactivado.';
}

echo json_encode(['success' => true, 'message' => $msg]);
exit; 