<!DOCTYPE html>
<?php
session_start();
$User = $_SESSION["username"];
$cod_user = $_SESSION['cod_user'];

include('../Setting.php');

if ($mantenimiento == true) {
  header('location:../mantenimiento.php');
  exit;
}

if (!isset($User)) {
  header('location:login.php');
  exit();
}

include("../conexion.php");
include("logica/ac_permiso.php");

// Obtención de tiendas a las que tiene acceso el usuario
$sql = "SELECT cod_tienda FROM `asignacion_tienda` where cod_user = $cod_user";
$consulta = mysqli_query($conn, $sql);
while ($fila = mysqli_fetch_array($consulta)) {
  $marcas[] = $fila['cod_tienda'];
}
if (!empty($marcas)) {
  $tiendas = implode(', ', $marcas);
  $_SESSION['tiendas'] = $tiendas;
}
$tiendas = $_SESSION['tiendas'];
?>

<html lang="es">

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>iThink | Dashboard</title>
  <link rel="icon" href="/home/dist/img/logo_ithink.png" type="image/x-icon">

  <!-- Estilos y fuentes -->
  <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">
  <link rel="stylesheet" href="plugins/fontawesome-free/css/all.min.css">
  <link rel="stylesheet" href="plugins/tempusdominus-bootstrap-4/css/tempusdominus-bootstrap-4.min.css">
  <link rel="stylesheet" href="plugins/icheck-bootstrap/icheck-bootstrap.min.css">
  <link rel="stylesheet" href="dist/css/adminlte.min.css">
  <link rel="stylesheet" href="plugins/overlayScrollbars/css/OverlayScrollbars.min.css">
  <link rel="stylesheet" href="plugins/daterangepicker/daterangepicker.css">
  <link rel="stylesheet" href="plugins/summernote/summernote-bs4.min.css">
</head>

