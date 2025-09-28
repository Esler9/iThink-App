<?php
// modal-users.php — seguro: no asume que las queries siempre funcionan
// Debe incluirse después de que $conn exista (en listado_users.php).

// Inicializar
$grupos_q = false;
$tiendas_q = false;

// Usar $conn si está disponible y conectado
if (isset($conn) && $conn && @mysqli_ping($conn)) {
    $grupos_q = @mysqli_query($conn, "SELECT codigo, nombre_grupo FROM grupo_user ORDER BY nombre_grupo");
    $tiendas_q = @mysqli_query($conn, "SELECT cod_tienda, tienda_nombre FROM tienda ORDER BY tienda_nombre");
}

// Helper: generar <option> desde resultado seguro
function render_options($res, $valField, $labelField) {
    if (!($res && $res instanceof mysqli_result)) return '';
    $html = '';
    mysqli_data_seek($res, 0);
    while ($r = mysqli_fetch_assoc($res)) {
        $v = htmlspecialchars($r[$valField] ?? '');
        $l = htmlspecialchars($r[$labelField] ?? '');
        $html .= "<option value=\"{$v}\">{$l}</option>\n";
    }
    return $html;
}
?>
<!-- Crear Usuario -->
<div class="modal fade" id="createUserModal" tabindex="-1" role="dialog" aria-hidden="true">
  <div class="modal-dialog modal-lg" role="document">
    <form id="formCreateUser" method="post" action="../../logica/Usuarios/accion_usuarios.php?accion=0" class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Crear usuario</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Cerrar"><span aria-hidden="true">&times;</span></button>
      </div>
      <div class="modal-body">
        <!-- formulario simplificado -->
        <div class="form-row">
          <div class="form-group col-md-6">
            <label>Usuario</label>
            <input name="c_user" required class="form-control" />
          </div>
          <div class="form-group col-md-6">
            <label>Contraseña</label>
            <input name="c_password" type="password" required class="form-control" />
          </div>
        </div>

        <div class="form-row">
          <div class="form-group col-md-4">
            <label>Código empleado</label>
            <input name="c_cod_empleado" class="form-control" />
          </div>
          <div class="form-group col-md-4">
            <label>Email</label>
            <input name="c_email" type="email" class="form-control" />
          </div>
          <div class="form-group col-md-4">
            <label>Grupo</label>
            <select name="c_id_group" id="c_id_group" class="form-control">
              <option value="">-</option>
              <?php echo render_options($grupos_q, 'codigo', 'nombre_grupo'); ?>
            </select>
          </div>
        </div>

        <div class="form-row">
          <div class="form-group col-md-6">
            <label>Tienda</label>
            <select name="c_cod_tienda" id="c_cod_tienda" class="form-control">
              <option value="">-</option>
              <?php echo render_options($tiendas_q, 'cod_tienda', 'tienda_nombre'); ?>
            </select>
          </div>
          <div class="form-group col-md-3 d-flex align-items-center">
            <div class="form-check">
              <input class="form-check-input" type="checkbox" id="c_state" name="c_state">
              <label class="form-check-label" for="c_state">Activo</label>
            </div>
          </div>
          <div class="form-group col-md-3 d-flex align-items-center">
            <div class="form-check">
              <input class="form-check-input" type="checkbox" id="c_email_active" name="c_email_active">
              <label class="form-check-label" for="c_email_active">Emails activos</label>
            </div>
          </div>
        </div>

      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
        <button type="submit" class="btn btn-primary">Crear</button>
      </div>
    </form>
  </div>
</div>

<!-- Editar Usuario -->
<div class="modal fade" id="editUserModal" tabindex="-1" role="dialog" aria-hidden="true">
  <div class="modal-dialog modal-lg" role="document">
    <form id="formEditUser" method="post" action="../../logica/Usuarios/accion_usuarios.php?accion=1" class="modal-content">
      <input type="hidden" name="codigo" id="e_codigo" value="" />
      <div class="modal-header">
        <h5 class="modal-title">Editar usuario</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Cerrar"><span aria-hidden="true">&times;</span></button>
      </div>
      <div class="modal-body">
        <div class="form-row">
          <div class="form-group col-md-6">
            <label>Usuario</label>
            <input name="user" id="e_user" required class="form-control" />
          </div>
          <div class="form-group col-md-6">
            <label>Nueva contraseña (dejar vacío para no cambiar)</label>
            <input name="password" id="e_password" type="password" class="form-control" />
          </div>
        </div>

        <div class="form-row">
          <div class="form-group col-md-4">
            <label>Código empleado</label>
            <input name="cod_empleado" id="e_cod_empleado" class="form-control" />
          </div>
          <div class="form-group col-md-4">
            <label>Email</label>
            <input name="email" id="e_email" type="email" class="form-control" />
          </div>
          <div class="form-group col-md-4">
            <label>Grupo</label>
            <select name="id_group" id="e_id_group" class="form-control">
              <option value="">-</option>
              <?php echo render_options($grupos_q, 'codigo', 'nombre_grupo'); ?>
            </select>
          </div>
        </div>

        <div class="form-row">
          <div class="form-group col-md-6">
            <label>Tienda</label>
            <select name="cod_tienda" id="e_cod_tienda" class="form-control">
              <option value="">-</option>
              <?php echo render_options($tiendas_q, 'cod_tienda', 'tienda_nombre'); ?>
            </select>
          </div>
          <div class="form-group col-md-3 d-flex align-items-center">
            <div class="form-check">
              <input class="form-check-input" type="checkbox" id="e_state" name="state">
              <label class="form-check-label" for="e_state">Activo</label>
            </div>
          </div>
          <div class="form-group col-md-3 d-flex align-items-center">
            <div class="form-check">
              <input class="form-check-input" type="checkbox" id="e_email_active" name="email_active">
              <label class="form-check-label" for="e_email_active">Emails activos</label>
            </div>
          </div>
        </div>

      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
        <button type="submit" class="btn btn-primary">Guardar cambios</button>
      </div>
    </form>
  </div>
