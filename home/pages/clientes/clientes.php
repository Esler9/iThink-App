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

// Obtener los clientes
$sql = "SELECT * FROM clientes";
$clientes = $conn->query($sql);

// Almacena las tiendas a las que tiene acceso el usuario
$cod_user = $_SESSION['cod_user'];
$sql = "SELECT cod_tienda FROM `asignacion_tienda` WHERE cod_user = $cod_user";
$consulta = mysqli_query($conn, $sql);
while ($fila = mysqli_fetch_array($consulta)) {
    $marcas[] = $fila['cod_tienda'];
}
if (!empty($marcas)){
    $tiendas = implode(', ', $marcas);
    $_SESSION['tiendas'] = $tiendas;
}
$tiendas = $_SESSION['tiendas'];
?>

<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>iThink | Gestión de Clientes</title>

  <!-- Google Font: Source Sans Pro -->
  <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">
  <!-- Font Awesome -->
  <link rel="stylesheet" href="/home/plugins/fontawesome-free/css/all.min.css">
  <!-- Bootstrap 4 (original) -->
  <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css">
  <!-- Theme style (original) -->
  <link rel="stylesheet" href="/home/dist/css/adminlte.min.css">
  <!-- Custom CSS -->
  <style>
      .btn-primary, .btn-info { background-color: #007bff; }
      .btn-danger { background-color: #dc3545; }
      .modal-header { background-color: #007bff; color: white; }
      .modal-footer { background-color: #f8f9fa; }
      .card-header { background-color: #007bff; color: white; }
      .table th, .table td { vertical-align: middle; }
      .content-wrapper { padding: 20px; }
      .alert { margin-top: 15px; }
  </style>
</head>

<body class="hold-transition sidebar-mini layout-fixed">

<?php include("../../sidebar.php"); ?>

<div class="content-wrapper">
  <div class="container mt-5">
    <h1 class="text-center mb-4">Gestión de Clientes</h1>

    <!-- Mensajes -->
    <?php if(isset($_GET['msg'])): ?>
    <div class="alert alert-success text-center" role="alert">
      <?= $_GET['msg']; ?>
    </div>
    <?php endif; ?>

    <!-- Formulario para Agregar Nuevo Cliente -->
    <h2 class="text-info">Agregar Cliente</h2>
    <form method="POST" action="cliente_acciones?func=1&id=0">
      <div class="form-group">
        <label for="nombre">Nombre:</label>
        <input type="text" class="form-control" id="nombre" name="nombre" required>
      </div>
      <div class="form-group">
        <label for="email">Email:</label>
        <input type="email" class="form-control" id="email" name="email" required>
      </div>
      <div class="form-group">
        <label for="telefono">Teléfono:</label>
        <input type="text" class="form-control" id="telefono" name="telefono" required>
      </div>
      <div class="form-group">
        <label for="direccion">Dirección:</label>
        <textarea class="form-control" id="direccion" name="direccion" required></textarea>
      </div>
      <button type="submit" class="btn btn-primary">Agregar Cliente</button>
    </form>

    <!-- Lista de Clientes -->
    <div class="card mt-4">
      <div class="card-header">
        <h2>Lista de Clientes</h2>
      </div>
      <div class="card-body">
      <table class="table table-bordered table-hover">
            <thead class="thead-light">
            <tr>
                <th>ID</th>
                <th>Nombre</th>
                <th>Email</th>
                <th>Teléfono</th>
                <th>Dirección</th>
                <th>Acciones</th>
            </tr>
            </thead>
            <tbody>
            <?php
            if ($clientes->num_rows > 0) {
                while($row = $clientes->fetch_assoc()) {
                    echo "<tr>
                            <td>".$row['id']."</td>
                            <td>".$row['nombre']."</td>
                            <td>".$row['email']."</td>
                            <td>".$row['telefono']."</td>
                            <td>".$row['direccion']."</td>
                            <td>
                                <button class='btn btn-warning btn-sm' data-toggle='modal' data-target='#editModal' data-id='".$row['id']."' data-nombre='".$row['nombre']."' data-email='".$row['email']."' data-telefono='".$row['telefono']."' data-direccion='".$row['direccion']."'>Editar</button>
                                <a href='cliente_acciones.php?func=3&id=".$row['id']."' class='btn btn-danger btn-sm' onclick='return confirm(\"¿Estás seguro de eliminar este cliente?\")'>Eliminar</a>
                            </td>
                          </tr>";
                }
            } else {
                echo "<tr><td colspan='6'>No hay clientes registrados.</td></tr>";
            }
            ?>
            </tbody>
        </table>
      </div>
    </div>
  </div>
</div>

<!-- Modal para Editar Cliente -->
<div class="modal fade" id="editModal" tabindex="-1" role="dialog" aria-labelledby="editModalLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="editModalLabel">Editar Cliente</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Cerrar">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <form id="edit-form" method="POST">
        <div class="modal-body">
          <input type="hidden" id="edit-id" name="id">
          <div class="form-group">
            <label for="edit-nombre">Nombre:</label>
            <input type="text" class="form-control" id="edit-nombre" name="nombre" required>
          </div>
          <div class="form-group">
            <label for="edit-email">Email:</label>
            <input type="email" class="form-control" id="edit-email" name="email" required>
          </div>
          <div class="form-group">
            <label for="edit-telefono">Teléfono:</label>
            <input type="text" class="form-control" id="edit-telefono" name="telefono" required>
          </div>
          <div class="form-group">
            <label for="edit-direccion">Dirección:</label>
            <textarea class="form-control" id="edit-direccion" name="direccion" required></textarea>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
          <button type="submit" class="btn btn-primary">Guardar Cambios</button>
        </div>
      </form>
    </div>
  </div>
</div>


<!-- Footer -->
<footer class="main-footer text-center mt-5">
  <strong>&copy; 2023 <a href="https://hexa.com">Hexa Systems</a>.</strong> Todos los derechos reservados.
  <div class="float-right d-none d-sm-inline-block">
    <b>Versión</b> 1
  </div>
</footer>

<!-- jQuery (original) -->
<script src="/home/plugins/jquery/jquery.min.js"></script>
<!-- Bootstrap 4 (original) -->
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.bundle.min.js"></script>
<!-- AdminLTE App (original) -->
<script src="/home/dist/js/adminlte.js"></script>

<script>
  // Modal Editar - Cargar datos en el modal
  $('#editModal').on('show.bs.modal', function (event) {
    var button = $(event.relatedTarget); // Botón que activa el modal
    var id = button.data('id'); // Extraer información de los atributos data-* del botón
    var nombre = button.data('nombre');
    var email = button.data('email');
    var telefono = button.data('telefono');
    var direccion = button.data('direccion');

    var modal = $(this);
    modal.find('#edit-id').val(id); // Asignar el ID al campo oculto
    modal.find('#edit-nombre').val(nombre); // Asignar el nombre al campo
    modal.find('#edit-email').val(email); // Asignar el email al campo
    modal.find('#edit-telefono').val(telefono); // Asignar el teléfono al campo
    modal.find('#edit-direccion').val(direccion); // Asignar la dirección al campo
  });

  // Enviar el formulario con el ID en la URL
  $('#edit-form').on('submit', function(event) {
    event.preventDefault(); // Evitar el envío normal del formulario
    var id = $('#edit-id').val(); // Obtener el ID
    var nombre = $('#edit-nombre').val();
    var email = $('#edit-email').val();
    var telefono = $('#edit-telefono').val();
    var direccion = $('#edit-direccion').val();

    // Construir la URL
    var url = "cliente_acciones.php?func=2&id=" + id;

    // Crear un formulario temporal para enviar datos por POST
    var form = $('<form>', {
      action: url,
      method: 'POST'
    });

    // Agregar los campos del formulario
    form.append($('<input>', { type: 'hidden', name: 'nombre', value: nombre }));
    form.append($('<input>', { type: 'hidden', name: 'email', value: email }));
    form.append($('<input>', { type: 'hidden', name: 'telefono', value: telefono }));
    form.append($('<input>', { type: 'hidden', name: 'direccion', value: direccion }));

    // Enviar el formulario
    form.appendTo('body').submit();
  });
</script>


</body>
</html>
