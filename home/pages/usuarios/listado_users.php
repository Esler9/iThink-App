<!DOCTYPE php>
<?php
session_start();
// Verificar que las variables de sesión estén configuradas
if (!isset($_SESSION["username"]) || !isset($_SESSION['cod_user'])) {
  header('Location: ../../login.php');
  exit();
}

$User = $_SESSION["username"];
$cod_user = $_SESSION['cod_user'];

include("../../../conexion.php");
include("../../logica/ac_permiso.php"); 

// Validar permiso
if (!Tiene_permiso($permisos_user,'ver-usuarios')) {
  echo "<script>
        alert('No tienes acceso a esta página.');
        window.location.href = '/home/dashboard.php';
        </script>";
  exit();
}
?>
<php lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>iThink | Usuarios </title>
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

  <style>
    .badge-group { font-weight:600; }
    .table-actions .btn { margin-right:5px; }
  </style>
</head>
<body class="hold-transition sidebar-mini layout-fixed">
 
<?php include("sidebar.php");?>

<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
  <!-- Content Header (Page header) -->
  <section class="content-header">
    <div class="container-fluid">
      <div class="row mb-2">
        <div class="col-sm-6">
          <h1>Usuarios</h1>
        </div>
        <div class="col-sm-6">
          <ol class="breadcrumb float-sm-right">
            <li class="breadcrumb-item"><a href="../../dashboard.php">Home</a></li>
            <li class="breadcrumb-item active">Usuarios</li>
          </ol>
        </div>
      </div>
    </div><!-- /.container-fluid -->
  </section>

  <!-- Main content: Tabla de usuarios -->
  <section class="content">
    <div class="container-fluid">
      <div class="row">
        <div class="col-12">
          <div class="card card-outline card-primary">
            <div class="card-header">
              <h3 class="card-title">Listado de Usuarios</h3>
              <div class="card-tools">
                <a href="crear_usuario.php" class="btn btn-sm btn-success"><i class="fas fa-plus"></i> Nuevo</a>
              </div>
            </div>
            <div class="card-body">
<?php
// Consulta con JOIN a tienda y grupo_user para mostrar nombres en lugar de sólo códigos
$sql_users = "
  SELECT u.Codigo, u.`User`, u.Cod_Empleado, u.Password, u.State, u.cod_tienda, u.email, u.email_active, u.id_group_user,
         t.nombre AS tienda_nombre,
         g.nombre_grupo
  FROM `Usuarios` u
  LEFT JOIN `tienda` t ON u.cod_tienda = t.cod_tienda
  LEFT JOIN `grupo_user` g ON u.id_group_user = g.codigo
  ORDER BY u.Codigo DESC
";
$res_users = mysqli_query($conn, $sql_users);

