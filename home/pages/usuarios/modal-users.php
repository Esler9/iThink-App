<!-- Modal: Crear Usuario --><script>

<div class="modal fade" id="createUserModal" tabindex="-1" role="dialog" aria-labelledby="createUserModalLabel" aria-hidden="true">$(document).ready(function() {

	<div class="modal-dialog" role="document">// Cargar opciones de t$('#editUserModal').on('show.bs.modal', function(){

		<form id="formCreateUser" autocomplete="off">  $('#e_cod_tienda').html('<option value="">Cargando...</option>');

			<div class="modal-content">  $('#e_id_group').html('<option value="">Cargando...</option>');

				<div class="modal-header bg-success">  cargarSelectsUsuarios();

					<h5 class="modal-title" id="createUserModalLabel">Nuevo Usuario</h5>});

					<button type="button" class="close" data-dismiss="modal" aria-label="Close">

						<span aria-hidden="true">&times;</span>}); // Cierre del document.ready

					</button></script> y grupos al abrir los modales de Crear/Editar

				</div>function cargarSelectsUsuarios() {

				<div class="modal-body">	console.log('Iniciando carga de selects...');

					<div class="form-group">	

						<label for="c_user">Usuario</label>	// Tiendas

						<input type="text" class="form-control" id="c_user" name="user" required>	$.ajax({

					</div>		url: 'usuarios_ajax.php',

					<div class="form-group">		type: 'POST',

						<label for="c_cod_empleado">Cod. Empleado</label>		data: { action: 'get_tiendas' },

						<input type="text" class="form-control" id="c_cod_empleado" name="cod_empleado">		dataType: 'json',

					</div>		success: function(resp) {

					<div class="form-group">			console.log('Tiendas recibidas:', resp);

						<label for="c_email">Email</label>			var opts = '<option value="">Seleccione...</option>';

						<input type="email" class="form-control" id="c_email" name="email">			if (resp && Array.isArray(resp) && resp.length > 0) {

					</div>				resp.forEach(function(t) {

					<div class="form-group">					opts += '<option value="'+t.cod_tienda+'">'+t.nombre+'</option>';

						<label for="c_password">Contraseña</label>				});

						<input type="password" class="form-control" id="c_password" name="password" required>			} else {

					</div>				console.warn('No hay tiendas o respuesta inválida');

					<div class="form-group">			}

						<label for="c_id_group">Grupo</label>			$('#c_cod_tienda, #e_cod_tienda').html(opts);

						<select class="form-control" id="c_id_group" name="id_group">		},

							<option value="">Seleccione...</option>		error: function(xhr, status, error) {

						</select>			console.error('Error cargando tiendas:', error, xhr.responseText);

					</div>			$('#c_cod_tienda, #e_cod_tienda').html('<option value="">Error al cargar</option>');

					<div class="form-group">		}

						<label for="c_cod_tienda">Tienda</label>	});

						<select class="form-control" id="c_cod_tienda" name="cod_tienda">	

							<option value="">Seleccione...</option>	// Grupos

						</select>	$.ajax({

					</div>		url: 'usuarios_ajax.php',

					<div class="form-group">		type: 'POST',

						<label for="c_state">Estado</label>		data: { action: 'get_grupos' },

						<select class="form-control" id="c_state" name="state">		dataType: 'json',

							<option value="1">Activo</option>		success: function(resp) {

							<option value="0">Inactivo</option>			console.log('Grupos recibidos:', resp);

						</select>			var opts = '<option value="">Seleccione...</option>';

					</div>			if (resp && Array.isArray(resp) && resp.length > 0) {

				</div>				resp.forEach(function(g) {

				<div class="modal-footer">					opts += '<option value="'+g.codigo+'">'+g.nombre_grupo+'</option>';

					<button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>				});

					<button type="submit" class="btn btn-success">Crear</button>			} else {

				</div>				console.warn('No hay grupos o respuesta inválida');

			</div>			}

		</form>			$('#c_id_group, #e_id_group').html(opts);

	</div>		},

</div>		error: function(xhr, status, error) {

			console.error('Error cargando grupos:', error, xhr.responseText);

<!-- Modal: Ver Usuario -->			$('#c_id_group, #e_id_group').html('<option value="">Error al cargar</option>');

<div class="modal fade" id="viewUserModal" tabindex="-1" role="dialog" aria-labelledby="viewUserModalLabel" aria-hidden="true">		}

	<div class="modal-dialog" role="document">	});

		<div class="modal-content">}

			<div class="modal-header bg-info">

				<h5 class="modal-title" id="viewUserModalLabel">Ver Usuario</h5>

				<button type="button" class="close" data-dismiss="modal" aria-label="Close">// Abrir modal crear usuario: cargar selects y limpiar valor

					<span aria-hidden="true">&times;</span>$('#createUserModal').on('show.bs.modal', function(){

				</button>	$('#c_cod_tienda').html('<option value="">Cargando...</option>');

			</div>	$('#c_id_group').html('<option value="">Cargando...</option>');

			<div class="modal-body">	cargarSelectsUsuarios();

				<dl class="row">});

					<dt class="col-sm-4">Código</dt><dd class="col-sm-8" id="v_codigo"></dd>// Abrir modal editar usuario: cargar selects y limpiar valor

					<dt class="col-sm-4">Usuario</dt><dd class="col-sm-8" id="v_user"></dd>$('#editUserModal').on('show.bs.modal', function(){

					<dt class="col-sm-4">Cod. Empleado</dt><dd class="col-sm-8" id="v_cod_empleado"></dd>	$('#e_cod_tienda').html('<option value="">Cargando...</option>');

					<dt class="col-sm-4">Email</dt><dd class="col-sm-8" id="v_email"></dd>	$('#e_id_group').html('<option value="">Cargando...</option>');

					<dt class="col-sm-4">Email Activo</dt><dd class="col-sm-8" id="v_email_active"></dd>	cargarSelectsUsuarios();

					<dt class="col-sm-4">Grupo</dt><dd class="col-sm-8" id="v_grupo"></dd>});

					<dt class="col-sm-4">Tienda</dt><dd class="col-sm-8" id="v_tienda"></dd></script>

					<dt class="col-sm-4">Estado</dt><dd class="col-sm-8" id="v_state"></dd><!-- Modal: Crear Usuario -->

				</dl><div class="modal fade" id="createUserModal" tabindex="-1" role="dialog" aria-labelledby="createUserModalLabel" aria-hidden="true">

			</div>	<div class="modal-dialog" role="document">

			<div class="modal-footer">		<form id="formCreateUser" autocomplete="off">

				<button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>			<div class="modal-content">

			</div>				<div class="modal-header bg-success">

		</div>					<h5 class="modal-title" id="createUserModalLabel">Nuevo Usuario</h5>

	</div>					<button type="button" class="close" data-dismiss="modal" aria-label="Close">

