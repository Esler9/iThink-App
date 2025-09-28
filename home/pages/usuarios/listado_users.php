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
                <button type="button" class="btn btn-sm btn-success" data-toggle="modal" data-target="#createUserModal">
                  <i class="fas fa-plus"></i> Nuevo
                </button>
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
  $email_checked = ((int)$email_active === 1) ? 'checked' : '';
  $email_switch = '
    <div class="custom-control custom-switch">
      <input type="checkbox" class="custom-control-input toggle-email" id="toggle-'.htmlspecialchars($codigo).'" data-codigo="'.htmlspecialchars($codigo).'" '. $email_checked .'>
      <label class="custom-control-label" for="toggle-'.htmlspecialchars($codigo).'"></label>
    </div>
  ';

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
        <td><?php echo $email_switch; ?></td>
        <td><span class="badge badge-<?php echo $group_badge; ?> badge-group"><?php echo htmlspecialchars($group_name); ?></span></td>
        <td><?php echo htmlspecialchars($tienda_nombre); ?></td>
        <td><span class="badge badge-<?php echo $state_badge; ?>"><?php echo htmlspecialchars($state_label); ?></span></td>
        <td class="table-actions">
          <button
            class="btn btn-sm btn-info btn-view"
            data-codigo="<?php echo htmlspecialchars($codigo);?>"
            data-user="<?php echo htmlspecialchars($user);?>"
            data-cod-empleado="<?php echo htmlspecialchars($cod_empleado);?>"
            data-email="<?php echo htmlspecialchars($email);?>"
            data-email-active="<?php echo (int)$email_active;?>"
            data-id-group="<?php echo htmlspecialchars($id_group);?>"
            data-grupo="<?php echo htmlspecialchars($group_name);?>"
            data-cod-tienda="<?php echo htmlspecialchars($cod_tienda);?>"
            data-tienda="<?php echo htmlspecialchars($tienda_nombre);?>"
            data-state="<?php echo htmlspecialchars($state);?>"
            title="Ver"><i class="fas fa-eye"></i></button>

          <button
            class="btn btn-sm btn-warning btn-edit"
            data-codigo="<?php echo htmlspecialchars($codigo);?>"
            data-user="<?php echo htmlspecialchars($user);?>"
            data-cod-empleado="<?php echo htmlspecialchars($cod_empleado);?>"
            data-email="<?php echo htmlspecialchars($email);?>"
            data-email-active="<?php echo (int)$email_active;?>"
            data-id-group="<?php echo htmlspecialchars($id_group);?>"
            data-cod-tienda="<?php echo htmlspecialchars($cod_tienda);?>"
            data-state="<?php echo htmlspecialchars($state);?>"
            title="Editar" <?php if(!Tiene_permiso($permisos_user,'editar-usuario')) echo 'disabled'; ?>><i class="fas fa-edit"></i></button>

          <button
            class="btn btn-sm btn-danger btn-delete"
            data-codigo="<?php echo htmlspecialchars($codigo);?>"
            data-user="<?php echo htmlspecialchars($user);?>"
            title="Eliminar" <?php if(!Tiene_permiso($permisos_user,'eliminar-usuario')) echo 'disabled'; ?>><i class="fas fa-trash"></i></button>
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

<!-- incluir modales justo antes de los scripts -->
<?php include("modal-users.php"); ?>

