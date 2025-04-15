<?php
    // Ejecutar consulta SQL
    $result = mysqli_query($conn, $sql);
    if (!$result) {
        die('Error en la consulta: ' . mysqli_error($conn));
    }
    ?>

    <?php while ($row = mysqli_fetch_array($result)): ?>
 
        <?Php if(Tiene_permiso($permisos_user,'ver-consultas')){ ?>
        <!-- INICIO DEL MODAL: Muestra la información del registro -->
        <div class="modal fade" id="ver-<?php echo htmlspecialchars($row['serie']); ?>" tabindex="-1" role="dialog" aria-labelledby="modalLabel<?php echo htmlspecialchars($row['serie']); ?>" aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <!-- CABECERA DEL MODAL -->
                    <div class="modal-header">
                        <h4 class="modal-title" id="modalLabel<?php echo htmlspecialchars($row['serie']); ?>">Información</h4>
                        <!-- Botón para cerrar el modal -->
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>

                    <!-- CUERPO DEL MODAL -->
                    <div class="modal-body">
                        <!-- Formulario para enviar datos a 'accion.php' -->
                        <form action="../../logica/accion?accion=8&imei=<?php echo urlencode($row['serie']); ?>" method="POST">
                            <div class="card-body">
                                <!-- Información del IMEI -->
                                <div class="form-group">
                                    <label for="imei">IMEI:</label>
                                    <p><?php echo htmlspecialchars($row['serie']); ?></p>
                                </div>
                                
                                <!-- Información del Modelo -->
                                <div class="form-group">
                                    <label for="modelo">Modelo:</label>
                                    <p><?php echo htmlspecialchars($row['modelo']); ?></p>
                                </div>
                                
                                <!-- Nombre del Cliente -->
                                <div class="form-group">
                                    <label for="nombre">Nombre:</label>
                                    <p><?php echo htmlspecialchars($row['Nombre_Cliente']); ?></p>
                                </div>
                                
                                <!-- Número de Celular -->
                                <div class="form-group">
                                    <label for="celular">Celular:</label>
                                    <p><?php echo htmlspecialchars($row['Celular']); ?></p>
                                </div>
                                
                                <!-- Información de la Tienda -->
                                <div class="form-group">
                                    <label for="tienda">Tienda:</label>
                                    <p><?php echo htmlspecialchars($row['nombre']); ?></p>
                                </div>
                                
                                <!-- Estado Actual -->
                                <div class="form-group">
                                    <label for="estado">Estado Actual:</label>
                                    <p>
                                        <?php
                                        // Asegurar que el índice existe antes de usarlo
                                        $st = $row['cod_estado'] - 1;
                                        $estado = isset($row_state[$st]) ? $row_state[$st][1] : 'Desconocido';
                                        echo htmlspecialchars($estado);
                                        ?>
                                    </p>
                                </div>
                                
                                <!-- Precio -->
                                <div class="form-group">
                                    <label for="precio">Precio:</label>
                                    <p><?php echo htmlspecialchars($row['Precio']); ?></p>
                                </div>
                                
                                <!-- Tiempo -->
                                <div class="form-group">
                                    <label for="tiempo">Tiempo:</label>
                                    <p><?php echo htmlspecialchars($row['Tiempo']); ?></p>
                                </div>
                                
                                <!-- Fecha de Creación -->
                                <div class="form-group">
                                    <label for="fecha_creacion">Fecha Creación:</label>
                                    <p><?php echo htmlspecialchars($row['date']); ?></p>
                                </div>
                                
                                <!-- Fecha de Último Cambio -->
                                <div class="form-group">
                                    <label for="fecha_ultimo_cambio">Fecha Último Cambio:</label>
                                    <p><?php echo htmlspecialchars($row['date_update']); ?></p>
                                </div>
                                
                                <!-- Observaciones -->
                                <div class="form-group">
                                    <label for="observaciones">Observaciones:</label>
                                    <p><?php echo htmlspecialchars($row['Observaciones']); ?></p>
                                </div>
                            </div> <!-- Fin del cuerpo de la tarjeta -->
                        </form> <!-- Fin del formulario -->
                    </div> <!-- Fin del cuerpo del modal -->

                    <!-- PIE DEL MODAL -->
                    <div class="modal-footer justify-content-between">
                        <!-- Botón para cerrar el modal -->
                        <button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
                    </div>
                </div> <!-- Fin del contenido del modal -->
            </div> <!-- Fin del diálogo del modal -->
        </div> <!-- Fin del modal --> <?php }?>



        
                  
