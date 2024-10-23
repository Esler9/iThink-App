<!DOCTYPE html>
<?php 
session_start();
$User = $_SESSION["username"];
$cod_user = $_SESSION['cod_user'];

include ('../Setting.php');

if($mantenimiento == true ){
  header('location:../mantenimiento.php');
  exit;}

if(!isset($User)){
  header('location:login.php');
  exit();
}

include("../conexion.php"); 


include("logica/ac_permiso.php");

  


   // almacena las tiendas a las que tiene acceso el usuario
   $cod_user = $_SESSION['cod_user'];
   $sql = "SELECT cod_tienda FROM `asignacion_tienda` where cod_user = $cod_user";
   $consulta = mysqli_query($conn,$sql);
   while ($fila = mysqli_fetch_array($consulta)) {
       $marcas[] = $fila['cod_tienda'];
   }
   if (!empty($marcas)){
   $tiendas = implode(', ', $marcas);
   $_SESSION['tiendas'] = $tiendas;}
   $tiendas = $_SESSION['tiendas'];
 
   // fin del Almacenamiento de tiendas

  
  
?>
 <lang="es">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>iThink | Dashboard </title>
  <link rel="icon" href="/home/dist/img/logo_ithink.png" type="image/x-icon">
  <!-- Google Font: Source Sans Pro -->
  <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">
  <!-- Font Awesome -->
  <link rel="stylesheet" href="plugins/fontawesome-free/css/all.min.css">
  <!-- Ionicons -->
  <link rel="stylesheet" href="https://code.ionicframework.com/ionicons/2.0.1/css/ionicons.min.css">
  <!-- Tempusdominus Bootstrap 4 -->
  <link rel="stylesheet" href="plugins/tempusdominus-bootstrap-4/css/tempusdominus-bootstrap-4.min.css">
  <!-- iCheck -->
  <link rel="stylesheet" href="plugins/icheck-bootstrap/icheck-bootstrap.min.css">
  <!-- JQVMap -->
  <link rel="stylesheet" href="plugins/jqvmap/jqvmap.min.css">
  <!-- Theme style -->
  <link rel="stylesheet" href="dist/css/adminlte.min.css">
  <!-- overlayScrollbars -->
  <link rel="stylesheet" href="plugins/overlayScrollbars/css/OverlayScrollbars.min.css">
  <!-- Daterange picker -->
  <link rel="stylesheet" href="plugins/daterangepicker/daterangepicker.css">
  <!-- summernote -->
  <link rel="stylesheet" href="plugins/summernote/summernote-bs4.min.css">

</head>

<body class="hold-transition sidebar-mini layout-fixed">


