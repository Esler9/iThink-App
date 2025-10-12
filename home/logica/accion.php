<?php
session_start();
if (!isset($_SESSION["username"])) {
    header('location:login.php');
    exit();
}

include("../../conexion.php");
include('correo.php');
include('funcion_logica.php');
include("diseño_correos.php");

// Función para enviar el correo a cada destinatario
function reenviarCorreo($listaCorreos, $imei, $body) {
    foreach ($listaCorreos as $destinatario) {
        enviar($destinatario, $imei, $body);
    }
}

$accion = $_GET['accion'];
$imei   = $_GET['imei'];
$desc   = isset($_POST['desc']) ? $_POST['desc'] : "";
$date   = date('Y-m-d H:i:s');
$result = isset($_POST['resultado']) ? $_POST['resultado'] : "";

// Consulta COMPLETA para obtener TODOS los datos (incluyendo los nuevos campos)
$sql = "SELECT 
            l.*,
            t.nombre as tienda_nombre,
            e.estado as estado_nombre
        FROM liberacion l
        LEFT JOIN tiendas t ON l.cod_tienda = t.cod_tienda
        LEFT JOIN estados e ON l.cod_estado = e.cod_estado
        WHERE l.serie = '$imei'";
$consulta = mysqli_query($conn, $sql);
$row_dt = mysqli_fetch_array($consulta);

// Variables obtenidas de la base de datos (con TODOS los campos nuevos)
$dtname         = $row_dt['Nombre_Cliente'] ?? "";
$dtcel          = $row_dt['Celular'] ?? "";
$dtmodel        = $row_dt['modelo'] ?? "";
$dtimei2        = $row_dt['serie_2'] ?? "";
$dttiempo_usa   = $row_dt['tiempo_usa'] ?? "";
$dtno_blacklist = $row_dt['no_blacklist'] ?? 0;
$dtno_icloud    = $row_dt['no_icloud'] ?? 0;
$dtprecio       = $row_dt['Precio'] ?? "";
$dttime         = $row_dt['Tiempo'] ?? "";
$dtobs          = $row_dt['Observaciones'] ?? "";
$dttienda       = $row_dt['tienda_nombre'] ?? "";
$dtestado       = $row_dt['estado_nombre'] ?? "";
$dtfecha_creacion = $row_dt['date'] ?? "";
$dtfecha_update = $row_dt['date_update'] ?? "";

// Arreglo común con TODOS los datos para la notificación por correo
$datosCommon = [
    'name'                => $dtname,
    'celular'             => $dtcel,
    'imei'                => $imei,
    'imei2'               => $dtimei2,
    'modelo'              => $dtmodel,
    'tiempo_usa'          => $dttiempo_usa,
    'no_blacklist'        => $dtno_blacklist,
    'no_icloud'           => $dtno_icloud,
    'precio'              => $dtprecio,
    'tiempo'              => $dttime,
    'observaciones'       => $desc,
    'tienda'              => $dttienda,
    'estado'              => $dtestado,
    'fecha_creacion'      => $dtfecha_creacion,
    'fecha_actualizacion' => $dtfecha_update
];

