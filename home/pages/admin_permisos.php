<?php
session_start();
include '../../conexion.php';

// Asegurar funciones de permisos
if (file_exists(__DIR__ . '/../logica/ac_permiso.php')) {
    include_once __DIR__ . '/../logica/ac_permiso.php';
}
if (!isset($permisos_user)) $permisos_user = [];

// Obtener grupos de permisos
$grupos = $conn->query("SELECT * FROM grupo_permiso");
$color_msj = 0;

// Manejo de formulario (sin cambios)
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $codigo = $_POST['codigo'] ?? null;
    $nombre_permiso = $_POST['nombre_permiso'] ?? '';
    $des_permiso = $_POST['des_permiso'] ?? '';
    $group_permiso = $_POST['group_permiso'] ?? 0;

    $slug = strtolower(trim(preg_replace('/\s+/', '-', $nombre_permiso)));

    $slug_query = $conn->prepare("SELECT COUNT(*) FROM Permiso WHERE slug = ? AND (codigo != ? OR ? IS NULL)");
    $slug_query->bind_param("ssi", $slug, $codigo, $codigo);
    $slug_query->execute();
    $slug_query->bind_result($slug_count);
    $slug_query->fetch();
    $slug_query->close();

    if ($slug_count > 0) {
        header("Location: admin_permisos.php?msg=" . urlencode("El Slug : $slug ya Existe"));
        exit();
    }

    if ($codigo) {
        $sql = "UPDATE Permiso SET nombre_permiso=?, des_permiso=?, group_permiso=?, slug=? WHERE codigo=?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ssisi", $nombre_permiso, $des_permiso, $group_permiso, $slug, $codigo);
    } else {
        $sql = "INSERT INTO Permiso (nombre_permiso, des_permiso, group_permiso, slug) VALUES (?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ssis", $nombre_permiso, $des_permiso, $group_permiso, $slug);
    }

    if ($stmt->execute()) {
        header("Location: admin_permisos.php?msg=" . urlencode("Permiso guardado exitosamente"));
        exit();
    } else {
        echo "Error: " . $stmt->error;
    }

    $stmt->close();
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Administrador de Permisos</title>

    <!-- CSS externos -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.1/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

    <!-- Estilos mínimos para layout sidebar + contenido (evita overflow) -->
    <style>
      :root { --sidebar-width: 260px; }
      html,body { height:100%; margin:0; }
      .wrapper { display:flex; min-height:100vh; }
      .main-sidebar {
        width: var(--sidebar-width);
        flex: 0 0 var(--sidebar-width);
        background:#343a40;
        color:#fff;
        padding-top:1rem;
        overflow:auto;
      }
      .main-sidebar a { color: #cfd8dc; text-decoration:none; display:block; padding:.5rem 1rem; }
      .main-sidebar a:hover { background:rgba(255,255,255,0.03); color:#fff; }
      .content-area { flex:1; padding:1.25rem; background:#f4f6f9; min-width:0; } /* min-width:0 evita overflow en flex */
      .content-inner { max-width:1200px; margin:0 auto; }
      .table-responsive { overflow:auto; }
      @media (max-width: 768px) {
        .wrapper { flex-direction:column; }
        .main-sidebar { width:100%; flex:0 0 auto; position:relative; }
        .content-area { margin-top:0; }
      }
    </style>
</head>
<body>

<div class="wrapper">
    <!-- Sidebar (archivo existente) -->
    <?php include_once __DIR__ . '/sidebar.php'; ?>

    <!-- Contenido principal -->
    <main class="content-area">
      <div class="content-inner">

        <!-- Mensajes -->
        <?php if (isset($_GET['msg'])): ?>
          <div class="alert <?php echo $color_msj == 1 ? 'alert-success' : 'alert-info'; ?> alert-dismissible fade show" role="alert">
            <?php echo htmlspecialchars($_GET['msg']); ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Cerrar"></button>
          </div>
        <?php endif; ?>

        <!-- Botón y formulario -->
        <div class="mb-3 d-flex justify-content-between align-items-center">
          <h3 class="m-0">Permisos</h3>
          <button id="crearPermisoBtn" class="btn btn-primary">Crear Permiso</button>
        </div>

        <div id="formularioPermiso" class="card mb-4" style="display:none;">
          <div class="card-body">
            <form method="POST" id="permisoForm">
              <div class="mb-3">
                <label class="form-label">Nombre del Permiso</label>
                <input type="text" name="nombre_permiso" class="form-control" required>
              </div>
              <div class="mb-3">
                <label class="form-label">Descripción</label>
                <textarea name="des_permiso" class="form-control"></textarea>
              </div>
              <div class="mb-3">
                <label class="form-label">Grupo de Permiso</label>
                <select name="group_permiso" class="form-select" required>
                  <?php while ($grupo = $grupos->fetch_assoc()): ?>
                    <option value="<?php echo $grupo['codigo']; ?>"><?php echo $grupo['nombre_grupo_p']; ?></option>
                  <?php endwhile; ?>
                </select>
              </div>
              <button type="submit" class="btn btn-success">Crear</button>
            </form>
          </div>
        </div>

        <!-- Tabla -->
        <div class="card">
          <div class="card-body table-responsive">
            <table id="permisoTable" class="table table-striped table-hover">
              <thead>
                <tr>
                  <th>Código</th><th>Nombre</th><th>Descripción</th><th>Grupo</th><th>Slug</th><th>Acciones</th>
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
                      <button class="btn btn-warning btn-sm editPermisoBtn"
                              data-codigo="<?php echo $permiso_existente['codigo']; ?>"
                              data-nombre="<?php echo htmlspecialchars($permiso_existente['nombre_permiso'], ENT_QUOTES); ?>"
                              data-descripcion="<?php echo htmlspecialchars($permiso_existente['des_permiso'], ENT_QUOTES); ?>"
                              data-grupo="<?php echo $permiso_existente['nombre_grupo_p']; ?>">
                        <i class="fa fa-edit"></i> Editar
                      </button>
                    </td>
                  </tr>
                <?php endwhile; ?>
              </tbody>
            </table>
          </div>
        </div>

      </div><!-- .content-inner -->
    </main>
</div><!-- .wrapper -->

<!-- Modales (igual que antes) -->
<div class="modal fade" id="editPermisoModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <form method="POST" id="editPermisoForm">
        <div class="modal-header">
          <h5 class="modal-title">Editar Permiso</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
        </div>
        <div class="modal-body">
          <input type="hidden" name="codigo" id="editCodigo">
          <div class="mb-3">
            <label class="form-label">Nombre</label>
            <input type="text" name="nombre_permiso" id="editNombrePermiso" class="form-control" required>
          </div>
          <div class="mb-3">
            <label class="form-label">Descripción</label>
            <textarea name="des_permiso" id="editDesPermiso" class="form-control"></textarea>
          </div>
          <div class="mb-3">
            <label class="form-label">Grupo</label>
            <select name="group_permiso" id="editGroupPermiso" class="form-select" required>
              <?php
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

<!-- JS -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.1/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.datatables.net/1.13.4/js/jquery.dataTables.min.js"></script>

<script>
  $(function(){
    $('#permisoTable').DataTable({ responsive:true });
    $('#crearPermisoBtn').on('click', function(){ $('#formularioPermiso').toggle(); });

    $('#permisoTable').on('click', '.editPermisoBtn', function(){
      $('#editCodigo').val($(this).data('codigo'));
      $('#editNombrePermiso').val($(this).data('nombre'));
      $('#editDesPermiso').val($(this).data('descripcion'));
      // seleccionar grupo por texto (si coincide) — puedes ajustar para usar value en data-*
      $('#editGroupPermiso option').filter(function(){ return $(this).text() == $(this).data('grupo'); }).prop('selected', true);
      $('#editPermisoModal').modal('show');
    });
  });
</script>
</body>
</html>
