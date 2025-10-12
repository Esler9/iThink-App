<!DOCTYPE html>
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
<html lang="es">
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
            <h1>Informar Consulta</h1>
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
                <h3 class="card-title">Crear Consulta de Liberación</h3>
              </div>
              <!-- /.card-header -->
              <div class="card-body">
                <form id="crearConsultaForm" action="../../logica/accion?accion=0" method="POST" novalidate>
                  <div class="card-body">
                    
                    <!-- Información del Cliente -->
                    <h5 class="mb-3"><i class="fas fa-user"></i> Información del Cliente</h5>
                    <div class="row">
                      <div class="col-md-6">
                        <div class="form-group">
                          <label for="name">Nombre Completo:</label>
                          <input type="text" name="name" id="name" class="form-control" 
                                 placeholder="Ingrese el nombre completo" required>
                          <div class="invalid-feedback">
                            El nombre es requerido.
                          </div>
                        </div>
                      </div>
                      <div class="col-md-6">
                        <div class="form-group">
                          <label for="celular">Celular:</label>
                          <div class="input-group">
                            <div class="input-group-prepend">
                              <span class="input-group-text">+502</span>
                            </div>
                            <input type="text" name="celular" id="celular" class="form-control" 
                                   placeholder="Ingrese 8 dígitos" required
                                   pattern="^\d{8}$" maxlength="8"
                                   title="El celular debe contener 8 dígitos">
                            <div class="invalid-feedback">
                              El celular es requerido y debe tener 8 dígitos.
                            </div>
                          </div>
                        </div>
                      </div>
                    </div>

                    <hr class="my-4">

                    <!-- Información del Equipo -->
                    <h5 class="mb-3"><i class="fas fa-mobile-alt"></i> Información del Equipo</h5>
                    <div class="row">
                      <div class="col-md-6">
                        <div class="form-group">
                          <label for="c_imei">IMEI Principal:</label>
                          <input type="text" id="c_imei" name="c_imei" class="form-control" 
                                 placeholder="Ingrese IMEI (15 dígitos)"
                                 pattern="^\d{15}$" maxlength="15" required 
                                 title="El IMEI debe contener 15 dígitos numéricos">
                          <div class="invalid-feedback">
                            Por favor, ingrese un IMEI válido que tenga 15 dígitos.
                          </div>
                        </div>
                      </div>
                      <div class="col-md-6">
                        <div class="form-group">
                          <label for="c_imei2">IMEI Secundario (Opcional):</label>
                          <input type="text" id="c_imei2" name="c_imei2" class="form-control" 
                                 placeholder="Ingrese IMEI secundario (15 dígitos)"
                                 pattern="^\d{15}$" maxlength="15"
                                 title="El IMEI debe contener 15 dígitos numéricos">
                          <small class="form-text text-muted">Solo para equipos con doble SIM</small>
                          <div class="invalid-feedback">
                            El IMEI secundario debe tener 15 dígitos.
                          </div>
                        </div>
                      </div>
                    </div>

                    <div class="row">
                      <div class="col-md-6">
                        <div class="form-group">
                          <label for="model">Modelo:</label>
                          <input type="text" name="model" id="model" class="form-control" 
                                 placeholder="Ej: iPhone 12 Pro, Samsung Galaxy S21" required>
                          <div class="invalid-feedback">
                            El modelo es requerido.
                          </div>
                        </div>
                      </div>
                      <div class="col-md-6">
                        <div class="form-group">
                          <label for="tiempo_usa">Tiempo de Uso en USA:</label>
                          <select name="tiempo_usa" id="tiempo_usa" class="form-control" required>
                            <option value="">Seleccione...</option>
                            <option value="0-3">0 a 3 meses</option>
                            <option value="3-6">3 a 6 meses</option>
                            <option value="6-12">6 a 12 meses</option>
                            <option value="12+">Más de 12 meses</option>
                            <option value="nunca">Nunca usado en USA</option>
                          </select>
                          <div class="invalid-feedback">
                            Seleccione el tiempo de uso en Estados Unidos.
                          </div>
                        </div>
                      </div>
                    </div>

                    <hr class="my-4">

                    <!-- Verificaciones de Seguridad -->
                    <h5 class="mb-3"><i class="fas fa-shield-alt"></i> Verificaciones de Seguridad</h5>
                    
                    <div class="form-group">
                      <div class="custom-control custom-checkbox">
                        <input type="checkbox" class="custom-control-input" id="no_blacklist" name="no_blacklist" required>
                        <label class="custom-control-label" for="no_blacklist">
                          <strong>Confirmo que el equipo NO está en la Blacklist</strong>
                          <br><small class="text-muted">El equipo no tiene reporte de pérdida o robo</small>
                        </label>
                        <div class="invalid-feedback" style="display: block;">
                          Debe confirmar que el equipo no está en la blacklist.
                        </div>
                      </div>
                    </div>

                    <div class="form-group">
                      <div class="custom-control custom-checkbox">
                        <input type="checkbox" class="custom-control-input" id="no_icloud" name="no_icloud" required>
                        <label class="custom-control-label" for="no_icloud">
                          <strong>Confirmo que el equipo NO tiene cuenta iCloud activa</strong>
                          <br><small class="text-muted">El equipo no tiene activado "Buscar mi iPhone" ni cuenta de iCloud vinculada</small>
                        </label>
                        <div class="invalid-feedback" style="display: block;">
                          Debe confirmar que el equipo no tiene iCloud activo.
                        </div>
                      </div>
                    </div>

                    <div class="alert alert-warning" role="alert">
                      <i class="fas fa-exclamation-triangle"></i> 
                      <strong>Importante:</strong> Verifique que toda la información sea correcta antes de enviar. 
                      Los equipos con iCloud activo o en blacklist NO pueden ser liberados.
                    </div>

                    <div class="form-group">
                      <label for="observaciones">Observaciones Adicionales (Opcional):</label>
                      <textarea name="observaciones" id="observaciones" class="form-control" rows="3" 
                                placeholder="Ingrese cualquier información adicional relevante"></textarea>
                    </div>

                  </div>
                  <!-- /.card-body -->
                  <div class="card-footer">
                    <button type="submit" class="btn btn-lg btn-primary">
                      <i class="fas fa-save"></i> Crear Consulta
                    </button>
                    <a href="index.php" class="btn btn-lg btn-secondary">
                      <i class="fas fa-times"></i> Cancelar
                    </a>
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

