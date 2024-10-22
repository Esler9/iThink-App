<div class="modal fade" id="modal_user">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Perfil de Usuario</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id="userProfileForm" method="POST" enctype="multipart/form-data">
                <div class="modal-body">
                    <!-- Token CSRF (si es necesario) -->
                    <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">

                    <!-- Profile Image -->
                    <div class="text-center mb-3">
                        <img id="profilePic" class="profile-user-img img-fluid img-circle" src="/home/dist/img/user4-128x128.jpg" alt="User profile picture">
                        <input type="file" name="profile_picture" class="form-control-file mt-2" accept="image/*" onchange="previewImage(event)">
                    </div>

                    <!-- Nombre de Usuario -->
                    <div class="form-group">
                        <label for="username">Usuario</label>
                        <input type="text" class="form-control" name="username" id="username" value="Nina Mcintire" required>
                    </div>

                    <!-- Correo Electrónico -->
                    <div class="form-group">
                        <label for="email">Correo Electrónico</label>
                        <input type="email" class="form-control" name="email" id="email" value="nina@example.com" required>
                    </div>

                    <!-- Número de Contacto -->
                    <div class="form-group">
                        <label for="contact_number">Número de Contacto</label>
                        <input type="text" class="form-control" name="contact_number" id="contact_number" value="555-1234" required>
                    </div>

                    <!-- Fecha de Nacimiento -->
                    <div class="form-group">
                        <label for="birthdate">Fecha de Nacimiento</label>
                        <input type="date" class="form-control" name="birthdate" id="birthdate" value="1990-01-01" required>
                    </div>

                    <!-- Cambio de Contraseña -->
                    <div class="form-group">
                        <label for="new_password">Nueva Contraseña</label>
                        <input type="password" class="form-control" name="new_password" id="new_password" placeholder="Nueva Contraseña">
                    </div>
                    <div class="form-group">
                        <label for="confirm_password">Confirmar Nueva Contraseña</label>
                        <input type="password" class="form-control" name="confirm_password" id="confirm_password" placeholder="Confirmar Nueva Contraseña">
                        <button type="button" class="btn btn-secondary mt-2" onclick="togglePasswordVisibility('new_password', 'confirm_password')">
                            <i id="toggleIcon" class="fas fa-eye"></i> Mostrar Contraseñas
                        </button>
                    </div>
                </div>
                <div class="modal-footer justify-content-between">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
                    <button type="submit" class="btn btn-primary">Guardar Cambios</button>
                </div>
            </form>
        </div>
    </div>
</div>