</div>

<!-- Ver Usuario -->
<div class="modal fade" id="viewUserModal" tabindex="-1" role="dialog" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Ver usuario</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Cerrar"><span aria-hidden="true">&times;</span></button>
      </div>
      <div class="modal-body">
        <dl class="row mb-0">
          <dt class="col-sm-4">Código</dt><dd class="col-sm-8" id="v_codigo">-</dd>
          <dt class="col-sm-4">Usuario</dt><dd class="col-sm-8" id="v_user">-</dd>
          <dt class="col-sm-4">Empleado</dt><dd class="col-sm-8" id="v_cod_empleado">-</dd>
          <dt class="col-sm-4">Email</dt><dd class="col-sm-8" id="v_email">-</dd>
          <dt class="col-sm-4">Emails activos</dt><dd class="col-sm-8" id="v_email_active">-</dd>
          <dt class="col-sm-4">Grupo</dt><dd class="col-sm-8" id="v_grupo">-</dd>
          <dt class="col-sm-4">Tienda</dt><dd class="col-sm-8" id="v_tienda">-</dd>
          <dt class="col-sm-4">Estado</dt><dd class="col-sm-8" id="v_state">-</dd>
        </dl>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
      </div>
    </div>
  </div>
</div>

<!-- Eliminar Usuario -->
<div class="modal fade" id="deleteUserModal" tabindex="-1" role="dialog" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <form id="formDeleteUser" method="post" action="../../logica/Usuarios/accion_usuarios.php?accion=2" class="modal-content">
      <input type="hidden" name="codigo" id="d_codigo" value="" />
      <div class="modal-header">
        <h5 class="modal-title">Eliminar usuario</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Cerrar"><span aria-hidden="true">&times;</span></button>
      </div>
      <div class="modal-body">
        <p>¿Confirma eliminar el usuario <strong id="d_user">-</strong>?</p>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
        <button type="submit" class="btn btn-danger">Eliminar</button>
      </div>
    </form>
  </div>
</div>

<script>
jQuery(function($){

  // Crear: resetear form al abrir modal
  $('#createUserModal').on('show.bs.modal', function () {
    $('#formCreateUser')[0].reset();
    $('#c_id_group, #c_cod_tienda').val('');
    $('#c_email_active, #c_state').prop('checked', false);
  });

  // Editar: rellenar campos desde data-* del botón
  $('#editUserModal').on('show.bs.modal', function (e) {
    var btn = $(e.relatedTarget);
    var data = btn.data() || {};

    $('#e_codigo').val(data.codigo || '');
    $('#e_user').val(data.user || '');
    $('#e_cod_empleado').val(data.cod_empleado || '');
    $('#e_email').val(data.email || '');
    $('#e_id_group').val(data.id_group || '');
    $('#e_cod_tienda').val(data.cod_tienda || '');
    $('#e_password').val('');
    // state puede venir como '1'/'0' o 'Activo'/'Inactivo'
    var stateChecked = (data.state === 1 || data.state === '1' || data.state === 'Activo' || data.state === true);
    $('#e_state').prop('checked', !!stateChecked);
    $('#e_email_active').prop('checked', !!(data.email_active == 1 || data.email_active === true));
  });

  // Ver: mostrar datos en texto
  $('#viewUserModal').on('show.bs.modal', function (e) {
    var btn = $(e.relatedTarget);
    $('#v_codigo').text(btn.data('codigo') || '-');
    $('#v_user').text(btn.data('user') || '-');
    $('#v_cod_empleado').text(btn.data('cod_empleado') || '-');
    $('#v_email').text(btn.data('email') || '-');
    $('#v_email_active').text(btn.data('email_active') ? 'Sí' : 'No');
    $('#v_grupo').text(btn.data('grupo') || '-');
    $('#v_tienda').text(btn.data('tienda') || '-');
    $('#v_state').text(btn.data('state') || '-');
  });

  // Eliminar: setear codigo y usuario
  $('#deleteUserModal').on('show.bs.modal', function (e) {
    var btn = $(e.relatedTarget);
    $('#d_codigo').val(btn.data('codigo') || '');
    $('#d_user').text(btn.data('user') || '-');
  });

  // Evitar doble submit (para envíos normales)
  $('#formCreateUser, #formEditUser, #formDeleteUser').on('submit', function(){
    $(this).find('button[type="submit"]').attr('disabled', true);
  });

});
</script>