<?php include("sidebar.php"); ?>

  <!-- Content Wrapper. Contains page content -->
  <div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <div class="content-header">
      <div class="container-fluid">
        <div class="row mb-2">
          <div class="col-sm-6">
            <h1 class="m-0">Liberaciones</h1>
          </div><!-- /.col -->
          <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
              <li class="breadcrumb-item"><a href="#">Home</a></li>
              <li class="breadcrumb-item active">Liberaciones</li>
            </ol>
          </div><!-- /.col -->
        </div><!-- /.row -->
      </div><!-- /.container-fluid -->
    </div>
    <!-- /.content-header -->

    <!-- Main content -->
    <section class="content">
      <div class="container-fluid">
        <!-- Small boxes (Stat box) -->
        <div class="row">

        <?php if(Tiene_permiso($permisos_user,'ver-consultas')){ ?>
          <div class="col-lg-3 col-6">
            <!-- small box -->
            <div class="small-box bg-info">
              <div class="inner">
                <h3><?php 
                echo $con;?>
                </h3>

                <p>Consultas</p>
              </div>
              <div class="icon">
                <i class="fas fa-solid fa-question"></i>
              </div>
              <a href="pages/tables/consultas.php" class="small-box-footer">Más info <i class="fas fa-arrow-circle-right"></i></a>
            </div>
          </div> <?php }?>
           <!-- ./col -->
            
           <?php if(Tiene_permiso($permisos_user,'ver-pendientes-lb')){ ?> 
           <div class="col-lg-3 col-6">
            <!-- small box -->
            <div class="small-box bg-danger">
              <div class="inner">
              <h3><?php $sql = "SELECT COUNT(*) as consultas FROM liberacion where Cod_estado = 2 and cod_tienda in ($tiendas)"; $consulta = mysqli_query($conn,$sql); $row =  mysqli_fetch_array($consulta);
                echo $row['consultas']; ?>
                </h3>

                <p>Pendientes</p>
              </div>
              <div class="icon">
                <i class="fas fa-solid fa-exclamation"></i>
              </div>
              <a href="pages/tables/pendientes.php" class="small-box-footer">Más info <i class="fas fa-arrow-circle-right"></i></a>
            </div>
          </div><?php }?>
          <!-- ./col -->

          <?php if(Tiene_permiso($permisos_user,'ver-aprobadas-lb')){ ?>
          <div class="col-lg-3 col-6">
            <!-- small box -->
            <div class="small-box bg-success">
              <div class="inner">
              <h3><?php $sql = "SELECT COUNT(*) as consultas FROM liberacion where Cod_estado BETWEEN 3 AND 4 and cod_tienda in ($tiendas)"; $consulta = mysqli_query($conn,$sql); $row =  mysqli_fetch_array($consulta);
                echo $row['consultas']; ?>
                </h3>

                <p>Aprobadas</p>
              </div>
              <div class="icon">
                <i class="fas fa-solid fa-check"></i>
              </div>
              <a href="pages/tables/aprobadas.php" class="small-box-footer">More info <i class="fas fa-arrow-circle-right"></i></a>
            </div>
          </div><?php }?>
          <!-- ./col -->

          <?php if(Tiene_permiso($permisos_user,'ver-finalizadas-lb')){ ?>
          <div class="col-lg-3 col-6">
            <!-- small box -->
            <div class="small-box bg-warning">
              <div class="inner">
              <h3><?php $sql = "SELECT COUNT(*) as consultas FROM liberacion where Cod_estado = 5 and cod_tienda in ($tiendas)"; $consulta = mysqli_query($conn,$sql); $row =  mysqli_fetch_array($consulta);
                echo $row['consultas']; ?>
                </h3>

                <p>Finalizadas</p>
              </div>
              <div class="icon">
                <i class="fas fa-solid fa-handshake"></i>
              </div>
              <a href="pages/tables/finalizadas.php" class="small-box-footer">More info <i class="fas fa-arrow-circle-right"></i></a>
            </div>
          </div><?php }?>

           
        <!-- /.row (main row) -->
              </div><!-- /.container-fluid -->
 <!-- DONUT CHART -->

 <?php if(Tiene_permiso($permisos_user,'ver-graficos-lb')){ ?>
 <div class="card card-danger">
                      <div class="card-header">
                        <h3 class="card-title">Grafico de Liberaciones</h3>

                        <div class="card-tools">
                          <button type="button" class="btn btn-tool" data-card-widget="collapse">
                            <i class="fas fa-minus"></i>
                          </button>
                          <button type="button" class="btn btn-tool" data-card-widget="remove">
                            <i class="fas fa-times"></i>
                          </button>
                        </div>
                      </div>
                      <div class="card-body">
                        <canvas id="donutChart" style="min-height: 250px; height: 250px; max-height: 250px; max-width: 100%;"></canvas>
                      </div>
                      <!-- /.card-body -->
                    </div>
                    <!-- /.card -->
          <!-- ./col -->
        </div> <?php } ?>        <!-- /.row -->
        <!-- Main row -->
      

         

    </section>

    <?php include("logica/modal.php"); ?>
    
    <!-- /.content -->
  </div>
  <!-- /.content-wrapper -->
  <footer class="main-footer">
    <strong>Copyright &copy; 2023 <a href="https://hexa.com">Hexa Systems</a>.</strong>
    All rights reserved.
    <div class="float-right d-none d-sm-inline-block">
      <b>Version</b> 1
    </div>
  </footer>

  <!-- Control Sidebar -->
  <aside class="control-sidebar control-sidebar-dark">
    <!-- Control sidebar content goes here -->
  </aside>
  <!-- /.control-sidebar -->
</div>
<!-- ./wrapper -->

<!-- jQuery -->
<script src="plugins/jquery/jquery.min.js"></script>
<!-- jQuery UI 1.11.4 -->
<script src="plugins/jquery-ui/jquery-ui.min.js"></script>
<!-- Resolve conflict in jQuery UI tooltip with Bootstrap tooltip -->
<script>
  $.widget.bridge('uibutton', $.ui.button)