switch ($accion) {

    case "0":
        // Creación de una nueva liberación
        if (empty($_POST['c_imei']) || empty($_POST['model']) || empty($_POST['name']) || empty($_POST['celular'])) {
            header("location:../pages/liberaciones/crear.php?alert=DataMissing");
            exit();
        }
        
        $imei           = mysqli_real_escape_string($conn, $_POST['c_imei']);
        $imei2          = mysqli_real_escape_string($conn, $_POST['c_imei2'] ?? '');
        $model          = mysqli_real_escape_string($conn, $_POST['model']);
        $name           = mysqli_real_escape_string($conn, $_POST['name']);
        $celular        = mysqli_real_escape_string($conn, $_POST['celular']);
        $tiempo_usa     = mysqli_real_escape_string($conn, $_POST['tiempo_usa'] ?? '');
        $no_blacklist   = isset($_POST['no_blacklist']) ? 1 : 0;
        $no_icloud      = isset($_POST['no_icloud']) ? 1 : 0;
        $observaciones  = mysqli_real_escape_string($conn, $_POST['observaciones'] ?? '');
        $cod_tienda     = $_SESSION['tienda_user'];

        // Verificar si ya existe
        $sql_check = "SELECT COUNT(*) AS contar FROM liberacion WHERE serie = '$imei'";
        $consulta_check = mysqli_query($conn, $sql_check);
        $existe = mysqli_fetch_array($consulta_check);

        if ($existe["contar"] == 0) {
            $sql_insert = "INSERT INTO liberacion (
                Nombre_Cliente, 
                Celular, 
                serie, 
                serie_2,
                modelo, 
                tiempo_usa,
                no_blacklist,
                no_icloud,
                Observaciones,
                cod_estado, 
                cod_tienda, 
                date_update, 
                date
            ) VALUES (
                '$name', 
                '$celular', 
                '$imei', 
                '$imei2',
                '$model', 
                '$tiempo_usa',
                '$no_blacklist',
                '$no_icloud',
                '$observaciones',
                1, 
                '$cod_tienda', 
                '$date', 
                '$date'
            )";
            mysqli_query($conn, $sql_insert);

            $datos = [
                'name'          => $name,
                'celular'       => $celular,
                'imei'          => $imei,
                'imei2'         => $imei2,
                'modelo'        => $model,
                'tiempo_usa'    => $tiempo_usa,
                'no_blacklist'  => $no_blacklist,
                'no_icloud'     => $no_icloud,
                'precio'        => "",
                'tiempo'        => "",
                'observaciones' => $observaciones,
                'tienda'        => "",
                'estado'        => "Consulta",
                'fecha_creacion' => $date,
                'fecha_actualizacion' => $date
            ];
            $body = correo_enviar("consulta", $datos);
            reenviarCorreo($correos, $imei, $body);

            header("location:../pages/liberaciones/crear.php?alert=0&imei=$imei&model=$model&name=$name&celular=$celular");
        } else {
            header("location:../pages/liberaciones/crear.php?alert=33&imei=$imei");
        }
        break;

    case "1":
        // Cambio de estado a Pendiente (Informar)
        if (empty($_POST['precio']) || empty($_POST['tiempo'])) {
            header("location:../pages/liberaciones/consultas.php?alert=DataMissing&imei=$imei");
            exit();
        }
        $precio = mysqli_real_escape_string($conn, $_POST['precio']);
        $tiempo = mysqli_real_escape_string($conn, $_POST['tiempo']);
        $desc_escaped = mysqli_real_escape_string($conn, $desc);
        
        $sql = "UPDATE liberacion SET 
                cod_estado = '2', 
                Precio = '$precio', 
                Tiempo = '$tiempo', 
                Observaciones = '$desc_escaped', 
                date_update = '$date' 
                WHERE serie = '$imei'";
        mysqli_query($conn, $sql);

        // Actualizar datos con los nuevos valores
        $datosCommon['precio'] = $precio;
        $datosCommon['tiempo'] = $tiempo;
        $datosCommon['observaciones'] = $desc;
        $datosCommon['estado'] = "Pendiente";
        
        $body = correo_enviar("pendiente", $datosCommon);
        reenviarCorreo($correos, $imei, $body);
        header("location:../pages/liberaciones/consultas.php?alert=1&imei=$imei");
        break;

    case "5.1":
        // Liberación rechazada (desde consultas)
        $desc_escaped = mysqli_real_escape_string($conn, $desc);
        $sql = "UPDATE liberacion SET 
                cod_estado = '6', 
                Observaciones = '$desc_escaped', 
                date_update = '$date' 
                WHERE serie = '$imei'";
        mysqli_query($conn, $sql);
        
        $datosCommon['observaciones'] = $desc;
        $datosCommon['estado'] = "Rechazada";
        $body = correo_enviar("rechazado", $datosCommon);
        reenviarCorreo($correos, $imei, $body);
        header("location:../pages/liberaciones/consultas.php?alert=5&imei=$imei");
        break;

    case "2":
        // Liberación aprobada
        $desc_escaped = mysqli_real_escape_string($conn, $desc);
        $sql = "UPDATE liberacion SET 
                cod_estado = '3', 
                Observaciones = '$desc_escaped', 
                date_update = '$date' 
                WHERE serie = '$imei'";
        mysqli_query($conn, $sql);
        
        $datosCommon['observaciones'] = $desc;
        $datosCommon['estado'] = "Aprobada";
        $body = correo_enviar("aprobado", $datosCommon);
        reenviarCorreo($correos, $imei, $body);
        header("location:../pages/liberaciones/pendientes.php?alert=2&imei=$imei");
        break;

    case "2.1":
        // Reinicio de Consulta
        if (empty($_POST['precio']) || empty($_POST['tiempo'])) {
            header("location:../pages/liberaciones/rechazadas.php?alert=DataMissing&imei=$imei");
            exit();
        }
        $precio = mysqli_real_escape_string($conn, $_POST['precio']);
        $tiempo = mysqli_real_escape_string($conn, $_POST['tiempo']);
        $desc_escaped = mysqli_real_escape_string($conn, $desc);
        
        $sql = "UPDATE liberacion SET 
                cod_estado = '1', 
                Precio = '$precio', 
                Tiempo = '$tiempo', 
                Observaciones = '$desc_escaped', 
                date_update = '$date' 
                WHERE serie = '$imei'";
        mysqli_query($conn, $sql);
        
        $datosCommon['precio'] = $precio;
        $datosCommon['tiempo'] = $tiempo;
        $datosCommon['observaciones'] = $desc;
        $datosCommon['estado'] = "Consulta Reiniciada";
        
        $body = correo_enviar("consultaReiniciada", $datosCommon);
        reenviarCorreo($correos, $imei, $body);
        header("location:../pages/liberaciones/rechazadas.php?alert=2.1&imei=$imei");
        break;

    case "5.2":
        // Liberación rechazada (desde pendientes)
        $desc_escaped = mysqli_real_escape_string($conn, $desc);
        $sql = "UPDATE liberacion SET 
                cod_estado = '6', 
                Observaciones = '$desc_escaped', 
                date_update = '$date' 
                WHERE serie = '$imei'";
        mysqli_query($conn, $sql);
        
        $datosCommon['observaciones'] = $desc;
        $datosCommon['estado'] = "Rechazada";
        $body = correo_enviar("rechazado", $datosCommon);
        reenviarCorreo($correos, $imei, $body);
        header("location:../pages/liberaciones/pendientes.php?alert=5&imei=$imei");
        break;

    case "5.3":
        // Liberación rechazada (desde aprobadas)
        $desc_escaped = mysqli_real_escape_string($conn, $desc);
        $sql = "UPDATE liberacion SET 
                cod_estado = '6', 
                Observaciones = '$desc_escaped', 
                date_update = '$date' 
                WHERE serie = '$imei'";
        mysqli_query($conn, $sql);
        
        $datosCommon['observaciones'] = $desc;
        $datosCommon['estado'] = "Rechazada";
        $body = correo_enviar("rechazado", $datosCommon);
        reenviarCorreo($correos, $imei, $body);
        header("location:../pages/liberaciones/aprobadas.php?alert=5&imei=$imei");
        break;

    case "5.4":
        // Liberación rechazada (desde finalizadas)
        $desc_escaped = mysqli_real_escape_string($conn, $desc);
        $sql = "UPDATE liberacion SET 
                cod_estado = '6', 
                Observaciones = '$desc_escaped', 
                date_update = '$date' 
                WHERE serie = '$imei'";
        mysqli_query($conn, $sql);
        
        $datosCommon['observaciones'] = $desc;
        $datosCommon['estado'] = "Rechazada";
        $body = correo_enviar("rechazado", $datosCommon);
        reenviarCorreo($correos, $imei, $body);
        header("location:../pages/liberaciones/finalizadas.php?alert=5&imei=$imei");
        break;

    case "3":
        // Inicio del proceso de liberación
        $desc_escaped = mysqli_real_escape_string($conn, $desc);
        $sql = "UPDATE liberacion SET 
                cod_estado = '4', 
                Observaciones = '$desc_escaped', 
                date_update = '$date' 
                WHERE serie = '$imei'";
        mysqli_query($conn, $sql);
        
        $datosCommon['observaciones'] = $desc;
        $datosCommon['estado'] = "En Proceso";
        $body = correo_enviar("iniciado", $datosCommon);
        reenviarCorreo($correos, $imei, $body);
        header("location:../pages/liberaciones/aprobadas.php?alert=3&imei=$imei");
        break;

    case "4":
        // Finalización del proceso de liberación
        $desc_escaped = mysqli_real_escape_string($conn, $desc);
        $sql = "UPDATE liberacion SET 
                cod_estado = '$result', 
                Observaciones = '$desc_escaped', 
                date_update = '$date' 
                WHERE serie = '$imei'";
        mysqli_query($conn, $sql);
        
        $datosCommon['observaciones'] = $desc;
        $datosCommon['estado'] = "Finalizada";
        $body = correo_enviar("finalizado", $datosCommon);
        reenviarCorreo($correos, $imei, $body);
        header("location:../pages/liberaciones/aprobadas.php?alert=4&imei=$imei");
        break;

    default:
        echo "Error: Acción no reconocida.";
        break;
}
?>
