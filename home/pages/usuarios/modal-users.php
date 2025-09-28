<?php
// modal-users.php — versión limpia sin marcadores de depuración
if (!isset($grupos) || !is_array($grupos)) $grupos = [];
if (!isset($tiendas) || !is_array($tiendas)) $tiendas = [];
if (!isset($states) || !is_array($states)) $states = [];

// helper para etiquetas de estado
function state_label($s){
    if ($s === null) return '-';
    if ($s === '1' || $s === 1) return 'Activo';
    if ($s === '0' || $s === 0) return 'Inactivo';
    return (string)$s;
}
?>
<!-- Crear Usuario -->
<div class="modal fade" id="createUserModal" tabindex="-1" role="dialog" aria-hidden="true">
  <div class="modal-dialog modal-lg" role="document">
    <form id="formCreateUser" method="post" action="../../logica/Usuarios/accion_usuarios.php?accion=0" class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Crear usuario</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Cerrar">&times;</button>
      </div>
      <div class="modal-body">
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
              <?php if (!empty($grupos)): foreach ($grupos as $g): ?>
                <option value="<?php echo htmlspecialchars($g['codigo'] ?? ''); ?>"><?php echo htmlspecialchars($g['nombre_grupo'] ?? ''); ?></option>
              <?php endforeach; endif; ?>
            </select>
          </div>
        </div>

        <div class="form-row">
          <div class="form-group col-md-6">
            <label>Tienda</label>
            <select name="c_cod_tienda" id="c_cod_tienda" class="form-control">
              <option value="">-</option>
              <?php if (!empty($tiendas)): foreach ($tiendas as $t): ?>
                <option value="<?php echo htmlspecialchars($t['cod_tienda'] ?? ''); ?>"><?php echo htmlspecialchars($t['tienda_nombre'] ?? ''); ?></option>
              <?php endforeach; endif; ?>
            </select>
          </div>

          <!-- State ahora es select (soporta >2 estados) -->
          <div class="form-group col-md-3">
            <label>Estado</label>
            <select name="c_state" id="c_state" class="form-control">
              <option value="">-</option>
              <?php foreach ($states as $st): ?>
                <option value="<?php echo htmlspecialchars($st); ?>"><?php echo htmlspecialchars(state_label($st)); ?></option>
              <?php endforeach; ?>
            </select>
          </div>

          <!-- Email active como switch -->
          <div class="form-group col-md-3 d-flex align-items-center">
            <div class="custom-control custom-switch">
              <input type="checkbox" class="custom-control-input" id="c_email_active" name="c_email_active" value="1">
              <label class="custom-control-label" for="c_email_active">Emails activos</label>
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
        <button type="button" class="close" data-dismiss="modal" aria-label="Cerrar">&times;</button>
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
              <?php if (!empty($grupos)): foreach ($grupos as $g): ?>
                <option value="<?php echo htmlspecialchars($g['codigo'] ?? ''); ?>"><?php echo htmlspecialchars($g['nombre_grupo'] ?? ''); ?></option>
              <?php endforeach; endif; ?>
            </select>
          </div>
        </div>

        <div class="form-row">
          <div class="form-group col-md-6">
            <label>Tienda</label>
            <select name="cod_tienda" id="e_cod_tienda" class="form-control">
              <option value="">-</option>
              <?php if (!empty($tiendas)): foreach ($tiendas as $t): ?>
                <option value="<?php echo htmlspecialchars($t['cod_tienda'] ?? ''); ?>"><?php echo htmlspecialchars($t['tienda_nombre'] ?? ''); ?></option>
              <?php endforeach; endif; ?>
            </select>
          </div>

          <div class="form-group col-md-3">
            <label>Estado</label>
            <select name="state" id="e_state" class="form-control">
              <option value="">-</option>
              <?php foreach ($states as $st): ?>
                <option value="<?php echo htmlspecialchars($st); ?>"><?php echo htmlspecialchars(state_label($st)); ?></option>
              <?php endforeach; ?>
            </select>
          </div>

          <div class="form-group col-md-3 d-flex align-items-center">
            <div class="custom-control custom-switch">
              <input type="checkbox" class="custom-control-input" id="e_email_active" name="email_active" value="1">
              <label class="custom-control-label" for="e_email_active">Emails activos</label>
            </div>
          </div>
        </div>

      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
        <button type="submit" class="btn btn-primary">Guardar</button>
      </div>
    </form>
  </div>
