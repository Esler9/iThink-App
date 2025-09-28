<script>
$(document).ready(function() {
// Cargar opciones de t$('#editUserModal').on('show.bs.modal', function(){
  $('#e_cod_tienda').html('<option value="">Cargando...</option>');
  $('#e_id_group').html('<option value="">Cargando...</option>');
  cargarSelectsUsuarios();
});

}); // Cierre del document.ready
</script> y grupos al abrir los modales de Crear/Editar
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


// Abrir modal crear usuario: cargar selects y limpiar valor
$('#createUserModal').on('show.bs.modal', function(){
	$('#c_cod_tienda').html('<option value="">Cargando...</option>');
	$('#c_id_group').html('<option value="">Cargando...</option>');
	cargarSelectsUsuarios();
});
// Abrir modal editar usuario: cargar selects y limpiar valor
$('#editUserModal').on('show.bs.modal', function(){
	$('#e_cod_tienda').html('<option value="">Cargando...</option>');
	$('#e_id_group').html('<option value="">Cargando...</option>');
	cargarSelectsUsuarios();
});
</script>
<!-- Modal: Crear Usuario -->
<div class="modal fade" id="createUserModal" tabindex="-1" role="dialog" aria-labelledby="createUserModalLabel" aria-hidden="true">
	<div class="modal-dialog" role="document">
		<form id="formCreateUser" autocomplete="off">
			<div class="modal-content">
				<div class="modal-header bg-success">
					<h5 class="modal-title" id="createUserModalLabel">Nuevo Usuario</h5>
					<button type="button" class="close" data-dismiss="modal" aria-label="Close">
						<span aria-hidden="true">&times;</span>
					</button>
				</div>
				<div class="modal-body">
					<div class="form-group">
						<label for="c_user">Usuario</label>
						<input type="text" class="form-control" id="c_user" name="user" required>
					</div>
					<div class="form-group">
						<label for="c_cod_empleado">Cod. Empleado</label>
						<input type="text" class="form-control" id="c_cod_empleado" name="cod_empleado">
					</div>
					<div class="form-group">
						<label for="c_email">Email</label>
						<input type="email" class="form-control" id="c_email" name="email">
					</div>
					<div class="form-group">
						<label for="c_password">Contraseña</label>
						<input type="password" class="form-control" id="c_password" name="password" required>
					</div>
					<div class="form-group">
						<label for="c_id_group">Grupo</label>
						<select class="form-control" id="c_id_group" name="id_group">
							<option value="">Seleccione...</option>
						</select>
					</div>
					<div class="form-group">
						<label for="c_cod_tienda">Tienda</label>
						<select class="form-control" id="c_cod_tienda" name="cod_tienda">
							<option value="">Seleccione...</option>
						</select>
					</div>
					<div class="form-group">
						<label for="c_state">Estado</label>
						<select class="form-control" id="c_state" name="state">
							<option value="1">Activo</option>
							<option value="0">Inactivo</option>
						</select>
					</div>
				</div>
				<div class="modal-footer">
					<button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
					<button type="submit" class="btn btn-success">Crear</button>
				</div>
			</div>
		</form>
	</div>
</div>

<!-- Modal: Ver Usuario -->
<div class="modal fade" id="viewUserModal" tabindex="-1" role="dialog" aria-labelledby="viewUserModalLabel" aria-hidden="true">
	<div class="modal-dialog" role="document">
		<div class="modal-content">
			<div class="modal-header bg-info">
				<h5 class="modal-title" id="viewUserModalLabel">Ver Usuario</h5>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span>
				</button>
			</div>
			<div class="modal-body">
				<dl class="row">
					<dt class="col-sm-4">Código</dt><dd class="col-sm-8" id="v_codigo"></dd>
					<dt class="col-sm-4">Usuario</dt><dd class="col-sm-8" id="v_user"></dd>
					<dt class="col-sm-4">Cod. Empleado</dt><dd class="col-sm-8" id="v_cod_empleado"></dd>
					<dt class="col-sm-4">Email</dt><dd class="col-sm-8" id="v_email"></dd>
					<dt class="col-sm-4">Email Activo</dt><dd class="col-sm-8" id="v_email_active"></dd>
					<dt class="col-sm-4">Grupo</dt><dd class="col-sm-8" id="v_grupo"></dd>
					<dt class="col-sm-4">Tienda</dt><dd class="col-sm-8" id="v_tienda"></dd>
					<dt class="col-sm-4">Estado</dt><dd class="col-sm-8" id="v_state"></dd>
				</dl>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
			</div>
		</div>
	</div>
