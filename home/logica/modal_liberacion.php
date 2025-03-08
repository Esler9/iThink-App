<?php
if($user_state == "active"){ 
$result = mysqli_query($conn, $sql);
while ($row = mysqli_fetch_array($result)) {?>

<div class="modal fade" id="ver-<?php echo $row["serie"];?>">

      <div class="modal-dialog">
        <div class="modal-content">
          <div class="modal-header">
            <h4 class="modal-title">Información</h4>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
              <span aria-hidden="true">&times;</span>
            </button>
          </div>
          <div class="modal-body">

           <form action="../../logica/accion?accion=8&imei=<?php echo $row['serie'];?>" id="ver-<?php echo $row['serie'];?>" method="POST">
               <div class="card-body">
                        <div class="form-group">
                            <label for="name">Imei:</label>
                            <p><?php echo $row['serie'];?> </p>
                          </div>
                          
                          <div class="form-group">
                            <label for="name">Modelo:</label>
                            <p><?php echo $row['modelo'];?> </p>
                          </div>
                          <div class="form-group">
                            <label for="name">Nombre:</label>
                            <p><?php echo $row['Nombre_Cliente'];?> </p>
                          </div>
                          <div class="form-group">
                            <label for="Celular">Celular: </label>
                            <p><?php echo $row['Celular'];?> </p>
                          </div>
                          <div class="form-group">
                            <label for="tienda">Tienda:</label>
                            <p><?php echo $row['nombre'];?> </p>
                          </div>
                          <div class="form-group">
                            <label for="state">Estado Actual: </label>
                                <p><?php 
                          $st = $row["cod_estado"];
                          $st = $st -1;
                          $estado = $row_state[$st][1];
                            echo $estado;
                          ?> </p>
                          </div>
                          <div class="form-group">
                            <label for="price">Precio:</label>
                            <p><?php echo $row['Precio'];?> </p>
                          </div>
                          <div class="form-group">
                            <label for="time">Tiempo:</label>
                            <p><?php echo $row['Tiempo'];?> </p>
                          </div>
                          <div class="form-group">
                            <label for="date">Fecha Creación:</label>
                            <p><?php echo $row['date'];?> </p>
                          </div>
                          <div class="form-group">
                            <label for="date">Fecha Ultimo Cambio:</label>
                            <p><?php echo $row['date_update'];?> </p>
                          </div>
                          <div class="form-group">
                            <label for="desc">Observaciones: </label>
                            <p><?php echo $row['Observaciones'];?> </p>

                          </div>
                    
                  </div>



                    </div>
                    <div class="modal-footer justify-content-between">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
                    </div>
                </div>
          </form>  
        <!-- /.modal-content -->
      </div>
      <!-- /.modal-dialog -->
    </div>                          
    <!-- /.modal --> 
                  
    <div class="modal fade" id="info-<?php echo $row["serie"];?>">
      <div class="modal-dialog">
        <div class="modal-content">
          <div class="modal-header">
            <h4 class="modal-title">Imformar</h4>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
              <span aria-hidden="true">&times;</span>
            </button>
          </div>
          <div class="modal-body">

              <form action="../../logica/accion?accion=1&imei=<?php echo $row['serie'];?>"  id="inform-<?php echo $row['serie'];?>" method="POST">
                <div class="card-body">
                  <div class="form-group">
                      <label for="name">Imei:</label>
                      <p><?php echo $row['serie'];?> </p>
                    </div>
                    <div class="form-group">
                      <label for="name">Modelo:</label>
                      <p><?php echo $row['modelo'];?> </p>
                    </div>
                    <div class="form-group">
                      <label for="name">Nombre:</label>
                      <p><?php echo $row['Nombre_Cliente'];?> </p>
                    </div>
                    <div class="form-group">
                      <label for="Celular">Celular: </label>
                      <p><?php echo $row['Celular'];?> </p>
                    </div>
                    <div class="form-group">
                      <label for="Celular">Estado Actual: </label>
                      <p><?php if ($row['Estado'] = 1){
                          echo "Consulta";
                      };?> </p>
                    </div>
                    <div class="form-group">
                      <label for="Celular">Precio: </label>
                      <input  type="text" name="precio" class="form-control" placeholder="Escriba Precio de Liberacion" >
                    </div>
                    <div class="form-group">
                      <label for="Celular">Tiempo: </label>
                      <input type="text" name="tiempo" class="form-control" placeholder="Escriba Tiempo Estimado" >
                    </div>
                    <div class="form-group">
                      <label for="Celular">Observaciones: </label>
                      <textarea   class="form-control" name="desc" rows="3" placeholder="Enter ..."></textarea>

                    </div>
              
                    
                  </div>



                  </div>
                  <div class="modal-footer justify-content-between">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
                    <button type="submit" class="btn btn-primary">Informar</button>
                  </div>
                </div>
              </form>  
        <!-- /.modal-content -->
      </div>
      <!-- /.modal-dialog -->
    </div>
     
    
    <!-- /.modal --> 
    <div class="modal fade" id="rechazar-<?php echo $row["serie"];?>">
      <div class="modal-dialog">
        <div class="modal-content">
          <div class="modal-header">
            <h4 class="modal-title">Rechazar</h4>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
              <span aria-hidden="true">&times;</span>
            </button>
          </div>
          <div class="modal-body">

           <form action="../../logica/accion?accion=5.1&imei=<?php echo $row['serie'];?>" id="rechazar-<?php echo $row['serie'];?>" method="POST">
               <div class="card-body">
                        <div class="form-group">
                            <label for="name">Imei:</label>
                            <p><?php echo $row['serie'];?> </p>
                          </div>
                          <div class="form-group">
                            <label for="name">Modelo:</label>
                            <p><?php echo $row['modelo'];?> </p>
                          </div>
                          <div class="form-group">
                            <label for="name">Nombre:</label>
                            <p><?php echo $row['Nombre_Cliente'];?> </p>
                          </div>
                          <div class="form-group">
                            <label for="Celular">Celular: </label>
                            <p><?php echo $row['Celular'];?> </p>
                          </div>
                          <div class="form-group">
                            <label for="Celular">Estado Actual: </label>
                            <p><?php if ($row['Estado'] = 1){
                                echo "Consulta";
                            };?> </p>
                          </div>
                      
                          <div class="form-group">
                            <label for="desc">Observaciones: </label>
                            <textarea   class="form-control" name="desc" rows="3" placeholder="Enter ..."></textarea>

                          </div>
                    
                  </div>



                    </div>
                    <div class="modal-footer justify-content-between">
                      <button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
                      <button type="submit" class="btn btn-danger">Rechazar</button>
                    </div>
                </div>
          </form>  
        <!-- /.modal-content -->
      </div>
      <!-- /.modal-dialog -->
    </div>
     
   
    <!-- /.modal --> 
    <?php }}?>