<script>
// Algoritmo de Luhn para validar el IMEI
function isValidIMEI(imei) {
  if (!imei || imei.length !== 15) return false;
  
  let sum = 0;
  let doubleDigit = false;
  for (let i = imei.length - 1; i >= 0; i--) {
    let digit = parseInt(imei.charAt(i), 10);
    if (doubleDigit) {
      digit *= 2;
      if (digit > 9) {
        digit -= 9;
      }
    }
    sum += digit;
    doubleDigit = !doubleDigit;
  }
  return (sum % 10 === 0);
}

// Activar validación de Bootstrap en el formulario
(function() {
  'use strict';
  var form = document.getElementById('crearConsultaForm');
  
  form.addEventListener('submit', function(event) {
    var isValid = true;
    
    // Reiniciar validación custom por IMEI
    var imeiInput = document.getElementById('c_imei');
    var imei2Input = document.getElementById('c_imei2');
    
    // Validar IMEI principal
    if (imeiInput.value.length === 15 && !isValidIMEI(imeiInput.value)) {
      imeiInput.classList.add('is-invalid');
      imeiInput.nextElementSibling.textContent = 'El IMEI no es válido según el algoritmo de Luhn.';
      isValid = false;
    } else if (imeiInput.value.length === 15) {
      imeiInput.classList.remove('is-invalid');
    }
    
    // Validar IMEI secundario (si se ingresó)
    if (imei2Input.value.length > 0) {
      if (imei2Input.value.length === 15 && !isValidIMEI(imei2Input.value)) {
        imei2Input.classList.add('is-invalid');
        imei2Input.nextElementSibling.nextElementSibling.textContent = 'El IMEI secundario no es válido según el algoritmo de Luhn.';
        isValid = false;
      } else if (imei2Input.value.length === 15) {
        imei2Input.classList.remove('is-invalid');
      }
    }
    
    // Validar checkboxes
    var blacklistCheck = document.getElementById('no_blacklist');
    var icloudCheck = document.getElementById('no_icloud');
    
    if (!blacklistCheck.checked || !icloudCheck.checked) {
      isValid = false;
    }
    
    // Validación HTML5 (pattern, required, etc.)
    if (!form.checkValidity() || !isValid) {
      // Mostrar alerta personalizada
      document.getElementById('customAlert').style.display = 'block';
      setTimeout(function(){
          document.getElementById('customAlert').style.display = 'none';
      }, 3000);
      
      event.preventDefault();
      event.stopPropagation();
    }

    form.classList.add('was-validated');
  }, false);
})();

// Validación en tiempo real al perder el foco del campo IMEI principal
document.getElementById('c_imei').addEventListener('blur', function() {
  var imei = this.value;
  if (imei.length === 15 && !isValidIMEI(imei)) {
    this.classList.add('is-invalid');
    this.nextElementSibling.textContent = 'El IMEI no es válido según el algoritmo de Luhn.';
  } else if (imei.length === 15) {
    this.classList.remove('is-invalid');
  }
});

// Validación en tiempo real al perder el foco del campo IMEI secundario
document.getElementById('c_imei2').addEventListener('blur', function() {
  var imei = this.value;
  if (imei.length > 0) {
    if (imei.length === 15 && !isValidIMEI(imei)) {
      this.classList.add('is-invalid');
      this.nextElementSibling.nextElementSibling.textContent = 'El IMEI secundario no es válido según el algoritmo de Luhn.';
    } else if (imei.length === 15) {
      this.classList.remove('is-invalid');
    }
  } else {
    this.classList.remove('is-invalid');
  }
});

// Solo permitir números en campos IMEI
document.getElementById('c_imei').addEventListener('input', function(e) {
  this.value = this.value.replace(/[^0-9]/g, '');
});

document.getElementById('c_imei2').addEventListener('input', function(e) {
  this.value = this.value.replace(/[^0-9]/g, '');
});

// Solo permitir números en campo celular
document.getElementById('celular').addEventListener('input', function(e) {
  this.value = this.value.replace(/[^0-9]/g, '');
});
</script>

<div id="customAlert" style="display:none; position: fixed; top: 50%; left: 50%; transform: translate(-50%, -50%);
     z-index: 9999; background-color: #f8d7da; border: 1px solid #f5c6cb; padding: 30px 40px;
     font-size: 24px; text-align: center; border-radius: 5px; color: #721c24;">
  <i class="fas fa-exclamation-triangle"></i><br>
  La información ingresada es incorrecta o falta, por favor verifíquela.
</div>

</body>
</html>