<body class="hold-transition sidebar-mini layout-fixed">
  <?php include("sidebar.php"); ?>

  <div class="content-wrapper">
    <div class="content-header">
      <div class="container-fluid">
        <div class="row mb-2">
          <div class="col-sm-6">
            <h1 class="m-0">Liberaciones</h1>
          </div>
          <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
              <li class="breadcrumb-item"><a href="#">Home</a></li>
              <li class="breadcrumb-item active">Liberaciones</li>
            </ol>
          </div>
        </div>
      </div>
    </div>

    <!-- Contenido Principal -->
    <section class="content">
      <div class="container-fluid">
        <div class="row">

          <?php if (Tiene_permiso($permisos_user, 'ver-consultas')): ?>
            <div class="col-lg-3 col-6">
              <div class="small-box bg-info">
                <div class="inner">
                  <h3><?php echo $con; ?></h3>
                  <p>Consultas</p>
                </div>
                <div class="icon">
                  <i class="fas fa-solid fa-question"></i>
                </div>
                <a href="pages/tables/consultas.php" class="small-box-footer">Más info <i class="fas fa-arrow-circle-right"></i></a>
              </div>
            </div>
          <?php endif; ?>

          <?php if (Tiene_permiso($permisos_user, 'ver-pendientes-lb')): ?>
            <div class="col-lg-3 col-6">
              <div class="small-box bg-danger">
                <div class="inner">
                  <h3>
                    <?php
                    $sql = "SELECT COUNT(*) as consultas FROM liberacion where Cod_estado = 2 and cod_tienda in ($tiendas)";
                    $consulta = mysqli_query($conn, $sql);
                    $row = mysqli_fetch_array($consulta);
                    echo $row['consultas'];
                    ?>
                  </h3>
                  <p>Pendientes 2</p>
                </div>
                <div class="icon">
                  <i class="fas fa-solid fa-exclamation"></i>
                </div>
                <a href="pages/tables/pendientes.php" class="small-box-footer">Más info <i class="fas fa-arrow-circle-right"></i></a>
              </div>
            </div>
          <?php endif; ?>

          <?php if (Tiene_permiso($permisos_user, 'ver-aprobadas-lb')): ?>
            <div class="col-lg-3 col-6">
              <div class="small-box bg-success">
                <div class="inner">
                  <h3>
                    <?php
                    $sql = "SELECT COUNT(*) as aprobadas FROM liberacion WHERE Cod_estado = 3 AND cod_tienda IN ($tiendas)";
                    $consulta = mysqli_query($conn, $sql);
                    $row = mysqli_fetch_array($consulta);
                    echo $row['aprobadas'];
                    ?>
                  </h3>
                  <p>Aprobadas</p>
                </div>
                <div class="icon">
                  <i class="fas fa-check"></i>
                </div>
                <a href="pages/tables/aprobadas.php" class="small-box-footer">Más info <i class="fas fa-arrow-circle-right"></i></a>
              </div>
            </div>
          <?php endif; ?>

          <?php if (Tiene_permiso($permisos_user, 'ver-finalizadas-lb')): ?>
            <div class="col-lg-3 col-6">
              <div class="small-box bg-secondary">
                <div class="inner">
                  <h3>
                    <?php
                    $sql = "SELECT COUNT(*) as finalizadas FROM liberacion WHERE Cod_estado = 5 AND cod_tienda IN ($tiendas)";
                    $consulta = mysqli_query($conn, $sql);
                    $row = mysqli_fetch_array($consulta);
                    echo $row['finalizadas'];
                    ?>
                  </h3>
                  <p>Finalizadas</p>
                </div>
                <div class="icon">
                  <i class="fas fa-flag-checkered"></i>
                </div>
                <a href="pages/tables/finalizadas.php" class="small-box-footer">Más info <i class="fas fa-arrow-circle-right"></i></a>
              </div>
            </div>
          <?php endif; ?>

          

        </div>

        <!-- Gráfico Donut -->
        <?php if (Tiene_permiso($permisos_user, 'ver-graficos-lb')): ?>
          <div class="card card-danger">
            <div class="card-header">
              <h3 class="card-title">Grafico de Liberaciones</h3>
              <div class="card-tools">
                <button type="button" class="btn btn-tool" data-card-widget="collapse"><i class="fas fa-minus"></i></button>
                <button type="button" class="btn btn-tool" data-card-widget="remove"><i class="fas fa-times"></i></button>
              </div>
            </div>
            <div class="card-body">
              <canvas id="donutChart" style="min-height: 250px; height: 250px; max-height: 250px; max-width: 100%;"></canvas>
            </div>
          </div>
        <?php endif; ?>

      </div>
    </section>
  </div>

  <footer class="main-footer">
    <strong>Copyright &copy; 2023 <a href="https://hexa.com">Hexa Systems</a>.</strong>
    All rights reserved.
    <div class="float-right d-none d-sm-inline-block">
      <b>Version</b> 1
    </div>
  </footer>

  <!-- Scripts -->
  <script src="plugins/jquery/jquery.min.js"></script>
  <script src="plugins/bootstrap/js/bootstrap.bundle.min.js"></script>
  <script src="plugins/chart.js/Chart.min.js"></script>
  <script src="dist/js/adminlte.js"></script>

  <!-- Configuración del gráfico Donut -->
  <script>
    <?php
    $sql = "SELECT cod_estado,COUNT(cod_estado) as Cantidad FROM liberacion where cod_tienda in ($tiendas) GROUP BY cod_estado;";
    $valores = mysqli_query($conn, $sql);

    // Procesamiento de datos del gráfico Donut
    $estados = [1 => 0, 2 => 0, 3 => 0, 4 => 0, 5 => 0, 6 => 0];
    while ($data = mysqli_fetch_assoc($valores)) {
      $estados[$data['cod_estado']] = $data['Cantidad'];
    }
    ?>

    var donutData = {
      labels: ['Consultas', 'Pendientes', 'Aprobadas', 'En Proceso', 'Finalizadas', 'Rechazadas'],
      datasets: [{
        data: [<?php echo implode(", ", $estados); ?>],
        backgroundColor: ['#007bff', '#dc3545', '#28a745', '#ffc107', '#6c757d', '#ff851b'],
      }]
    };

    var donutChartCanvas = $('#donutChart').get(0).getContext('2d');
    new Chart(donutChartCanvas, {
      type: 'doughnut',
      data: donutData,
      options: {
        maintainAspectRatio: false,
        responsive: true,
      }
    });
  </script>
</body>

</html>