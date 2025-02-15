<?php 

// Consulta para obtener el conteo de registros agrupados por 'cod_estado'
$sql = "SELECT cod_estado, COUNT(cod_estado) as Cantidad FROM liberacion GROUP BY cod_estado;";

$user_state = $_SESSION['State']; // Obtiene el estado del usuario de la sesión
$valores = mysqli_query($conn, $sql); // Ejecuta la consulta
$con = $pen = $apro = $proc = $fin = $recha = 0; // Inicializa todas las variables en 0

// Procesa los resultados de la consulta
while ($data = mysqli_fetch_array($valores)) {
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
            $apro += $data['Cantidad']; // Acumula los valores si el estado es 4
            break;
        case 5:
            $fin = $data['Cantidad'];
            break;
        case 6:
            $recha = $data['Cantidad'];
        case 7:
            $recha += $data['Cantidad'];
        case 8:
            $recha += $data['Cantidad'];
        case 9:
            $recha += $data['Cantidad'];
            break;
    }
}

// Se incluye el archivo menú desde una ruta absoluta
include_once($_SERVER['DOCUMENT_ROOT'] . "/home/menu.php");

?>
