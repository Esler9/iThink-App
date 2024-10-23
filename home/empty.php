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

include("../../../conexion.php"); 

include("../../logica/ac_permiso.php");




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
  <link rel="stylesheet" href="/home/plugins/fontawesome-free/css/all.min.css">
  <!-- Ionicons -->
  <link rel="stylesheet" href="https://code.ionicframework.com/ionicons/2.0.1/css/ionicons.min.css">
  <!-- Tempusdominus Bootstrap 4 -->
  <link rel="stylesheet" href="/home/plugins/tempusdominus-bootstrap-4/css/tempusdominus-bootstrap-4.min.css">
  <!-- iCheck -->
  <link rel="stylesheet" href="/home/plugins/icheck-bootstrap/icheck-bootstrap.min.css">
  <!-- JQVMap -->
  <link rel="stylesheet" href="/home/plugins/jqvmap/jqvmap.min.css">
  <!-- Theme style -->
  <link rel="stylesheet" href="/home/dist/css/adminlte.min.css">
  <!-- overlayScrollbars -->
  <link rel="stylesheet" href="/home/plugins/overlayScrollbars/css/OverlayScrollbars.min.css">
  <!-- Daterange picker -->
  <link rel="stylesheet" href="/home/plugins/daterangepicker/daterangepicker.css">
  <!-- summernote -->
  <link rel="stylesheet" href="/home/plugins/summernote/summernote-bs4.min.css">

</head>

<body class="hold-transition sidebar-mini layout-fixed">


<?php include("../../sidebar.php"); ?>

  <!-- Content Wrapper. Contains page content -->
  <div class="content-wrapper">

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
<script src="/home/plugins/jquery/jquery.min.js"></script>
<!-- jQuery UI 1.11.4 -->
<script src="/home/plugins/jquery-ui/jquery-ui.min.js"></script>
<!-- Resolve conflict in jQuery UI tooltip with Bootstrap tooltip -->

<!-- Bootstrap 4 -->
<script src="/home/plugins/bootstrap/js/bootstrap.bundle.min.js"></script>
<!-- ChartJS -->
<script src="/home/plugins/chart.js/Chart.min.js"></script>
<!-- Sparkline -->
<script src="/home/plugins/sparklines/sparkline.js"></script>
<!-- JQVMap -->
<script src="/home/plugins/jqvmap/jquery.vmap.min.js"></script>
<script src="/home/plugins/jqvmap/maps/jquery.vmap.usa.js"></script>
<!-- jQuery Knob Chart -->
<script src="/home/plugins/jquery-knob/jquery.knob.min.js"></script>
<!-- daterangepicker -->
<script src="/home/plugins/moment/moment.min.js"></script>
<script src="/home/plugins/daterangepicker/daterangepicker.js"></script>
<!-- Tempusdominus Bootstrap 4 -->
<script src="/home/plugins/tempusdominus-bootstrap-4/js/tempusdominus-bootstrap-4.min.js"></script>
<!-- Summernote -->
<script src="/home/plugins/summernote/summernote-bs4.min.js"></script>
<!-- overlayScrollbars -->
<script src="/home/plugins/overlayScrollbars/js/jquery.overlayScrollbars.min.js"></script>
<!-- AdminLTE App -->
<script src="/home/dist/js/adminlte.js"></script>

<!-- AdminLTE dashboard demo (This is only for demo purposes) -->
<script src="/home/dist/js/pages/dashboard.js"></script>


</body>