</div>						<span aria-hidden="true">&times;</span>

					</button>

<!-- Modal: Editar Usuario -->				</div>

<div class="modal fade" id="editUserModal" tabindex="-1" role="dialog" aria-labelledby="editUserModalLabel" aria-hidden="true">				<div class="modal-body">

	<div class="modal-dialog" role="document">					<div class="form-group">

		<form id="formEditUser" autocomplete="off">						<label for="c_user">Usuario</label>

			<div class="modal-content">						<input type="text" class="form-control" id="c_user" name="user" required>

				<div class="modal-header bg-warning">					</div>

					<h5 class="modal-title" id="editUserModalLabel">Editar Usuario</h5>					<div class="form-group">

					<button type="button" class="close" data-dismiss="modal" aria-label="Close">						<label for="c_cod_empleado">Cod. Empleado</label>

						<span aria-hidden="true">&times;</span>						<input type="text" class="form-control" id="c_cod_empleado" name="cod_empleado">

					</button>					</div>

				</div>					<div class="form-group">

				<div class="modal-body">						<label for="c_email">Email</label>

					<input type="hidden" id="e_codigo" name="codigo">						<input type="email" class="form-control" id="c_email" name="email">

					<div class="form-group">					</div>

						<label for="e_user">Usuario</label>					<div class="form-group">

						<input type="text" class="form-control" id="e_user" name="user" required>						<label for="c_password">Contraseña</label>

					</div>						<input type="password" class="form-control" id="c_password" name="password" required>

					<div class="form-group">					</div>

						<label for="e_cod_empleado">Cod. Empleado</label>					<div class="form-group">

						<input type="text" class="form-control" id="e_cod_empleado" name="cod_empleado">						<label for="c_id_group">Grupo</label>

					</div>						<select class="form-control" id="c_id_group" name="id_group">

					<div class="form-group">							<option value="">Seleccione...</option>

						<label for="e_email">Email</label>						</select>

						<input type="email" class="form-control" id="e_email" name="email">					</div>

					</div>					<div class="form-group">

					<div class="form-group">						<label for="c_cod_tienda">Tienda</label>

						<label for="e_email_active">Email Activo</label>						<select class="form-control" id="c_cod_tienda" name="cod_tienda">

						<input type="checkbox" id="e_email_active" name="email_active" value="1">							<option value="">Seleccione...</option>

					</div>						</select>

					<div class="form-group">					</div>

						<label for="e_id_group">Grupo</label>					<div class="form-group">

						<select class="form-control" id="e_id_group" name="id_group">						<label for="c_state">Estado</label>

							<option value="">Seleccione...</option>						<select class="form-control" id="c_state" name="state">

						</select>							<option value="1">Activo</option>

					</div>							<option value="0">Inactivo</option>

					<div class="form-group">						</select>

						<label for="e_cod_tienda">Tienda</label>					</div>

						<select class="form-control" id="e_cod_tienda" name="cod_tienda">				</div>

							<option value="">Seleccione...</option>				<div class="modal-footer">

						</select>					<button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>

					</div>					<button type="submit" class="btn btn-success">Crear</button>

					<div class="form-group">				</div>

						<label for="e_state">Estado</label>			</div>

						<select class="form-control" id="e_state" name="state">		</form>

							<option value="1">Activo</option>	</div>

							<option value="0">Inactivo</option></div>

						</select>

					</div><!-- Modal: Ver Usuario -->

					<div class="form-group"><div class="modal fade" id="viewUserModal" tabindex="-1" role="dialog" aria-labelledby="viewUserModalLabel" aria-hidden="true">

						<label for="e_password">Contraseña (dejar vacío para no cambiar)</label>	<div class="modal-dialog" role="document">

						<input type="password" class="form-control" id="e_password" name="password">		<div class="modal-content">

					</div>			<div class="modal-header bg-info">

				</div>				<h5 class="modal-title" id="viewUserModalLabel">Ver Usuario</h5>

				<div class="modal-footer">				<button type="button" class="close" data-dismiss="modal" aria-label="Close">

					<button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>					<span aria-hidden="true">&times;</span>

					<button type="submit" class="btn btn-warning">Guardar Cambios</button>				</button>

				</div>			</div>

			</div>			<div class="modal-body">

		</form>				<dl class="row">

	</div>					<dt class="col-sm-4">Código</dt><dd class="col-sm-8" id="v_codigo"></dd>

