<?php
session_start();
if (!isset($_SESSION["username"])) {
    header('location:login.php');
    exit();
}

include("../../conexion.php");
include('correo.php');             // Contiene la función correo_enviar()
include('funcion_logica.php');
include("diseño_correos.php");

// Se asume que $correos es un arreglo con las direcciones de correo destino.
// Si no está definido, puedes definirlo aquí:
$correos = ["correo1@example.com", "correo2@example.com"]; // Ajusta según corresponda

/**
 * Función para enviar el correo a cada destinatario.
 *
 * @param array  $listaCorreos Arreglo con las direcciones de correo.
 * @param string $imei         IMEI (o identificador) asociado.
 * @param string $body         Cuerpo del correo.
 */
function reenviar_correo($listaCorreos, $imei, $body) {
    foreach ($listaCorreos as $correo) {
        enviar($correo, $imei, $body);
    }
}

if (isset($_GET['accion'])) {
    $accion = $_GET['accion'];
    $imei   = $_GET['imei'];
    $desc   = isset($_POST['desc']) ? $_POST['desc'] : "";
    $date   = date('Y-m-d H:i:s');
    $result = isset($_POST['resultado']) ? $_POST['resultado'] : "";

    // Obtener datos de la liberación (si ya existe) desde la base de datos.
    $sql = "SELECT * FROM liberacion WHERE serie = '$imei'";
    $consulta = mysqli_query($conn, $sql);
    $row_dt = mysqli_fetch_array($consulta);

    // Datos comunes obtenidos de la base de datos.
    $datosCommon = [
        'name'          => $row_dt['Nombre_Cliente'] ?? "",
        'celular'       => $row_dt['Celular'] ?? "",
        'imei'          => $imei,
        'modelo'        => $row_dt['modelo'] ?? "",
        'precio'        => $row_dt['Precio'] ?? "",
        'tiempo'        => $row_dt['Tiempo'] ?? "",
        'observaciones' => $desc
    ];

    switch ($accion) {
        case "0": 
            // Crear una nueva liberación.
            $imei    = $_POST['c_imei'];
            $model   = $_POST['model'];
            $name    = $_POST['name'];
            $celular = $_POST['celular'];
            $cod_tienda = $_SESSION['tienda_user'];

            // Verificar si ya existe la liberación.
            $sql = "SELECT COUNT(*) AS contar FROM liberacion WHERE serie = '$imei'";
            $consulta = mysqli_query($conn, $sql);
            $existe = mysqli_fetch_array($consulta);

            if ($existe["contar"] == 0) {
                $sql = "INSERT INTO liberacion (Nombre_Cliente, Celular, serie, modelo, cod_estado, cod_tienda, date_update, date) 
                        VALUES ('$name', '$celular', '$imei', '$model', 1, '$cod_tienda', '$date', '$date')";
                mysqli_query($conn, $sql);

                // Datos para el correo de nueva consulta.
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
                reenviar_correo($correos, $imei, $body);
                header("location:../pages/tables/crear.php?alert=0&imei=$imei&model=$model&name=$name&celular=$celular");
            } else {
                header("location:../pages/tables/crear.php?alert=33&imei=$imei");
            }
            break;

        case "1": 
            // Cambiar estado a Pendiente.
            $precio = $_POST['precio'];
            $tiempo = $_POST['tiempo'];
            $sql = "UPDATE liberacion SET cod_estado = '2', Precio = '$precio', Tiempo = '$tiempo', Observaciones = '$desc', date_update = '$date' 
                    WHERE serie = '$imei'";
            mysqli_query($conn, $sql);

            // Se actualizan los datos comunes con los valores recibidos.
            $datos = array_merge($datosCommon, [
                'precio'        => $precio,
                'tiempo'        => $tiempo,
                'observaciones' => $desc
            ]);
            $body = correo_enviar("pendiente", $datos);
            reenviar_correo($correos, $imei, $body);
            header("location:../pages/tables/consultas.php?alert=1&imei=$imei");
            break;

        case "5.1": 
            // Liberación rechazada.
            $sql = "UPDATE liberacion SET cod_estado = '6', Observaciones = '$desc', date_update = '$date' 
                    WHERE serie = '$imei'";
            mysqli_query($conn, $sql);
            $body = correo_enviar("rechazado", $datosCommon);
            reenviar_correo($correos, $imei, $body);
            header("location:../pages/tables/consultas.php?alert=5&imei=$imei");
            break;

        case "2": 
            // Liberación aprobada.
            $sql = "UPDATE liberacion SET cod_estado = '3', Observaciones = '$desc', date_update = '$date' 
                    WHERE serie = '$imei'";
            mysqli_query($conn, $sql);
            $body = correo_enviar("aprobado", $datosCommon);
            reenviar_correo($correos, $imei, $body);
            header("location:../pages/tables/pendientes.php?alert=2&imei=$imei");
            break;

        case "2.1": 
            // Consulta reiniciada.
            $precio = $_POST['precio'];
            $tiempo = $_POST['tiempo'];
            $sql = "UPDATE liberacion SET cod_estado = '1', Precio = '$precio', Tiempo = '$tiempo', Observaciones = '$desc', date_update = '$date' 
                    WHERE serie = '$imei'";
            mysqli_query($conn, $sql);
            $datos = array_merge($datosCommon, [
                'precio'        => $precio,
                'tiempo'        => $tiempo,
                'observaciones' => $desc
            ]);
            $body = correo_enviar("consultaReiniciada", $datos);
            reenviar_correo($correos, $imei, $body);
            header("location:../pages/tables/rechazadas.php?alert=2.1&imei=$imei");
            break;

        case "5.2": 
            // Liberación rechazada (variante).
            $sql = "UPDATE liberacion SET cod_estado = '6', Observaciones = '$desc', date_update = '$date' 
                    WHERE serie = '$imei'";
            mysqli_query($conn, $sql);
            $body = correo_enviar("rechazado", $datosCommon);
            reenviar_correo($correos, $imei, $body);
            header("location:../pages/tables/pendientes.php?alert=5&imei=$imei");
            break;

        case "5.3": 
            // Liberación rechazada desde Aprobadas.
            $sql = "UPDATE liberacion SET cod_estado = '6', Observaciones = '$desc', date_update = '$date' 
                    WHERE serie = '$imei'";
            mysqli_query($conn, $sql);
            $body = correo_enviar("rechazado", $datosCommon);
            reenviar_correo($correos, $imei, $body);
            header("location:../pages/tables/aprobadas.php?alert=5&imei=$imei");
            break;

        case "5.4": 
            // Liberación rechazada desde Finalizadas.
            $sql = "UPDATE liberacion SET cod_estado = '6', Observaciones = '$desc', date_update = '$date' 
                    WHERE serie = '$imei'";
            mysqli_query($conn, $sql);
            $body = correo_enviar("rechazado", $datosCommon);
            reenviar_correo($correos, $imei, $body);
            header("location:../pages/tables/finalizadas.php?alert=5&imei=$imei");
            break;

        case "3": 
            // Inicio del proceso de liberación.
            $sql = "UPDATE liberacion SET cod_estado = '4', Observaciones = '$desc', date_update = '$date' 
                    WHERE serie = '$imei'";
            mysqli_query($conn, $sql);
            $body = correo_enviar("iniciado", $datosCommon);
            reenviar_correo($correos, $imei, $body);
            header("location:../pages/tables/aprobadas.php?alert=3&imei=$imei");
            break;

        case "4": 
            // Proceso finalizado.
            $sql = "UPDATE liberacion SET cod_estado = '$result', Observaciones = '$desc', date_update = '$date' 
                    WHERE serie = '$imei'";
            mysqli_query($conn, $sql);
            $body = correo_enviar("finalizado", $datosCommon);
            reenviar_correo($correos, $imei, $body);
            header("location:../pages/tables/aprobadas.php?alert=4&imei=$imei");
            break;

        default:
            echo "Error: Acción no reconocida.";
            break;
    }
} else {
    echo "No se recibió acción.";
}
?>
