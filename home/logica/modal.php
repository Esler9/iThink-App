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
            <div class="modal-dialog modal-lg" role="document">
                <div class="modal-content">
                    <!-- CABECERA DEL MODAL -->
                    <div class="modal-header bg-primary">
                        <h4 class="modal-title text-white" id="modalLabel<?php echo htmlspecialchars($row['serie']); ?>">
                            <i class="fas fa-info-circle"></i> Información Detallada
                        </h4>
                        <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>

                    <!-- CUERPO DEL MODAL -->
                    <div class="modal-body">
                        <form action="../../logica/accion?accion=8&imei=<?php echo urlencode($row['serie']); ?>" method="POST">
                            <div class="card-body">
                                <!-- Sección: Información del Cliente -->
                                <h5 class="text-primary border-bottom pb-2 mb-3">
                                    <i class="fas fa-user"></i> Información del Cliente
                                </h5>
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="nombre"><strong>Nombre:</strong></label>
                                            <p><?php echo htmlspecialchars($row['Nombre_Cliente']); ?></p>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="celular"><strong>Celular:</strong></label>
                                            <p>+502 <?php echo htmlspecialchars($row['Celular']); ?></p>
                                        </div>
                                    </div>
                                </div>

                                <!-- Sección: Información del Equipo -->
                                <h5 class="text-primary border-bottom pb-2 mb-3 mt-4">
                                    <i class="fas fa-mobile-alt"></i> Información del Equipo
                                </h5>
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="imei"><strong>IMEI Principal:</strong></label>
                                            <p class="font-weight-bold"><?php echo htmlspecialchars($row['serie']); ?></p>
                                        </div>
                                    </div>
                                    <?php if (!empty($row['serie_2'])): ?>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="imei2"><strong>IMEI Secundario:</strong></label>
                                            <p class="font-weight-bold">
                                                <?php echo htmlspecialchars($row['serie_2']); ?>
                                                <span class="badge badge-info">Dual SIM</span>
                                            </p>
                                        </div>
                                    </div>
                                    <?php endif; ?>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="modelo"><strong>Modelo:</strong></label>
                                            <p><?php echo htmlspecialchars($row['modelo']); ?></p>
                                        </div>
                                    </div>
                                    <?php if (!empty($row['tiempo_usa'])): ?>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="tiempo_usa"><strong>Tiempo en USA:</strong></label>
                                            <p>
                                                <?php 
                                                $tiempos = [
                                                    '0-3' => '0 a 3 meses',
                                                    '3-6' => '3 a 6 meses',
                                                    '6-12' => '6 a 12 meses',
                                                    '12+' => 'Más de 12 meses',
                                                    'nunca' => 'Nunca usado en USA'
                                                ];
                                                echo isset($tiempos[$row['tiempo_usa']]) ? $tiempos[$row['tiempo_usa']] : $row['tiempo_usa'];
                                                ?>
                                            </p>
                                        </div>
                                    </div>
                                    <?php endif; ?>
                                </div>

                                <!-- Sección: Verificaciones de Seguridad -->
                                <?php if ($row['no_blacklist'] == 1 || $row['no_icloud'] == 1): ?>
                                <h5 class="text-primary border-bottom pb-2 mb-3 mt-4">
                                    <i class="fas fa-shield-alt"></i> Verificaciones de Seguridad
                                </h5>
                                <div class="row">
                                    <?php if ($row['no_blacklist'] == 1): ?>
                                    <div class="col-md-6">
                                        <div class="alert alert-success">
                                            <i class="fas fa-check-circle"></i>
                                            <strong>NO</strong> está en Blacklist
                                        </div>
                                    </div>
                                    <?php endif; ?>
                                    <?php if ($row['no_icloud'] == 1): ?>
                                    <div class="col-md-6">
                                        <div class="alert alert-success">
                                            <i class="fas fa-check-circle"></i>
                                            <strong>NO</strong> tiene iCloud activo
                                        </div>
                                    </div>
                                    <?php endif; ?>
                                </div>
                                <?php endif; ?>

                                <!-- Sección: Información del Servicio -->
                                <h5 class="text-primary border-bottom pb-2 mb-3 mt-4">
                                    <i class="fas fa-cogs"></i> Información del Servicio
                                </h5>
                                <div class="row">
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="tienda"><strong>Tienda:</strong></label>
                                            <p><?php echo htmlspecialchars($row['nombre']); ?></p>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="estado"><strong>Estado Actual:</strong></label>
                                            <p>
                                                <?php
                                                $st = $row['cod_estado'] - 1;
                                                $estado = isset($row_state[$st]) ? $row_state[$st][1] : 'Desconocido';
                                                echo htmlspecialchars($estado);
                                                ?>
                                            </p>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="precio"><strong>Precio:</strong></label>
                                            <p class="text-success font-weight-bold">Q <?php echo htmlspecialchars($row['Precio']); ?></p>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="tiempo"><strong>Tiempo Estimado:</strong></label>
                                            <p><?php echo htmlspecialchars($row['Tiempo']); ?> días</p>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="fecha_creacion"><strong>Fecha Creación:</strong></label>
                                            <p><?php echo htmlspecialchars($row['date']); ?></p>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="fecha_ultimo_cambio"><strong>Última Actualización:</strong></label>
                                            <p><?php echo htmlspecialchars($row['date_update']); ?></p>
                                        </div>
                                    </div>
                                </div>

                                <!-- Observaciones -->
                                <?php if (!empty($row['Observaciones'])): ?>
                                <div class="form-group mt-3">
                                    <label for="observaciones"><strong><i class="fas fa-comments"></i> Observaciones:</strong></label>
                                    <div class="alert alert-warning">
                                        <?php echo nl2br(htmlspecialchars($row['Observaciones'])); ?>
                                    </div>
                                </div>
                                <?php endif; ?>
                            </div>
                        </form>
                    </div>

                    <!-- PIE DEL MODAL -->
                    <div class="modal-footer justify-content-between">
                        <button type="button" class="btn btn-default" data-dismiss="modal">
                            <i class="fas fa-times"></i> Cerrar
                        </button>
                    </div>
                </div>
            </div>
        </div>
        <?php }?>

        
                  
