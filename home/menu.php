<div class="wrapper">
    <!-- Preloader -->
    <div class="preloader flex-column justify-content-center align-items-center">
        <img class="animation__shake" src="/home/dist/img/logo_ithink.png" alt="iThinkWeb" height="60" width="60">
    </div>

    <!-- Navbar (Header) -->
    <nav class="main-header navbar navbar-expand navbar-white navbar-light">
        <!-- Left navbar links -->
        <ul class="navbar-nav">
            <li class="nav-item">
                <a class="nav-link" data-widget="pushmenu" href="#" role="button">
                    <i class="fas fa-bars"></i>
                </a>
            </li>
            <li class="nav-item d-none d-sm-inline-block">
                <a href="/home/dashboard.php" class="nav-link" id="homeLink">Home</a>
            </li>
        </ul>

        <!-- Right navbar links -->
        <ul class="navbar-nav ml-auto">
            <!-- Search -->
            <li class="nav-item">
                <a class="nav-link" data-widget="navbar-search" href="#" role="button">
                    <i class="fas fa-search"></i>
                </a>
                <div class="navbar-search-block">
                    <form class="form-inline" action="/home/pages/liberaciones/buscar.php" method="GET">
                        <div class="input-group input-group-sm">
                            <input class="form-control form-control-navbar" name="search" type="search" placeholder="Buscar" aria-label="Search">
                            <div class="input-group-append">
                                <button class="btn btn-navbar" type="submit">
                                    <i class="fas fa-search"></i>
                                </button>
                                <button class="btn btn-navbar" type="button" data-widget="navbar-search">
                                    <i class="fas fa-times"></i>
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </li>
            <!-- Dark Mode Switch (afecta header y sidebar) -->
            <li class="nav-item d-flex align-items-center">
                <div class="custom-control custom-switch">
                    <input type="checkbox" class="custom-control-input" id="darkModeSwitch">
                    <label class="custom-control-label" for="darkModeSwitch" style="cursor:pointer;">Modo Oscuro</label>
                </div>
            </li>
            <!-- Logout -->
            <li class="nav-item">
                <a class="nav-link" href="/home/logica/salir.php" role="button">
                    <i class="fas fa-sign-out-alt"></i>
                </a>
            </li>
            <!-- Fullscreen -->
            <li class="nav-item">
                <a class="nav-link" data-widget="fullscreen" href="#" role="button">
                    <i class="fas fa-expand-arrows-alt"></i>
                </a>
            </li>
        </ul>
    </nav>

    <!-- Sidebar -->
    <aside class="main-sidebar">
        <!-- Brand Logo -->
        <a href="/home/dashboard" class="brand-link text-center">
            <!-- Se utiliza la misma imagen del usuario para el logo -->
            <img src="/home/dist/img/user2-160x160.jpg" alt="iThink Logo" class="brand-image img-circle elevation-3" style="opacity: .9">
            <span class="brand-text font-weight-bold" id="siteTitle" style="font-size:1.3rem; letter-spacing: 1px; text-transform: uppercase;">iThink Web</span>
        </a>

        <!-- Sidebar -->
        <div class="sidebar">
            <!-- User Panel -->
            <div class="user-panel mt-3 pb-3 mb-3 d-flex align-items-center">
                <a href="#" data-toggle="modal" data-target="#modal_user" class="d-flex align-items-center">
                    <div class="image">
                        <img src="/home/dist/img/user2-160x160.jpg" class="img-circle elevation-2" alt="User Image">
                    </div>
                    <div class="info ml-2">
                        <span class="d-block" id="username"><?php echo $User; ?></span>
                    </div>
                </a>
            </div>

            <!-- Sidebar Menu -->
            <nav class="mt-2">
                <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">
                    <!-- Dashboard -->
                    <li class="nav-item">
                        <a href="/home/dashboard.php" class="nav-link active">
                            <i class="nav-icon fas fa-tachometer-alt"></i>
                            <p>Dashboard</p>
                        </a>
                    </li>

                    <!-- Liberaciones -->
                    <li class="nav-item has-treeview">
                        <a href="#" class="nav-link">
                            <i class="nav-icon fas fa-file-alt"></i>
                            <p>
                                Liberaciones
                                <i class="fas fa-angle-left right"></i>
                            </p>
                        </a>
                        <ul class="nav nav-treeview">
                            <li class="nav-item">
                                <a href="/home/pages/liberaciones/crear.php" class="nav-link">
                                    <i class="fas fa-plus nav-icon"></i>
                                    <p>Crear Consulta</p>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="/home/pages/liberaciones/consultas.php" class="nav-link">
                                    <i class="fas fa-question-circle nav-icon"></i>
                                    <p>Consultas</p>
                                    <span class="badge badge-info right"><?php echo $con; ?></span>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="/home/pages/liberaciones/pendientes.php" class="nav-link">
                                    <i class="fas fa-exclamation-triangle nav-icon"></i>
                                    <p>Pendientes</p>
                                    <span class="badge badge-danger right"><?php echo $pen; ?></span>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="/home/pages/liberaciones/aprobadas.php" class="nav-link">
                                    <i class="fas fa-check-circle nav-icon"></i>
                                    <p>Aprobadas</p>
                                    <span class="badge badge-success right"><?php echo $apro; ?></span>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="/home/pages/liberaciones/finalizadas.php" class="nav-link">
                                    <i class="fas fa-handshake nav-icon"></i>
                                    <p>Finalizadas</p>
                                    <span class="badge badge-warning right"><?php echo $fin; ?></span>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="/home/pages/liberaciones/rechazadas.php" class="nav-link">
                                    <i class="fas fa-times-circle nav-icon"></i>
                                    <p>Rechazadas</p>
                                    <span class="badge badge-danger right"><?php echo $recha; ?></span>
                                </a>
                            </li>
                        </ul>
                    </li>

                    <!-- iCloud (Permisos) -->
                    <?php if(Tiene_permiso($permisos_user, 'ver-icloud')) { ?>
                    <li class="nav-item has-treeview">
                        <a href="/home/pages/permisos_view.php" class="nav-link">
                            <i class="nav-icon fas fa-cloud"></i>
                            <p>
                                iCloud
                                <i class="fas fa-angle-left right"></i>
                            </p>
                        </a>
                        <ul class="nav nav-treeview">
                            <li class="nav-item">
                                <a href="/home/pages/guardar_icloud.php" class="nav-link">
                                    <i class="fas fa-cloud-upload-alt nav-icon"></i>
                                    <p>Guardar iCloud</p>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="/home/pages/admin_permisos.php" class="nav-link">
                                    <i class="fas fa-envelope nav-icon"></i>
                                    <p>Enviar Correo a iCloud</p>
                                </a>
                            </li>
                        </ul>
                    </li>
                    <?php } ?>

                    <!-- Clientes -->
                    <?php if(Tiene_permiso($permisos_user, 'ver-garantia')) { ?>
                    <li class="nav-item has-treeview">
                        <a href="#" class="nav-link">
                            <i class="nav-icon fas fa-shield-alt"></i>
                            <p>
                                Clientes
                                <i class="fas fa-angle-left right"></i>
                            </p>
                        </a>
                        <ul class="nav nav-treeview">
                            <li class="nav-item">
                                <a href="/home/pages/clientes/clientes.php" class="nav-link">
                                    <i class="fas fa-file-alt nav-icon"></i>
                                    <p>Lista de Clientes</p>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="/home/pages/clientes/seguimiento.php" class="nav-link">
                                    <i class="fas fa-file-alt nav-icon"></i>
                                    <p>Seguimientos de Clientes</p>
                                </a>
                            </li>
                        </ul>
                    </li>
                    <?php } ?>

                    <!-- Garantías -->
                    <?php if(Tiene_permiso($permisos_user, 'ver-garantia')) { ?>
                    <li class="nav-item has-treeview">
                        <a href="#" class="nav-link">
                            <i class="nav-icon fas fa-shield-alt"></i>
                            <p>
                                Garantías
                                <i class="fas fa-angle-left right"></i>
                            </p>
                        </a>
                        <ul class="nav nav-treeview">
                            <li class="nav-item">
                                <a href="/home/pages/form_garantia.php" class="nav-link">
                                    <i class="fas fa-file-alt nav-icon"></i>
                                    <p>Iniciar Garantía</p>
                                </a>
                            </li>
                        </ul>
                    </li>
                    <?php } ?>

                    <!-- Permisos -->
                    <?php if(Tiene_permiso($permisos_user, 'ver_permiso')) { ?>
                    <li class="nav-item has-treeview">
                        <a href="/home/pages/permisos_view.php" class="nav-link">
                            <i class="nav-icon fas fa-user-shield"></i>
                            <p>
                                Permisos
                                <i class="fas fa-angle-left right"></i>
                            </p>
                        </a>
                        <ul class="nav nav-treeview">
                            <li class="nav-item">
                                <a href="/home/pages/permisos_view.php" class="nav-link">
                                    <i class="fas fa-users nav-icon"></i>
                                    <p>Permisos para Grupos</p>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="/home/pages/admin_permisos.php" class="nav-link">
                                    <i class="fas fa-user-cog nav-icon"></i>
                                    <p>Administrar Permisos</p>
                                </a>
                            </li>
                        </ul>
                    </li>
                    <?php } ?>


                    <!-- Empleados -->
                    <li class="nav-item has-treeview">
                        <a href="#" class="nav-link">
                            <i class="nav-icon fas fa-user"></i>
                            <p>
                                Empleados
                                <i class="fas fa-angle-left right"></i>
                            </p>
                        </a>
                        <ul class="nav nav-treeview">
                            <li class="nav-item">
                                <a href="/home/pages/empleados/lista.php" class="nav-link">
                                    <i class="fas fa-list nav-icon"></i>
                                    <p>Listado de Empleados</p>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="/home/pages/empleados/nuevo.php" class="nav-link">
                                    <i class="fas fa-user-plus nav-icon"></i>
                                    <p>Nuevo Empleado</p>
                                </a>
                            </li>
                        </ul>
                    </li>

                    <!-- Horarios -->
                    <li class="nav-item has-treeview">
                        <a href="#" class="nav-link">
                            <i class="nav-icon fas fa-clock"></i>
                            <p>
                                Horarios
                                <i class="fas fa-angle-left right"></i>
                            </p>
                        </a>
                        <ul class="nav nav-treeview">
                            <li class="nav-item">
                                <a href="/home/pages/horarios/configurar.php" class="nav-link">
                                    <i class="fas fa-cog nav-icon"></i>
                                    <p>Configurar Horarios</p>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="/home/pages/horarios/marcar.php" class="nav-link">
                                    <i class="fas fa-edit nav-icon"></i>
                                    <p>Marcar Asistencia</p>
                                </a>
                            </li>
                        </ul>
                    </li>

                    <!-- Tares -->
                    <li class="nav-item has-treeview">
                        <a href="#" class="nav-link">
                            <i class="nav-icon fas fa-tasks"></i>
                            <p>
                                Tares
                                <i class="fas fa-angle-left right"></i>
                            </p>
                        </a>
                        <ul class="nav nav-treeview">
                            <li class="nav-item">
                                <a href="/home/pages/tares/lista.php" class="nav-link">
                                    <i class="fas fa-list nav-icon"></i>
                                    <p>Listado de Tares</p>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="/home/pages/tares/nuevo.php" class="nav-link">
                                    <i class="fas fa-plus nav-icon"></i>
                                    <p>Nueva Tarea</p>
                                </a>
                            </li>
                        </ul>
                    </li>

                    <!-- Usuarios -->
                    <li class="nav-item has-treeview">
                        <a href="#" class="nav-link">
                            <i class="nav-icon fas fa-users-cog"></i>
                            <p>
                                Usuarios
                                <i class="fas fa-angle-left right"></i>
                            </p>
                        </a>
                        <ul class="nav nav-treeview">
                            <li class="nav-item">
                                <a href="/home/pages/usuarios/index.php" class="nav-link">
                                    <i class="fas fa-list nav-icon"></i>
                                    <p>Listado de Usuarios</p>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="/home/pages/usuarios/nuevo.php" class="nav-link">
                                    <i class="fas fa-user-plus nav-icon"></i>
                                    <p>Nuevo Usuario</p>
                                </a>
                            </li>
                        </ul>
                    </li>

                </ul>
            </nav>
        </div>
    </aside>
