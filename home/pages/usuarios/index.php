<?php 
session_start();
if (!isset($_SESSION["username"]) || !isset($_SESSION['cod_user'])) {
  header('Location: ../../login.php');
  exit();
}

$User = $_SESSION["username"];
$cod_user = $_SESSION['cod_user'];


include("../../../conexion.php");
include("../../logica/ac_permiso.php");

// Validar permiso para ver usuarios
if (Tiene_permiso($permisos_user, 'ver-usuarios')) {
  echo "<script>
          alert('No tienes acceso a esta página.');
          window.location.href = '/home/dashboard.php';
        </script>";
  exit();
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Listado de Usuarios | iThink Web</title>
  <link rel="icon" href="/home/dist/img/logo_ithink.png" type="image/x-icon">
  <!-- Google Font: Source Sans Pro -->
  <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">
  <!-- Font Awesome -->
  <link rel="stylesheet" href="../../plugins/fontawesome-free/css/all.min.css">
  <!-- DataTables -->
  <link rel="stylesheet" href="../../plugins/datatables-bs4/css/dataTables.bootstrap4.min.css">
  <link rel="stylesheet" href="../../plugins/datatables-responsive/css/responsive.bootstrap4.min.css">
  <link rel="stylesheet" href="../../plugins/datatables-buttons/css/buttons.bootstrap4.min.css">
  <!-- Theme style -->
  <link rel="stylesheet" href="../../dist/css/adminlte.min.css">
  <link rel="stylesheet" href="../../dist/css/app.css">
</head>
<body class="hold-transition sidebar-mini layout-fixed">
<div class="wrapper">
  <!-- Incluimos el Sidebar de raíz -->
  <?php include("sidebar.php"); ?>

  <!-- Content Wrapper. Contenido de la Página -->
  <div class="content-wrapper">
    <!-- Encabezado de la Página -->
    <section class="content-header">
      <div class="container-fluid">
        <div class="row mb-2">
          <div class="col-sm-6">
            <h1>Listado de Usuarios</h1>
          </div>
          <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
              <li class="breadcrumb-item"><a href="../../dashboard.php">Home</a></li>
              <li class="breadcrumb-item active">Usuarios</li>
            </ol>
          </div>
        </div>
      </div>
    </section>

    <!-- Contenido Principal -->
    <section class="content">
      <div class="container-fluid">
        <div class="card">
          <div class="card-header">
            <h3 class="card-title">Usuarios del Sistema</h3>
            <div class="card-tools">
              <!-- Botón para abrir el modal de nuevo usuario -->
              <button type="button" class="btn btn-success btn-sm" data-toggle="modal" data-target="#modalNuevoUsuario">
                <i class="fas fa-user-plus"></i> Nuevo Usuario
              </button>
            </div>
          </div>
          <!-- /.card-header -->
          <div class="card-body table-responsive p-0">
            <table id="usuariosTable" class="table table-bordered table-striped">
              <thead>
                <tr>
                  <th>ID</th>
                  <th>Nombre</th>
                  <th>Email</th>
                  <th>Rol</th>
                  <th class="text-right">Acciones</th>
                </tr>
              </thead>
              <tbody>
                <?php 
                $sql = "SELECT * FROM usuarios ORDER BY id ASC";
                $result = mysqli_query($conn, $sql);
                if($result){
                  while($row = mysqli_fetch_array($result)) {
                    echo "<tr>";
                      echo "<td>" . $row['id'] . "</td>";
                      echo "<td>" . htmlspecialchars($row['nombre']) . "</td>";
                      echo "<td>" . htmlspecialchars($row['email']) . "</td>";
                      echo "<td>" . htmlspecialchars($row['rol']) . "</td>";
                      echo "<td class='text-right'>";
                        echo "<a href='editar.php?id=" . $row['id'] . "' class='btn btn-info btn-sm'><i class='fas fa-edit'></i> Editar</a> ";
                        echo "<a href='eliminar.php?id=" . $row['id'] . "' class='btn btn-danger btn-sm' onclick=\"return confirm('¿Desea eliminar este usuario?');\"><i class='fas fa-trash-alt'></i> Eliminar</a>";
                      echo "</td>";
                    echo "</tr>";
                  }
                } else {
                  echo "<tr><td colspan='5'>No se encontraron usuarios.</td></tr>";
                }
                ?>
              </tbody>
              <tfoot>
                <tr>
                  <th>ID</th>
                  <th>Nombre</th>
                  <th>Email</th>
                  <th>Rol</th>
                  <th class="text-right">Acciones</th>
                </tr>
              </tfoot>
            </table>
          </div>
          <!-- /.card-body -->
        </div>
        <!-- /.card -->
      </div><!-- /.container-fluid -->
    </section>
    <!-- /.section content -->
  </div>
  <!-- /.content-wrapper -->

  <!-- Footer -->
  <footer class="main-footer">
    <div class="float-right d-none d-sm-block">
      <b>Version</b> 1
    </div>
    <strong>Copyright &copy; 2023 
      <a href="https://hexasystems.com">Hexa Systems</a>.
    </strong> All rights reserved.
  </footer>

  <!-- Control Sidebar -->
  <aside class="control-sidebar control-sidebar-dark">
    <!-- Contenido del sidebar de control -->
  </aside>
</div>
<!-- ./wrapper -->

<!-- Modal para Nuevo Usuario -->
<div class="modal fade" id="modalNuevoUsuario" tabindex="-1" role="dialog" aria-labelledby="modalNuevoUsuarioLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <form id="nuevoUsuarioForm" action="nuevo_usuario.php" method="post">
        <div class="modal-header">
          <h5 class="modal-title" id="modalNuevoUsuarioLabel">Crear Nuevo Usuario</h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Cerrar">
              <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body">
          <!-- Campos para el nuevo usuario -->
          <div class="form-group">
            <label for="nuevo_nombre">Nombre</label>
            <input type="text" class="form-control" id="nuevo_nombre" name="nombre" required>
          </div>
          <div class="form-group">
            <label for="nuevo_email">Email</label>
            <input type="email" class="form-control" id="nuevo_email" name="email" required>
          </div>
          <div class="form-group">
            <label for="nuevo_password">Contraseña</label>
            <input type="password" class="form-control" id="nuevo_password" name="password" required>
          </div>
          <div class="form-group">
            <label for="nuevo_rol">Rol</label>
            <select class="form-control" id="nuevo_rol" name="rol" required>
              <option value="">Seleccione Rol</option>
              <option value="admin">Admin</option>
              <option value="usuario">Usuario</option>
              <!-- Agrega más roles según sea necesario -->
            </select>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
          <button type="submit" class="btn btn-primary">Crear Usuario</button>
        </div>
      </form>
    </div>
  </div>
</div>

<!-- jQuery -->
<script src="../../plugins/jquery/jquery.min.js"></script>
<!-- Bootstrap 4 -->
<script src="../../plugins/bootstrap/js/bootstrap.bundle.min.js"></script>
<!-- DataTables & Plugins -->
<script src="../../plugins/datatables/jquery.dataTables.min.js"></script>
<script src="../../plugins/datatables-bs4/js/dataTables.bootstrap4.min.js"></script>
<script src="../../plugins/datatables-responsive/js/dataTables.responsive.min.js"></script>
<script src="../../plugins/datatables-responsive/js/responsive.bootstrap4.min.js"></script>
<script src="../../plugins/datatables-buttons/js/dataTables.buttons.min.js"></script>
<script src="../../plugins/datatables-buttons/js/buttons.bootstrap4.min.js"></script>
<!-- AdminLTE App -->
<script src="../../dist/js/adminlte.min.js"></script>

<!-- Inicialización de DataTables -->
<script>
  $(function () {
    $("#usuariosTable").DataTable({
      "responsive": true, 
      "lengthChange": false, 
      "autoWidth": false,
      "buttons": ["copy", "csv", "excel", "pdf", "print", "colvis"]
    }).buttons().container().appendTo('#usuariosTable_wrapper .col-md-6:eq(0)');
  });
</script>
</body>
</html>