<!-- INICIO DEL MODAL: Sección para la funcionalidad "Informar" -->
<?Php if(Tiene_permiso($permisos_user,'informar_consulta')){ ?>
<div class="modal fade" id="info-<?php echo htmlspecialchars($row['serie']); ?>">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-info">
                <h4 class="modal-title text-white">
                    <i class="fas fa-file-invoice"></i> Informar Consulta
                </h4>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>

            <div class="modal-body">
                <form action="../../logica/accion?accion=1&imei=<?php echo htmlspecialchars($row['serie']); ?>" method="POST" class="modal-form">
                    <div class="card-body">
                        <!-- Información del Equipo -->
                        <h5 class="text-info border-bottom pb-2 mb-3">
                            <i class="fas fa-mobile-alt"></i> Datos del Equipo
                        </h5>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label><strong>IMEI Principal:</strong></label>
                                    <p class="font-weight-bold"><?php echo htmlspecialchars($row['serie']); ?></p>
                                </div>
                            </div>
                            <?php if (!empty($row['serie_2'])): ?>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label><strong>IMEI Secundario:</strong></label>
                                    <p class="font-weight-bold">
                                        <?php echo htmlspecialchars($row['serie_2']); ?>
                                        <span class="badge badge-info">Dual SIM</span>
                                    </p>
                                </div>
                            </div>
                            <?php endif; ?>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label><strong>Modelo:</strong></label>
                                    <p><?php echo htmlspecialchars($row['modelo']); ?></p>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label><strong>Nombre Cliente:</strong></label>
                                    <p><?php echo htmlspecialchars($row['Nombre_Cliente']); ?></p>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label><strong>Celular:</strong></label>
                                    <p>+502 <?php echo htmlspecialchars($row['Celular']); ?></p>
                                </div>
                            </div>
                            <?php if (!empty($row['tiempo_usa'])): ?>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label><strong>Tiempo en USA:</strong></label>
                                    <p>
                                        <?php 
                                        $tiempos = [
                                            '0-3' => '0 a 3 meses',
                                            '3-6' => '3 a 6 meses',
                                            '6-12' => '6 a 12 meses',
                                            '12+' => 'Más de 12 meses',
                                            'nunca' => 'Nunca usado en USA'
                                        ];
                                        echo isset($tiempos[$row['tiempo_usa']]) ? $tiempos[$row['tiempo_usa']] : $row['tiempo_usa'];
                                        ?>
                                    </p>
                                </div>
                            </div>
                            <?php endif; ?>
                        </div>

                        <!-- Verificaciones -->
                        <?php if ($row['no_blacklist'] == 1 || $row['no_icloud'] == 1): ?>
                        <div class="alert alert-success">
                            <strong><i class="fas fa-shield-alt"></i> Verificaciones de Seguridad:</strong>
                            <ul class="mb-0 mt-2">
                                <?php if ($row['no_blacklist'] == 1): ?>
                                <li><i class="fas fa-check"></i> NO está en Blacklist</li>
                                <?php endif; ?>
                                <?php if ($row['no_icloud'] == 1): ?>
                                <li><i class="fas fa-check"></i> NO tiene iCloud activo</li>
                                <?php endif; ?>
                            </ul>
                        </div>
                        <?php endif; ?>

                        <!-- Información del Servicio -->
                        <h5 class="text-info border-bottom pb-2 mb-3 mt-4">
                            <i class="fas fa-dollar-sign"></i> Información del Servicio
                        </h5>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="precio">Precio: <span class="text-danger">*</span></label>
                                    <div class="input-group">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text">Q</span>
                                        </div>
                                        <input type="number" name="precio" class="form-control" 
                                               placeholder="Ingrese el precio" min="0" step="0.01" required>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="tiempo">Tiempo Estimado (días): <span class="text-danger">*</span></label>
                                    <input type="number" name="tiempo" class="form-control" 
                                           placeholder="Días hábiles" min="1" required>
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="desc">Observaciones:</label>
                            <textarea class="form-control" name="desc" rows="3" 
                                      placeholder="Ingrese observaciones adicionales..."><?php echo htmlspecialchars($row['Observaciones']); ?></textarea>
                        </div>
                    </div>
                    
                    <div class="modal-footer justify-content-between">
                        <button type="button" class="btn btn-default" data-dismiss="modal">
                            <i class="fas fa-times"></i> Cerrar
                        </button>
                        <button type="submit" class="btn btn-info">
                            <i class="fas fa-paper-plane"></i> Informar
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<?php }?>

