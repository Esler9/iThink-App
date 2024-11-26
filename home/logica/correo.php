<?php
session_start();
$User = $_SESSION["username"];
if(!isset($User)){
  header('location:../login.php');
}else{
 
  function enviar_todos_los_emails($conn) {
    $sql = "SELECT Usuarios.email FROM Usuarios JOIN send_email 
    ON Usuarios.Codigo = send_email.id_user 
    WHERE Usuarios.email_active = 1 
    AND send_email.active_send = 1 
    AND send_email.id_tipo_email = 1; ";
   
    $result = mysqli_query($conn, $sql);
    $emails = [];

    if ($result && mysqli_num_rows($result) > 0) {
        while ($row = mysqli_fetch_assoc($result)) {
            $emails[] = $row['email']; // Añade el email al array
        }
    }

    return $emails; // Retorna el array de emails
    
}

$correos = enviar_todos_los_emails($conn);

function enviar($destinatario,$asunto,$mensaje){
  
     // Cabeceras del correo
     $headers = "From: App iThink <app@ithinkguatemala.com>\r\n";
     $headers .= "Reply-To: app@ithinkguatemala.com\r\n";
     $headers .= "X-Mailer: PHP/" . phpversion() . "\r\n";
     $headers .= "MIME-Version: 1.0\r\n";
     $headers .= "Content-Type: text/html; charset=UTF-8\r\n"; // Cabecera para correo HTML
 
    
    // Enviar el correo
    $mailEnviado = mail($destinatario, $asunto, $mensaje, $headers);
    
    // Verificar si el correo se envió correctamente
    if ($mailEnviado) {
        echo "El correo se envió correctamente.";
    } else {
        echo "Error al enviar el correo.";
    }
    }}

?>