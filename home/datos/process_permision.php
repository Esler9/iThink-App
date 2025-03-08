<!DOCTYPE php>
<?php 
session_start();
$User = $_SESSION["username"];
if(!isset($User)){
  header('location:login.php');
  exit();
}
include("../../conexion.php"); 
include("dt_permisos.php");
$groupId = "";

if ($conn->connect_error) {
    die("Conexión fallida: " . $conn->connect_error);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $selectedPermissions = $_POST['permiso']; // Array de permisos seleccionados

    // Obtener todos los permisos de la tabla 'Permiso'
    $sql = "SELECT * FROM Permiso;";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $permisoId = $row['codigo']; // 'codigo' asume que es el ID del permiso
            $found = false; // Variable para verificar si el permiso está en los seleccionados

            foreach ($selectedPermissions as $groupId => $permissions) {
                // Verifica si el permiso está en el grupo actual
                if (array_key_exists($permisoId, $permissions)) {
                    // Primero, verificar si ya existe la asignación en 'asignacion_permiso'
                    $checkSql = "SELECT codigo_asi FROM Asignacion_permiso WHERE id_permiso = ? AND id_group = ?";
                    $checkStmt = $conn->prepare($checkSql);
                    $checkStmt->bind_param("ii", $permisoId, $groupId);
                    $checkStmt->execute();
                    $checkStmt->store_result(); // Almacenar el resultado para usar num_rows

                    if ($checkStmt->num_rows > 0) {
                        // Si existe, hacer un UPDATE
                        $updateSql = "UPDATE Asignacion_permiso SET active = 1 WHERE id_permiso = ? AND id_group = ?";
                        $updateStmt = $conn->prepare($updateSql);
                        $updateStmt->bind_param("ii", $permisoId, $groupId);
                        $updateStmt->execute();
                        $updateStmt->close();
                    } else {
                        // Si no existe, hacer un INSERT
                        $insertSql = "INSERT INTO Asignacion_permiso (id_permiso, id_group, active) VALUES (?, ?, 1)";
                        $insertStmt = $conn->prepare($insertSql);
                        $insertStmt->bind_param("ii", $permisoId, $groupId);
                        $insertStmt->execute();
                        $insertStmt->close();
                    }
                    $checkStmt->close();

                    $found = true; // Se encontró y activó el permiso
                    break; // Ya encontramos el grupo, no es necesario seguir buscando
                }
            }

            if (!$found) {
                // Si el permiso no fue seleccionado, desactivar en 'asignacion_permiso'
                $stmt = $conn->prepare("UPDATE Asignacion_permiso SET active = 0 WHERE id_permiso = ? AND id_group = ?");
                foreach ($selectedPermissions as $groupId => $permissions) {
                    $stmt->bind_param("ii", $permisoId, $groupId);
                    $stmt->execute();
                }
                $stmt->close();
            }
        }
    } else {
        echo "No se encontraron permisos en la tabla 'Permiso'.";
    }
}




header("location: /home/Liberaciones/permisos_view.php")


?>