<!-- Incio de Modal para Rechazar la Liberacion --> 
<?Php if(Tiene_permiso($permisos_user,'rechazar_lb')){ ?>
<div class="modal fade" id="rechazar-<?php echo htmlspecialchars($row['serie']); ?>">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-danger">
                <h4 class="modal-title text-white">
                    <i class="fas fa-times-circle"></i> Rechazar Liberación
                </h4>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form action="../../logica/accion?accion=5.1&imei=<?php echo htmlspecialchars($row['serie']); ?>" method="POST">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label><strong>IMEI:</strong></label>
                                    <p><?php echo htmlspecialchars($row['serie']); ?></p>
                                </div>
                            </div>
                            <?php if (!empty($row['serie_2'])): ?>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label><strong>IMEI Secundario:</strong></label>
                                    <p><?php echo htmlspecialchars($row['serie_2']); ?></p>
                                </div>
                            </div>
                            <?php endif; ?>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label><strong>Modelo:</strong></label>
                                    <p><?php echo htmlspecialchars($row['modelo']); ?></p>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label><strong>Nombre:</strong></label>
                                    <p><?php echo htmlspecialchars($row['Nombre_Cliente']); ?></p>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label><strong>Celular:</strong></label>
                                    <p>+502 <?php echo htmlspecialchars($row['Celular']); ?></p>
                                </div>
                            </div>
                        </div>
                        
                        <div class="form-group">
                            <label for="desc">Motivo del Rechazo: <span class="text-danger">*</span></label>
                            <textarea class="form-control" name="desc" rows="4" 
                                      placeholder="Explique detalladamente el motivo del rechazo..." required></textarea>
                        </div>
                    </div>
                    
                    <div class="modal-footer justify-content-between">
                        <button type="button" class="btn btn-default" data-dismiss="modal">
                            <i class="fas fa-times"></i> Cerrar
                        </button>
                        <button type="submit" class="btn btn-danger">
                            <i class="fas fa-ban"></i> Rechazar
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<?php } ?>

