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

$User = $_SESSION["username"];
$cod_user = $_SESSION['cod_user'];
$page = 2;

include("../../conexion.php");
include("../logica/ac_permiso.php");

// Obtener la consulta de búsqueda si existe
$search = isset($_GET['search']) ? $_GET['search'] : '';

// Crear la consulta SQL
$sql = "SELECT name, email, phone_number, password FROM icloud_accounts";

// Si hay una búsqueda, añade un WHERE a la consulta
if (!empty($search)) {
    $search = $conn->real_escape_string($search); // Seguridad para prevenir inyecciones SQL
    $sql .= " WHERE name LIKE '%$search%' OR email LIKE '%$search%' OR phone_number LIKE '%$search%'";
}

$result = $conn->query($sql);

?>

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>iThink | Web </title>
    <link rel="icon" href="/home/dist/img/logo_ithink.png" type="image/x-icon">
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
    
    <style>
        /* CSS personalizado para mejorar el diseño */
        body {
            font-family: 'Source Sans Pro', sans-serif;
        }
        h1, h2 {
            color: #007bff;
        }
        .search-bar {
            margin-bottom: 20px;
        }
    </style>
</head>
<body class="hold-transition sidebar-mini layout-fixed">

<?php include("sidebar.php"); ?>

<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1>Guardar iClouds</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="/home/dashboard.php">Home</a></li>
                        <li class="breadcrumb-item active">Guardar iCloud</li>
                    </ol>
                </div>
            </div>
        </div><!-- /.container-fluid -->
    </section>

    <!-- Main content -->
    <section class="content">
        <div class="container-fluid">
            <div class="container mt-5">
                <h2 class="text-center">Agregar Cuenta de iCloud</h2>
                <form id="icloudForm" action="/home/logica/accion_icloud.php" method="POST">
                    <div class="form-group">
                        <label for="name">Nombre:</label>
                        <input type="text" class="form-control" name="name" required>
                    </div>

                    <div class="form-group">
                        <label for="email">Correo iCloud:</label>
                        <input type="email" class="form-control" name="email" required>
                    </div>

                    <div class="form-group">
                        <label for="password">Contraseña:</label>
                        <input type="password" class="form-control" name="password" required>
                    </div>

                    <div class="form-group">
                        <label for="phone">Número de Teléfono:</label>
                        <input type="text" class="form-control" name="phone" required>
                    </div>

                    <button type="submit" class="btn btn-primary">Agregar Cuenta</button>
                </form>
            </div>

            <!-- Buscador -->
            <div class="container mt-5 search-bar">
                <h2 class="text-center">Buscar Cuentas de iCloud</h2>
                <form id="searchForm" method="GET" action="">
                    <input type="text" name="search" class="form-control" placeholder="Buscar por Nombre, Email o Teléfono">
                    <button type="submit" class="btn btn-secondary mt-2">Buscar</button>
                </form>
            </div>


            <!-- Tabla de cuentas creadas -->
            <div class="container mt-5">
                <h2 class="text-center">Cuentas de iCloud Creadas</h2>
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th>Nombre</th>
                            <th>Correo</th>
                            <th>Teléfono</th>
                           
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if ($result->num_rows > 0): ?>
                            <?php while($row = $result->fetch_assoc()): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($row['name']); ?></td>
                                    <td><?php echo htmlspecialchars($row['email']); ?></td>
                                    <td><?php echo htmlspecialchars($row['phone_number']); ?></td>
                                    
                                    <td>
                                        <button class="btn btn-secondary" onclick="printAccount('<?php echo htmlspecialchars($row['name']); ?>', '<?php echo htmlspecialchars($row['email']); ?>', '<?php echo htmlspecialchars($row['phone_number']); ?>', '<?php echo htmlspecialchars($row['password']); ?>')">
                                            <i class="fas fa-print"></i> Imprimir
                                        </button>
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="5" class="text-center">No hay cuentas creadas.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>

        </div>
        <!-- /.container-fluid -->
    </section>
    <!-- /.content -->

</div>


<!-- Control Sidebar -->
<aside class="control-sidebar control-sidebar-dark">
    <!-- Control sidebar content goes here --> 
