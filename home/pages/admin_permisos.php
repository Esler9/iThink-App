<?php
session_start();

// Config / mantenimiento
include_once __DIR__ . '/../../Setting.php';

// Si hay mantenimiento detener
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

// Traer grupos y permisos (función Traer_grupo_usuario asumida en dt_permisos.php)
$grupos = Traer_grupo_usuario($conn);

// Construir arreglo de permisos por grupo (si dt_permisos provee permisos)
$groupPermissionsArray = [];
if (function_exists('Traer_permisos_por_grupo')) {
    foreach ($grupos as $g) {
        $groupPermissionsArray[$g['codigo']] = Traer_permisos_por_grupo($conn, $g['codigo']);
    }
}

// Manejo de formulario (crear/editar permiso) - similar a la versión previa pero compatible con la estructura de permisos_view
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

  <!-- estilos (puedes cambiar por AdminLTE si lo usas) -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.1/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">

  <style>
    :root{--sidebar-width:260px}
    html,body{height:100%;margin:0}
    .wrapper{display:flex;min-height:100vh}
    .main-sidebar{width:var(--sidebar-width);flex:0 0 var(--sidebar-width);background:#343a40;color:#fff;padding:1rem;overflow:auto}
    .main-sidebar a{color:#cfd8dc;display:block;padding:.5rem 1rem;text-decoration:none}
    .main-sidebar a:hover{background:rgba(255,255,255,.03);color:#fff}
    .content-area{flex:1;padding:1.25rem;background:#f4f6f9;min-width:0}
    .content-inner{max-width:1200px;margin:0 auto}
    .table-responsive{overflow:auto}
    @media (max-width:768px){.wrapper{flex-direction:column}.main-sidebar{width:100%}}
  </style>
</head>
<body>
<div class="wrapper">
  <!-- Sidebar -->
  <?php include_once __DIR__ . '/sidebar.php'; ?>

  <!-- Contenido -->
  <main class="content-area">
    <div class="content-inner">

      <div class="d-flex justify-content-between align-items-center mb-3">
        <h3 class="m-0">Administrar Permisos</h3>
        <div>
          <button id="nuevoPermisoBtn" class="btn btn-primary">Nuevo Permiso</button>
        </div>
      </div>

      <!-- Mensaje -->
      <?php if (!empty($_GET['msg'])): ?>
        <div class="alert alert-info alert-dismissible fade show" role="alert">
          <?php echo htmlspecialchars($_GET['msg']); ?>
          <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Cerrar"></button>
        </div>
      <?php endif; ?>

      <!-- Formulario creación -->
      <div id="formCrear" class="card mb-4" style="display:none;">
        <div class="card-body">
          <form method="post" id="formPermiso">
            <input type="hidden" name="codigo" id="codigoPermiso" value="">
            <div class="mb-3">
              <label class="form-label">Nombre permiso</label>
              <input class="form-control" name="nombre_permiso" id="nombrePermiso" required>
            </div>
            <div class="mb-3">
              <label class="form-label">Descripción</label>
              <textarea class="form-control" name="des_permiso" id="desPermiso"></textarea>
            </div>
            <div class="mb-3">
              <label class="form-label">Grupo</label>
              <select class="form-select" name="group_permiso" id="groupPermiso" required>
                <?php
                // $grupos puede ser array o mysqli_result dependiendo de Traer_grupo_usuario
                if (is_array($grupos)) {
                    foreach ($grupos as $g) {
                        echo '<option value="'.htmlspecialchars($g['codigo']).'">'.htmlspecialchars($g['nombre_grupo_p'] ?? $g['nombre']).'</option>';
                    }
                } else {
                    while ($g = $grupos->fetch_assoc()) {
                        echo '<option value="'.htmlspecialchars($g['codigo']).'">'.htmlspecialchars($g['nombre_grupo_p'] ?? $g['nombre']).'</option>';
                    }
                }
                ?>
              </select>
            </div>
            <button class="btn btn-success" type="submit">Guardar</button>
            <button type="button" id="cancelCrear" class="btn btn-secondary">Cancelar</button>
          </form>
        </div>
      </div>

      <!-- Tabla de permisos -->
      <div class="card">
        <div class="card-body table-responsive">
          <table class="table table-striped" id="tablaPermisos">
            <thead>
              <tr><th>Código</th><th>Nombre</th><th>Descripción</th><th>Grupo</th><th>Slug</th><th>Acciones</th></tr>
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
                      <i class="fa fa-edit"></i>
                    </button>
                  </td>
                </tr>
              <?php endwhile; ?>
            </tbody>
          </table>
        </div>
      </div>

    </div>
  </main>
</div>

<!-- Scripts -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.1/dist/js/bootstrap.bundle.min.js"></script>
<script>
  (function($){
    $('#nuevoPermisoBtn').on('click', function(){ $('#formCrear').slideToggle(); });
    $('#cancelCrear').on('click', function(){ $('#formCrear').slideUp(); $('#formPermiso')[0].reset(); });

    // editar: cargar datos en formulario y mostrar
    $('.editarBtn').on('click', function(){
      var btn = $(this);
      $('#codigoPermiso').val(btn.data('codigo'));
      $('#nombrePermiso').val(btn.data('nombre'));
      $('#desPermiso').val(btn.data('des'));
      // intentar seleccionar grupo por texto (si coincide)
      $('#groupPermiso option').filter(function(){ return $(this).text() === btn.data('grupo'); }).prop('selected', true);
      $('#formCrear').slideDown();
      $('html,body').animate({scrollTop: $('#formCrear').offset().top - 20}, 300);
    });
  })(jQuery);
</script>
</body>
</html>