</div>

<!-- Modal: Editar Usuario -->
<div class="modal fade" id="editUserModal" tabindex="-1" role="dialog" aria-labelledby="editUserModalLabel" aria-hidden="true">
	<div class="modal-dialog" role="document">
		<form id="formEditUser" autocomplete="off">
			<div class="modal-content">
				<div class="modal-header bg-warning">
					<h5 class="modal-title" id="editUserModalLabel">Editar Usuario</h5>
					<button type="button" class="close" data-dismiss="modal" aria-label="Close">
						<span aria-hidden="true">&times;</span>
					</button>
				</div>
				<div class="modal-body">
					<input type="hidden" id="e_codigo" name="codigo">
					<div class="form-group">
						<label for="e_user">Usuario</label>
						<input type="text" class="form-control" id="e_user" name="user" required>
					</div>
					<div class="form-group">
						<label for="e_cod_empleado">Cod. Empleado</label>
						<input type="text" class="form-control" id="e_cod_empleado" name="cod_empleado">
					</div>
					<div class="form-group">
						<label for="e_email">Email</label>
						<input type="email" class="form-control" id="e_email" name="email">
					</div>
					<div class="form-group">
						<label for="e_email_active">Email Activo</label>
						<input type="checkbox" id="e_email_active" name="email_active" value="1">
					</div>
					<div class="form-group">
						<label for="e_id_group">Grupo</label>
						<select class="form-control" id="e_id_group" name="id_group">
							<option value="">Seleccione...</option>
						</select>
					</div>
					<div class="form-group">
						<label for="e_cod_tienda">Tienda</label>
						<select class="form-control" id="e_cod_tienda" name="cod_tienda">
							<option value="">Seleccione...</option>
						</select>
					</div>
					<div class="form-group">
						<label for="e_state">Estado</label>
						<select class="form-control" id="e_state" name="state">
							<option value="1">Activo</option>
							<option value="0">Inactivo</option>
						</select>
					</div>
					<div class="form-group">
						<label for="e_password">Contraseña (dejar vacío para no cambiar)</label>
						<input type="password" class="form-control" id="e_password" name="password">
					</div>
				</div>
				<div class="modal-footer">
					<button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
					<button type="submit" class="btn btn-warning">Guardar Cambios</button>
				</div>
			</div>
		</form>
	</div>
</div>

<!-- Modal: Eliminar Usuario -->
<div class="modal fade" id="deleteUserModal" tabindex="-1" role="dialog" aria-labelledby="deleteUserModalLabel" aria-hidden="true">
	<div class="modal-dialog" role="document">
		<form id="formDeleteUser">
			<div class="modal-content">
				<div class="modal-header bg-danger">
					<h5 class="modal-title" id="deleteUserModalLabel">Eliminar Usuario</h5>
					<button type="button" class="close" data-dismiss="modal" aria-label="Close">
						<span aria-hidden="true">&times;</span>
					</button>
				</div>
				<div class="modal-body">
					<input type="hidden" id="d_codigo" name="codigo">
					<p>¿Está seguro que desea eliminar al usuario <strong id="d_user"></strong> (código <span id="d_codigo_txt"></span>)?</p>
				</div>
				<div class="modal-footer">
					<button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
					<button type="submit" class="btn btn-danger">Eliminar</button>
				</div>
			</div>
		</form>
	</div>
</div>