</div>

<!-- Ver Usuario -->
<div class="modal fade" id="viewUserModal" tabindex="-1" role="dialog" aria-hidden="true">
  <div class="modal-dialog" role="document"><div class="modal-content">
    <div class="modal-header"><h5 class="modal-title">Ver usuario</h5>
      <button type="button" class="close" data-dismiss="modal" aria-label="Cerrar">&times;</button></div>
    <div class="modal-body">
      <dl class="row mb-0">
        <dt class="col-sm-4">Código</dt><dd class="col-sm-8" id="v_codigo">-</dd>
        <dt class="col-sm-4">Usuario</dt><dd class="col-sm-8" id="v_user">-</dd>
        <dt class="col-sm-4">Email</dt><dd class="col-sm-8" id="v_email">-</dd>
        <dt class="col-sm-4">Grupo</dt><dd class="col-sm-8" id="v_grupo">-</dd>
        <dt class="col-sm-4">Tienda</dt><dd class="col-sm-8" id="v_tienda">-</dd>
      </dl>
    </div>
    <div class="modal-footer"><button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button></div>
  </div></div>
</div>

<!-- Eliminar Usuario -->
<div class="modal fade" id="deleteUserModal" tabindex="-1" role="dialog" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <form id="formDeleteUser" method="post" action="../../logica/Usuarios/accion_usuarios.php?accion=2" class="modal-content">
      <input type="hidden" name="codigo" id="d_codigo" value="" />
      <div class="modal-header"><h5 class="modal-title">Eliminar usuario</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Cerrar">&times;</button></div>
      <div class="modal-body">
        <p>¿Confirma eliminar el usuario <strong id="d_user">-</strong>?</p>
      </div>
      <div class="modal-footer"><button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
        <button type="submit" class="btn btn-danger">Eliminar</button></div>
    </form>
  </div>
</div>

<script>
jQuery(function($){
  // Crear: reset y defaults
  $('#createUserModal').on('show.bs.modal', function () {
    var f = $('#formCreateUser')[0];
    if (f) f.reset();
    $('#c_id_group,#c_cod_tienda,#c_state').val('');
    $('#c_email_active').prop('checked', false);
  });

  // Edit: rellenar (state -> select, email_active -> switch)
  $('#editUserModal').on('show.bs.modal', function (e) {
    var b = $(e.relatedTarget);
    $('#e_codigo').val(b.data('codigo') || '');
    $('#e_user').val(b.data('user') || '');
    $('#e_cod_empleado').val(b.data('cod_empleado') || '');
    $('#e_email').val(b.data('email') || '');
    $('#e_id_group').val(b.data('id_group') || '');
    $('#e_cod_tienda').val(b.data('cod_tienda') || '');
    // set state select by raw value
    $('#e_state').val(typeof b.data('state') !== 'undefined' ? b.data('state') : '');
    // email_active switch (data may be 1/0)
    $('#e_email_active').prop('checked', (b.data('email_active') == 1 || b.data('email_active') === true));
    $('#e_password').val('');
  });

  // View modal uses displayed label (if you prefer raw, map with state_label server-side)
  $('#viewUserModal').on('show.bs.modal', function (e) {
    var b = $(e.relatedTarget);
    $('#v_codigo').text(b.data('codigo') || '-');
    $('#v_user').text(b.data('user') || '-');
    $('#v_email').text(b.data('email') || '-');
    // state: if trigger has raw state, map on client for readability
    var st = b.data('state');
    var stLabel = (st === 1 || st === '1') ? 'Activo' : (st === 0 || st === '0') ? 'Inactivo' : (st || '-');
    $('#v_tienda').text(b.data('tienda') || '-');
    $('#v_grupo').text(b.data('grupo') || '-');
    $('#v_state').text(stLabel);
  });

  // Delete modal unchanged
  $('#deleteUserModal').on('show.bs.modal', function (e) {
    var b = $(e.relatedTarget);
    $('#d_codigo').val(b.data('codigo') || '');
    $('#d_user').text(b.data('user') || '-');
  });
});
</script>

<?php
// fin del include
?>