<?php

// obtener grupos
$options_groups = '<option value="">--Seleccione grupo--</option>';
if (isset($conn)) {
  $res = mysqli_query($conn, "SELECT codigo, nombre_grupo FROM grupo_user ORDER BY nombre_grupo ASC");
  if ($res) {
    while ($g = mysqli_fetch_assoc($res)) {
      $options_groups .= '<option value="'.htmlspecialchars($g['codigo']).'">'.htmlspecialchars($g['nombre_grupo']).'</option>';
    }
  }
}

// obtener tiendas
$options_tiendas = '<option value="">--Seleccione tienda--</option>';
if (isset($conn)) {
  $res = mysqli_query($conn, "SELECT cod_tienda, nombre FROM tienda ORDER BY nombre ASC");
  if ($res) {
    while ($t = mysqli_fetch_assoc($res)) {
      $options_tiendas .= '<option value="'.htmlspecialchars($t['cod_tienda']).'">'.htmlspecialchars($t['nombre']).'</option>';
    }
  }
}
?>
<!-- Modal: Crear usuario -->
<div class="modal fade" id="createUserModal" tabindex="-1" role="dialog" aria-labelledby="createUserLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
      <form id="formCreateUser" method="post" action="./usuarios_accion.php?action=create_user">
        <input type="hidden" name="action" value="create_user">
        <input type="hidden" name="debug" value="1"> <!-- temporal -->
        <input type="hidden" name="ajax" value="1">   <!-- añade para recibir JSON y evitar redirect -->
        <div class="modal-header">
          <h5 class="modal-title">Crear usuario</h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Cerrar">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body">
          <div class="form-group">
            <label>Usuario</label>
            <input type="text" name="user" class="form-control" required>
          </div>
          <div class="form-group">
            <label>Código empleado</label>
            <input type="text" name="cod_empleado" class="form-control">
          </div>
          <div class="form-group">
            <label>Contraseña</label>
            <input type="password" name="password" class="form-control" required>
          </div>
          <div class="form-group">
            <label>Grupo</label>
            <select name="id_group" id="c_id_group" class="form-control" required>
              <?php echo $options_groups; ?>
            </select>
          </div>
          <div class="form-group">
            <label>Tienda</label>
            <select name="cod_tienda" id="c_cod_tienda" class="form-control" required>
              <?php echo $options_tiendas; ?>
            </select>
          </div>
          <div class="form-row">
            <div class="form-group col-md-6">
              <label>Email</label>
              <input type="email" name="email" class="form-control" required>
            </div>
            <div class="form-group col-md-3">
              <label>Estado</label>
              <select name="state" class="form-control">
                <option value="1">Activo</option>
                <option value="0">Inactivo</option>
              </select>
            </div>
            <div class="form-group col-md-3">
              <label>Email activo</label><br>
              <input type="checkbox" name="email_active" value="1">
            </div>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
          <button type="submit" class="btn btn-success">Guardar</button>
        </div>
      </form>
    </div>
  </div>
</div>

<!-- Modal: Ver usuario -->
<div class="modal fade" id="viewUserModal" tabindex="-1" role="dialog" aria-labelledby="viewUserLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header bg-info text-white">
        <h5 class="modal-title" id="viewUserLabel"><i class="fas fa-eye mr-1"></i> Detalle de usuario</h5>
        <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <dl class="row mb-0">
          <dt class="col-sm-5">Código</dt>
          <dd class="col-sm-7" id="v_codigo">-</dd>
          <dt class="col-sm-5">Usuario</dt>
          <dd class="col-sm-7" id="v_user">-</dd>
          <dt class="col-sm-5">Código Empleado</dt>
          <dd class="col-sm-7" id="v_cod_empleado">-</dd>
          <dt class="col-sm-5">Email</dt>
          <dd class="col-sm-7" id="v_email">-</dd>
          <dt class="col-sm-5">Email activo</dt>
          <dd class="col-sm-7" id="v_email_active">-</dd>
          <dt class="col-sm-5">Grupo</dt>
          <dd class="col-sm-7" id="v_grupo">-</dd>
          <dt class="col-sm-5">Tienda</dt>
          <dd class="col-sm-7" id="v_tienda">-</dd>
          <dt class="col-sm-5">Estado</dt>
          <dd class="col-sm-7" id="v_state">-</dd>
        </dl>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
      </div>
    </div>
  </div>
