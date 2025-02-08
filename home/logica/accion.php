<?php

use function PHPSTORM_META\sql_injection_subst;

include("diseño_correos.php");

session_start();
$User = $_SESSION["username"];
if (!isset($User)) {
  header('location:login.php');
} else {
  include("../../conexion.php");
  include('correo.php');
  include('funcion_logica.php');



  if (isset($_GET['accion'])) {

    $imei = $_GET['imei'];
    $accion = $_GET['accion'];
    $desc = $_POST['desc'];
    $sql = "";


    $sql = "SELECT * from liberacion where serie = '$imei' ";

    $consulta = mysqli_query($conn, $sql);
    $row_dt = mysqli_fetch_array($consulta);

    $dtimei = $row_dt['serie'];
    $dtname = $row_dt['Nombre_Cliente'];
    $dtcel = $row_dt['Celular'];
    $dtmodel = $row_dt['modelo'];
    $dtprecio = $row_dt['Precio'];
    $dttime = $row_dt['Tiempo'];
    $dtobs = $row_dt['Observaciones'];
    $date = date('Y-m-d H:i:s');
    $result = $_POST['resultado'];



    function Renviar_correo($correos, $correo, $imei, $body)
    {
      foreach ($correos as $correo) {
        enviar($correo, "$imei", $body);
      }
    }

    if ($accion == 0) {

      // Crea una Nueva Liberacion

      $imei = $_POST['c_imei'];
      $model = $_POST['model'];
      $name = $_POST['name'];
      $celular = $_POST['celular'];
      $cod_tienda = $_SESSION['tienda_user'];

      $sql = "SELECT COUNT(*) AS contar from liberacion where serie = $imei";
      $consulta =  mysqli_query($conn, $sql);
      $existe = mysqli_fetch_array($consulta);

      if ($existe["contar"] == 0) {

        $sql = "INSERT INTO `liberacion` (Nombre_Cliente, Celular, serie, modelo, cod_estado, cod_tienda, date_update, date) VALUES ('$name', '$celular', '$imei','$model',1,'$cod_tienda','$date','$date')";
        $consulta = mysqli_query($conn, $sql);

        // Envia Correo de Notificacion
        $datos = [
          'name'          => $name,       // Ejemplo: "Juan Pérez"
          'celular'       => $celular,    // Ejemplo: "5551234567"
          'imei'          => $imei,       // Ejemplo: "123456789012345"
          'modelo'        => $model,      // Ejemplo: "iPhone 12"
          // Los siguientes son opcionales, según el tipo de correo:
          'precio'        => $precio,     // Ejemplo: "$200"
          'tiempo'        => $tiempo,     // Ejemplo: "2 días"
          'observaciones' => $desc        // Ejemplo: "Ninguna observación adicional"
        ];

        $body = correo_enviar("consulta", $datos);
        Renviar_correo($correos, $correo, $imei, $body);

        // redirige a Crear Notificando la creacion de la Nueva lIberacion

        header("location:../pages/tables/crear.php?alert=0&imei=$imei&model=$model&name=$name&celular=$celular");
      } else {
        header("location:../pages/tables/crear.php?alert=33&imei=$imei");
      }
    } elseif ($accion == 1) {

      // Cambia el estado de la Liberacion a Pendiente con la Informacion necesaria
      $precio = $_POST['precio'];
      $tiempo = $_POST['tiempo'];


      $sql = "UPDATE `liberacion` SET `cod_estado` = '2', `Precio` = '$precio', `Tiempo` = '$tiempo', `Observaciones` = '$desc' , `date_update` = '$date' WHERE `serie` = '$imei'; ";
      $consulta = mysqli_query($conn, $sql);



      // Envia Correo de Notificacion

      $body = correo_enviar("consulta", $name, $imei, $model, $celular);
      Renviar_correo($correos, $correo, $imei, $body);
      //envia los Correos

      Renviar_correo($correos, $correo, $imei, $body);

      header("location:../pages/tables/consultas.php?alert=1&imei=$imei");
    } elseif ($accion == 5.1) {

      $sql = "UPDATE `liberacion` SET `cod_estado` = '6',  `Observaciones` = '$desc' , `date_update` = '$date' WHERE `serie` = '$imei'; ";
      $consulta = mysqli_query($conn, $sql);

      // Envia Correo de Notificacion

      $datos = [
        'name'          => $name,       // Ejemplo: "Juan Pérez"
        'celular'       => $celular,    // Ejemplo: "5551234567"
        'imei'          => $imei,       // Ejemplo: "123456789012345"
        'modelo'        => $model,      // Ejemplo: "iPhone 12"
        // Los siguientes son opcionales, según el tipo de correo:
        'precio'        => $precio,     // Ejemplo: "$200"
        'tiempo'        => $tiempo,     // Ejemplo: "2 días"
        'observaciones' => $desc        // Ejemplo: "Ninguna observación adicional"
      ];

      $body = correo_enviar("rechazado", $datos);

      //envia los Correos

      Renviar_correo($correos, $correo, $imei, $body);
      header("location:../pages/tables/consultas.php?alert=5&imei=$imei");
    } elseif ($accion == 2) {
      // Cambia el estado de Liberacion a Aprobada

      $sql = "UPDATE `liberacion` SET `cod_estado` = '3',  `Observaciones` = '$desc' , `date_update` = '$date' WHERE `serie` = '$imei'; ";
      $consulta = mysqli_query($conn, $sql);

      // Envia Correo de Notificacion

      $datos = [
        'name'          => $name,       // Ejemplo: "Juan Pérez"
        'celular'       => $celular,    // Ejemplo: "5551234567"
        'imei'          => $imei,       // Ejemplo: "123456789012345"
        'modelo'        => $model,      // Ejemplo: "iPhone 12"
        // Los siguientes son opcionales, según el tipo de correo:
        'precio'        => $precio,     // Ejemplo: "$200"
        'tiempo'        => $tiempo,     // Ejemplo: "2 días"
        'observaciones' => $desc        // Ejemplo: "Ninguna observación adicional"
      ];

      $body = correo_enviar("pendiente", $datos);

      //envia los Correos

      Renviar_correo($correos, $correo, $imei, $body);
      header("location:../pages/tables/pendientes.php?alert=2&imei=$imei");
    } elseif ($accion == 2.1) {

      // Cambia el estado de la Liberacion  para Reiniciar Consulta
      $precio = $_POST['precio'];
      $tiempo = $_POST['tiempo'];
      $sql = "UPDATE `liberacion` SET `cod_estado` = '1', `Precio` = '$precio', `Tiempo` = '$tiempo', `Observaciones` = '$desc' , `date_update` = '$date' WHERE `serie` = '$imei'; ";
      $consulta = mysqli_query($conn, $sql);

      // Envia Correo de Notificacion

      $body = "

       *¡Nueva Consulta de Liberación: Consulta Reinicia da por Sistema, se Solicita nuevamente Información!*

       ----------------------------------------------------
       se detallan los datos asociados de Liberacion:
    
       - Nombre: $dtname
       - Celular: $dtcel
       - IMEI: $dtimei
       - Modelo: $dtmodel
       - Precio: $dtprecio
       - Tiempo: $dttime
       - Observaciones: $dtobs
       -----------------------------------------------------
    
       En el Siguiente Link Puedes realizar la Accion Necesaria:
    
       https://app.ithinkguatemala.com/home/pages/tables/buscar.php?search=$imei
    
       ¡Gracias!
           
       Este es un Correo Automatico no responder.

       Codigo: 2.1
       ";

      //envia los Correos

      Renviar_correo($correos, $correo, $imei, $body);

      header("location:../pages/tables/rechazadas.php?alert=2.1&imei=$imei");
    } elseif ($accion == 5.2) {

      $sql = "UPDATE `liberacion` SET `cod_estado` = '6',  `Observaciones` = '$desc' , `date_update` = '$date' WHERE `serie` = '$imei'; ";
      $consulta = mysqli_query($conn, $sql);

      // Envia Correo de Notificacion

      $body = "
   *¡Liberacion Rechazada:!*
   ----------------------------------------------------
   se detallan los datos asociados:

   - Nombre: $dtname
   - Celular: $dtcel
   - IMEI: $dtimei
   - Modelo: $dtmodel
   - Precio: $dtprecio
   - Tiempo: $dttime
   - Observaciones: $dtobs
   -----------------------------------------------------

   En el Siguiente Link Puedes realizar la Accion Necesaria:

   https://app.ithinkguatemala.com/home/pages/tables/buscar.php?search=$imei

   ¡Gracias!
       
   Este es un Correo Automatico no responder.

   Codigo: 5.2
   ";

      //envia los Correos

      Renviar_correo($correos, $correo, $imei, $body);
      header("location:../pages/tables/pendientes.php?alert=5&imei=$imei");
    } elseif ($accion == 5.3) {

      // cambia el estado de la lIberacion a rechazado en Aprobados

      $sql = "UPDATE `liberacion` SET `cod_estado` = '6',  `Observaciones` = '$desc' , `date_update` = '$date' WHERE `serie` = '$imei'; ";
      $consulta = mysqli_query($conn, $sql);

      // Envia Correo de Notificacion

      $body = "

         *¡Liberacion Rechazada desde Aprobadas:!*
  
         ----------------------------------------------------
         se detallan los datos asociados de Liberacion:
      
         - Nombre: $dtname
         - Celular: $dtcel
         - IMEI: $dtimei
         - Modelo: $dtmodel
         - Precio: $dtprecio
         - Tiempo: $dttime
         - Observaciones: $dtobs
         -----------------------------------------------------
      
         En el Siguiente Link Puedes realizar la Accion Necesaria:
      
         https://app.ithinkguatemala.com/home/pages/tables/buscar.php?search=$imei
      
         ¡Gracias!
             
         Este es un Correo Automatico no responder.

         Codigo: 5.3
         ";

      //envia los Correos

      Renviar_correo($correos, $correo, $imei, $body);

      header("location:../pages/tables/aprobadas.php?alert=5&imei=$imei");
    } elseif ($accion == 5.4) {
      // cambia el estado de la liberacion a rechazada en Finalizadas
      $sql = "UPDATE `liberacion` SET `cod_estado` = '6',  `Observaciones` = '$desc' , `date_update` = '$date' WHERE `serie` = '$imei'; ";
      $consulta = mysqli_query($conn, $sql);

      // Envia Correo de Notificacion

      $body = "

       *¡Liberacion Rechazada desde Finalizadas:!*

       ----------------------------------------------------
       se detallan los datos asociados de Liberacion:
    
       - Nombre: $dtname
       - Celular: $dtcel
       - IMEI: $dtimei
       - Modelo: $dtmodel
       - Precio: $dtprecio
       - Tiempo: $dttime
       - Observaciones: $dtobs
       -----------------------------------------------------
    
       En el Siguiente Link Puedes realizar la Accion Necesaria:
    
       https://app.ithinkguatemala.com/home/pages/tables/buscar.php?search=$imei
    
       ¡Gracias!
           
       Este es un Correo Automatico no responder.

       Codigo: 5.4
       ";

      //envia los Correos

      Renviar_correo($correos, $correo, $imei, $body);

      header("location:../pages/tables/finalizadas.php?alert=5&imei=$imei");
    } elseif ($accion == 3) {

      $sql = "UPDATE `liberacion` SET `cod_estado` = '4',  `Observaciones` = '$desc' , `date_update` = '$date' WHERE `serie` = '$imei'; ";
      $consulta = mysqli_query($conn, $sql);

      // Envia Correo de Notificacion

      $body = "

         *¡Inicio de Proceso de Liberación:!*
  
         ----------------------------------------------------
         se detallan los datos asociados de Liberacion:
      
         - Nombre: $dtname
         - Celular: $dtcel
         - IMEI: $dtimei
         - Modelo: $dtmodel
         - Precio: $dtprecio
         - Tiempo: $dttime
         - Observaciones: $dtobs
         -----------------------------------------------------
      
         En el Siguiente Link Puedes realizar la Accion Necesaria:
      
         https://app.ithinkguatemala.com/home/pages/tables/buscar.php?search=$imei
      
         ¡Gracias!
             
         Este es un Correo Automatico no responder.

         Codigo: 3
         ";

      //envia los Correos

      Renviar_correo($correos, $correo, $imei, $body);
      header("location:../pages/tables/aprobadas.php?alert=3&imei=$imei");
    } elseif ($accion == 4) {

      $sql = "UPDATE `liberacion` SET `cod_estado` = '$result',  `Observaciones` = '$desc' , `date_update` = '$date' WHERE `serie` = '$imei'; ";
      $consulta = mysqli_query($conn, $sql);

      // Envia Correo de Notificacion

      $body = "

         *¡Finalizado el Proceso de Liberacion:!*
        
         Notificar a Cliente de Finalizacion
         ----------------------------------------------------
         se detallan los datos asociados de Liberacion:
      
         - Nombre: $dtname
         - Celular: $dtcel
         - IMEI: $dtimei
         - Modelo: $dtmodel
         - Precio: $dtprecio
         - Tiempo: $dttime
         - Observaciones: $dtobs
         -----------------------------------------------------

         recordar que Cliente debe quitar cuenta de Icloud previo a ingresar SIM
      
         En el Siguiente Link Puedes realizar la Accion Necesaria:
      
         https://app.ithinkguatemala.com/home/pages/tables/buscar.php?search=$imei
      
         ¡Gracias!

         Este es un Correo Automatico no responder.

         Codigo: 4
             
         ";

      //envia los Correos

      Renviar_correo($correos, $correo, $imei, $body);
      header("location:../pages/tables/aprobadas.php?alert=4&imei=$imei");
    } else {
      echo "Error";
    }
  } else {
  }
}
