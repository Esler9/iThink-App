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
        <?php
        // justo antes de renderizar la tabla
        if (isset($_SESSION['ok_usuario'])) {
          echo '<div class="alert alert-success">'.htmlspecialchars($_SESSION['ok_usuario']).'</div>';
          unset($_SESSION['ok_usuario']);
        }
        if (isset($_SESSION['error_usuario'])) {
          echo '<div class="alert alert-danger">'.htmlspecialchars($_SESSION['error_usuario']).'</div>';
          unset($_SESSION['error_usuario']);
        }
        ?>
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
              <td>
                <div class="d-flex align-items-center">
                  <div class="mr-2" style="min-width:220px; overflow:hidden; text-overflow:ellipsis; white-space:nowrap;">
                    <?php echo $email ?: '-'; ?>
                  </div>
                  <div>
                    <div class="custom-control custom-switch">
                      <input type="checkbox"
                             class="custom-control-input email-toggle"
                             id="emailSwitch<?php echo $codigo;?>"
                             data-codigo="<?php echo $codigo;?>"
                             <?php echo ($email_active ? 'checked' : ''); ?>>
                      <label class="custom-control-label" for="emailSwitch<?php echo $codigo;?>" title="Activar/desactivar envíos de email"></label>
                    </div>
                  </div>
                </div>
              </td>
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

<?php
// <-- añadir esto antes de incluir los modales -->
$grupos_q = false;
$tiendas_q = false;
if (isset($conn) && $conn && @mysqli_ping($conn)) {
    $grupos_q = @mysqli_query($conn, "SELECT codigo, nombre_grupo FROM grupo_user ORDER BY nombre_grupo");
    $tiendas_q = @mysqli_query($conn, "SELECT cod_tienda, tienda_nombre FROM tienda ORDER BY tienda_nombre");
}
?>
<!-- incluir modales justo antes de los scripts -->
<?php
$modalFile = __DIR__ . '/modal-users.php';
if (file_exists($modalFile) && is_readable($modalFile)) {
    include $modalFile;
} else {
    // DEBUG: mostrar comentario HTML y modal de fallback mínimo para pruebas
    echo "<!-- ERROR: modal-users.php no encontrado en: {$modalFile} -->";
    ?>
    <!-- Modal fallback mínimo para pruebas -->
    <div class="modal fade" id="createUserModal" tabindex="-1" role="dialog" aria-hidden="true">
      <div class="modal-dialog" role="document">
        <div class="modal-content">
          <div class="modal-header"><h5 class="modal-title">Crear usuario (fallback)</h5>
            <button type="button" class="close" data-dismiss="modal">&times;</button></div>
          <div class="modal-body">Modal de fallback: modal-users.php faltante o no legible.</div>
          <div class="modal-footer"><button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button></div>
        </div>
      </div>
    </div>
    <?php
}
?>

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

  // --- REEMPLAZO: usar opciones ya renderizadas en [modal-users.php](http://_vscodecontentref_/0) (sin AJAX) ---
  // Crear: resetear form y dejar selects con las opciones ya generadas por PHP
  $('#createUserModal').on('show.bs.modal', function () {
    $('#formCreateUser')[0].reset();
    $('#c_email_active').prop('checked', false);
    // asegúrate de seleccionar el placeholder (si existe)
    $('#c_id_group').val('');
    $('#c_cod_tienda').val('');
  });

  // Edit: rellenar campos desde data-* del botón (no AJAX)
  $('#editUserModal').on('show.bs.modal', function (e) {
    var trigger = $(e.relatedTarget);
    var codigo = trigger.data('codigo') || $('#e_codigo').val();
    if (!codigo) { return; }

    // llenar campos desde atributos data-... definidos en la tabla
    $('#e_codigo').val(codigo);
    $('#e_user').val(trigger.data('user') || '');
    $('#e_cod_empleado').val(trigger.data('cod_empleado') || '');
    $('#e_email').val(trigger.data('email') || '');
    // state en la tabla puede venir como '1' o '0' — mapear al select
    var stateVal = (typeof trigger.data('state') !== 'undefined') ? String(trigger.data('state')) : '1';
    $('#e_state').val(stateVal);

    // email_active checkbox
    var emailActive = trigger.data('email_active') ? 1 : 0;
    $('#e_email_active').prop('checked', emailActive == 1);

    // seleccionar grupo y tienda usando los value ya en los <option>
    var gid = trigger.data('id_group') || '';
    var tid = trigger.data('cod_tienda') || '';
    if (gid !== '' && $('#e_id_group').find('option[value="' + gid + '"]').length) {
      $('#e_id_group').val(gid);
    } else {
      $('#e_id_group').val('');
    }
    if (tid !== '' && $('#e_cod_tienda').find('option[value="' + tid + '"]').length) {
      $('#e_cod_tienda').val(tid);
    } else {
      $('#e_cod_tienda').val('');
    }

    // limpiar campo password (no traer contraseña)
    $('#e_password').val('');
  });

  // Delete modal open (ya rellenabas con data-*)
  $('#deleteUserModal').on('show.bs.modal', function (e) {
    var trigger = $(e.relatedTarget);
    var codigo = trigger.data('codigo') || '';
    var user = trigger.data('user') || '';
    $('#d_codigo').val(codigo);
    $('#d_user').text(user || '-');
    $('#d_codigo_txt').text(codigo || '-');
  });

  // --- IMPORTANTE: eliminar handlers AJAX para submit y permitir envío normal (GET) ---
  // Si tienes handlers previos que hacen e.preventDefault(), quítalos o desactívalos:
  $('#formCreateUser').off('submit');
  $('#formEditUser').off('submit');
  $('#formDeleteUser').off('submit');

  // Opcional: prevenir doble envío deshabilitando botón al submit (seguirá enviando GET)
  $('#formCreateUser, #formEditUser, #formDeleteUser').on('submit', function(){
    $(this).find('button[type="submit"]').attr('disabled', true);
  });

  // Botones tabla -> abrir modales (llenado ya manejado arriba)
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

});
</script>

<script>
console.log('DEBUG jQuery version:', (window.jQuery && jQuery.fn && jQuery.fn.jquery) || 'NO_JQUERY');
console.log('DEBUG modal plugin:', (window.jQuery && $.fn && $.fn.modal) ? 'OK' : 'NO_MODAL_PLUGIN');
console.log('DEBUG #createUserModal found:', !!document.getElementById('createUserModal'));

var btn = document.createElement('button');
btn.textContent = 'TEST Abrir modal';
btn.style = 'position:fixed;right:10px;bottom:10px;z-index:99999';
btn.onclick = function(){
  try {
    if (window.jQuery && $.fn && $.fn.modal) {
      $('#createUserModal').modal('show');
      console.log('Intento abrir #createUserModal');
    } else {
      console.error('Plugin modal no disponible');
    }
  } catch(e) { console.error(e); }
};
document.body.appendChild(btn);
</script>
</body>
</html>