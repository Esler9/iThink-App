<?php
session_start();
if (!isset($_SESSION["username"])) {
    header('location:../login.php');
    exit();
}

include("../../conexion.php");

/**
 * Función que obtiene todos los correos a los que se debe enviar la notificación.
 *
 * @param mysqli $conn Conexión a la base de datos.
 * @return array Arreglo con las direcciones de correo.
 */
function enviar_todos_los_emails($conn) {
    $sql = "SELECT Usuarios.email FROM Usuarios 
            JOIN send_email ON Usuarios.Codigo = send_email.id_user 
            WHERE Usuarios.email_active = 1 
              AND send_email.active_send = 1 
              AND send_email.id_tipo_email = 1;";
   
    $result = mysqli_query($conn, $sql);
    $emails = [];

    if ($result && mysqli_num_rows($result) > 0) {
        while ($row = mysqli_fetch_assoc($result)) {
            $emails[] = $row['email'];
        }
    } else {
        error_log("No se encontraron correos en la consulta: " . mysqli_error($conn));
    }
    
    // Registro para verificar los emails obtenidos.
    error_log("Emails obtenidos: " . implode(", ", $emails));
    
    return $emails;
}

$correos = enviar_todos_los_emails($conn);

/**
 * Función para enviar correo en formato HTML.
 *
 * @param string $destinatario Correo electrónico del destinatario.
 * @param string $asunto       Asunto del correo.
 * @param string $mensaje      Cuerpo del mensaje en HTML.
 */
function enviar($destinatario, $asunto, $mensaje) {
    // Cabeceras del correo
    $headers = "From: App iThink <app@ithinkguatemala.com>\r\n";
    $headers .= "Reply-To: app@ithinkguatemala.com\r\n";
    $headers .= "X-Mailer: PHP/" . phpversion() . "\r\n";
    $headers .= "MIME-Version: 1.0\r\n";
    $headers .= "Content-Type: text/html; charset=UTF-8\r\n";
    
    // Registro para depuración antes de enviar
    error_log("Intentando enviar correo a: $destinatario con asunto: $asunto");
    
    $mailEnviado = mail($destinatario, $asunto, $mensaje, $headers);
    
    if ($mailEnviado) {
        error_log("El correo se envió correctamente a: $destinatario");
    } else {
        error_log("Error al enviar el correo a: $destinatario");
    }
}
?>
