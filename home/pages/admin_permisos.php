<?php
session_start();

// Config / mantenimiento
include_once __DIR__ . '/../../Setting.php';
if (isset($mantenimiento) && $mantenimiento === true) {
    echo "<h3>Sitio en mantenimiento</h3>";
    exit();
}

// Verificar sesión
if (!isset($_SESSION["username"]) || !isset($_SESSION['cod_user'])) {
    header('Location: ../login.php');
    exit();
}

$User = htmlspecialchars($_SESSION["username"]);
$cod_user = htmlspecialchars($_SESSION['cod_user']);
$page = 2;

include_once __DIR__ . '/../../conexion.php';
include_once __DIR__ . '/../logica/ac_permiso.php';
include_once __DIR__ . '/../datos/dt_permisos.php';

// Obtener grupos para el select del formulario
$grupos = Traer_grupo_usuario($conn);

// Manejo de formulario (crear/editar permiso)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $codigo = $_POST['codigo'] ?? null;
    $nombre_permiso = trim($_POST['nombre_permiso'] ?? '');
    $des_permiso = trim($_POST['des_permiso'] ?? '');
    $group_permiso = (int)($_POST['group_permiso'] ?? 0);

    if ($nombre_permiso === '') {
        header("Location: admin_permisos.php?msg=" . urlencode("El nombre del permiso es requerido"));
        exit();
    }

    $slug = strtolower(preg_replace('/\s+/', '-', $nombre_permiso));

    // Verificar unicidad del slug
    $q = $conn->prepare("SELECT COUNT(*) FROM Permiso WHERE slug = ? " . ($codigo ? "AND codigo <> ?" : ""));
    if ($codigo) {
        $q->bind_param("si", $slug, $codigo);
    } else {
        $q->bind_param("s", $slug);
    }
    $q->execute();
    $q->bind_result($slug_count);
    $q->fetch();
    $q->close();

    if ($slug_count > 0) {
        header("Location: admin_permisos.php?msg=" . urlencode("El slug ya existe"));
        exit();
    }

    if ($codigo) {
        $stmt = $conn->prepare("UPDATE Permiso SET nombre_permiso = ?, des_permiso = ?, group_permiso = ?, slug = ? WHERE codigo = ?");
        $stmt->bind_param("ssisi", $nombre_permiso, $des_permiso, $group_permiso, $slug, $codigo);
    } else {
        $stmt = $conn->prepare("INSERT INTO Permiso (nombre_permiso, des_permiso, group_permiso, slug) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("ssis", $nombre_permiso, $des_permiso, $group_permiso, $slug);
    }

    if ($stmt->execute()) {
        header("Location: admin_permisos.php?msg=" . urlencode("Permiso guardado correctamente"));
        exit();
    } else {
        header("Location: admin_permisos.php?msg=" . urlencode("Error al guardar permiso"));
        exit();
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <title>Admin Permisos</title>

  <!-- Mantener consistencia con permisos_view: AdminLTE / DataTables / FontAwesome -->
  <link rel="stylesheet" href="../plugins/fontawesome-free/css/all.min.css">
  <link rel="stylesheet" href="../plugins/datatables-bs4/css/dataTables.bootstrap4.min.css">
  <link rel="stylesheet" href="../plugins/datatables-responsive/css/responsive.bootstrap4.min.css">
  <link rel="stylesheet" href="../plugins/datatables-buttons/css/buttons.bootstrap4.min.css">
  <link rel="stylesheet" href="../dist/css/adminlte.min.css">
  <style>
    /* Ajustes menores */
    #formCrear { display:none; }
    .card .card-body.table-responsive { max-height: 70vh; overflow:auto; }
  </style>
</head>
<body class="hold-transition sidebar-mini layout-fixed">
  <div class="wrapper">

    <!-- Sidebar (igual que permisos_view) -->
    <?php include("sidebar.php"); ?>

    <!-- Content Wrapper. Contains page content -->
    <div class="content-wrapper">
      <!-- Content Header -->
      <section class="content-header">
        <div class="container-fluid">
          <div class="row mb-2">
            <div class="col-sm-6">
              <h1>Administrar Permisos</h1>
            </div>
            <div class="col-sm-6">
              <ol class="breadcrumb float-sm-right">
                <li class="breadcrumb-item"><a href="../dashboard.php">Home</a></li>
                <li class="breadcrumb-item active">Permisos</li>
              </ol>
            </div>
          </div>
        </div>
      </section>

      <!-- Main content -->
      <section class="content">
        <div class="container-fluid">

          <div class="d-flex justify-content-between align-items-center mb-3">
            <div>
              <?php if (!empty($_GET['msg'])): ?>
                <div class="alert alert-info alert-dismissible fade show" role="alert">
                  <?php echo htmlspecialchars($_GET['msg']); ?>
                  <button type="button" class="close" data-dismiss="alert" aria-label="Cerrar">&times;</button>
                </div>
              <?php endif; ?>
            </div>
            <div>
              <button id="nuevoPermisoBtn" class="btn btn-primary"><i class="fa fa-plus"></i> Nuevo Permiso</button>
            </div>
          </div>

          <!-- Form crear/editar (oculto por defecto) -->
          <div id="formCrear" class="card mb-3">
            <div class="card-header">
              <h3 class="card-title">Crear / Editar Permiso</h3>
            </div>
            <div class="card-body">
              <form method="post" id="formPermiso">
                <input type="hidden" name="codigo" id="codigoPermiso" value="">
                <div class="form-group">
                  <label>Nombre permiso</label>
                  <input class="form-control" name="nombre_permiso" id="nombrePermiso" required>
                </div>
                <div class="form-group">
                  <label>Descripción</label>
                  <textarea class="form-control" name="des_permiso" id="desPermiso"></textarea>
                </div>
                <div class="form-group">
                  <label>Grupo</label>
                  <select class="form-control" name="group_permiso" id="groupPermiso" required>
                    <?php
                    if (is_array($grupos)) {
                        foreach ($grupos as $g) {
                            echo '<option value="'.htmlspecialchars($g['codigo']).'">'.htmlspecialchars($g['nombre_grupo_p'] ?? $g['nombre']).'</option>';
                        }
                    } else {
                        $gq = $conn->query("SELECT * FROM grupo_permiso");
                        while ($g = $gq->fetch_assoc()) {
                            echo '<option value="'.htmlspecialchars($g['codigo']).'">'.htmlspecialchars($g['nombre_grupo_p'] ?? $g['nombre']).'</option>';
                        }
                    }
                    ?>
                  </select>
                </div>
                <div class="d-flex justify-content-end">
                  <button type="submit" class="btn btn-success mr-2">Guardar</button>
                  <button type="button" id="cancelCrear" class="btn btn-secondary">Cancelar</button>
                </div>
              </form>
            </div>
          </div>

          <!-- Tabla de permisos (ancho completo) -->
          <div class="card">
            <div class="card-header">
              <h3 class="card-title">Listado de Permisos</h3>
              <div class="card-tools">
                <div class="input-group input-group-sm" style="width: 250px;">
                  <input type="text" id="tableSearch" class="form-control float-right" placeholder="Buscar permisos">
                  <div class="input-group-append">
                    <button class="btn btn-default"><i class="fas fa-search"></i></button>
                  </div>
                </div>
              </div>
            </div>
            <div class="card-body table-responsive">
              <table id="tablaPermisos" class="table table-bordered table-hover">
                <thead>
                  <tr>
                    <th>Código</th>
                    <th>Nombre</th>
                    <th>Descripción</th>
                    <th>Grupo</th>
                    <th>Slug</th>
                    <th>Acciones</th>
                  </tr>
                </thead>
                <tbody>
                  <?php
                  $q = $conn->query("SELECT p.codigo, p.nombre_permiso, p.des_permiso, g.nombre_grupo_p, p.slug FROM Permiso p LEFT JOIN grupo_permiso g ON p.group_permiso = g.codigo ORDER BY p.codigo DESC");
                  while ($row = $q->fetch_assoc()):
                  ?>
                    <tr>
                      <td><?php echo $row['codigo']; ?></td>
                      <td><?php echo htmlspecialchars($row['nombre_permiso']); ?></td>
                      <td><?php echo htmlspecialchars($row['des_permiso']); ?></td>
                      <td><?php echo htmlspecialchars($row['nombre_grupo_p']); ?></td>
                      <td><?php echo htmlspecialchars($row['slug']); ?></td>
                      <td>
                        <button class="btn btn-sm btn-warning editarBtn"
                          data-codigo="<?php echo $row['codigo']; ?>"
                          data-nombre="<?php echo htmlspecialchars($row['nombre_permiso'], ENT_QUOTES); ?>"
                          data-des="<?php echo htmlspecialchars($row['des_permiso'], ENT_QUOTES); ?>"
                          data-grupo="<?php echo htmlspecialchars($row['nombre_grupo_p'], ENT_QUOTES); ?>">
                          <i class="fa fa-edit"></i> Editar
                        </button>
                      </td>
                    </tr>
                  <?php endwhile; ?>
                </tbody>
              </table>
            </div>
          </div>

        </div>
      </section>
    </div>

    <!-- Footer -->
    <footer class="main-footer">
      <div class="float-right d-none d-sm-block">
        <b>Version</b> 1
      </div>
      <strong>Copyright &copy; <?php echo date('Y'); ?> <a href="https://hexasystems.com">Hexa Systems</a>.</strong> Todos los derechos reservados.
    </footer>

    <aside class="control-sidebar control-sidebar-dark"></aside>
  </div>

  <!-- Scripts (mantener mismos plugins que permisos_view para compatibilidad con sidebar) -->
  <script src="../plugins/jquery/jquery.min.js"></script>
  <script src="../plugins/bootstrap/js/bootstrap.bundle.min.js"></script>
  <script src="../plugins/datatables/jquery.dataTables.min.js"></script>
  <script src="../plugins/datatables-bs4/js/dataTables.bootstrap4.min.js"></script>
  <script src="../plugins/datatables-responsive/js/dataTables.responsive.min.js"></script>
  <script src="../plugins/datatables-responsive/js/responsive.bootstrap4.min.js"></script>
  <script src="../dist/js/adminlte.min.js"></script>

  <script>
    $(function() {
      // Inicializar DataTable
      $('#tablaPermisos').DataTable({
        responsive: true,
        paging: true,
        lengthChange: true,
        searching: true,
        ordering: true,
        info: true,
        autoWidth: false,
      });

      // Mostrar/ocultar formulario
      $('#nuevoPermisoBtn').on('click', function(){ $('#formCrear').slideToggle(); });
      $('#cancelCrear').on('click', function(){ $('#formCrear').slideUp(); $('#formPermiso')[0].reset(); });

      // Cargar datos al editar
      $('#tablaPermisos').on('click', '.editarBtn', function(){
        var btn = $(this);
        $('#codigoPermiso').val(btn.data('codigo'));
        $('#nombrePermiso').val(btn.data('nombre'));
        $('#desPermiso').val(btn.data('des'));
        // seleccionar grupo por texto si coincide
        $("#groupPermiso option").filter(function(){ return $(this).text() === btn.data('grupo'); }).prop('selected', true);
        $('#formCrear').slideDown();
        $('html,body').animate({scrollTop: $('#formCrear').offset().top - 20}, 300);
      });

      // Búsqueda rápida vinculada al input de header
      $('#tableSearch').on('keyup', function(){
        var table = $('#tablaPermisos').DataTable();
        table.search(this.value).draw();
      });
    });
  </script>
</body>
</html>
