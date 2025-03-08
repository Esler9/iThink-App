<?php include '../../conexion.php';

// Obtener grupos de permisos
$grupos = $conn->query("SELECT * FROM grupo_permiso");
$color_msj = 0;

// Manejo de formulario
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $codigo = $_POST['codigo'] ?? null;
    $nombre_permiso = $_POST['nombre_permiso'];
    $des_permiso = $_POST['des_permiso'];
    $group_permiso = $_POST['group_permiso'];

    // Generar slug a partir del nombre del permiso
    $slug = strtolower(trim(preg_replace('/\s+/', '-', $nombre_permiso)));

    // Verificar que el slug sea único
    $slug_query = $conn->prepare("SELECT COUNT(*) FROM Permiso WHERE slug = ? AND (codigo != ? OR ? IS NULL)");
    $slug_query->bind_param("ssi", $slug, $codigo, $codigo);
    $slug_query->execute();
    $slug_query->bind_result($slug_count);
    $slug_query->fetch();
    $slug_query->close();

    if ($slug_count > 0) {
        header("Location: admin_permisos.php?msg='El Slug : $slug ya Existe'");
        exit();
    }

    if ($codigo) {
        // Editar permiso existente
        $sql = "UPDATE Permiso SET nombre_permiso=?, des_permiso=?, group_permiso=?, slug=? WHERE codigo=?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ssisi", $nombre_permiso, $des_permiso, $group_permiso, $slug, $codigo);
    } else {
        // Crear nuevo permiso
        $sql = "INSERT INTO Permiso (nombre_permiso, des_permiso, group_permiso, slug) VALUES (?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ssis", $nombre_permiso, $des_permiso, $group_permiso, $slug);
    }

    if ($stmt->execute()) {
        header("Location: admin_permisos.php?msg='Permiso guardado exitosamente'");
        exit();
    } else {
        echo "Error: " . $stmt->error;
    }

    $stmt->close();
}

include ('../../Setting.php');

if($mantenimiento == true ){
  header('location:../../mantenimiento.php');
  exit;}

// Verificar que las variables de sesión estén configuradas
if (!isset($_SESSION["username"]) || !isset($_SESSION['cod_user'])) {
  header('Location: ../../login.php');
  exit();
}

$User = htmlspecialchars($_SESSION["username"]);
$cod_user = htmlspecialchars($_SESSION['cod_user']);
$page = 2;

include("../logica/ac_permiso.php");
include("../datos/dt_permisos.php");

?>

<!DOCTYPE html>
<html lang="es">
<head>
    <link rel="icon" href="/home/dist/img/logo_ithink.png" type="image/x-icon">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.4/css/dataTables.bootstrap5.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.1/dist/css/bootstrap.min.css" rel="stylesheet">
    <title>Formulario de Permisos</title>
</head>
<body>