</div>					<dt class="col-sm-4">Usuario</dt><dd class="col-sm-8" id="v_user"></dd>

					<dt class="col-sm-4">Cod. Empleado</dt><dd class="col-sm-8" id="v_cod_empleado"></dd>

<!-- Modal: Eliminar Usuario -->					<dt class="col-sm-4">Email</dt><dd class="col-sm-8" id="v_email"></dd>

<div class="modal fade" id="deleteUserModal" tabindex="-1" role="dialog" aria-labelledby="deleteUserModalLabel" aria-hidden="true">					<dt class="col-sm-4">Email Activo</dt><dd class="col-sm-8" id="v_email_active"></dd>

	<div class="modal-dialog" role="document">					<dt class="col-sm-4">Grupo</dt><dd class="col-sm-8" id="v_grupo"></dd>

		<form id="formDeleteUser">					<dt class="col-sm-4">Tienda</dt><dd class="col-sm-8" id="v_tienda"></dd>

			<div class="modal-content">					<dt class="col-sm-4">Estado</dt><dd class="col-sm-8" id="v_state"></dd>

				<div class="modal-header bg-danger">				</dl>

					<h5 class="modal-title" id="deleteUserModalLabel">Eliminar Usuario</h5>			</div>

					<button type="button" class="close" data-dismiss="modal" aria-label="Close">			<div class="modal-footer">

						<span aria-hidden="true">&times;</span>				<button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>

					</button>			</div>

				</div>		</div>

				<div class="modal-body">	</div>

					<input type="hidden" id="d_codigo" name="codigo"></div>

					<p>¿Está seguro que desea eliminar al usuario <strong id="d_user"></strong> (código <span id="d_codigo_txt"></span>)?</p>

				</div><!-- Modal: Editar Usuario -->

				<div class="modal-footer"><div class="modal fade" id="editUserModal" tabindex="-1" role="dialog" aria-labelledby="editUserModalLabel" aria-hidden="true">

					<button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>	<div class="modal-dialog" role="document">

					<button type="submit" class="btn btn-danger">Eliminar</button>		<form id="formEditUser" autocomplete="off">

				</div>			<div class="modal-content">

			</div>				<div class="modal-header bg-warning">

		</form>					<h5 class="modal-title" id="editUserModalLabel">Editar Usuario</h5>

	</div>					<button type="button" class="close" data-dismiss="modal" aria-label="Close">

</div>						<span aria-hidden="true">&times;</span>
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
