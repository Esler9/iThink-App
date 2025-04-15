<div class="modal fade" id="modal_user" tabindex="-1" role="dialog" aria-labelledby="modal_userLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <!-- Agregamos enctype para permitir la subida de archivos -->
      <form id="editProfileForm" action="/home/logica/actualizar_usuario" method="post" enctype="multipart/form-data">
        <div class="modal-header">
          <h5 class="modal-title" id="modal_userLabel">Editar Perfil</h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Cerrar">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body">
          <!-- Campo para actualizar el nombre de usuario -->
          <div class="form-group">
            <label for="username_input">Nombre de Usuario</label>
            <input type="text" class="form-control" id="username_input" name="username" value="<?php echo $User; ?>" required>
          </div>
          <!-- Campo para actualizar la contraseña -->
          <div class="form-group">
            <label for="password_input">Contraseña</label>
            <input type="password" class="form-control" id="password_input" name="password">
            <small class="form-text text-muted">Déjalo en blanco si no deseas cambiar la contraseña.</small>
          </div>
          <!-- Campo para cambiar la foto de perfil -->
          <div class="form-group">
            <label for="profile_picture">Foto de Perfil</label>
            <input type="file" class="form-control-file" id="profile_picture" name="profile_picture">
          </div>
          <!-- Agrega aquí otros campos que desees editar -->
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
          <button type="submit" class="btn btn-primary">Guardar cambios</button>
        </div>
      </form>
    </div>
  </div>
</div>