</div>

<!-- Modal: Editar usuario -->
<div class="modal fade" id="editUserModal" tabindex="-1" role="dialog" aria-labelledby="editUserLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
      <form id="formEditUser" method="post" action="./usuarios_accion.php?action=update_user">
        <input type="hidden" name="action" value="update_user">
        <input type="hidden" id="e_codigo" name="codigo">
        <input type="hidden" name="debug" value="1"> <!-- temporal -->
        <input type="hidden" name="ajax" value="1">
        <div class="modal-header">
          <h5 class="modal-title">Editar usuario</h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Cerrar">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body">
          <!-- mismos inputs que crear; values se llenan con JS cuando se abre el modal -->
          <div class="form-group">
            <label>Usuario</label>
            <input type="text" name="user" id="e_user" class="form-control" required>
          </div>
          <div class="form-group">
            <label>Código empleado</label>
            <input type="text" name="cod_empleado" id="e_cod_empleado" class="form-control">
          </div>
          <div class="form-group">
            <label>Nueva contraseña (dejar vacío para no cambiar)</label>
            <input type="password" name="password" id="e_password" class="form-control">
          </div>
          <div class="form-group">
            <label>Grupo</label>
            <select name="id_group" id="e_id_group" class="form-control" required>
              <?php echo $options_groups; ?>
            </select>
          </div>
          <div class="form-group">
            <label>Tienda</label>
            <select name="cod_tienda" id="e_cod_tienda" class="form-control" required>
              <?php echo $options_tiendas; ?>
            </select>
          </div>
          <div class="form-row">
            <div class="form-group col-md-6">
              <label>Email</label>
              <input type="email" name="email" id="e_email" class="form-control" required>
            </div>
            <div class="form-group col-md-3">
              <label>Estado</label>
              <select name="state" id="e_state" class="form-control">
                <option value="1">Activo</option>
                <option value="0">Inactivo</option>
              </select>
            </div>
            <div class="form-group col-md-3">
              <label>Email activo</label><br>
              <input type="checkbox" name="email_active" id="e_email_active" value="1">
            </div>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
          <button type="submit" class="btn btn-warning">Actualizar</button>
        </div>
      </form>
    </div>
  </div>
</div>

<!-- Modal: Eliminar usuario -->
<div class="modal fade" id="deleteUserModal" tabindex="-1" role="dialog" aria-labelledby="deleteUserLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <form id="formDeleteUser" method="post" action="./usuarios_accion.php?action=delete_user">
        <input type="hidden" name="action" value="delete_user">
        <input type="hidden" id="d_codigo" name="codigo">
        <input type="hidden" name="ajax" value="1">
        <div class="modal-header">
          <h5 class="modal-title">Eliminar usuario</h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Cerrar">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body">
          ¿Confirma eliminar este usuario?
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
          <button type="submit" class="btn btn-danger">Eliminar</button>
        </div>
      </form>
    </div>
  </div>
</div>

<script>
document.querySelectorAll('#formCreateUser,#formEditUser,#formDeleteUser').forEach(function(f){
  f.addEventListener('submit', function(e){
    try {
      var params = new URLSearchParams(new FormData(f));
      console.log('Enviando formulario', f.id, params.toString());
      // alerta temporal para confirmar qué se envía
      alert('Enviando ' + f.id + '\\n' + params.toString());
    } catch(err){
      console.log('Form logging error', err);
    }
    // no prevenimos envío
  });
});
</script>
