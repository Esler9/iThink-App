<!DOCTYPE html>
<?php
include('vistas/header.php');
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
                <a href="pages/liberaciones/consultas.php" class="small-box-footer">Más info <i class="fas fa-arrow-circle-right"></i></a>
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
                  <p>Pendientes</p>
                </div>
                <div class="icon">
                  <i class="fas fa-solid fa-exclamation"></i>
                </div>
                <a href="pages/liberaciones/pendientes.php" class="small-box-footer">Más info <i class="fas fa-arrow-circle-right"></i></a>
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
                <a href="pages/liberaciones/aprobadas.php" class="small-box-footer">Más info <i class="fas fa-arrow-circle-right"></i></a>
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
                <a href="pages/liberaciones/finalizadas.php" class="small-box-footer">Más info <i class="fas fa-arrow-circle-right"></i></a>
              </div>
            </div>
          <?php endif; ?>

          

        </div>

        <!-- Gráfico Donut -->
        <?php if (Tiene_permiso($permisos_user, 'ver-graficos-lb')): ?>
          <div class="card card-danger collapsed-card">
            <div class="card-header">
              <h3 class="card-title">Gráfico de Liberaciones</h3>
              <div class="card-tools">
                <button type="button" class="btn btn-tool" data-card-widget="collapse">
                  <i class="fas fa-plus"></i>
                </button>
              </div>
            </div>
            <div class="card-body" style="display: none;">
              <canvas id="donutChart" style="min-height: 250px; height: 250px; max-height: 250px; max-width: 100%;"></canvas>
            </div>
          </div>
        <?php endif; ?>

        <?php
        // Consulta para obtener liberaciones actualizadas en los últimos 7 días
        $sqlLatest = "SELECT * FROM liberacion WHERE date_update >= DATE_SUB(NOW(), INTERVAL 7 DAY) ORDER BY date_update DESC";
        $resultLatest = mysqli_query($conn, $sqlLatest);
        ?>

        <?php
        // Consulta para obtener los estados desde la base de datos
        $sqlEstados = "SELECT codigo_estado, Descripcion FROM Estado";
        $resultEstados = mysqli_query($conn, $sqlEstados);
        $estadoMapping = [];
        while ($estadoData = mysqli_fetch_assoc($resultEstados)) {
          $codigo = (int)$estadoData['codigo_estado'];
          $estadoMapping[$codigo]['nombre'] = $estadoData['Descripcion'];
          
          // Asignar URL según el código de estado (ajusta estas condiciones a tu lógica)
          switch ($codigo) {
            case 1:
              $estadoMapping[$codigo]['url'] = 'pages/liberaciones/consultas.php';
              break;
            case 2:
              $estadoMapping[$codigo]['url'] = 'pages/liberaciones/pendientes.php';
              break;
            case 3:
              $estadoMapping[$codigo]['url'] = 'pages/liberaciones/aprobadas.php';
              break;
            case 4:
              $estadoMapping[$codigo]['url'] = 'pages/liberaciones/proceso.php';
              break;
            case 5:
              $estadoMapping[$codigo]['url'] = 'pages/liberaciones/finalizadas.php';
              break;
            case 6:
              $estadoMapping[$codigo]['url'] = 'pages/liberaciones/rechazadas.php';
              break;
            default:
              $estadoMapping[$codigo]['url'] = '#';
              break;
          }
        }
        ?>

        <!-- Tabla de Liberaciones - Últimos 7 Días -->
        <div class="card">
          <div class="card-header">
            <h3 class="card-title">Liberaciones - Últimos 7 Días</h3>
          </div>
          <div class="card-body">
            <!-- Contenedor responsive para la tabla -->
            <div class="table-responsive">
              <table class="table table-head-fixed text-nowrap">
                <thead>
                  <tr>
                    <th>IMEI</th>
                    <th>Modelo</th>
                    <th>Cliente</th>
                    <th>Última actualización</th>
                    <th>Estado</th>
                    <th>Acción</th>
                  </tr>
                </thead>
                <tbody>
                  <?php 
                    while($row = mysqli_fetch_assoc($resultLatest)):
                      $estadoCod = (int)$row['cod_estado'];
                      $estadoNombre = isset($estadoMapping[$estadoCod]) ? $estadoMapping[$estadoCod]['nombre'] : $row['cod_estado'];
                      $estadoUrl = isset($estadoMapping[$estadoCod]) ? $estadoMapping[$estadoCod]['url'] : '#';
                      switch($estadoCod) {
                          case 1: 
                            $btnClass = 'btn-info';
                            $btnIcon = 'fas fa-question-circle';
                            break;
                          case 2:
                            $btnClass = 'btn-warning';
                            $btnIcon = 'fas fa-hourglass-half';
                            break;
                          case 3:
                            $btnClass = 'btn-success';
                            $btnIcon = 'fas fa-thumbs-up';
                            break;
                          case 4:
                            $btnClass = 'btn-primary';
                            $btnIcon = 'fas fa-cogs';
                            break;
                          case 5:
                            $btnClass = 'btn-secondary';
                            $btnIcon = 'fas fa-flag-checkered';
                            break;
                          case 6:
                            $btnClass = 'btn-danger';
                            $btnIcon = 'fas fa-times-circle';
                            break;
                          default:
                            $btnClass = 'btn-dark';
                            $btnIcon = 'fas fa-info-circle';
                            break;
                      }
                  ?>
                  <tr>
                    <td><?php echo htmlspecialchars($row['serie']); ?></td>
                    <td><?php echo htmlspecialchars($row['modelo']); ?></td>
                    <td><?php echo htmlspecialchars($row['Nombre_Cliente']); ?></td>
                    <td><?php echo htmlspecialchars($row['date_update']); ?></td>
                    <td><?php echo htmlspecialchars($estadoNombre); ?></td>
                    <td>
                      <a href="<?php echo $estadoUrl; ?>?imei=<?php echo urlencode($row['serie']); ?>" class="btn <?php echo $btnClass; ?> btn-sm">
                        <i class="<?php echo $btnIcon; ?>"></i> <?php echo htmlspecialchars($estadoNombre); ?>
                      </a>
                    </td>
                  </tr>
                  <?php endwhile; ?>
                </tbody>
              </table>
            </div>
          </div>
        </div>

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