<!-- Incio de Modal para Aprobacion de Liberacion --> 
<?Php if(Tiene_permiso($permisos_user,'aprobar-liberacion')){ ?>
<div class="modal fade" id="check-<?php echo htmlspecialchars($row['serie']); ?>">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-success">
                <h4 class="modal-title text-white">
                    <i class="fas fa-check-circle"></i> Aprobar Liberación
                </h4>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form action="../../logica/accion?accion=2&imei=<?php echo htmlspecialchars($row['serie']); ?>" method="POST">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label><strong>IMEI:</strong></label>
                                    <p><?php echo htmlspecialchars($row['serie']); ?></p>
                                </div>
                            </div>
                            <?php if (!empty($row['serie_2'])): ?>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label><strong>IMEI Secundario:</strong></label>
                                    <p><?php echo htmlspecialchars($row['serie_2']); ?></p>
                                </div>
                            </div>
                            <?php endif; ?>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label><strong>Modelo:</strong></label>
                                    <p><?php echo htmlspecialchars($row['modelo']); ?></p>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label><strong>Nombre:</strong></label>
                                    <p><?php echo htmlspecialchars($row['Nombre_Cliente']); ?></p>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label><strong>Celular:</strong></label>
                                    <p>+502 <?php echo htmlspecialchars($row['Celular']); ?></p>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label><strong>Estado Actual:</strong></label>
                                    <p>
                                        <?php 
                                        $st = $row['cod_estado'] - 1;
                                        echo htmlspecialchars($row_state[$st][1]);
                                        ?>
                                    </p>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label><strong>Precio:</strong></label>
                                    <p class="text-success font-weight-bold">Q <?php echo htmlspecialchars($row['Precio']); ?></p>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label><strong>Tiempo:</strong></label>
                                    <p><?php echo htmlspecialchars($row['Tiempo']); ?> días</p>
                                </div>
                            </div>
                        </div>
                        
                        <?php if ($row['no_blacklist'] == 1 || $row['no_icloud'] == 1): ?>
                        <div class="alert alert-success">
                            <strong><i class="fas fa-shield-alt"></i> Verificaciones:</strong>
                            <ul class="mb-0 mt-2">
                                <?php if ($row['no_blacklist'] == 1): ?>
                                <li><i class="fas fa-check"></i> NO está en Blacklist</li>
                                <?php endif; ?>
                                <?php if ($row['no_icloud'] == 1): ?>
                                <li><i class="fas fa-check"></i> NO tiene iCloud activo</li>
                                <?php endif; ?>
                            </ul>
                        </div>
                        <?php endif; ?>
                        
                        <div class="form-group">
                            <label><strong>Observaciones:</strong></label>
                            <textarea class="form-control" name="desc" rows="3"><?php echo htmlspecialchars($row['Observaciones']); ?></textarea>
                        </div>
                    </div>
                    
                    <div class="modal-footer justify-content-between">
                        <button type="button" class="btn btn-default" data-dismiss="modal">
                            <i class="fas fa-times"></i> Cerrar
                        </button>
                        <button type="submit" class="btn btn-success">
                            <i class="fas fa-check"></i> Aprobar
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<?php } ?>

