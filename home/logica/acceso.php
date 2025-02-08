<?php
function Obtener_permisos_usuario($usuario, $conexion) {
    // Escapar el parámetro para evitar inyección SQL
    $usuario_escapado = (int)$usuario;

    // Preparar la consulta SQL para obtener todos los permisos activos del usuario y el slug
    $sql = "SELECT Asignacion_permiso.id_permiso, Permiso.slug
            FROM Asignacion_permiso
            INNER JOIN Usuarios ON Usuarios.id_group_user = Asignacion_permiso.id_group
            INNER JOIN Permiso ON Asignacion_permiso.id_permiso = Permiso.codigo
            WHERE Usuarios.codigo = $usuario_escapado
            AND Asignacion_permiso.active = 1;";

    // Ejecutar la consulta
    $resultado = mysqli_query($conexion, $sql);

    // Inicializar un array para almacenar los permisos y sus slugs
    $permisos = [];

    // Verificar si se obtuvieron resultados
    if ($resultado && mysqli_num_rows($resultado) > 0) {
        // Recorrer cada fila de los resultados y almacenar los permisos en el array
        while ($fila = mysqli_fetch_assoc($resultado)) {
            $permisos[] = [
                'id_permiso' => (int)$fila['id_permiso'],
                'slug' => $fila['slug']
            ];
        }
        // Cerrar el resultado
        mysqli_free_result($resultado);
    }

    // Retornar el array de permisos
    return $permisos;
}


?>
