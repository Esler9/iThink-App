<!DOCTYPE html>
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
<html lang="es">
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
<?php
// Consulta con JOIN a tienda y grupo_user para mostrar nombres en lugar de sólo códigos
$sql_users = "
  SELECT u.Codigo, u.`User`, u.Cod_Empleado, u.email, u.email_active, u.State,
         u.cod_tienda, u.id_group_user,
         t.nombre AS tienda_nombre,
         g.nombre_grupo
  FROM `Usuarios` u
  LEFT JOIN tienda t ON u.cod_tienda = t.cod_tienda
  LEFT JOIN grupo_user g ON u.id_group_user = g.codigo
  ORDER BY u.Codigo DESC
";
$res_users = mysqli_query($conn, $sql_users);

if (!$res_users) {
  echo '<div class="alert alert-danger">Error al consultar usuarios: '.htmlspecialchars(mysqli_error($conn)).'</div>';
} else {
?>

<section class="content">
  <div class="container-fluid">
    <div class="card">
      <div class="card-header">
        <h3 class="card-title">Listado de Usuarios</h3>
        <div class="card-tools">
          <button class="btn btn-success btn-sm" data-toggle="modal" data-target="#createUserModal"><i class="fas fa-user-plus"></i> Crear</button>
        </div>
      </div>
      <div class="card-body">
        <table id="usersTable" class="table table-bordered table-striped">
          <thead>
            <tr>
              <th>Código</th>
              <th>Usuario</th>
              <th>Cód. Empleado</th>
              <th>Email</th>
              <th>Grupo</th>
              <th>Tienda</th>
              <th>Estado</th>
              <th>Acción</th>
            </tr>
          </thead>
          <tbody>
            <?php while ($u = mysqli_fetch_assoc($res_users)) {
              $codigo = htmlspecialchars($u['Codigo']);
              $user = htmlspecialchars($u['User']);
              $cod_empleado = htmlspecialchars($u['Cod_Empleado']);
              $email = htmlspecialchars($u['email']);
              $email_active = intval($u['email_active']);
              $grupo_id = htmlspecialchars($u['id_group_user']);
              $grupo_nombre = htmlspecialchars($u['nombre_grupo'] ?: '-');
              $tienda_id = htmlspecialchars($u['cod_tienda']);
              $tienda_nombre = htmlspecialchars($u['tienda_nombre'] ?: '-');
              $state = ($u['State'] == '1') ? 'Activo' : 'Inactivo';
            ?>
            <tr>
              <td><?php echo $codigo; ?></td>
              <td><?php echo $user; ?></td>
              <td><?php echo $cod_empleado; ?></td>
              <td><?php echo $email; ?></td>
              <td><?php echo $grupo_nombre; ?></td>
              <td><?php echo $tienda_nombre; ?></td>
              <td><?php echo $state; ?></td>
              <td>
                <button type="button" class="btn btn-app bg-info btn-view"
                         data-toggle="modal" data-target="#viewUserModal"
                         data-codigo="<?php echo $codigo;?>"
                         data-user="<?php echo $user;?>"
                         data-cod_empleado="<?php echo $cod_empleado;?>"
                         data-email="<?php echo $email;?>"
                         data-email_active="<?php echo $email_active;?>"
                         data-id_group="<?php echo $grupo_id;?>"
                         data-grupo="<?php echo $grupo_nombre;?>"
                         data-cod_tienda="<?php echo $tienda_id;?>"
                         data-tienda="<?php echo $tienda_nombre;?>"
                         data-state="<?php echo $state;?>"
                         title="Ver"><i class="fas fa-eye"></i></button>

                <button type="button" class="btn btn-app bg-warning btn-edit"
                         data-toggle="modal" data-target="#editUserModal"
                         data-codigo="<?php echo $codigo;?>"
                         data-user="<?php echo $user;?>"
                         data-cod_empleado="<?php echo $cod_empleado;?>"
                         data-email="<?php echo $email;?>"
                         data-email_active="<?php echo $email_active;?>"
                         data-id_group="<?php echo $grupo_id;?>"
                         data-cod_tienda="<?php echo $tienda_id;?>"
                         data-state="<?php echo ($u['State']);?>"
                         title="Editar"><i class="fas fa-edit"></i></button>

                <button type="button" class="btn btn-app bg-danger btn-delete"
                         data-toggle="modal" data-target="#deleteUserModal"
                         data-codigo="<?php echo $codigo;?>"
                         data-user="<?php echo $user;?>"
                         title="Eliminar"><i class="fas fa-user-times"></i></button>
              </td>
            </tr>
            <?php } // end while ?>
          </tbody>
          <tfoot>
            <tr>
              <th>Código</th>
              <th>Usuario</th>
              <th>Cód. Empleado</th>
              <th>Email</th>
              <th>Grupo</th>
              <th>Tienda</th>
              <th>Estado</th>
              <th>Acción</th>
            </tr>
          </tfoot>
        </table>
      </div>
    </div>
  </div>
</section>

<?php } // end else ?>
</div>
<!-- /.content-wrapper -->