<!-- INICIO DEL MODAL: Sección para la funcionalidad "Informar" -->
<?Php if(Tiene_permiso($permisos_user,'informar_consulta')){ ?>
<div class="modal fade" id="info-<?php echo htmlspecialchars($row['serie']); ?>">
    <div class="modal-dialog">
        <div class="modal-content">
            <!-- CABECERA DEL MODAL -->
            <div class="modal-header">
                <h4 class="modal-title">Informar</h4>
                <!-- Botón para cerrar el modal -->
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>

            <!-- CUERPO DEL MODAL -->
            <div class="modal-body">
                <!-- Formulario para enviar datos a 'accion.php' con acción específica y número de serie -->
                <form action="../../logica/accion?accion=1&imei=<?php echo htmlspecialchars($row['serie']); ?>" method="POST" class="modal-form">
                    <div class="card-body">
                        <!-- Mostrar información del IMEI -->
                        <div class="form-group">
                            <label for="name">IMEI:</label>
                            <p><?php echo htmlspecialchars($row['serie']); ?></p>
                        </div>

                        <!-- Mostrar información del Modelo -->
                        <div class="form-group">
                            <label for="name">Modelo:</label>
                            <p><?php echo htmlspecialchars($row['modelo']); ?></p>
                        </div>

                        <!-- Mostrar nombre del cliente -->
                        <div class="form-group">
                            <label for="name">Nombre:</label>
                            <p><?php echo htmlspecialchars($row['Nombre_Cliente']); ?></p>
                        </div>

                        <!-- Mostrar número de celular -->
                        <div class="form-group">
                            <label for="Celular">Celular:</label>
                            <p><?php echo htmlspecialchars($row['Celular']); ?></p>
                        </div>

                        <!-- Mostrar el estado actual (uso correcto de comparación) -->
                        <div class="form-group">
                            <label for="Estado">Estado Actual:</label>
                            <p>
                                <?php 
                                // Comparación adecuada usando '=='
                                if ($row['Estado'] == 1) {
                                    echo "Consulta";
                                }
                                ?>
                            </p>
                        </div>

                        <!-- Campo para ingresar el precio -->
                        <div class="form-group">
                            <label for="precio">Precio:</label>
                            <input type="text" name="precio" class="form-control" placeholder="Escriba Precio de Liberación" required>
                        </div>

                        <!-- Campo para ingresar el tiempo estimado -->
                        <div class="form-group">
                            <label for="tiempo">Tiempo:</label>
                            <input type="text" name="tiempo" class="form-control" placeholder="Escriba Tiempo Estimado" required>
                        </div>

                        <!-- Campo de texto para observaciones -->
                        <div class="form-group">
                            <label for="desc">Observaciones:</label>
                            <textarea class="form-control" name="desc" rows="3" placeholder="Ingrese observaciones..."></textarea>
                        </div>
                    </div> <!-- Fin del cuerpo de la tarjeta -->
                </div> <!-- Fin del cuerpo del modal -->

                <!-- PIE DEL MODAL -->
                <div class="modal-footer justify-content-between">
                    <!-- Botón para cerrar el modal sin enviar datos -->
                    <button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
                    <!-- Botón para enviar el formulario y ejecutar la acción de "Informar" -->
                    <button type="submit" class="btn btn-primary">Informar</button>
                </div>
                </form> <!-- Fin del formulario -->
            </div> <!-- Fin del contenido del modal -->
        </div> <!-- Fin del diálogo del modal -->
    </div> <!-- Fin del modal -->

    <?php }?>
<!-- FIN DEL MODAL: Termina la estructura del código para la funcionalidad "Informar" -->


