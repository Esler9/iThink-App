<?php

include("acceso.php");

function Tiene_permiso($permisos, $slug_requerido) {
    // Verificar si el slug requerido está en el array de permisos
    foreach ($permisos as $permiso) {
        if ($permiso['slug'] === $slug_requerido) {
            return true;
        }
    }
    return false;
}


// Obtener todos los permisos del usuario en una sola consulta
$permisos_user = Obtener_permisos_usuario($cod_user, $conn);


?>