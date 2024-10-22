<!DOCTYPE php>
<?php 
session_start();

// Verificar que las variables de sesión estén configuradas
if (!isset($_SESSION["username"]) || !isset($_SESSION['cod_user'])) {
    header('Location: ../../login.php');
    exit();
}

$User = $_SESSION["username"];
$cod_user = $_SESSION['cod_user'];


// Incluir archivos de conexión y acceso
include("../../../conexion.php"); 

include("../../logica/ac_permiso.php");

// Validar permiso
if (Tiene_permiso($permisos_user,'ver-consultas')) {
   
} else {
  echo "<script>
        alert('No tienes acceso a esta página.');
        window.location.href = '../../dashboard.php'; // Redirigir a la página deseada
        </script>";
  exit();
}
?>

<php lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>iThink | Web </title>

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


 

      <?php 
     
      include("sidebar.php");?>
    
  </aside>

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
      </div><!-- /.container-fluid -->
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

                    <?php $sql = "SELECT liberacion.*, tienda.*
                                  FROM liberacion
                                  INNER JOIN tienda ON liberacion.cod_tienda = tienda.cod_tienda 
                                  where liberacion.cod_estado = 1 and liberacion.cod_tienda in ($tiendas) 
                                  order by liberacion.date_update ASC" ;

                    $result = mysqli_query($conn, $sql);

                    $sql2 = "SELECT * from Estado";

                    $resulta_state =mysqli_query($conn, $sql2); 
                    $row_state = mysqli_fetch_all($resulta_state);

                    while ($row = mysqli_fetch_array($result)) {?>

                       <tr>
                            <td><?php echo $row["serie"];?> </td>
                            <td><?php echo $row["modelo"];?> </td>
                            <td><?php echo $row["Nombre_Cliente"];?> </td>
                            <td><?php echo $row["Celular"];?> </td>
                            <td><?php echo $row["nombre"];?> </td>
                            <td><?php echo $row["date_update"];?> </td>
                            <td><?php
                      
                      $st = $row["cod_estado"];
                      $st = $st -1;
                      $estado = $row_state[$st][1];
                        echo $estado;
                                         ?> </td>
                  
                      <td>
                        <button type="button" class="btn btn-app <?php echo $user_state;?>" data-toggle="modal" data-target="#ver-<?php echo $row["serie"];?>"><i class="fas fa-solid fa-eye" title="Ver"></i></button>                    
                        <button type="button" 
                                class="btn bg-primary btn-app <?php echo $user_state;?>" 
                                data-toggle="modal" 
                                data-target="#info-<?php echo $row["serie"];?>"
                                <?php if(!Tiene_permiso($permisos_user,'informar_consulta')){echo "disabled";} ?>>
                            <i class="fas fa-solid fa-exclamation" title="Informar"></i>
                        </button>
                        <button type="button" 
                                class="btn bg-danger btn-app <?php echo $user_state;?>" 
                                data-toggle="modal" data-target="#rechazar-<?php echo $row["serie"];?>"
                                <?php if(!Tiene_permiso($permisos_user,'rechazar_lb')){echo "disabled";} ?>>
                                <i class="fas fa-solid fa-times  " title="Rechazar"></i> </button>
                      </td>
                    </tr>
                     <?php }?>

                 
                   
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
                      
      <?php 
      include("../../logica/modal_liberacion.php");?>
            
         </section> 
    <!-- /.content -->


  </div>
  <!-- /.content-wrapper -->
  <footer class="main-footer">
    <div class="float-right d-none d-sm-block">
      <b>Version</b> 1
    </div>
    <strong>Copyright &copy; 2023 <a href="https://hexasystems.com">Hexa Systems</a>.</strong> All rights reserved.
  </footer>

  <!-- Control Sidebar -->
  <aside class="control-sidebar control-sidebar-dark">
    <!-- Control sidebar content goes here --> 
  </aside>
  <!-- /.control-sidebar -->
</div>
<!-- ./wrapper -->

<!-- jQuery -->
<script src="../../plugins/jquery/jquery.min.js"></script>
<!-- Bootstrap 4 -->
<script src="../../plugins/bootstrap/js/bootstrap.bundle.min.js"></script>
<!-- DataTables  & Plugins -->
<script src="../../plugins/datatables/jquery.dataTables.min.js"></script>
<script src="../../plugins/datatables-bs4/js/dataTables.bootstrap4.min.js"></script>
<script src="../../plugins/datatables-responsive/js/dataTables.responsive.min.js"></script>
<script src="../../plugins/datatables-responsive/js/responsive.bootstrap4.min.js"></script>
<script src="../../plugins/datatables-buttons/js/dataTables.buttons.min.js"></script>
<script src="../../plugins/datatables-buttons/js/buttons.bootstrap4.min.js"></script>
<!-- Validacion -->
<script src="../../plugins/jquery-validation/jquery.validate.min.js"></script>
<script src="../../plugins/jquery-validation/additional-methods.min.js"></script>

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
      "responsive": true, "lengthChange": false, "autoWidth": false,
      "buttons": ["copy", "csv", "excel", "pdf", "print", "colvis"]
    }).buttons().container().appendTo('#example1_wrapper .col-md-6:eq(0)');
    $('#example2').DataTable({
      "paging": true,
      "lengthChange": false,
      "searching": false,
      "ordering": true,
      "info": true,
      "autoWidth": false,
      "responsive": true,
    });
  });

  <?php
      
      if (isset($_GET['alert'])){
        $alert = $_GET['alert'];
        $imei = $_GET['imei'];
        if ($alert == 1){?>
         
          $(document).Toasts('create', {
            class: 'bg-info',
            title: 'Consulta Informada',
            subtitle: '<?php echo $imei; ?>',
            body: "La consulta ha sido trasladada a Pendientes con Éxito <br>Imei:<?php echo $imei; ?><br><br> <a href='pendientes.php' class='btn btn-default' >Ir a Pendientes</a> "
          });
    <?php  }elseif($alert ==5){?>
           $(document).Toasts('create', {
            class: 'bg-danger',
            title: 'Liberacion Rechazada',
            subtitle: '<?php echo $imei; ?>',
            body: "La Liberacion ha sido Rechazada <br>Imei:<?php echo $imei; ?><br><br> <a href='rechazadas.php' class='btn btn-default' >Ir a rechazadas</a>"
          });
     <?php    
      }}
    
    ?>
</script>

<?php
$sql = "SELECT * from liberacion where Cod_estado = 1 and cod_tienda in ($tiendas)";
$result = mysqli_query($conn, $sql);

while ($row = mysqli_fetch_array($result)) {?>

<script>
$(function () {
  $.validator.setDefaults({
    
  });
  $('#inform-<?php echo $row['serie'];?>').validate({
    rules: {
      precio: {
        required: true,
      
      },
      tiempo: {
        required: true,
      
      },
      desc: {
        required: true
      },
    },
    messages: {
      precio: {
        required: "Ingrese el Precio Establecido",
        
      },
      tiempo: {
        required: "Ingrese el Tiempo Requerido",
    
      },
      desc: "Ingrese Observaciones"
    },
    errorElement: 'span',
    errorPlacement: function (error, element) {
      error.addClass('invalid-feedback');
      element.closest('.form-group').append(error);
    },
    highlight: function (element, errorClass, validClass) {
      $(element).addClass('is-invalid');
    },
    unhighlight: function (element, errorClass, validClass) {
      $(element).removeClass('is-invalid');
    }

  });
  
});

</script>

<?php }?>
</body> 
</php>
