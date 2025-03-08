<?php
session_start();
if (!isset($_SESSION["username"])) {
    header('location:login.php');
    exit();
}

include("../../conexion.php");
include('correo.php');          // Debe contener la función correo_enviar()
include('funcion_logica.php');
include("diseño_correos.php");

// Función para enviar el correo a cada destinatario (se asume que $correos se obtiene en otro archivo)
function reenviarCorreo($listaCorreos, $imei, $body) {
    foreach ($listaCorreos as $destinatario) {
        enviar($destinatario, $imei, $body);
    }
}

// Se asume que la variable $correos se obtiene desde el archivo de envío de emails
// Si no está definida, se podría obtener con una función como: $correos = enviar_todos_los_emails($conn);

if (isset($_GET['accion'])) {

    $accion = $_GET['accion'];
    $imei   = $_GET['imei'];
    $desc   = isset($_POST['desc']) ? $_POST['desc'] : "";
    $date   = date('Y-m-d H:i:s');
    $result = isset($_POST['resultado']) ? $_POST['resultado'] : "";

    // Consulta para obtener datos de la liberación (si ya existe)
    $sql = "SELECT * FROM liberacion WHERE serie = '$imei'";
    $consulta = mysqli_query($conn, $sql);
    $row_dt = mysqli_fetch_array($consulta);

    // Variables obtenidas de la base de datos (si existen)
    $dtname  = $row_dt['Nombre_Cliente'] ?? "";
    $dtcel   = $row_dt['Celular'] ?? "";
    $dtmodel = $row_dt['modelo'] ?? "";
    $dtprecio= $row_dt['Precio'] ?? "";
    $dttime  = $row_dt['Tiempo'] ?? "";
    $dtobs   = $row_dt['Observaciones'] ?? "";

    // Arreglo común con los datos para la notificación por correo
    $datosCommon = [
        'name'          => $dtname,
        'celular'       => $dtcel,
        'imei'          => $imei,
        'modelo'        => $dtmodel,
        'precio'        => $dtprecio,
        'tiempo'        => $dttime,
        'observaciones' => $desc
    ];

    switch ($accion) {

        case "0":
            // Creación de una nueva liberación
            $imei    = $_POST['c_imei'];
            $model   = $_POST['model'];
            $name    = $_POST['name'];
            $celular = $_POST['celular'];
            $cod_tienda = $_SESSION['tienda_user'];

            // Verificar si ya existe la liberación
            $sql = "SELECT COUNT(*) AS contar FROM liberacion WHERE serie = '$imei'";
            $consulta = mysqli_query($conn, $sql);
            $existe = mysqli_fetch_array($consulta);

            if ($existe["contar"] == 0) {
                $sql = "INSERT INTO liberacion (Nombre_Cliente, Celular, serie, modelo, cod_estado, cod_tienda, date_update, date) 
                        VALUES ('$name', '$celular', '$imei', '$model', 1, '$cod_tienda', '$date', '$date')";
                mysqli_query($conn, $sql);

                $datos = [
                    'name'          => $name,
                    'celular'       => $celular,
                    'imei'          => $imei,
                    'modelo'        => $model,
                    'precio'        => "",
                    'tiempo'        => "",
                    'observaciones' => $desc
                ];
                $body = correo_enviar("consulta", $datos);
                reenviarCorreo($correos, $imei, $body);

                header("location:../Liberaciones/tables/crear.php?alert=0&imei=$imei&model=$model&name=$name&celular=$celular");
            } else {
                header("location:../Liberaciones/tables/crear.php?alert=33&imei=$imei");
            }
            break;

        case "1":
            // Cambio de estado a Pendiente
            $precio = $_POST['precio'];
            $tiempo = $_POST['tiempo'];
            $sql = "UPDATE liberacion SET cod_estado = '2', Precio = '$precio', Tiempo = '$tiempo', Observaciones = '$desc', date_update = '$date' WHERE serie = '$imei'";
            mysqli_query($conn, $sql);

            $datos = array_merge($datosCommon, [
                'precio'        => $precio,
                'tiempo'        => $tiempo,
                'observaciones' => $desc
            ]);
            $body = correo_enviar("pendiente", $datos);
            reenviarCorreo($correos, $imei, $body);
            header("location:../Liberaciones/tables/consultas.php?alert=1&imei=$imei");
            break;

        case "5.1":
            // Liberación rechazada (caso 5.1)
            $sql = "UPDATE liberacion SET cod_estado = '6', Observaciones = '$desc', date_update = '$date' WHERE serie = '$imei'";
            mysqli_query($conn, $sql);
            $body = correo_enviar("rechazado", $datosCommon);
            reenviarCorreo($correos, $imei, $body);
            header("location:../Liberaciones/tables/consultas.php?alert=5&imei=$imei");
            break;

        case "2":
            // Liberación aprobada
            $sql = "UPDATE liberacion SET cod_estado = '3', Observaciones = '$desc', date_update = '$date' WHERE serie = '$imei'";
            mysqli_query($conn, $sql);
            $body = correo_enviar("aprobado", $datosCommon);
            reenviarCorreo($correos, $imei, $body);
            header("location:../Liberaciones/tables/pendientes.php?alert=2&imei=$imei");
            break;

        case "2.1":
            // Reinicio de Consulta
            $precio = $_POST['precio'];
            $tiempo = $_POST['tiempo'];
            $sql = "UPDATE liberacion SET cod_estado = '1', Precio = '$precio', Tiempo = '$tiempo', Observaciones = '$desc', date_update = '$date' WHERE serie = '$imei'";
            mysqli_query($conn, $sql);
            $datos = array_merge($datosCommon, [
                'precio'        => $precio,
                'tiempo'        => $tiempo,
                'observaciones' => $desc
            ]);
            $body = correo_enviar("consultaReiniciada", $datos);
            reenviarCorreo($correos, $imei, $body);
            header("location:../Liberaciones/tables/rechazadas.php?alert=2.1&imei=$imei");
            break;

        case "5.2":
            // Liberación rechazada (variante 5.2)
            $sql = "UPDATE liberacion SET cod_estado = '6', Observaciones = '$desc', date_update = '$date' WHERE serie = '$imei'";
            mysqli_query($conn, $sql);
            $body = correo_enviar("rechazado", $datosCommon);
            reenviarCorreo($correos, $imei, $body);
            header("location:../Liberaciones/tables/pendientes.php?alert=5&imei=$imei");
            break;

        case "5.3":
            // Liberación rechazada desde Aprobadas
            $sql = "UPDATE liberacion SET cod_estado = '6', Observaciones = '$desc', date_update = '$date' WHERE serie = '$imei'";
            mysqli_query($conn, $sql);
            $body = correo_enviar("rechazado", $datosCommon);
            reenviarCorreo($correos, $imei, $body);
            header("location:../Liberaciones/tables/aprobadas.php?alert=5&imei=$imei");
            break;

        case "5.4":
            // Liberación rechazada desde Finalizadas
            $sql = "UPDATE liberacion SET cod_estado = '6', Observaciones = '$desc', date_update = '$date' WHERE serie = '$imei'";
            mysqli_query($conn, $sql);
            $body = correo_enviar("rechazada", $datosCommon);
            reenviarCorreo($correos, $imei, $body);
            header("location:../Liberaciones/tables/finalizadas.php?alert=5&imei=$imei");
            break;

        case "3":
            // Inicio del proceso de liberación
            $sql = "UPDATE liberacion SET cod_estado = '4', Observaciones = '$desc', date_update = '$date' WHERE serie = '$imei'";
            mysqli_query($conn, $sql);
            $body = correo_enviar("iniciado", $datosCommon);
            reenviarCorreo($correos, $imei, $body);
            header("location:../Liberaciones/tables/aprobadas.php?alert=3&imei=$imei");
            break;

        case "4":
            // Finalización del proceso de liberación
            $sql = "UPDATE liberacion SET cod_estado = '$result', Observaciones = '$desc', date_update = '$date' WHERE serie = '$imei'";
            mysqli_query($conn, $sql);
            $body = correo_enviar("finalizado", $datosCommon);
            reenviarCorreo($correos, $imei, $body);
            header("location:../Liberaciones/tables/aprobadas.php?alert=4&imei=$imei");
            break;

        default:
            echo "Error: Acción no reconocida.";
            break;
    }
} else {
    echo "No se recibió acción.";
}
?>