</script>
<!-- Bootstrap 4 -->
<script src="plugins/bootstrap/js/bootstrap.bundle.min.js"></script>
<!-- ChartJS -->
<script src="plugins/chart.js/Chart.min.js"></script>
<!-- Sparkline -->
<script src="plugins/sparklines/sparkline.js"></script>
<!-- JQVMap -->
<script src="plugins/jqvmap/jquery.vmap.min.js"></script>
<script src="plugins/jqvmap/maps/jquery.vmap.usa.js"></script>
<!-- jQuery Knob Chart -->
<script src="plugins/jquery-knob/jquery.knob.min.js"></script>
<!-- daterangepicker -->
<script src="plugins/moment/moment.min.js"></script>
<script src="plugins/daterangepicker/daterangepicker.js"></script>
<!-- Tempusdominus Bootstrap 4 -->
<script src="plugins/tempusdominus-bootstrap-4/js/tempusdominus-bootstrap-4.min.js"></script>
<!-- Summernote -->
<script src="plugins/summernote/summernote-bs4.min.js"></script>
<!-- overlayScrollbars -->
<script src="plugins/overlayScrollbars/js/jquery.overlayScrollbars.min.js"></script>
<!-- AdminLTE App -->
<script src="dist/js/adminlte.js"></script>

<!-- AdminLTE dashboard demo (This is only for demo purposes) -->
<script src="dist/js/pages/dashboard.js"></script>


<script>
    //-------------
    //- DONUT CHART -
    //-------------OBTENER DATO DE LA BASE DATOS
    <?php $sql ="SELECT cod_estado,COUNT(cod_estado) as Cantidad FROM liberacion where  cod_tienda in ($tiendas) GROUP BY cod_estado;";


    $valores = mysqli_query($conn,$sql);
    $data =  mysqli_fetch_array($valores);

    $con = 0;
    if ($data['cod_estado'][0]== 1 ){
      $con = $data['Cantidad'][0];
    }
    $pen = 0;
    if ($data['cod_estado'][0]== 2 ){
      $pen = $data['Cantidad'][0];
    }
    $apro = 0;
    if ($data['cod_estado'][0]== 3 ){
      $apro = $data['Cantidad'][0];
    }
    $proc = 0;
    if ($data['cod_estado'][0]== 4 ){
      $proc = $data['Cantidad'][0];
    }
    $fin = 0;
    if ($data['cod_estado'][0]== 5){
      $fin = $data['Cantidad'][0];
    }
    $recha = 0;
    if ($data['cod_estado'][0]== 6 ){
      $recha = $data['Cantidad'][0];
    }

    while ($data =  mysqli_fetch_array($valores)){

      
      
      if ($data['cod_estado'] == 1 ){
 
        $con = $data['Cantidad'][0];
          

      }
      if($data['cod_estado'] == 2){
  
          $pen = $data['Cantidad'];
     
      }
      if($data['cod_estado'] == 3){
   
          $apro = $data['Cantidad'];
     
      }
      if($data['cod_estado'] == 4){
      
          $proc = $data['Cantidad'];
      
      }
      if($data['cod_estado'] == 5){

         $fin = $data['Cantidad'];
     
      }
      if($data['cod_estado'] == 6){
      
          $recha = $data['Cantidad'];
        
      }
      if($data['cod_estado'] == 1){
  
        $con = $data['Cantidad'];
      }
    }


    ?>
    // Get context with jQuery - using jQuery's .get() method.
    var donutChartCanvas = $('#donutChart').get(0).getContext('2d')
    var donutData        = {
      labels: [
          'Consultas',
          'Pendientes',
          'Aprobadas',
          'En Proceso',
          'Finalizadas',
          'Rechazadas',
      ],
      datasets: [
        {
          data: ['<?php echo $con;?>','<?php echo $pen;?>','<?php echo $apro;?>','<?php echo $proc;?>','<?php echo $fin;?>','<?php echo $recha;?>'],
          
          backgroundColor : ['#17a2b8', '#dc3545', '#28a745', '#8a8d94', '#ffc107', '#b1202e'],
        }
      ]
    }
    var donutOptions     = {
      maintainAspectRatio : false,
      responsive : true,
    }
    //Create pie or douhnut chart
    // You can switch between pie and douhnut using the method below.
    new Chart(donutChartCanvas, {
      type: 'doughnut',
      data: donutData,
      options: donutOptions
    })




</script>

</body>

  </php>