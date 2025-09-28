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
    console.log('users ready');

    // Inicializar DataTable
    try {
      if ($.fn.DataTable) {
        $("#example1").DataTable({
          responsive: true,
          lengthChange: true,
          pageLength: 25,
          autoWidth: false,
          buttons: ["copy","csv","excel","pdf","print","colvis"],
          language: { url: "//cdn.datatables.net/plug-ins/1.13.6/i18n/es-ES.json" }
        }).buttons().container().appendTo('#example1_wrapper .col-md-6:eq(0)');
      } else {
        console.warn('DataTable no disponible');
      }
    } catch (e) { console.error('DataTable init error', e); }

    // Toggle email_active (AJAX)
    $(document).on('change', '.toggle-email', function(){
      var $cb = $(this);
      var codigo = $cb.data('codigo') || $cb.attr('data-codigo');
      var val = $cb.is(':checked') ? 1 : 0;
      $cb.prop('disabled', true);
      $.post('toggle_email_active.php', { Codigo: codigo, email_active: val })
        .done(function(resp){
          var j;
          try { j = (typeof resp === 'object') ? resp : JSON.parse(resp); }
          catch(e){ alert('Respuesta inesperada del servidor'); $cb.prop('checked', !val); console.error(e, resp); return; }
          if (!j.success) { alert('Error: '+j.message); $cb.prop('checked', !val); }
          else {
            if (typeof $(document).Toasts === 'function') {
              $(document).Toasts('create',{ class: 'bg-success', title: 'Email', body: j.message });
            } else {
              console.log(j.message);
            }
          }
        })
        .fail(function(){ alert('Error de conexión'); $cb.prop('checked', !val); })
        .always(function(){ $cb.prop('disabled', false); });
    });

    // Lectura robusta de data-* (soporte dataset, .data() y .attr())
    function getBtnData($btn){
      var el = $btn.get(0);
      var ds = (el && el.dataset) ? el.dataset : {};
      return {
        codigo: ds.codigo || $btn.attr('data-codigo') || $btn.data('codigo') || $btn.data('codCodigo') || '',
        user: ds.user || $btn.attr('data-user') || $btn.data('user') || '',
        codEmpleado: ds.codEmpleado || $btn.attr('data-cod-empleado') || $btn.data('codEmpleado') || $btn.data('cod-empleado') || '',
        email: ds.email || $btn.attr('data-email') || $btn.data('email') || '',
        emailActive: ds.emailActive || $btn.attr('data-email-active') || $btn.data('emailActive') || $btn.data('email-active') || 0,
        idGroup: ds.idGroup || $btn.attr('data-id-group') || $btn.data('idGroup') || $btn.data('id-group') || '',
        grupo: ds.grupo || $btn.attr('data-grupo') || $btn.data('grupo') || '',
        codTienda: ds.codTienda || $btn.attr('data-cod-tienda') || $btn.data('codTienda') || $btn.data('cod-tienda') || '',
        tienda: ds.tienda || $btn.attr('data-tienda') || $btn.data('tienda') || '',
        state: ds.state || $btn.attr('data-state') || $btn.data('state') || ''
      };
    }

    // Ver modal
    $(document).on('click', '.btn-view', function(e){
      try {
        e.preventDefault();
        var d = getBtnData($(this));
        console.log('open view', d);
        if ($('#viewUserModal').length === 0) { console.warn('Modal #viewUserModal no encontrado'); return; }
        $('#v_codigo').text(d.codigo || '');
        $('#v_user').text(d.user || '');
        $('#v_cod_empleado').text(d.codEmpleado || '');
        $('#v_email').text(d.email || '');
        $('#v_email_active').text((parseInt(d.emailActive) === 1) ? 'Si' : 'No');
        $('#v_grupo').text(d.grupo || d.idGroup || '');
        $('#v_tienda').text(d.tienda || d.codTienda || '');
        $('#v_state').text((parseInt(d.state) === 1) ? 'Activo' : ((parseInt(d.state) === 0) ? 'Inactivo' : (d.state || '')));
        $('#viewUserModal').modal('show');
      } catch(err) { console.error('btn-view error', err); }
    });

    // Editar modal
    $(document).on('click', '.btn-edit', function(e){
      try {
        e.preventDefault();
        var d = getBtnData($(this));
        console.log('open edit', d);
        if ($('#editUserModal').length === 0) { console.warn('Modal #editUserModal no encontrado'); return; }
        $('#e_codigo').val(d.codigo || '');
        $('#e_user').val(d.user || '');
        $('#e_cod_empleado').val(d.codEmpleado || '');
        $('#e_email').val(d.email || '');
        $('#e_email_active').prop('checked', parseInt(d.emailActive) === 1);
        $('#e_id_group').val(d.idGroup || '');
        $('#e_cod_tienda').val(d.codTienda || '');
        $('#e_state').val(d.state || '');
        $('#e_password').val('');
        $('#editUserModal').modal('show');
      } catch(err) { console.error('btn-edit error', err); }
    });

    // Eliminar modal
    $(document).on('click', '.btn-delete', function(e){
      try {
        e.preventDefault();
        var d = getBtnData($(this));
        console.log('open delete', d);
        if ($('#deleteUserModal').length === 0) { console.warn('Modal #deleteUserModal no encontrado'); return; }
        $('#d_codigo').val(d.codigo || '');
        $('#d_user').text(d.user || '');
        $('#d_codigo_txt').text(d.codigo || '');
        $('#deleteUserModal').modal('show');
      } catch(err) { console.error('btn-delete error', err); }
    });

    // Cargar tiendas y grupos para los selects
    function cargarSelectsUsuarios() {
      console.log('Iniciando carga de selects...');
      
      // Tiendas
      $.ajax({
        url: 'usuarios_ajax.php',
        type: 'POST',
        data: { action: 'get_tiendas' },
        dataType: 'json',
        success: function(resp) {
          console.log('Tiendas recibidas:', resp);
          var opts = '<option value="">Seleccione...</option>';
          if (resp && Array.isArray(resp) && resp.length > 0) {
            resp.forEach(function(t) {
              opts += '<option value="'+t.cod_tienda+'">'+t.nombre+'</option>';
            });
          } else {
            console.warn('No hay tiendas o respuesta inválida');
          }
          $('#c_cod_tienda, #e_cod_tienda').html(opts);
        },
        error: function(xhr, status, error) {
          console.error('Error cargando tiendas:', error, xhr.responseText);
          $('#c_cod_tienda, #e_cod_tienda').html('<option value="">Error al cargar</option>');
        }
      });
      
      // Grupos
      $.ajax({
        url: 'usuarios_ajax.php',
        type: 'POST',
        data: { action: 'get_grupos' },
        dataType: 'json',
        success: function(resp) {
          console.log('Grupos recibidos:', resp);
          var opts = '<option value="">Seleccione...</option>';
          if (resp && Array.isArray(resp) && resp.length > 0) {
            resp.forEach(function(g) {
              opts += '<option value="'+g.codigo+'">'+g.nombre_grupo+'</option>';
            });
          } else {
            console.warn('No hay grupos o respuesta inválida');
          }
          $('#c_id_group, #e_id_group').html(opts);
        },
        error: function(xhr, status, error) {
          console.error('Error cargando grupos:', error, xhr.responseText);
          $('#c_id_group, #e_id_group').html('<option value="">Error al cargar</option>');
        }
      });
    }

    // Eventos de modales
    $('#createUserModal').on('show.bs.modal', function(){
      $('#c_cod_tienda').html('<option value="">Cargando...</option>');
      $('#c_id_group').html('<option value="">Cargando...</option>');
      cargarSelectsUsuarios();
    });

    $('#editUserModal').on('show.bs.modal', function(){
      $('#e_cod_tienda').html('<option value="">Cargando...</option>');
      $('#e_id_group').html('<option value="">Cargando...</option>');
      cargarSelectsUsuarios();
    });

    // Reportar errores JS en consola (no suprimir)
    window.onerror = function(msg, url, line, col, error) {
      console.error('JS error:', msg, 'at', url+':'+line+':'+col, error);
      return false;
    };
  });
</script>
</body>
</php>