<!-- Incio de Modal para Rechazar la Liberacion --> 
<?Php if(Tiene_permiso($permisos_user,'rechazar_lb')){ ?>
<div class="modal fade" id="rechazar-<?php echo htmlspecialchars($row['serie']); ?>">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Rechazar</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <!-- Formulario para enviar datos al archivo accion.php con acción específica y número de serie -->
                <form action="../../logica/accion?accion=5.1&imei=<?php echo htmlspecialchars($row['serie']); ?>" method="POST">
                    <div class="card-body">
                        <!-- Mostrar información del IMEI -->
                        <div class="form-group">
                            <label>Imei:</label>
                            <p><?php echo htmlspecialchars($row['serie']); ?></p>
                        </div>
                        <!-- Mostrar información del Modelo -->
                        <div class="form-group">
                            <label>Modelo:</label>
                            <p><?php echo htmlspecialchars($row['modelo']); ?></p>
                        </div>
                        <!-- Mostrar nombre del cliente -->
                        <div class="form-group">
                            <label>Nombre:</label>
                            <p><?php echo htmlspecialchars($row['Nombre_Cliente']); ?></p>
                        </div>
                        <!-- Mostrar número de celular -->
                        <div class="form-group">
                            <label>Celular:</label>
                            <p><?php echo htmlspecialchars($row['Celular']); ?></p>
                        </div>
                        <!-- Mostrar el estado actual (ajustado el operador de comparación) -->
                        <div class="form-group">
                            <label>Estado Actual:</label>
                            <p>
                                <?php 
                                // Utilizar operador de comparación correcto '=='
                                if ($row['Estado'] == 1) {
                                    echo "Consulta";
                                }
                                ?>
                            </p>
                        </div>
                        <!-- Campo de texto para observaciones -->
                        <div class="form-group">
                            <label for="desc">Observaciones:</label>
                            <textarea class="form-control" name="desc" rows="3" placeholder="Enter ..."></textarea>
                        </div>
                    </div>
                </div>
                <!-- Botones del modal: cerrar y enviar formulario -->
                <div class="modal-footer justify-content-between">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
                    <button type="submit" class="btn btn-danger">Rechazar</button>
                </div>
                </form>
            </div>
        </div>
    </div>
</div>
<?php } ?>
<!-- /.modal --> 

    <!-- Incio de Modal para Aprobacion de Liberacion --> 
<?Php if(Tiene_permiso($permisos_user,'aprobar-liberacion')){ ?>
    <div class="modal fade" id="check-<?php echo htmlspecialchars($row['serie']); ?>">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Aprobar Liberación</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form action="../../logica/accion?accion=2&imei=<?php echo htmlspecialchars($row['serie']); ?>" method="POST">
                    <div class="card-body">
                        <div class="form-group">
                            <label>Imei:</label>
                            <p><?php echo htmlspecialchars($row['serie']); ?></p>
                        </div>
                        <div class="form-group">
                            <label>Modelo:</label>
                            <p><?php echo htmlspecialchars($row['modelo']); ?></p>
                        </div>
                        <div class="form-group">
                            <label>Nombre:</label>
                            <p><?php echo htmlspecialchars($row['Nombre_Cliente']); ?></p>
                        </div>
                        <div class="form-group">
                            <label>Celular:</label>
                            <p><?php echo htmlspecialchars($row['Celular']); ?></p>
                        </div>
                        <div class="form-group">
                            <label>Estado Actual:</label>
                            <p>
                                <?php 
                                $st = $row['cod_estado'] - 1;
                                echo htmlspecialchars($row_state[$st][1]);
                                ?>
                            </p>
                        </div>
                        <div class="form-group">
                            <label>Precio:</label>
                            <p><?php echo htmlspecialchars($row['Precio']); ?></p>
                        </div>
                        <div class="form-group">
                            <label>Tiempo:</label>
                            <p><?php echo htmlspecialchars($row['Tiempo']); ?></p>
                        </div>
                        <div class="form-group">
                            <label>Observaciones:</label>
                            <textarea class="form-control" name="desc" rows="3"><?php echo htmlspecialchars($row['Observaciones']); ?></textarea>
                        </div>
                    </div>
                    <div class="modal-footer justify-content-between">
                        <button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
                        <button type="submit" class="btn btn-success"><i class="fas fa-check"></i> Aprobar</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<?php } ?>
<!-- /.modal --> 