<footer class="main-footer">
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

<script>
$(function () {
  // DataTable init
  $("#usersTable").DataTable({
    "responsive": true, "lengthChange": false, "autoWidth": false,
    "buttons": ["copy", "csv", "excel", "pdf", "print", "colvis"]
  }).buttons().container().appendTo('#usersTable_wrapper .col-md-6:eq(0)');

  function populateSelect($sel, items, valueKey, textKey, placeholder) {
    $sel.empty();
    if (placeholder) $sel.append($('<option>').val('').text(placeholder));
    if (!Array.isArray(items)) return;
    items.forEach(function(it){
      if ($sel.find('option[value="' + it[valueKey] + '"]').length === 0) {
        $sel.append($('<option>').val(it[valueKey]).text(it[textKey]));
      }
    });
  }

  function loadGroupsAndTiendas(opts) {
    var gSel = opts.groupSel;
    var tSel = opts.tiendaSel;
    var selG = (typeof opts.selectedGroup !== 'undefined') ? String(opts.selectedGroup) : '';
    var selT = (typeof opts.selectedTienda !== 'undefined') ? String(opts.selectedTienda) : '';

    var url = 'usuarios_ajax.php';
    var gReq = $.getJSON(url, { action: 'list_groups' });
    var tReq = $.getJSON(url, { action: 'list_tiendas' });

    $.when(gReq, tReq).done(function(gRes, tRes){
      var groups = gRes[0];
      var tiendas = tRes[0];
      populateSelect(gSel, groups, 'codigo', 'nombre', 'Seleccione grupo');
      populateSelect(tSel, tiendas, 'cod_tienda', 'nombre', 'Seleccione tienda');
      if (selG !== '' && gSel.find('option[value="' + selG + '"]').length) gSel.val(selG);
      if (selT !== '' && tSel.find('option[value="' + selT + '"]').length) tSel.val(selT);
    }).fail(function(jq, textStatus, errorThrown){
      console.error('Error cargando grupos/tiendas:', textStatus, errorThrown);
      gSel.empty().append($('<option>').val('').text('Error al cargar'));
      tSel.empty().append($('<option>').val('').text('Error al cargar'));
    });
  }

  // Crear
  $('#formCreateUser').on('submit', function (e) {
    e.preventDefault();
    var $f = $(this);
    var data = $f.serialize();
    $.post('usuarios_ajax.php?action=create_user', data, null, 'json')
      .done(function(resp){
        if (resp.success) {
          $('#createUserModal').modal('hide');
          location.reload();
        } else {
          alert(resp.error || 'Error creando usuario');
        }
      })
      .fail(function(xhr){
        console.error('create_user error:', xhr.responseText);
        alert('Error de servidor al crear usuario');
      });
  });

  // Abrir Create modal -> cargar selects
  $('#createUserModal').on('show.bs.modal', function () {
    loadGroupsAndTiendas({ groupSel: $('#c_id_group'), tiendaSel: $('#c_cod_tienda') });
    $('#formCreateUser')[0].reset();
    $('#c_email_active').prop('checked', false);
  });

  // Abrir Edit modal -> obtener datos si es necesario y poblar selects
  $('#editUserModal').on('show.bs.modal', function (e) {
    var trigger = $(e.relatedTarget);
    var codigo = trigger.data('codigo') || $('#e_codigo').val();
    if (!codigo) {
      return;
    }
    $.getJSON('usuarios_ajax.php', { action: 'get_user', codigo: codigo })
      .done(function(res){
        if (res.success && res.data) {
          var d = res.data;
          $('#e_codigo').val(d.Codigo);
          $('#e_user').val(d.User);
          $('#e_cod_empleado').val(d.Cod_Empleado);
          $('#e_email').val(d.email);
          $('#e_state').val(d.State);
          $('#e_email_active').prop('checked', d.email_active == 1);
          loadGroupsAndTiendas({
            groupSel: $('#e_id_group'),
            tiendaSel: $('#e_cod_tienda'),
            selectedGroup: d.id_group_user,
            selectedTienda: d.cod_tienda
          });
        } else {
          alert(res.error || 'Usuario no encontrado');
          $('#editUserModal').modal('hide');
        }
      })
      .fail(function(xhr){
        console.error('get_user error:', xhr.responseText);
        alert('Error al obtener datos del usuario');
        $('#editUserModal').modal('hide');
      });
  });

  // Enviar edición
  $('#formEditUser').on('submit', function (e) {
    e.preventDefault();
    var $f = $(this);
    var data = $f.serialize();
    $.post('usuarios_ajax.php?action=update_user', data, null, 'json')
      .done(function(resp){
        if (resp.success) {
          $('#editUserModal').modal('hide');
          location.reload();
        } else {
          alert(resp.error || 'Error actualizando usuario');
        }
      })
      .fail(function(xhr){
        console.error('update_user error:', xhr.responseText);
        alert('Error de servidor al actualizar usuario');
      });
  });

  // Delete open
  $('#deleteUserModal').on('show.bs.modal', function (e) {
    var trigger = $(e.relatedTarget);
    var codigo = trigger.data('codigo') || '';
    var user = trigger.data('user') || '';
    $('#d_codigo').val(codigo);
    $('#d_user').text(user || '-');
    $('#d_codigo_txt').text(codigo || '-');
  });

  // Enviar eliminación
  $('#formDeleteUser').on('submit', function (e) {
    e.preventDefault();
    var codigo = $('#d_codigo').val();
    if (!codigo) { alert('Código inválido'); return; }
    $.post('usuarios_ajax.php?action=delete_user', { codigo: codigo }, null, 'json')
      .done(function(resp){
        if (resp.success) {
          $('#deleteUserModal').modal('hide');
          location.reload();
        } else {
          alert(resp.error || 'Error eliminando usuario');
        }
      })
      .fail(function(xhr){
        console.error('delete_user error:', xhr.responseText);
        alert('Error de servidor al eliminar usuario');
      });
  });

  // Botones tabla -> abrir modales (si usan data-target ya abren; estos solo llenan campos)
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

  $(document).on('click', '.btn-edit', function(){
    var $b = $(this);
    // rellenar algunos campos antes de show.bs.modal (show.bs.modal hará la petición get_user)
    $('#e_codigo').val($b.data('codigo') || '');
    $('#e_user').val($b.data('user') || '');
    $('#e_cod_empleado').val($b.data('cod_empleado') || '');
    $('#e_email').val($b.data('email') || '');
    $('#e_state').val($b.data('state') || '1');
    $('#e_email_active').prop('checked', $b.data('email_active') ? true : false);
    $('#editUserModal').modal('show');
  });

  $(document).on('click', '.btn-delete', function(){
    var $b = $(this);
    $('#d_codigo').val($b.data('codigo') || '');
    $('#d_user').text($b.data('user') || '-');
    $('#d_codigo_txt').text($b.data('codigo') || '-');
    $('#deleteUserModal').modal('show');
  });
});
</script>
</body>
</html>