</div> <!-- Fin del contenedor principal -->

<?php include('modal_perfil.php'); ?>

<!-- JavaScript -->
<!-- jQuery -->
<script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
<!-- Bootstrap JS -->
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
<!-- Script para el menú Sidebar con animaciones y Dark Mode para header y sidebar -->
<script>
  $(document).ready(function() {
      // Toggle submenús
      $('.nav-item.has-treeview > a').on('click', function(e) {
          e.preventDefault();
          var $parent = $(this).parent();
          var $submenu = $parent.find('ul.nav-treeview').first();
          if ($parent.hasClass('menu-open')) {
              $submenu.slideUp(300, function() {
                  $parent.removeClass('menu-open');
              });
          } else {
              // Cerrar otros submenús abiertos
              $('.nav-item.has-treeview.menu-open').not($parent).each(function() {
                  $(this).find('ul.nav-treeview').slideUp(300, function() {
                      $(this).closest('.nav-item').removeClass('menu-open');
                  });
              });
              $submenu.slideDown(300, function() {
                  $parent.addClass('menu-open');
              });
          }
      });

      // Verificar en localStorage la preferencia de Dark Mode
      if (localStorage.getItem('darkMode') === 'true') {
          $('.main-header, .main-sidebar').addClass('dark-mode');
          $('#darkModeSwitch').prop('checked', true);
      }

      // Dark Mode Switch: Solo afecta header y sidebar
      $('#darkModeSwitch').on('change', function() {
          if ($(this).is(':checked')) {
              $('.main-header, .main-sidebar').addClass('dark-mode');
              localStorage.setItem('darkMode', 'true');
          } else {
              $('.main-header, .main-sidebar').removeClass('dark-mode');
              localStorage.setItem('darkMode', 'false');
          }
      });
  });
