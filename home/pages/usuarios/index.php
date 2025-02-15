<?php 
session_start(); 
include "../../conexion.php"; // Asegúrate de que la ruta sea correcta
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Lista de Usuarios</title>
  <!-- Estilos de AdminLTE y FontAwesome -->
  <link rel="stylesheet" href="/home/dist/css/adminlte.min.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
  <link rel="stylesheet" href="/home/plugins/bootstrap/css/bootstrap.min.css">
</head>
<body class="hold-transition sidebar-mini">
<div class="wrapper">
  <!-- Se incluye el Sidebar de raíz -->
  <?php include("../sidebar.php"); ?>
  
  <!-- Content Wrapper. Contenido de la página -->
  <div class="content-wrapper">
    <!-- Content Header (Encabezado de la Página) -->
    <section class="content-header">
      <div class="container-fluid">
        <div class="row mb-2">
          <div class="col-sm-6">
            <h1>Lista de Usuarios</h1>
          </div>
          <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
              <li class="breadcrumb-item"><a href="/home/dashboard.php">Home</a></li>
              <li class="breadcrumb-item active">Usuarios</li>
            </ol>
          </div>
        </div>
      </div>
    </section>

    <!-- Main content -->
    <section class="content">
      <div class="container-fluid">
        <div class="card">
          <div class="card-header">
            <h3 class="card-title">Usuarios</h3>
            <div class="card-tools">
              <a href="nuevo.php" class="btn btn-success btn-sm">
                <i class="fas fa-user-plus"></i> Nuevo Usuario
              </a>
            </div>
          </div>
          <!-- /.card-header -->
          <div class="card-body table-responsive p-0">
            <table class="table table-hover">
              <thead>
                <tr>
                  <th>ID</th>
                  <th>Nombre</th>
                  <th>Email</th>
                  <th class="text-right">Acciones</th>
                </tr>
              </thead>
              <tbody>
                <?php 
                $query = "SELECT * FROM usuarios ORDER BY id ASC";
                $result = mysqli_query($conn, $query);
                if($result){
                    while($row = mysqli_fetch_assoc($result)) {
                      echo '<tr>';
                      echo '<td>' . $row['id'] . '</td>';
                      echo '<td>' . htmlspecialchars($row['nombre']) . '</td>';
                      echo '<td>' . htmlspecialchars($row['email']) . '</td>';
                      echo '<td class="text-right">';
                      echo '<a href="editar.php?id=' . $row['id'] . '" class="btn btn-info btn-sm"><i class="fas fa-edit"></i> Editar</a> ';
                      echo '<a href="eliminar.php?id=' . $row['id'] . '" class="btn btn-danger btn-sm" onclick="return confirm(\'¿Desea eliminar este usuario?\')"><i class="fas fa-trash-alt"></i> Eliminar</a>';
                      echo '</td>';
                      echo '</tr>';
                    }
                } else {
                    echo '<tr><td colspan="4">No se encontraron usuarios.</td></tr>';
                }
                ?>
              </tbody>
            </table>
          </div>
          <!-- /.card-body -->
        </div>
        <!-- /.card -->
      </div><!-- /.container-fluid -->
    </section>
    <!-- /.content -->
  </div>
  <!-- /.content-wrapper -->

  <!-- Opcional: Footer -->
  <?php include("../footer.php"); ?>
</div>
<!-- ./wrapper -->

<!-- Scripts -->
<script src="/home/plugins/jquery/jquery.min.js"></script>
<script src="/home/plugins/bootstrap/js/bootstrap.bundle.min.js"></script>
<script src="/home/dist/js/adminlte.min.js"></script>
</body>
</html>