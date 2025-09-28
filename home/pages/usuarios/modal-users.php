<!-- Modal: Crear usuario -->
<div class="modal fade" id="createUserModal" tabindex="-1" role="dialog" aria-labelledby="createUserLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
      <form id="formCreateUser" method="post">
        <div class="modal-header bg-success text-white">
          <h5 class="modal-title" id="createUserLabel"><i class="fas fa-user-plus mr-1"></i> Crear usuario</h5>
          <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body">
          <div class="row">
            <div class="col-md-6">
              <div class="form-group">
                <label for="c_user">Usuario</label>
                <input type="text" class="form-control" id="c_user" name="user" placeholder="Usuario" required>
              </div>
            </div>
            <div class="col-md-6">
              <div class="form-group">
                <label for="c_cod_empleado">Código Empleado</label>
                <input type="text" class="form-control" id="c_cod_empleado" name="cod_empleado" placeholder="Código del empleado">
              </div>
            </div>
            <div class="col-md-6">
              <div class="form-group">
                <label for="c_email">Email</label>
                <input type="email" class="form-control" id="c_email" name="email" placeholder="correo@dominio.com" required>
              </div>
            </div>
            <div class="col-md-6">
              <div class="form-group">
                <label for="c_password">Contraseña</label>
                <input type="password" class="form-control" id="c_password" name="password" placeholder="Contraseña" required>
              </div>
            </div>
            <div class="col-md-6">
              <div class="form-group">
                <label for="c_id_group">Grupo</label>
                <select class="form-control" id="c_id_group" name="id_group" required>
                  <option value="">Cargando...</option>
                </select>
              </div>
            </div>
            <div class="col-md-6">
              <div class="form-group">
                <label for="c_cod_tienda">Tienda</label>
                <select class="form-control" id="c_cod_tienda" name="cod_tienda" required>
                  <option value="">Cargando...</option>
                </select>
              </div>
            </div>
            <div class="col-md-6">
              <div class="form-group">
                <label for="c_state">Estado</label>
                <select class="form-control" id="c_state" name="state" required>
                  <option value="1">Activo</option>
                  <option value="0">Inactivo</option>
                </select>
              </div>
            </div>
            <div class="col-md-6 d-flex align-items-center">
              <div class="custom-control custom-switch mt-3">
                <input type="checkbox" class="custom-control-input" id="c_email_active" name="email_active" value="1">
                <label class="custom-control-label" for="c_email_active">Email activo</label>
              </div>
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
      <form id="formEditUser" method="post">
        <div class="modal-header bg-warning">
          <h5 class="modal-title" id="editUserLabel"><i class="fas fa-user-edit mr-1"></i> Editar usuario</h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body">
          <input type="hidden" id="e_codigo" name="codigo">
          <div class="row">
            <div class="col-md-6">
              <div class="form-group">
                <label for="e_user">Usuario</label>
                <input type="text" class="form-control" id="e_user" name="user" required>
              </div>
            </div>
            <div class="col-md-6">
              <div class="form-group">
                <label for="e_cod_empleado">Código Empleado</label>
                <input type="text" class="form-control" id="e_cod_empleado" name="cod_empleado">
              </div>
            </div>
            <div class="col-md-6">
              <div class="form-group">
                <label for="e_email">Email</label>
                <input type="email" class="form-control" id="e_email" name="email" required>
              </div>
            </div>
            <div class="col-md-6">
              <div class="form-group">
                <label for="e_password">Contraseña (dejar en blanco para no cambiar)</label>
                <input type="password" class="form-control" id="e_password" name="password" placeholder="Nueva contraseña">
              </div>
            </div>
            <div class="col-md-6">
              <div class="form-group">
                <label for="e_id_group">Grupo</label>
                <select class="form-control" id="e_id_group" name="id_group" required>
                  <option value="">Cargando...</option>
                </select>
              </div>
            </div>
            <div class="col-md-6">
              <div class="form-group">
                <label for="e_cod_tienda">Tienda</label>
                <select class="form-control" id="e_cod_tienda" name="cod_tienda" required>
                  <option value="">Cargando...</option>
                </select>
              </div>
            </div>
            <div class="col-md-6">
              <div class="form-group">
                <label for="e_state">Estado</label>
                <select class="form-control" id="e_state" name="state" required>
                  <option value="1">Activo</option>
                  <option value="0">Inactivo</option>
                </select>
              </div>
            </div>
            <div class="col-md-6 d-flex align-items-center">
              <div class="custom-control custom-switch mt-3">
                <input type="checkbox" class="custom-control-input" id="e_email_active" name="email_active" value="1">
                <label class="custom-control-label" for="e_email_active">Email activo</label>
              </div>
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
      <form id="formDeleteUser" method="post">
        <div class="modal-header bg-danger text-white">
          <h5 class="modal-title" id="deleteUserLabel"><i class="fas fa-user-times mr-1"></i> Eliminar usuario</h5>
          <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body">
          <input type="hidden" id="d_codigo" name="codigo">
          <p>¿Seguro que deseas eliminar al usuario <strong id="d_user">-</strong> con código <strong id="d_codigo_txt">-</strong>?</p>
          <p class="text-muted mb-0">Esta acción no se puede deshacer.</p>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
          <button type="submit" class="btn btn-danger">Eliminar</button>
        </div>
      </form>
    </div>
  </div>
</div>