</script>

<!-- Estilos para Light y Dark Mode (solo header y sidebar cambian de tema) -->
<style>
  /* Light Mode (por defecto) */
  .main-header {
      background-color: #ffffff;
      color:  #515a5a;
  }
  .main-sidebar {
      background-color: #ffffff;
      color:  #515a5a;
  }
  .main-header .nav-link,
  .main-sidebar .nav-link {
      color:  #515a5a !important;
  }
  .main-header .nav-link:hover,
  .main-sidebar .nav-link:hover {
      background-color: #e6e6e6;
      color:  #515a5a !important;
  }
  
  /* Dark Mode para header y sidebar */
  .main-header.dark-mode {
      background-color: #2c2c2c !important;
      color: #ffffff;
  }
  .main-header.dark-mode .nav-link,
  .main-header.dark-mode #homeLink {
      color: #ffffff !important;
  }
  .main-header.dark-mode .nav-link:hover,
  .main-header.dark-mode #homeLink:hover {
      background-color: #444444 !important;
      color: #ffffff !important;
  }
  .main-sidebar.dark-mode {
      background-color: #2c2c2c !important;
      color: #ffffff;
  }
  .main-sidebar.dark-mode .nav-link {
      color: #ffffff !important;
  }
  .main-sidebar.dark-mode .nav-link:hover {
      background-color: #444444 !important;
      color: #ffffff !important;
  }
  /* Ajuste para el nombre de usuario en el panel (modo oscuro) */
  .main-sidebar.dark-mode .user-panel .info span {
      color: #ffcc00 !important;
  }
  /* Ajuste del título del sitio */
  .brand-link .brand-text {
      font-size: 1.3rem;
      letter-spacing: 1px;
      text-transform: uppercase;
      color:  #515a5a !important;
  }
  .main-sidebar.dark-mode .brand-link .brand-text {
      color: #ffffff !important;
  }
  /* Estilos para la pestaña activa */
  .nav-sidebar .nav-link.active {
      background-color: #007bff !important;
      color: #ffffff !important;
  }
</style>