<!-- Modal para Reiniciar -->
<?Php if(Tiene_permiso($permisos_user,'reiniciar_lb')){ ?>
<div class="modal fade" id="redo-<?php echo htmlspecialchars($row['serie']); ?>" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header bg-warning">
                <h4 class="modal-title">
                    <i class="fas fa-redo"></i> Reiniciar Consulta
                </h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            
            <div class="modal-body">
                <form action="../../logica/accion" method="POST">
                    <input type="hidden" name="accion" value="2.1">
                    <input type="hidden" name="imei" value="<?php echo urlencode($row['serie']); ?>">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label><strong>IMEI Principal:</strong></label>
                                    <p><?php echo htmlspecialchars($row['serie']); ?></p>
                                </div>
                            </div>
                            <?php if (!empty($row['serie_2'])): ?>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label><strong>IMEI Secundario:</strong></label>
                                    <p><?php echo htmlspecialchars($row['serie_2']); ?></p>
                                </div>
                            </div>
                            <?php endif; ?>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label><strong>Modelo:</strong></label>
                                    <p><?php echo htmlspecialchars($row['modelo']); ?></p>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label><strong>Nombre:</strong></label>
                                    <p><?php echo htmlspecialchars($row['Nombre_Cliente']); ?></p>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label><strong>Celular:</strong></label>
                                    <p>+502 <?php echo htmlspecialchars($row['Celular']); ?></p>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label><strong>Estado Actual:</strong></label>
                                    <p>
                                        <?php
                                        $st = max($row['cod_estado'] - 1, 0);
                                        $estado = isset($row_state[$st]) ? htmlspecialchars($row_state[$st][1]) : 'Desconocido';
                                        echo $estado;
                                        ?>
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="modal-footer justify-content-between">
                        <button type="button" class="btn btn-default" data-dismiss="modal">
                            <i class="fas fa-times"></i> Cerrar
                        </button>
                        <button type="submit" class="btn btn-warning">
                            <i class="fas fa-redo"></i> Reiniciar
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<?php } ?>

