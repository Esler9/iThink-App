<?php
// modal-users.php - modales para ver, editar y eliminar usuario
// Este archivo debe incluirse desde listado_users.php: include("modal-users.php");
include("../../../conexion.php");
?>
<!-- Ver Usuario -->
<div class="modal fade" id="viewUserModal" tabindex="-1" role="dialog" aria-labelledby="viewUserLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
      <div class="modal-header bg-info">
        <h5 class="modal-title" id="viewUserLabel">Ver Usuario</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Cerrar"><span aria-hidden="true">&times;</span></button>
      </div>
      <div class="modal-body">
        <table class="table table-borderless">
          <tr><th>Código</th><td id="v_codigo"></td></tr>
          <tr><th>User</th><td id="v_user"></td></tr>
          <tr><th>Cod Empleado</th><td id="v_cod_empleado"></td></tr>
          <tr><th>Email</th><td id="v_email"></td></tr>
          <tr><th>Email verificado</th><td id="v_email_active"></td></tr>
          <tr><th>Grupo</th><td id="v_grupo"></td></tr>
          <tr><th>Tienda</th><td id="v_tienda"></td></tr>
          <tr><th>Estado</th><td id="v_state"></td></tr>
        </table>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
      </div>
    </div>
  </div>
</div>

<!-- Editar Usuario -->
<div class="modal fade" id="editUserModal" tabindex="-1" role="dialog" aria-labelledby="editUserLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <form id="formEditUser" method="post" action="editar_usuario.php">
      <input type="hidden" name="Codigo" id="e_codigo">
      <div class="modal-content">
        <div class="modal-header bg-warning">
          <h5 class="modal-title" id="editUserLabel">Editar Usuario</h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Cerrar"><span aria-hidden="true">&times;</span></button>
        </div>
        <div class="modal-body">
          <div class="form-group">
            <label for="e_user">User</label>
            <input type="text" class="form-control" id="e_user" name="User" required>
          </div>
          <div class="form-group">
            <label for="e_cod_empleado">Cod Empleado</label>
            <input type="text" class="form-control" id="e_cod_empleado" name="Cod_Empleado">
          </div>
          <div class="form-group">
            <label for="e_email">Email</label>
            <input type="email" class="form-control" id="e_email" name="email">
          </div>
          <div class="form-group">
            <label for="e_email_active">Email verificado</label>
            <select class="form-control" id="e_email_active" name="email_active">
              <option value="0">No</option>
              <option value="1">Si</option>
            </select>
          </div>
          <div class="form-group">
            <label for="e_id_group">Grupo</label>
            <select class="form-control" id="e_id_group" name="id_group_user">
              <option value="">-- Seleccione --</option>
              <?php
                // cargar grupos
                $rg = mysqli_query($conn,"SELECT codigo,nombre_grupo FROM grupo_user ORDER BY codigo");
                if($rg){
                  while($gr = mysqli_fetch_assoc($rg)){
                    echo '<option value="'.htmlspecialchars($gr['codigo']).'">'.htmlspecialchars($gr['nombre_grupo']).'</option>';
                  }
                }
              ?>
            </select>
          </div>
          <div class="form-group">
            <label for="e_cod_tienda">Tienda</label>
            <select class="form-control" id="e_cod_tienda" name="cod_tienda">
              <option value="">-- Seleccione --</option>
              <?php
                // cargar tiendas
                $rt = mysqli_query($conn,"SELECT cod_tienda,nombre FROM tienda ORDER BY nombre");
                if($rt){
                  while($t = mysqli_fetch_assoc($rt)){
                    echo '<option value="'.htmlspecialchars($t['cod_tienda']).'">'.htmlspecialchars($t['nombre']).'</option>';
                  }
                }
              ?>
            </select>
          </div>
          <div class="form-group">
            <label for="e_state">Estado</label>
            <select class="form-control" id="e_state" name="State">
              <option value="1">Activo</option>
              <option value="0">Inactivo</option>
            </select>
          </div>
          <!-- Si necesita cambiar contraseña, habilitar campo (opcional) -->
          <div class="form-group">
            <label for="e_password">Contraseña (dejar vacío para no cambiar)</label>
            <input type="password" class="form-control" id="e_password" name="Password">
          </div>
        </div>
        <div class="modal-footer">
          <button type="submit" class="btn btn-warning">Guardar cambios</button>
          <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
        </div>
      </div>
    </form>
  </div>
</div>

<!-- Eliminar Usuario -->
<div class="modal fade" id="deleteUserModal" tabindex="-1" role="dialog" aria-labelledby="deleteUserLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <form id="formDeleteUser" method="post" action="eliminar_usuario.php">
      <input type="hidden" name="Codigo" id="d_codigo">
      <div class="modal-content">
        <div class="modal-header bg-danger">
          <h5 class="modal-title" id="deleteUserLabel">Eliminar Usuario</h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Cerrar"><span aria-hidden="true">&times;</span></button>
        </div>
        <div class="modal-body">
          <p>¿Seguro que deseas eliminar al usuario <strong id="d_user"></strong> (código <span id="d_codigo_txt"></span>)?</p>
          <p class="text-danger small">Esta acción no se puede deshacer.</p>
        </div>
        <div class="modal-footer">
          <button type="submit" class="btn btn-danger">Eliminar</button>
          <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
        </div>
      </div>
    </form>
  </div>
</div>

<!-- Script para manejar apertura y llenado de modales -->
<script>
  // llenar y abrir modal Ver
  $(document).on('click','.btn-view',function(e){
    e.preventDefault();
    var data = $(this).data();
    $('#v_codigo').text(data.codigo);
    $('#v_user').text(data.user);
    $('#v_cod_empleado').text(data.codEmpleado || '');
    $('#v_email').text(data.email || '');
    $('#v_email_active').text(data.emailActive==1 ? 'Si' : 'No');
    $('#v_grupo').text(data.grupo || data.idGroup || '');
    $('#v_tienda').text(data.tienda || data.codTienda || '');
    $('#v_state').text(data.state==1 ? 'Activo' : (data.state==0 ? 'Inactivo' : data.state));
    $('#viewUserModal').modal('show');
  });

  // llenar y abrir modal Editar
  $(document).on('click','.btn-edit',function(e){
    e.preventDefault();
    var data = $(this).data();
    $('#e_codigo').val(data.codigo);
    $('#e_user').val(data.user);
    $('#e_cod_empleado').val(data.codEmpleado || '');
    $('#e_email').val(data.email || '');
    $('#e_email_active').val(data.emailActive);
    $('#e_id_group').val(data.idGroup);
    $('#e_cod_tienda').val(data.codTienda);
    $('#e_state').val(data.state);
    $('#e_password').val('');
    $('#editUserModal').modal('show');
  });

  // llenar y abrir modal Eliminar
  $(document).on('click','.btn-delete',function(e){
    e.preventDefault();
    var data = $(this).data();
    $('#d_codigo').val(data.codigo);
    $('#d_user').text(data.user);
    $('#d_codigo_txt').text(data.codigo);
    $('#deleteUserModal').modal('show');
  });
</script>