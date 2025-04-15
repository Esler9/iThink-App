<?php
session_start();

if(!isset($_SESSION["username"])){
  header('Location: login.php');
  exit();
}

include("../../conexion.php"); 
include("dt_permisos.php");

// Se asume que $updatedPermissionsArray se construye dentro de este script.
$updatedPermissionsArray = [];  // Define la variable si no se utiliza

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $selectedPermissions = $_POST['permiso']; // Array de permisos seleccionados

    // Obtener todos los permisos de la tabla 'Permiso'
    $sql = "SELECT * FROM Permiso;";
    $result = $conn->query($sql);

    if ($result && $result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $permisoId = $row['codigo'];
            $found = false;

            foreach ($selectedPermissions as $groupId => $permissions) {
                if (array_key_exists($permisoId, $permissions)) {
                    $checkSql = "SELECT codigo_asi FROM Asignacion_permiso WHERE id_permiso = ? AND id_group = ?";
                    $checkStmt = $conn->prepare($checkSql);
                    $checkStmt->bind_param("ii", $permisoId, $groupId);
                    $checkStmt->execute();
                    $checkStmt->store_result();

                    if ($checkStmt->num_rows > 0) {
                        $updateSql = "UPDATE Asignacion_permiso SET active = 1 WHERE id_permiso = ? AND id_group = ?";
                        $updateStmt = $conn->prepare($updateSql);
                        $updateStmt->bind_param("ii", $permisoId, $groupId);
                        $updateStmt->execute();
                        $updateStmt->close();
                    } else {
                        $insertSql = "INSERT INTO Asignacion_permiso (id_permiso, id_group, active) VALUES (?, ?, 1)";
                        $insertStmt = $conn->prepare($insertSql);
                        $insertStmt->bind_param("ii", $permisoId, $groupId);
                        $insertStmt->execute();
                        $insertStmt->close();
                    }
                    $checkStmt->close();

                    $found = true;
                    break;
                }
            }

            if (!$found) {
                $stmt = $conn->prepare("UPDATE Asignacion_permiso SET active = 0 WHERE id_permiso = ? AND id_group = ?");
                foreach ($selectedPermissions as $groupId => $permissions) {
                    $stmt->bind_param("ii", $permisoId, $groupId);
                    $stmt->execute();
                }
                $stmt->close();
            }
        }
    } else {
        // (Opcional) Puedes asignar un mensaje en el arreglo de permisos actualizado
        $updatedPermissionsArray = [];
    }
}

header('Content-Type: application/json');
echo json_encode(['status' => 'success', 'updatedPermissions' => $updatedPermissionsArray]);
exit();
?>