<!-- JS: inicializar DataTables, toggle y handlers de modales -->
<script>
  $(function () {
    // Utilidades
    function populateSelect($sel, items, valueKey, textKey, placeholder) {
      $sel.empty();
      if (placeholder) $sel.append($('<option>').val('').text(placeholder));
      if (!Array.isArray(items)) return;
      items.forEach(function(it){
        // Evitar option duplicadas
        if ($sel.find('option[value="' + it[valueKey] + '"]').length === 0) {
          $sel.append($('<option>').val(it[valueKey]).text(it[textKey]));
        }
      });
    }

    // Carga combinada de grupos y tiendas; selected opcional
    function loadGroupsAndTiendas(opts) {
      var gSel = opts.groupSel;
      var tSel = opts.tiendaSel;
      var selG = (typeof opts.selectedGroup !== 'undefined') ? opts.selectedGroup : '';
      var selT = (typeof opts.selectedTienda !== 'undefined') ? opts.selectedTienda : '';

      var url = 'usuarios_ajax.php';

      var gReq = $.getJSON(url, { action: 'list_groups' });
      var tReq = $.getJSON(url, { action: 'list_tiendas' });

      $.when(gReq, tReq).done(function(gRes, tRes){
        // gRes[0] y tRes[0] contienen los arrays de datos
        var groups = gRes[0];
        var tiendas = tRes[0];

        populateSelect(gSel, groups, 'codigo', 'nombre', 'Seleccione grupo');
        populateSelect(tSel, tiendas, 'cod_tienda', 'nombre', 'Seleccione tienda');

        if (selG !== '' && gSel.find('option[value="' + selG + '"]').length) gSel.val(selG);
        if (selT !== '' && tSel.find('option[value="' + selT + '"]').length) tSel.val(selT);
      }).fail(function(jq, textStatus, errorThrown){
        console.error('Error cargando grupos/tiendas:', textStatus, errorThrown);
        gSel.empty().append($('<option>').val('').text('Error al cargar grupos'));
        tSel.empty().append($('<option>').val('').text('Error al cargar tiendas'));
      });
    }

    // Al abrir Crear: poblar (sin seleccionados) y resetear formulario
    $('#createUserModal').on('show.bs.modal', function () {
      loadGroupsAndTiendas({
        groupSel: $('#c_id_group'),
        tiendaSel: $('#c_cod_tienda')
      });
      $('#formCreateUser')[0].reset();
      $('#c_email_active').prop('checked', false);
    });

    // Al abrir Editar: leer data-* del trigger y poblar, seleccionar valores
    $('#editUserModal').on('show.bs.modal', function (e) {
      var trigger = $(e.relatedTarget);
      var codigo = trigger.data('codigo') || '';
      var user = trigger.data('user') || '';
      var cod_empleado = trigger.data('cod_empleado') || '';
      var email = trigger.data('email') || '';
      var id_group = trigger.data('id_group') || '';
      var cod_tienda = trigger.data('cod_tienda') || '';
      var state = (typeof trigger.data('state') !== 'undefined') ? trigger.data('state') : '1';
      var email_active = trigger.data('email_active') ? true : false;

      $('#e_codigo').val(codigo);
      $('#e_user').val(user);
      $('#e_cod_empleado').val(cod_empleado);
      $('#e_email').val(email);
      $('#e_password').val('');
      $('#e_state').val(state);
      $('#e_email_active').prop('checked', email_active);

      loadGroupsAndTiendas({
        groupSel: $('#e_id_group'),
        tiendaSel: $('#e_cod_tienda'),
        selectedGroup: id_group,
        selectedTienda: cod_tienda
      });
    });

    // Handler básico para Ver (si usas botones con data-*)
    $(document).on('click', '.btn-view', function(){
      var $b = $(this);
      $('#v_codigo').text($b.data('codigo') || '-');
      $('#v_user').text($b.data('user') || '-');
      $('#v_cod_empleado').text($b.data('cod_empleado') || '-');
      $('#v_email').text($b.data('email') || '-');
      $('#v_email_active').text($b.data('email_active') ? 'Sí' : 'No');
      $('#v_grupo').text($b.data('grupo') || '-');
      $('#v_tienda').text($b.data('tienda') || '-');
      $('#v_state').text($b.data('state') || '-');
      $('#viewUserModal').modal('show');
    });

    // Depuración: informa si no encuentra el endpoint
    $.ajaxSetup({
      error: function (jqXHR, textStatus, errorThrown) {
        // No sobreescribir globales si ya tienes manejo
      }
    });
  });
</script>
</body>
</php>