if (!$res_users) {
  echo '<div class="alert alert-danger">Error al consultar usuarios: '.htmlspecialchars(mysqli_error($conn)).'</div>';
} elseif (mysqli_num_rows($res_users) === 0) {
  echo '<div class="alert alert-info">No se encontraron usuarios.</div>';
} else {
?>
  <div class="table-responsive">
    <table id="example1" class="table table-bordered table-striped table-hover">
      <thead class="thead-dark">
        <tr>
          <th>Codigo</th>
          <th>User</th>
          <th>Cod_Empleado</th>
          <th>Email</th>
          <th>Email Active</th>
          <th>Grupo</th>
          <th>Tienda</th>
          <th>Estado</th>
          <th style="width:140px">Acciones</th>
        </tr>
      </thead>
      <tbody>
<?php
while ($u = mysqli_fetch_assoc($res_users)) {
  $codigo = $u['Codigo'];
  $user = $u['User'];
  $cod_empleado = $u['Cod_Empleado'];
  $email = $u['email'];
  $email_active = $u['email_active'];
  $id_group = $u['id_group_user'];
  $cod_tienda = $u['cod_tienda'];
  $state = $u['State'];

  $group_name = $u['nombre_grupo'] ?? ($id_group ? 'Grupo '.$id_group : 'Sin grupo');
  // Badges
  switch ((string)$id_group) {
    case '1': $group_badge = 'danger'; break;
    case '2': $group_badge = 'primary'; break;
    case '3': $group_badge = 'info'; break;
    default: $group_badge = 'secondary';
  }
  $email_badge = (int)$email_active === 1 ? '<span class="badge badge-success">Verificado</span>' : '<span class="badge badge-secondary">No</span>';
  $state_lower = strtolower((string)$state);
  if (in_array($state_lower, ['1','activo','true','yes','on'])) { $state_label = 'Activo'; $state_badge = 'success'; }
  elseif (in_array($state_lower, ['0','inactivo','false','no','off'])) { $state_label = 'Inactivo'; $state_badge = 'dark'; }
  else { $state_label = ucfirst($state_lower); $state_badge = 'info'; }

  $tienda_nombre = $u['tienda_nombre'] ?? $cod_tienda;
?>
      <tr>
        <td><?php echo htmlspecialchars($codigo); ?></td>
        <td><?php echo htmlspecialchars($user); ?></td>
        <td><?php echo htmlspecialchars($cod_empleado); ?></td>
        <td><?php echo htmlspecialchars($email); ?></td>
        <td><?php echo $email_badge; ?></td>
        <td><span class="badge badge-<?php echo $group_badge; ?> badge-group"><?php echo htmlspecialchars($group_name); ?></span></td>
        <td><?php echo htmlspecialchars($tienda_nombre); ?></td>
        <td><span class="badge badge-<?php echo $state_badge; ?>"><?php echo htmlspecialchars($state_label); ?></span></td>
        <td class="table-actions">
          <a href="ver_usuario.php?codigo=<?php echo urlencode($codigo); ?>" class="btn btn-sm btn-info" title="Ver"><i class="fas fa-eye"></i></a>
          <a href="editar_usuario.php?codigo=<?php echo urlencode($codigo); ?>" class="btn btn-sm btn-warning" title="Editar" <?php if(!Tiene_permiso($permisos_user,'editar-usuarios')) echo 'disabled'; ?>><i class="fas fa-edit"></i></a>
          <a href="eliminar_usuario.php?codigo=<?php echo urlencode($codigo); ?>" class="btn btn-sm btn-danger" title="Eliminar" onclick="return confirm('¿Eliminar usuario <?php echo addslashes($user); ?>?')" <?php if(!Tiene_permiso($permisos_user,'eliminar-usuarios')) echo 'disabled'; ?>><i class="fas fa-trash"></i></a>
        </td>
      </tr>
<?php } // end while ?>
      </tbody>
      <tfoot>
        <tr>
          <th>Codigo</th>
          <th>User</th>
          <th>Cod_Empleado</th>
          <th>Email</th>
          <th>Email Active</th>
          <th>Grupo</th>
          <th>Tienda</th>
          <th>Estado</th>
          <th>Acciones</th>
        </tr>
      </tfoot>
    </table>
  </div>
<?php } // end else ?>
            </div>
          </div>
        </div>
      </div>
    </div>
  </section>
  <!-- /.content -->
</div>
<!-- /.content-wrapper -->

<footer class="main-footer">
  <div class="float-right d-none d-sm-block">
    <b>Version</b> 1
  </div>
  <strong>Copyright &copy; 2023 <a href="https://hexasystems.com">Hexa Systems</a>.</strong> All rights reserved.
</footer>

<!-- Control Sidebar -->
<aside class="control-sidebar control-sidebar-dark"></aside>

<!-- jQuery -->
<script src="../../plugins/jquery/jquery.min.js"></script>
<!-- Bootstrap 4 -->
<script src="../../plugins/bootstrap/js/bootstrap.bundle.min.js"></script>
<!-- DataTables  & Plugins -->
<script src="../../plugins/datatables/jquery.dataTables.min.js"></script>
<script src="../../plugins/datatables-bs4/js/dataTables.bootstrap4.min.js"></script>
<script src="../../plugins/datatables-responsive/js/dataTables.responsive.min.js"></script>
<script src="../../plugins/datatables-responsive/js/responsive.bootstrap4.min.js"></script>
<script src="../../plugins/datatables-buttons/js/dataTables.buttons.min.js"></script>
<script src="../../plugins/datatables-buttons/js/buttons.bootstrap4.min.js"></script>
<script src="../../plugins/jszip/jszip.min.js"></script>
<script src="../../plugins/pdfmake/pdfmake.min.js"></script>
<script src="../../plugins/pdfmake/vfs_fonts.js"></script>
<script src="../../plugins/datatables-buttons/js/buttons.html5.min.js"></script>
<script src="../../plugins/datatables-buttons/js/buttons.print.min.js"></script>
<script src="../../plugins/datatables-buttons/js/buttons.colVis.min.js"></script>
<!-- AdminLTE App -->
<script src="../../dist/js/adminlte.min.js"></script>

<script>
  $(function () {
    $("#example1").DataTable({
      responsive: true,
      lengthChange: true,
      pageLength: 25,
      autoWidth: false,
      buttons: ["copy", "csv", "excel", "pdf", "print", "colvis"],
      language: { url: "//cdn.datatables.net/plug-ins/1.13.6/i18n/es-ES.json" }
    }).buttons().container().appendTo('#example1_wrapper .col-md-6:eq(0)');
  });
</script>
</body>
</php>