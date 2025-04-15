<?php
session_start();

// Verificar que las variables de sesión estén configuradas
if (!isset($_SESSION["username"]) || !isset($_SESSION['cod_user'])) {
    header('Location: ../../login.php');
    exit();
}

$User = $_SESSION["username"];
$cod_user = $_SESSION['cod_user'];

// Incluir archivos de conexión y lógicas de permisos
include("../../../conexion.php"); 
include("../../logica/ac_permiso.php");

// Validar permiso de acceso a consultas de liberaciones
if (!Tiene_permiso($permisos_user, 'ver-consultas')) {
    echo "<script>
            alert('No tienes acceso a esta página.');
            window.location.href = '../../dashboard.php';
          </script>";
    exit();
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>iThink | Consultas</title>
  <link rel="icon" href="/home/dist/img/logo_ithink.png" type="image/x-icon">
  <!-- Google Font: Source Sans Pro -->
  <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">
  <!-- Font Awesome -->
  <link rel="stylesheet" href="../../plugins/fontawesome-free/css/all.min.css">
  <!-- DataTables -->
  <link rel="stylesheet" href="../../plugins/datatables-bs4/css/dataTables.bootstrap4.min.css">
  <link rel="stylesheet" href="../../plugins/datatables-responsive/css/responsive.bootstrap4.min.css">
  <link rel="stylesheet" href="../../plugins/datatables-buttons/css/buttons.bootstrap4.min.css">
  <!-- Theme style -->
  <link rel="stylesheet" href="../../dist/css/adminlte.min.css">
  <link rel="stylesheet" href="../../dist/css/app.css">
</head>
<body class="hold-transition sidebar-mini layout-fixed">
  <div class="wrapper">
    
    <!-- Sidebar -->
    <?php include("sidebar.php"); ?>
    
    <!-- Content Wrapper. Contains page content -->
    <div class="content-wrapper">
      <!-- Content Header (Page header) -->
      <section class="content-header">
        <div class="container-fluid">
          <div class="row mb-2">
            <div class="col-sm-6">
              <h1>Consultas de Liberaciones</h1>
            </div>
            <div class="col-sm-6">
              <ol class="breadcrumb float-sm-right">
                <li class="breadcrumb-item"><a href="../../dashboard.php">Home</a></li>
                <li class="breadcrumb-item active">Consultas de Liberaciones</li>
              </ol>
            </div>
          </div>
        </div>
      </section>
  
      <!-- Main content -->
      <section class="content">
        <div class="container-fluid">
          <div class="row">
            <div class="col-12">
              <div class="card">
                <div class="card-header">
                  <h3 class="card-title">Tabla de Consultas</h3>
                </div>
                <!-- /.card-header -->
                <div class="card-body">
                  <table id="example1" class="table table-bordered table-striped">
                    <thead>
                      <tr>
                        <th>Imei</th>
                        <th>Modelo</th>
                        <th>Nombre</th>
                        <th>Celular</th>
                        <th>Tienda</th>
                        <th>Update</th>
                        <th>Estado</th>
                        <th>Acción</th>
                      </tr>
                    </thead>
                    <tbody>
                      <?php 
                        $sql = "SELECT liberacion.*, tienda.*
                                FROM liberacion
                                INNER JOIN tienda ON liberacion.cod_tienda = tienda.cod_tienda 
                                WHERE liberacion.cod_estado = 1 
                                AND liberacion.cod_tienda IN ($tiendas) 
                                ORDER BY liberacion.date_update ASC";
                        $result = mysqli_query($conn, $sql);

                        $sql2 = "SELECT * FROM Estado";
                        $resulta_state = mysqli_query($conn, $sql2); 
                        $row_state = mysqli_fetch_all($resulta_state);
                        while ($row = mysqli_fetch_array($result)) { 
                      ?>
                      <tr>
                        <td><?php echo $row["serie"]; ?></td>
                        <td><?php echo $row["modelo"]; ?></td>
                        <td><?php echo $row["Nombre_Cliente"]; ?></td>
                        <td><?php echo $row["Celular"]; ?></td>
                        <td><?php echo $row["nombre"]; ?></td>
                        <td><?php echo $row["date_update"]; ?></td>
                        <td>
                          <?php 
                            $st = $row["cod_estado"] - 1;
                            $estado = $row_state[$st][1];
                            echo $estado;
                          ?>
                        </td>
                        <td>
                          <button type="button" class="btn btn-app <?php echo $user_state;?>" data-toggle="modal" data-target="#ver-<?php echo $row["serie"];?>">
                            <i class="fas fa-eye" title="Ver"></i>
                          </button>
                          <button type="button" class="btn bg-primary btn-app <?php echo $user_state;?>"
                                  data-toggle="modal" data-target="#info-<?php echo $row["serie"];?>"
                                  <?php if (!Tiene_permiso($permisos_user, 'informar_consulta')) echo "disabled"; ?>>
                            <i class="fas fa-exclamation" title="Informar"></i>
                          </button>
                          <button type="button" class="btn bg-danger btn-app <?php echo $user_state;?>"
                                  data-toggle="modal" data-target="#rechazar-<?php echo $row["serie"];?>"
                                  <?php if (!Tiene_permiso($permisos_user, 'rechazar_lb')) echo "disabled"; ?>>
                            <i class="fas fa-times" title="Rechazar"></i>
                          </button>
                        </td>
                      </tr>
                      <?php } ?>
                    </tbody>
                    <tfoot>
                      <tr>
                        <th>Imei</th>
                        <th>Modelo</th>
                        <th>Nombre</th>
                        <th>Celular</th>
                        <th>Tienda</th>
                        <th>Update</th>
                        <th>Estado</th>
                        <th>Acción</th>
                      </tr>
                    </tfoot>
                  </table>
                </div>
                <!-- /.card-body -->
              </div>
              <!-- /.card -->
            </div>
            <!-- /.col -->
          </div>
          <!-- /.row -->
        </div>
        <!-- /.container-fluid -->
        <?php include("../../logica/modal_liberacion.php"); ?>
      </section>
      <!-- /.content -->
    </div>
    <!-- /.content-wrapper -->
  
    <footer class="main-footer">
      <div class="float-right d-none d-sm-block">
        <b>Version</b> 1
      </div>
      <strong>&copy; 2023 <a href="https://hexasystems.com">Hexa Systems</a>.</strong> All rights reserved.
    </footer>
  
    <!-- Control Sidebar -->
    <aside class="control-sidebar control-sidebar-dark">
      <!-- Control sidebar content goes here -->
    </aside>
  </div>
  <!-- ./wrapper -->
  
  <!-- jQuery -->
  <script src="../../plugins/jquery/jquery.min.js"></script>
  <!-- Bootstrap 4 -->
  <script src="../../plugins/bootstrap/js/bootstrap.bundle.min.js"></script>
  <!-- DataTables & Plugins -->
  <script src="../../plugins/datatables/jquery.dataTables.min.js"></script>
  <script src="../../plugins/datatables-bs4/js/dataTables.bootstrap4.min.js"></script>
  <script src="../../plugins/datatables-responsive/js/dataTables.responsive.min.js"></script>
  <script src="../../plugins/datatables-responsive/js/responsive.bootstrap4.min.js"></script>
  <script src="../../plugins/datatables-buttons/js/dataTables.buttons.min.js"></script>
  <script src="../../plugins/datatables-buttons/js/buttons.bootstrap4.min.js"></script>
  <!-- jQuery Validation -->
  <script src="../../plugins/jquery-validation/jquery.validate.min.js"></script>
  <script src="../../plugins/jquery-validation/additional-methods.min.js"></script>
  <!-- JSZip and PDFMake -->
  <script src="../../plugins/jszip/jszip.min.js"></script>
  <script src="../../plugins/pdfmake/pdfmake.min.js"></script>
  <script src="../../plugins/pdfmake/vfs_fonts.js"></script>
  <script src="../../plugins/datatables-buttons/js/buttons.html5.min.js"></script>
  <script src="../../plugins/datatables-buttons/js/buttons.print.min.js"></script>
  <script src="../../plugins/datatables-buttons/js/buttons.colVis.min.js"></script>
  <!-- AdminLTE App -->
  <script src="../../dist/js/adminlte.min.js"></script>
  <!-- Page specific script -->
  <script>
    $(function () {
      $("#example1").DataTable({
        "responsive": true,
        "lengthChange": false,
        "autoWidth": false,
        "buttons": ["copy", "csv", "excel", "pdf", "print", "colvis"]
      }).buttons().container().appendTo('#example1_wrapper .col-md-6:eq(0)');
    });
  
    <?php if (isset($_GET['alert'])) {
          $alert = $_GET['alert'];
          $imei  = $_GET['imei'];
          if ($alert == 1) { ?>
    $(document).Toasts('create', {
      class: 'bg-info',
      title: 'Consulta Informada',
      subtitle: '<?php echo $imei; ?>',
      body: "La consulta ha sido trasladada a Pendientes con Éxito <br>Imei: <?php echo $imei; ?><br><br> <a href='pendientes.php' class='btn btn-default'>Ir a Pendientes</a>"
    });
    <?php } elseif($alert == 5) { ?>
    $(document).Toasts('create', {
      class: 'bg-danger',
      title: 'Liberación Rechazada',
      subtitle: '<?php echo $imei; ?>',
      body: "La liberación ha sido rechazada <br>Imei: <?php echo $imei; ?><br><br> <a href='rechazadas.php' class='btn btn-default'>Ir a Rechazadas</a>"
    });
    <?php } } ?>
  </script>
  
  <?php 
    $sql = "SELECT * FROM liberacion WHERE Cod_estado = 1 AND cod_tienda IN ($tiendas)";
    $result = mysqli_query($conn, $sql);
    while ($row = mysqli_fetch_array($result)) { ?>
  <script>
    $(function () {
      $.validator.setDefaults({});
      $('#inform-<?php echo $row['serie'];?>').validate({
        rules: {
          precio: { required: true },
          tiempo: { required: true },
          desc: { required: true }
        },
        messages: {
          precio: { required: "Ingrese el Precio Establecido" },
          tiempo: { required: "Ingrese el Tiempo Requerido" },
          desc: "Ingrese Observaciones"
        },
        errorElement: 'span',
        errorPlacement: function (error, element) {
          error.addClass('invalid-feedback');
          element.closest('.form-group').append(error);
        },
        highlight: function (element) {
          $(element).addClass('is-invalid');
        },
        unhighlight: function (element) {
          $(element).removeClass('is-invalid');
        }
      });
    });
  </script>
  <?php } ?>
</body>
</html>
