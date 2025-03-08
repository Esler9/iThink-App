<!DOCTYPE php>
<?php 
session_start();
$User = $_SESSION["username"];
$cod_user = $_SESSION['cod_user'];


if(!isset($User)){
  header('location:../../login.php');
  exit();
}

include("../../../conexion.php"); 


include("../../logica/ac_permiso.php");

?>
<php lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>iThink | Web </title>
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
</head>
<body class="hold-transition sidebar-mini layout-fixed">

 

 
      <?php include("sidebar.php");?>
  


  <!-- Content Wrapper. Contains page content -->
  <div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
      <div class="container-fluid">
        <div class="row mb-2">
          <div class="col-sm-6">
            <h1>informar Consulta</h1>
          </div>
          <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
              <li class="breadcrumb-item"><a href="../../dashboard.php">Home</a></li>
              <li class="breadcrumb-item active">Informar</li>
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
                <h3 class="card-title">Crear Consulta</h3>
              </div>
              <!-- /.card-header -->
              <div class="card-body">

            
              <form action="../../logica/accion?accion=0" method="POST">
                <div class="card-body">
                <div class="form-group">
                    <label for="c_imei">Imei:</label>
                    <input  type="number" name="c_imei"class="form-control"  minlength="15"  placeholder="Escriba Precio de Liberacion" required>
                  </div>
                  <div class="form-group">
                    <label for="model">Modelo:</label>
                    <input  type="text" name="model"class="form-control" placeholder="Escriba Precio de Liberacion" required>
                  </div>
                  <div class="form-group">
                    <label for="name">Nombre:</label>
                    <input  type="text" name="name"class="form-control" placeholder="Escriba Precio de Liberacion" required>
                  </div>
                  <div class="form-group">
                    <label for="Celular">Celular: </label>
                    <input  type="text" name="celular"class="form-control" placeholder="Escriba Precio de Liberacion" required>
                  </div>
       
                </div>
                <!-- /.card-body -->
                <div class="card-footer">
                  <button type="submit" class="btn-lg btn-primary" data-toggle="modal" data-target="#modal-default">Crear</button>
                </div>
              </form>
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
<script src="../../plugins/jszip/jszip.min.js"></script>
<script src="../../plugins/pdfmake/pdfmake.min.js"></script>
<script src="../../plugins/pdfmake/vfs_fonts.js"></script>
<script src="../../plugins/datatables-buttons/js/buttons.html5.min.js"></script>
<script src="../../plugins/datatables-buttons/js/buttons.print.min.js"></script>
<script src="../../plugins/datatables-buttons/js/buttons.colVis.min.js"></script>
<!-- AdminLTE App -->
<script src="../../dist/js/adminlte.min.js"></script>
<!-- AdminLTE for demo purposes -->

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

        // datos necesario 

        $alert = $_GET['alert'];
        $imei = $_GET['imei'];
        $name = $_GET['name'];
        $model = $_GET['model'];
        $celular = $_GET['celular'];

        if ($alert == 33){?>
         
          $(document).Toasts('create', {
            class: 'bg-danger',
            title: 'Error',
            subtitle: 'Imei:<?php echo $imei; ?>',
            body: 'Es posible que ya haya sido creada la consulta para: <br>Imei:<?php echo $imei; ?>'
          });

      <?php  }elseif($alert == 0){?>

        $(document).Toasts('create', {
            class: 'bg-success',
            title: 'Consulta Exitosa',
            subtitle: 'Imei:<?php echo $imei; ?>',
            body: 'La Siguiente Liberacion ha Sido Creada <br>Imei:<?php echo $imei; ?> <br>Modelo:<?php echo $model; ?><br>Nombre:<?php echo $name; ?><br>Celular:<?php echo $celular; ?>'
          });

     <?php    
      }}
    
    ?>

</script>
</body>

</php>