<!-- Modal para Reiniciar -->
<?Php if(Tiene_permiso($permisos_user,'reiniciar_lb')){ ?>
<div class="modal fade" id="redo-<?php echo htmlspecialchars($row['serie']); ?>" tabindex="-1" role="dialog" aria-labelledby="modalLabel-<?php echo htmlspecialchars($row['serie']); ?>" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <!-- Cabecera del Modal -->
            <div class="modal-header">
                <h4 class="modal-title" id="modalLabel-<?php echo htmlspecialchars($row['serie']); ?>">Reiniciar</h4>
                <!-- Botón para cerrar el modal -->
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            
            <!-- Cuerpo del Modal -->
            <div class="modal-body">
                <!-- Formulario para reiniciar -->
                <form action="../../logica/accion" method="POST">
                    <input type="hidden" name="accion" value="2.1">
                    <input type="hidden" name="imei" value="<?php echo urlencode($row['serie']); ?>">
                    <div class="card-body">
                        <!-- Información del IMEI -->
                        <div class="form-group">
                            <label for="imei">IMEI:</label>
                            <p><?php echo htmlspecialchars($row['serie']); ?></p>
                        </div>
                        
                        <!-- Información del Modelo -->
                        <div class="form-group">
                            <label for="modelo">Modelo:</label>
                            <p><?php echo htmlspecialchars($row['modelo']); ?></p>
                        </div>
                        
                        <!-- Nombre del Cliente -->
                        <div class="form-group">
                            <label for="nombre">Nombre:</label>
                            <p><?php echo htmlspecialchars($row['Nombre_Cliente']); ?></p>
                        </div>
                        
                        <!-- Número de Celular -->
                        <div class="form-group">
                            <label for="celular">Celular:</label>
                            <p><?php echo htmlspecialchars($row['Celular']); ?></p>
                        </div>
                        
                        <!-- Estado Actual -->
                        <div class="form-group">
                            <label for="estado">Estado Actual:</label>
                            <p>
                                <?php
                                // Validar y ajustar el índice del estado
                                $st = max($row['cod_estado'] - 1, 0); // Asegurar que el índice no sea negativo
                                $estado = isset($row_state[$st]) ? htmlspecialchars($row_state[$st][1]) : 'Desconocido';
                                echo $estado;
                                ?>
                            </p>
                        </div>
                    </div> <!-- Fin del cuerpo de la tarjeta -->
                    
                    <!-- Pie del Modal -->
                    <div class="modal-footer justify-content-between">
                        <!-- Botón para cerrar el modal -->
                        <button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
                        <!-- Botón para enviar el formulario -->
                        <button type="submit" class="btn btn-info">Reiniciar</button>
                    </div>
                </form> <!-- Fin del formulario -->
            </div> <!-- Fin del cuerpo del modal -->
        </div> <!-- Fin del contenido del modal -->
    </div> <!-- Fin del diálogo del modal -->
</div> <?php } ?> <!-- Fin del modal -->