<?php include("sidebar.php"); ?>
    <div class="container mt-5">
        <?php if (isset($_GET['msg'])): ?>
            <div class="alert <?php echo $color_msj == 1 ? 'alert-success' : 'alert-danger'; ?>" role="alert">
                <?php echo htmlspecialchars($_GET['msg']); ?>
            </div>
        <?php endif; ?>

        <!-- Botón para mostrar el formulario de crear permiso -->
        <button id="crearPermisoBtn" class="btn btn-primary mb-3">Crear Permiso</button>

        <!-- Formulario oculto inicialmente para crear permiso -->
        <div id="formularioPermiso" style="display: none;">
            <h1>Crear Permiso</h1>
            <form method="POST" id="permisoForm">
                <div class="form-group">
                    <label for="nombre_permiso">Nombre del Permiso:</label>
                    <input type="text" name="nombre_permiso" class="form-control" required>
                </div>

                <div class="form-group">
                    <label for="des_permiso">Descripción:</label>
                    <textarea name="des_permiso" class="form-control"></textarea>
                </div>

                <div class="form-group">
                    <label for="group_permiso">Grupo de Permiso:</label>
                    <select name="group_permiso" class="form-control" required>
                        <?php while ($grupo = $grupos->fetch_assoc()): ?>
                            <option value="<?php echo $grupo['codigo']; ?>">
                                <?php echo $grupo['nombre_grupo_p']; ?>
                            </option>
                        <?php endwhile; ?>
                    </select>
                </div>

                <button type="submit" class="btn btn-primary mt-3">Crear</button>
            </form>
        </div>

        <h2>Permisos Existentes</h2>

        <table id="permisoTable" class="table table-striped">
            <thead>
                <tr>
                    <th>Código</th>
                    <th>Nombre del Permiso</th>
                    <th>Descripción</th>
                    <th>Grupo de Permiso</th>
                    <th>Slug</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $result = $conn->query("SELECT p.codigo, p.nombre_permiso, p.des_permiso, g.nombre_grupo_p, p.slug FROM Permiso p JOIN grupo_permiso g ON p.group_permiso = g.codigo");
                while ($permiso_existente = $result->fetch_assoc()):
                ?>
                    <tr>
                        <td><?php echo $permiso_existente['codigo']; ?></td>
                        <td><?php echo $permiso_existente['nombre_permiso']; ?></td>
                        <td><?php echo $permiso_existente['des_permiso']; ?></td>
                        <td><?php echo $permiso_existente['nombre_grupo_p']; ?></td>
                        <td><?php echo $permiso_existente['slug']; ?></td>
                        <td>
                            <button class="btn btn-warning btn-sm editPermisoBtn" data-codigo="<?php echo $permiso_existente['codigo']; ?>"
                                    data-nombre="<?php echo $permiso_existente['nombre_permiso']; ?>"
                                    data-descripcion="<?php echo $permiso_existente['des_permiso']; ?>"
                                    data-grupo="<?php echo $permiso_existente['nombre_grupo_p']; ?>">Editar
                            </button>
                        </td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>

    <!-- Modal de edición -->
    <div class="modal fade" id="editPermisoModal" tabindex="-1" aria-labelledby="editPermisoLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <form method="POST" id="editPermisoForm">
                    <div class="modal-header">
                        <h5 class="modal-title" id="editPermisoLabel">Editar Permiso</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <input type="hidden" name="codigo" id="editCodigo">

                        <div class="form-group">
                            <label for="editNombrePermiso">Nombre del Permiso:</label>
                            <input type="text" name="nombre_permiso" id="editNombrePermiso" class="form-control" required>
                        </div>

                        <div class="form-group">
                            <label for="editDesPermiso">Descripción:</label>
                            <textarea name="des_permiso" id="editDesPermiso" class="form-control"></textarea>
                        </div>

                        <div class="form-group">
                            <label for="editGroupPermiso">Grupo de Permiso:</label>
                            <select name="group_permiso" id="editGroupPermiso" class="form-control" required>
                                <?php
                                // Se vuelve a consultar para llenar el select
                                $grupos = $conn->query("SELECT * FROM grupo_permiso");
                                while ($grupo = $grupos->fetch_assoc()):
                                ?>
                                    <option value="<?php echo $grupo['codigo']; ?>"><?php echo $grupo['nombre_grupo_p']; ?></option>
                                <?php endwhile; ?>
                            </select>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                        <button type="submit" class="btn btn-primary">Guardar Cambios</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- JavaScript -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.4/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.4/js/dataTables.bootstrap5.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.1/dist/js/bootstrap.bundle.min.js"></script>

    <script>
        $(document).ready(function() {
    // Inicializar DataTable
    $('#permisoTable').DataTable();

    // Mostrar formulario de creación de permiso
    $('#crearPermisoBtn').click(function() {
        $('#formularioPermiso').toggle();
    });

    // Delegación de eventos para el botón de editar
    $('#permisoTable').on('click', '.editPermisoBtn', function() {
        var codigo = $(this).data('codigo');
        var nombre = $(this).data('nombre');
        var descripcion = $(this).data('descripcion');
        var grupo = $(this).data('grupo');

        // Llenar el formulario del modal con los datos
        $('#editCodigo').val(codigo);
        $('#editNombrePermiso').val(nombre);
        $('#editDesPermiso').val(descripcion);
        $('#editGroupPermiso').val(grupo);

        // Mostrar el modal
        $('#editPermisoModal').modal('show');
    });
});

    </script>
</body>
</html>
