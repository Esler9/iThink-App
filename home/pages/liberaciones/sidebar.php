<?php
// Verifica si la sesión del usuario está activa
if (isset($_SESSION['cod_user'])) {
    $cod_user = $_SESSION['cod_user'];

    // Obtiene las tiendas asignadas al usuario
    $sql = "SELECT cod_tienda FROM `asignacion_tienda` WHERE cod_user = ?";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, 'i', $cod_user);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);

    // Almacena las tiendas en un array
    $marcas = [];
    while ($fila = mysqli_fetch_assoc($result)) {
        $marcas[] = $fila['cod_tienda'];
    }
    
    // Convierte el array de tiendas a una cadena separada por comas
    $tiendas = implode(', ', $marcas);
    $_SESSION['tiendas'] = $tiendas;
    $tiendas = $_SESSION['tiendas'];

    $urlActual = $_SERVER['REQUEST_URI'];

    // Valida el estado del usuario
    if ($_SESSION['state'] > 5) {
        $user_state = "active";
        $search_active_pendiente = true;
    } elseif ($_SESSION['state'] <= 2) {
        $user_state = "disabled";
        $search_active_pendiente = false;
    } else {
        $user_state = "disabled";
        $search_active_pendiente = false;
    }
}

// Consulta para obtener el conteo de liberaciones por estado
$sql = "SELECT cod_estado, COUNT(cod_estado) as Cantidad FROM liberacion WHERE cod_tienda IN ($tiendas) GROUP BY cod_estado";
$valores = mysqli_query($conn, $sql);

// Inicializa las variables
$con = 0;
$pen = 0;
$apro = 0;
$proc = 0;
$fin = 0;
$recha = 0;

// Procesa los resultados de la consulta
while ($data = mysqli_fetch_assoc($valores)) {
    switch ($data['cod_estado']) {
        case 1:
            $con = $data['Cantidad'];
            break;
        case 2:
            $pen = $data['Cantidad'];
            break;
        case 3:
            $apro = $data['Cantidad'];
            break;
        case 4:
            $apro += $data['Cantidad'];
            break;
        case 5:
            $fin = $data['Cantidad'];
            break;
        case 6:
            $recha = $data['Cantidad'];
            break;
        case 7:
          $recha += $data['Cantidad'];
          break;    
        case 8:
          $recha += $data['Cantidad'];
            break;
        case 9:
            $recha += $data['Cantidad'];
            break;
        default:
            // Maneja estados desconocidos si es necesario
            break;
    }
}

include("../../menu.php");
?>