<!-- Modal for Starting/Ending Process -->
<?Php if(Tiene_permiso($permisos_user,'inciar_lb') || Tiene_permiso($permisos_user,'finalizar_liberacion')){ ?>
<div class="modal fade" id="<?php echo ($row['cod_estado'] == $page ? 'star-' : 'end-') . htmlspecialchars($row['serie'], ENT_QUOTES, 'UTF-8'); ?>">
    <div class="modal-dialog">
        <div class="modal-content">
            
            <!-- Modal Header -->
            <div class="modal-header">
                <h4 class="modal-title">Iniciar / Finalizar</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            
            <!-- Modal Body -->
            <div class="modal-body">
                <form action="../../logica/accion?accion=<?php echo (int)$row['cod_estado']; ?>&imei=<?php echo urlencode($row['serie']); ?>" 
                      id="<?php echo ($row['cod_estado'] == $page ? 'start-' : 'end-') . htmlspecialchars($row['serie'], ENT_QUOTES, 'UTF-8'); ?>" 
                      method="POST">
                    
                    <!-- Form Content -->
                    <div class="card-body">
                        
                        <!-- Display IMEI -->
                        <div class="form-group">
                            <label for="name">IMEI:</label>
                            <p><?php echo htmlspecialchars($row['serie'], ENT_QUOTES, 'UTF-8'); ?></p>
                        </div>
                        
                        <!-- Display Model -->
                        <div class="form-group">
                            <label for="name">Modelo:</label>
                            <p><?php echo htmlspecialchars($row['modelo'], ENT_QUOTES, 'UTF-8'); ?></p>
                        </div>
                        
                        <!-- Display Customer Name -->
                        <div class="form-group">
                            <label for="name">Nombre:</label>
                            <p><?php echo htmlspecialchars($row['Nombre_Cliente'], ENT_QUOTES, 'UTF-8'); ?></p>
                        </div>
                        
                        <!-- Display Phone Number -->
                        <div class="form-group">
                            <label for="Celular">Celular:</label>
                            <p><?php echo htmlspecialchars($row['Celular'], ENT_QUOTES, 'UTF-8'); ?></p>
                        </div>
                        
                        <!-- Display Current Status -->
                        <div class="form-group">
                            <label for="Celular">Estado Actual:</label>
                            <p><?php 
                                $estadoIndex = (int)$row['cod_estado'] - 1; 
                                $estado = isset($row_state[$estadoIndex][1]) ? $row_state[$estadoIndex][1] : 'Estado desconocido';
                                echo htmlspecialchars($estado, ENT_QUOTES, 'UTF-8'); 
                            ?></p>
                        </div>
                        
                        <!-- Display Price -->
                        <div class="form-group">
                            <label for="precio">Precio:</label>
                            <p><?php echo htmlspecialchars($row['Precio'], ENT_QUOTES, 'UTF-8'); ?></p>
                        </div>
                        
                        <!-- Display Estimated Time -->
                        <div class="form-group">
                            <label for="tiempo">Tiempo:</label>
                            <p><?php echo htmlspecialchars($row['Tiempo'], ENT_QUOTES, 'UTF-8'); ?></p>
                        </div>
                        
                        <!-- Observations -->
                        <div class="form-group">
                            <label for="Observaciones">Observaciones:</label>
                            <textarea required class="form-control" name="desc" rows="3"><?php echo htmlspecialchars($row['Observaciones'], ENT_QUOTES, 'UTF-8'); ?></textarea>
                        </div>
                        
                    </div> <!-- /.card-body -->
                    
                    <!-- Modal Footer -->
                    <div class="modal-footer justify-content-between">
                        
                        <?php if ($row['cod_estado'] == $page) { ?>
                            <!-- Start Button -->
                            <button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
                            <button type="submit" class="btn btn-default">Iniciar</button>
                        
                        <?php } elseif ($row['cod_estado'] == $pageend) { ?>
                            <!-- End Process with Select Options -->
                            <div class="form-group">
                                <select name="resultado" class="form-control select2bs4" style="width: 100%;" required>
                                    <option value="5">Finalizada con Éxito</option>
                                    <option value="8">Únicamente SemiFabrica</option>
                                    <option value="7">Rechazada Por Reporte</option>
                                    <option value="9">Rechazada Por Sistema</option>
                                </select>
                            </div>
                            <button type="submit" class="btn btn-warning">Finalizar</button>
                        
                        <?php } ?>
                        
                    </div> <!-- /.modal-footer -->
                    
                </form> <!-- /.form -->
            </div> <!-- /.modal-body -->

        </div> <!-- /.modal-content -->
    </div> <!-- /.modal-dialog -->
</div>  <?php } ?><!-- /.modal -->


<?php endwhile; ?>



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



<script>
    // Previsualizar imagen de perfil seleccionada
    function previewImage(event) {
        var reader = new FileReader();
        reader.onload = function(){
            var output = document.getElementById('profilePic');
            output.src = reader.result;
        };
        reader.readAsDataURL(event.target.files[0]);
    }

    // Mostrar/Ocultar contraseñas
    function togglePasswordVisibility(...fields) {
        fields.forEach(function(field) {
            var inputField = document.getElementById(field);
            if (inputField.type === 'password') {
                inputField.type = 'text';
            } else {
                inputField.type = 'password';
            }
        });
    }

    // Envío del formulario con AJAX (sin recargar la página)
    $('#userProfileForm').on('submit', function(e) {
        e.preventDefault(); // Evita el envío normal del formulario
        var formData = new FormData(this);
        
        $.ajax({
            url: '/home/logica/update_profile.php', // Usa rutas absolutas
            type: 'POST',
            data: formData,
            contentType: false,
            processData: false,
            success: function(response) {
                alert('Perfil actualizado correctamente');
                $('#modal_user').modal('hide');
            },
            error: function() {
                alert('Error al actualizar el perfil');
            }
        });
    });
</script>