</aside>
<!-- /.control-sidebar -->
</div>
<!-- ./wrapper -->

<!-- jQuery -->
<script src="../plugins/jquery/jquery.min.js"></script>
<!-- Bootstrap 4 -->
<script src="../plugins/bootstrap/js/bootstrap.bundle.min.js"></script>
<!-- DataTables & Plugins -->
<script src="../plugins/datatables/jquery.dataTables.min.js"></script>
<script src="../plugins/datatables-bs4/js/dataTables.bootstrap4.min.js"></script>
<script src="../plugins/datatables-responsive/js/dataTables.responsive.min.js"></script>
<script src="../plugins/datatables-responsive/js/responsive.bootstrap4.min.js"></script>
<script src="../plugins/datatables-buttons/js/dataTables.buttons.min.js"></script>
<script src="../plugins/datatables-buttons/js/buttons.bootstrap4.min.js"></script>
<!-- Validacion -->
<script src="../plugins/jquery-validation/jquery.validate.min.js"></script>
<script src="../plugins/jquery-validation/additional-methods.min.js"></script>
<script src="../plugins/jszip/jszip.min.js"></script>
<script src="../plugins/pdfmake/pdfmake.min.js"></script>
<script src="../plugins/pdfmake/vfs_fonts.js"></script>
<script src="../plugins/datatables-buttons/js/buttons.html5.min.js"></script>
<script src="../plugins/datatables-buttons/js/buttons.print.min.js"></script>
<script src="../plugins/datatables-buttons/js/buttons.colVis.min.js"></script>
<!-- AdminLTE App -->
<script src="../dist/js/adminlte.min.js"></script>

<script>
function printAccount(name, email, phone, password) {
    const printContent = `
        <div style="text-align:center; font-family: Arial, sans-serif;">
            <style>
                /* Estilos para la impresión */
                @media print {
                    @page {
                        size: 80mm 297mm landscape; /* Formato horizontal */
                        margin: 5mm; /* Margen pequeño */
                    }
                    body {
                        margin: 5px;
                        padding: 0;
                        font-family: Arial, sans-serif;
                        font-size: 18px; /* Texto más grande */
                        background-color: #fff;
                        color: #000;
                    }
                    .container {
                        width: 100%;
                        padding: 10px; /* Margen pequeño */
                        box-sizing: border-box;
                    }
                    h2 {
                        font-size: 22px; /* Título más grande */
                        margin: 0;
                        padding: 5px 0;
                        text-align: center;
                        border-bottom: 2px solid #007bff; /* Línea elegante debajo del título */
                        margin-bottom: 10px;
                        color: #007bff; /* Color azul para resaltar el título */
                    }
                    p {
                        font-size: 18px; /* Texto de párrafo más grande */
                        margin: 8px 0;
                        padding: 0;
                        line-height: 1.6;
                        border-bottom: 1px solid #ccc; /* Separador entre líneas */
                        padding-bottom: 5px;
                        display: flex;
                        align-items: center;
                    }
                    .icon {
                        font-size: 22px; /* Tamaño del ícono más grande */
                        margin-right: 10px;
                        color: #007bff; /* Color de los íconos */
                    }
                    .footer {
                        margin-top: 15px;
                        text-align: center;
                        font-size: 14px;
                        color: #666;
                    }
                }
            </style>
            <div class="container">
                <h2>Detalles de la Cuenta iCloud</h2>
                <p><i class="fas fa-user icon"></i> ${name}</p> <!-- Campo para el nombre -->
                <p><i class="fas fa-envelope icon"></i> ${email}</p>
                
                <p><i class="fas fa-phone icon"></i> ${phone}</p>
                <div class="footer">
                    <p>Gracias por utilizar nuestro servicio.</p>
                </div>
            </div>
        </div>
    `;
    const win = window.open('', '', 'height=400,width=600');
    win.document.write('<html><head><title>Imprimir Cuenta iCloud</title>');
    win.document.write('<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">'); // Cargar Font Awesome
    win.document.write('</head><body>');
    win.document.write(printContent);
    win.document.write('</body></html>');
    win.document.close();
    win.print();
}
</script>



</body>
</html>
