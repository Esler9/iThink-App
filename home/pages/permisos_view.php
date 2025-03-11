<!DOCTYPE html>
<?php 
session_start();

include ('../../Setting.php');

if($mantenimiento == true ){
  header('location:../../mantenimiento.php');
  exit;}

// Verificar que las variables de sesión estén configuradas
if (!isset($_SESSION["username"]) || !isset($_SESSION['cod_user'])) {
  header('Location: ../../login.php');
  exit();
}

$User = htmlspecialchars($_SESSION["username"]);
$cod_user = htmlspecialchars($_SESSION['cod_user']);
$page = 2;

include("../../conexion.php");
include("../logica/ac_permiso.php");
include("../datos/dt_permisos.php");

// Obtener los grupos de usuarios
$grupos = Traer_grupo_usuario($conn);

// Construir el arreglo de permisos por grupo de manera centralizada
$groupPermissionsArray = [];
foreach ($grupos as $grupo) {
    $permisos = traer_permisos($conn, $grupo['codigo']);
    $permissionsGrouped = [];
    foreach ($permisos as $permiso) {
        $grupoPermiso = $permiso['group_permiso'];
        if (!isset($permissionsGrouped[$grupoPermiso])) {
            $permissionsGrouped[$grupoPermiso] = [
                'nombre' => $permiso['nombre_grupo_p'],
                'permisos' => []
            ];
        }
        $permissionsGrouped[$grupoPermiso]['permisos'][] = [
            'id'    => $permiso['codigo'],
            'label' => $permiso['nombre_permiso'],
            'check' => $permiso['active'] == 1 ? 'checked' : ''
        ];
    }
    $groupPermissionsArray[$grupo['codigo']] = $permissionsGrouped;
}

$updatedPermissionsArray = []; // Asegúrate de asignarle los valores actualizados si es necesario.
?>

<head>
  <link rel="icon" href="/home/dist/img/logo_ithink.png" type="image/x-icon">
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>iThink | Web</title>
  <!-- Google Font: Source Sans Pro -->
  <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">
  <!-- Font Awesome -->
  <link rel="stylesheet" href="../plugins/fontawesome-free/css/all.min.css">
  <!-- DataTables -->
  <link rel="stylesheet" href="../plugins/datatables-bs4/css/dataTables.bootstrap4.min.css">
  <link rel="stylesheet" href="../plugins/datatables-responsive/css/responsive.bootstrap4.min.css">
  <link rel="stylesheet" href="../plugins/datatables-buttons/css/buttons.bootstrap4.min.css">
  <!-- Theme style -->
  <link rel="stylesheet" href="../dist/css/adminlte.min.css">
  <link rel="stylesheet" href="../dist/css/app.css">
</head>
<body class="hold-transition sidebar-mini layout-fixed">

<?php include("sidebar.php"); ?>

<div class="content-wrapper">
  <section class="content-header">
    <div class="container-fluid">
      <div class="row mb-2">
        <div class="col-sm-6">
          <h1>Permisos de Grupos</h1>
        </div>
        <div class="col-sm-6">
          <ol class="breadcrumb float-sm-right">
            <li class="breadcrumb-item"><a href="../dashboard.php">Home</a></li>
            <li class="breadcrumb-item active">Permisos</li>
          </ol>
        </div>
      </div>
    </div>
  </section>

  <section class="content">
    <div class="container-fluid">
      <div class="container mt-5">
        <div class="row">
          <!-- Lista de grupos a la izquierda -->
          <div class="col-md-4">
            <h4>Grupos de Usuarios</h4>
            <ul id="groupList" class="list-group">
              <?php foreach ($grupos as $grupo): ?>
                <li class="list-group-item" data-group="<?php echo htmlspecialchars($grupo['codigo']); ?>">
                  <?php echo htmlspecialchars($grupo['nombre_grupo']); ?>
                </li>
              <?php endforeach; ?>
            </ul>
          </div>

          <!-- Puedes colocar este contenedor encima del formulario de permisos -->
          <div id="msgContainer"></div>

          <!-- Permisos a la derecha -->
          <div class="col-md-8">
            <form id="permissionsForm" method="POST" action="../datos/process_permision">
              <div class="card mt-3">
                <div class="card-header">
                  <h5 class="card-title mb-0">Permisos</h5>
                </div>
                <div class="card-body" id="permissionsContainer" style="max-height: 400px; overflow-y: auto;">
                  <p class="text-muted">Selecciona un grupo para ver y editar sus permisos.</p>
                </div>
              </div>
              <div class="text-center mt-3">
                <button type="submit" class="btn btn-primary">Guardar Cambios</button>
              </div>
            </form>
          </div>
        </div>
      </div>
    </div>
  </section>

  <footer class="main-footer">
    <div class="float-right d-none d-sm-block">
      <b>Version</b> 1
    </div>
    <strong>Copyright &copy; 2023 <a href="https://hexasystems.com">Hexa Systems</a>.</strong> Todos los derechos reservados.
  </footer>

  <aside class="control-sidebar control-sidebar-dark"></aside>
