<?php 

// Asegurarse de que $tiendas solo contenga números y comas
$tiendas = preg_replace('/[^0-9,]/', '', $tiendas);

// Consulta segura ahora que $tiendas ha sido sanitizado
$sql = "SELECT cod_estado, COUNT(cod_estado) as Cantidad FROM liberacion WHERE cod_tienda IN ($tiendas) GROUP BY cod_estado";
$valores = mysqli_query($conn, $sql);

// Inicialización de contadores
$con = $pen = $apro = $fin = $recha = 0;

// Verificar si hay resultados
if ($valores && mysqli_num_rows($valores) > 0) {
    // Procesar los resultados
    while ($data = mysqli_fetch_assoc($valores)) {
        // Controlar los diferentes estados usando un switch
        switch ($data['cod_estado']) {
            case 1:
                $con = intval($data['Cantidad']);
                break;
            case 2:
                $pen = intval($data['Cantidad']);
                break;
            case 3:
                $apro = intval($data['Cantidad']);
                break;
            case 4:
                $apro += intval($data['Cantidad']); // Acumular cantidad para el estado 4
                break;
            case 5:
                $fin = intval($data['Cantidad']);
                break;
            case 6:
                $recha = intval($data['Cantidad']);
                break;
            case 7:
                $recha += intval($data['Cantidad']); // Estado 7 sumado a rechazados
                break;
            case 8:
                $recha += intval($data['Cantidad']); // Estado 8 sumado a rechazados
                break;
            case 9:
                $recha += intval($data['Cantidad']); // Estado 9 sumado a rechazados
                break;
        }
    }
}

// Incluye el archivo de menú
include("menu.php");




?>