<!-- Modal for Starting/Ending Process -->
<?Php if(Tiene_permiso($permisos_user,'inciar_lb') || Tiene_permiso($permisos_user,'finalizar_liberacion')){ ?>
<div class="modal fade" id="<?php echo ($row['cod_estado'] == $page ? 'star-' : 'end-') . htmlspecialchars($row['serie'], ENT_QUOTES, 'UTF-8'); ?>">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            
            <div class="modal-header <?php echo ($row['cod_estado'] == $page ? 'bg-primary' : 'bg-orange'); ?>">
                <h4 class="modal-title text-white">
                    <i class="fas fa-<?php echo ($row['cod_estado'] == $page ? 'play' : 'flag-checkered'); ?>"></i>
                    <?php echo ($row['cod_estado'] == $page ? 'Iniciar Proceso' : 'Finalizar Proceso'); ?>
                </h4>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            
            <div class="modal-body">
                <form action="../../logica/accion?accion=<?php echo (int)$row['cod_estado']; ?>&imei=<?php echo urlencode($row['serie']); ?>" 
                      method="POST">
                    
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label><strong>IMEI Principal:</strong></label>
                                    <p><?php echo htmlspecialchars($row['serie'], ENT_QUOTES, 'UTF-8'); ?></p>
                                </div>
                            </div>
                            <?php if (!empty($row['serie_2'])): ?>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label><strong>IMEI Secundario:</strong></label>
                                    <p><?php echo htmlspecialchars($row['serie_2'], ENT_QUOTES, 'UTF-8'); ?></p>
                                </div>
                            </div>
                            <?php endif; ?>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label><strong>Modelo:</strong></label>
                                    <p><?php echo htmlspecialchars($row['modelo'], ENT_QUOTES, 'UTF-8'); ?></p>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label><strong>Nombre:</strong></label>
                                    <p><?php echo htmlspecialchars($row['Nombre_Cliente'], ENT_QUOTES, 'UTF-8'); ?></p>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label><strong>Celular:</strong></label>
                                    <p>+502 <?php echo htmlspecialchars($row['Celular'], ENT_QUOTES, 'UTF-8'); ?></p>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label><strong>Estado Actual:</strong></label>
                                    <p><?php 
                                        $estadoIndex = (int)$row['cod_estado'] - 1; 
                                        $estado = isset($row_state[$estadoIndex][1]) ? $row_state[$estadoIndex][1] : 'Estado desconocido';
                                        echo htmlspecialchars($estado, ENT_QUOTES, 'UTF-8'); 
                                    ?></p>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label><strong>Precio:</strong></label>
                                    <p class="text-success font-weight-bold">Q <?php echo htmlspecialchars($row['Precio'], ENT_QUOTES, 'UTF-8'); ?></p>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label><strong>Tiempo:</strong></label>
                                    <p><?php echo htmlspecialchars($row['Tiempo'], ENT_QUOTES, 'UTF-8'); ?> días</p>
                                </div>
                            </div>
                        </div>
                        
                        <?php if ($row['no_blacklist'] == 1 || $row['no_icloud'] == 1): ?>
                        <div class="alert alert-info">
                            <strong><i class="fas fa-shield-alt"></i> Verificaciones:</strong>
                            <ul class="mb-0 mt-2">
                                <?php if ($row['no_blacklist'] == 1): ?>
                                <li>NO está en Blacklist</li>
                                <?php endif; ?>
                                <?php if ($row['no_icloud'] == 1): ?>
                                <li>NO tiene iCloud activo</li>
                                <?php endif; ?>
                            </ul>
                        </div>
                        <?php endif; ?>
                        
                        <div class="form-group">
                            <label for="Observaciones"><strong>Observaciones:</strong> <span class="text-danger">*</span></label>
                            <textarea required class="form-control" name="desc" rows="3" 
                                      placeholder="Ingrese observaciones..."><?php echo htmlspecialchars($row['Observaciones'], ENT_QUOTES, 'UTF-8'); ?></textarea>
                        </div>
                        
                    </div>
                    
                    <div class="modal-footer justify-content-between">
                        <?php if ($row['cod_estado'] == $page) { ?>
                            <button type="button" class="btn btn-default" data-dismiss="modal">
                                <i class="fas fa-times"></i> Cerrar
                            </button>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-play"></i> Iniciar
                            </button>
                        <?php } elseif ($row['cod_estado'] == $pageend) { ?>
                            <div class="form-group w-100">
                                <label><strong>Resultado:</strong> <span class="text-danger">*</span></label>
                                <select name="resultado" class="form-control select2bs4" required>
                                    <option value="">Seleccione un resultado...</option>
                                    <option value="5">✅ Finalizada con Éxito</option>
                                    <option value="8">⚠️ Únicamente SemiFabrica</option>
                                    <option value="7">❌ Rechazada Por Reporte</option>
                                    <option value="9">🚫 Rechazada Por Sistema</option>
                                </select>
                            </div>
                            <button type="button" class="btn btn-default" data-dismiss="modal">
                                <i class="fas fa-times"></i> Cerrar
                            </button>
                            <button type="submit" class="btn btn-warning">
                                <i class="fas fa-flag-checkered"></i> Finalizar
                            </button>
                        <?php } ?>
                    </div>
                    
                </form>
            </div>

        </div>
    </div>
</div>
<?php } ?>

<?php endwhile; ?>

<!-- Modal de Perfil de Usuario -->
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
                    <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
                    <div class="text-center mb-3">
                        <img id="profilePic" class="profile-user-img img-fluid img-circle" src="/home/dist/img/user4-128x128.jpg" alt="User profile picture">
                        <input type="file" name="profile_picture" class="form-control-file mt-2" accept="image/*" onchange="previewImage(event)">
                    </div>
                    <div class="form-group">
                        <label for="username">Usuario</label>
                        <input type="text" class="form-control" name="username" id="username" value="Nina Mcintire" required>
                    </div>
                    <div class="form-group">
                        <label for="email">Correo Electrónico</label>
                        <input type="email" class="form-control" name="email" id="email" value="nina@example.com" required>
                    </div>
                    <div class="form-group">
                        <label for="contact_number">Número de Contacto</label>
                        <input type="text" class="form-control" name="contact_number" id="contact_number" value="555-1234" required>
                    </div>
                    <div class="form-group">
                        <label for="birthdate">Fecha de Nacimiento</label>
                        <input type="date" class="form-control" name="birthdate" id="birthdate" value="1990-01-01" required>
                    </div>
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
                    <button type="submit" class="btn btn-primary">Guardar Cambios</button