</div>

<!-- Scripts JS -->
<script src="../plugins/jquery/jquery.min.js"></script>
<script src="../plugins/bootstrap/js/bootstrap.bundle.min.js"></script>
<script src="../dist/js/adminlte.min.js"></script>

<script>
  // Se asigna el arreglo de permisos generado en PHP al objeto JavaScript.
  const groupPermissions = <?php echo json_encode($groupPermissionsArray, JSON_UNESCAPED_UNICODE); ?>;
  
  /**
   * Carga los permisos del grupo seleccionado y los inyecta en el contenedor.
   *
   * @param {string} group - Código del grupo seleccionado.
   */
  function loadPermissions(group) {
    const permissionsGrouped = groupPermissions[group];
    let html = '';

    for (const [grupoPermisoId, grupoPermiso] of Object.entries(permissionsGrouped)) {
      html += `
        <div class="permission-group mb-4">
          <h5 class="text-primary">${grupoPermiso.nombre}</h5>
          <div class="permissions-list border p-3 rounded">
      `;
      grupoPermiso.permisos.forEach(permission => {
        html += `
          <div class="custom-control custom-checkbox mb-2">
            <input type="checkbox" class="custom-control-input" id="${permission.id}" name="permiso[${group}][${permission.id}]" ${permission.check}>
            <label class="custom-control-label" for="${permission.id}">${permission.label}</label>
          </div>
        `;
      });
      html += '</div></div>';
    }
    $('#permissionsContainer').html(html);
  }

  $(document).ready(function() {
    // Cargar permisos al seleccionar un grupo.
    $('#groupList').on('click', 'li', function(event) {
      event.preventDefault();
      const selectedGroup = $(this).data('group');
      if (!$(this).hasClass('active')) {
        loadPermissions(selectedGroup);
        $('#groupList li').removeClass('active');
        $(this).addClass('active');
      }
    });

    // Enviar formulario vía AJAX y actualizar checkbox con la respuesta.
    $('#permissionsForm').on('submit', function(e) {
      e.preventDefault();
      $.ajax({
        url: '../datos/process_permision',
        type: 'POST',
        dataType: 'json', // Se espera respuesta en JSON.
        data: $(this).serialize(),
        success: function(response) {
          const selectedGroup = $('#groupList li.active').data('group');
          if (selectedGroup) {
            // Suponiendo que el endpoint retorna los permisos actualizados para el grupo.
            if(response.updatedPermissions){
              groupPermissions[selectedGroup] = response.updatedPermissions;
            }
            loadPermissions(selectedGroup);
          }
          // Mostrar notificación de éxito.
          $('#msgContainer').html(
            '<div class="alert alert-success alert-dismissible fade show" role="alert">' +
              'Configuración guardada correctamente.' +
              '<button type="button" class="close" data-dismiss="alert" aria-label="Close">' +
                '<span aria-hidden="true">&times;</span>' +
              '</button>' +
            '</div>'
          );
          // Cerrar la alerta automáticamente después de 3 segundos.
          setTimeout(function() {
            $(".alert").alert('close');
          }, 3000);
        },
        error: function() {
          $('#msgContainer').html(
            '<div class="alert alert-danger alert-dismissible fade show" role="alert">' +
              'Ha ocurrido un error al guardar los cambios.' +
              '<button type="button" class="close" data-dismiss="alert" aria-label="Close">' +
                '<span aria-hidden="true">&times;</span>' +
              '</button>' +
            '</div>'
          );
          setTimeout(function() {
            $(".alert").alert('close');
          }, 3000);
        }
      });
    });
  });
</script>
</body>
</php>

<?php
// Procesamiento y actualización de permisos aquí...

// Asegúrate de definir $updatedPermissionsArray, por ejemplo:
$updatedPermissionsArray = isset($updatedPermissionsArray) ? $updatedPermissionsArray : [];

// Enviar la respuesta JSON sin output extra.
header('Content-Type: application/json');
echo json_encode(['status' => 'success', 'updatedPermissions' => $updatedPermissionsArray]);
exit();
?>
