<?php 


   
    
        function Traer_grupo_usuario($conn) {
            
    
            // Verificar conexión
            if (!$conn) {
                die("Conexión fallida: " . mysqli_connect_error());
            }
    
            // Consulta SQL
            $sql_grupos = "SELECT * FROM grupo_user";
            $result = mysqli_query($conn, $sql_grupos);
    
            // Verificar si hay resultados
            if (mysqli_num_rows($result) > 0) {
                // Crear un array para almacenar los resultados
                $grupos = array();
                while ($row = mysqli_fetch_assoc($result)) {
                    $grupos[] = $row;
                }
             
                // Retornar los resultados
                return $grupos;
            } else {
                
                return array();  // Retornar un array vacío si no hay resultados
            }
        }
    
        function traer_permisos($conn, $id_grupo) {

             // Verificar conexión
             if (!$conn) {
                die("Conexión fallida: " . mysqli_connect_error());
            }
            
            // Construir la consulta SQL
            $sql_permisos = "
                                SELECT 
                            Permiso.*, 
                            Asignacion_permiso.*, 
                            grupo_permiso.nombre_grupo_p 
                        FROM 
                            Permiso
                        LEFT JOIN 
                            Asignacion_permiso 
                            ON Asignacion_permiso.id_permiso = Permiso.codigo 
                            AND Asignacion_permiso.id_group = $id_grupo
                        LEFT JOIN 
                            grupo_permiso 
                            ON grupo_permiso.codigo = Permiso.group_permiso
                        WHERE 
                            Asignacion_permiso.id_group = $id_grupo 
                            OR Asignacion_permiso.id_group IS NULL;
                        ;
            ";
        
            // Ejecutar la consulta
            $result = mysqli_query($conn, $sql_permisos);
        
            // Verificar si la consulta tuvo resultados
            if ($result) {
                // Crear un array para almacenar los permisos
                $permisos = array();
        
                // Recorrer los resultados y agregar cada fila al array
                while ($row = mysqli_fetch_assoc($result)) {
                    $permisos[] = $row;
                }
        
                // Retornar el array de permisos
                return $permisos;
            } else {
                // Manejo de errores de la consulta
                error_log("Error en la consulta SQL: " . mysqli_error($conn));
                
                // Retornar un array vacío si hay un error
                return "Faltal Error";
            }
        }
        

  
    


        
    
    
       
    


?>
