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
                    <form class="form-inline" action="/home/pages/liberaciones/buscar" method="GET">

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

                    <!-- PRINCIPAL -->
                    <li class="nav-header">PRINCIPAL</li>
                    <li class="nav-item">
                        <a href="/home/dashboard.php" class="nav-link <?php echo ($_SERVER['PHP_SELF'] === '/home/dashboard.php') ? 'active' : ''; ?>">
                            <i class="nav-icon fas fa-tachometer-alt"></i>
                            <p>Dashboard</p>
                        </a>
                    </li>
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
                                    <i class="fas fa-plus nav-icon"></i><p>Crear Consulta</p>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="/home/pages/liberaciones/consultas.php" class="nav-link">
                                    <i class="fas fa-question-circle nav-icon"></i><p>Consultas</p>
                                    <span class="badge badge-info right"><?php echo $con; ?></span>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="/home/pages/liberaciones/pendientes.php" class="nav-link">
                                    <i class="fas fa-exclamation-triangle nav-icon"></i><p>Pendientes</p>
                                    <span class="badge badge-danger right"><?php echo $pen; ?></span>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="/home/pages/liberaciones/aprobadas.php" class="nav-link">
                                    <i class="fas fa-check-circle nav-icon"></i><p>Aprobadas</p>
                                    <span class="badge badge-success right"><?php echo $apro; ?></span>
                                </a>
                            </li>
                        </ul>
                    </li>

                    <?php if(Tiene_permiso($permisos_user, 'ver-guias')) { ?>
                    <li class="nav-item has-treeview">
                        <a href="#" class="nav-link">
                            <i class="nav-icon fas fa-shipping-fast"></i>
                            <p>Guías <i class="fas fa-angle-left right"></i></p>
                        </a>
                        <ul class="nav nav-treeview">
                            <?php if(Tiene_permiso($permisos_user, 'generar-guia')) { ?>
                            <li class="nav-item">
                                <a href="/home/pages/guias/generar.php" class="nav-link">
                                    <i class="fas fa-plus nav-icon"></i><p>Generar Guía</p>
                                </a>
                            </li>
                            <?php } ?>
                            <?php if(Tiene_permiso($permisos_user, 'ver-listado-guias')) { ?>
                            <li class="nav-item">
                                <a href="/home/pages/guias/listado_guias.php" class="nav-link">
                                    <i class="fas fa-list nav-icon"></i><p>Listado de Guías</p>
                                </a>
                            </li>
                            <?php } ?>
                        </ul>
                    </li>
                    <?php } ?>

                    <!-- OPERACIONES -->
                    <li class="nav-header">OPERACIONES</li>
                    <?php if(Tiene_permiso($permisos_user, 'crear-venta') || Tiene_permiso($permisos_user, 'editar-venta') || Tiene_permiso($permisos_user, 'eliminar-venta')) { ?>
                    <li class="nav-item has-treeview">
                        <a href="#" class="nav-link">
                            <i class="nav-icon fas fa-dollar-sign"></i>
                            <p>Ventas <i class="fas fa-angle-left right"></i></p>
                        </a>
                        <ul class="nav nav-treeview">
                            <?php if(Tiene_permiso($permisos_user, 'crear-venta')) { ?>
                            <li class="nav-item">
                                <a href="/home/pages/ventas/crear.php" class="nav-link"><i class="fas fa-plus nav-icon"></i><p>Crear Venta</p></a>
                            </li>
                            <?php } ?>
                            <?php if(Tiene_permiso($permisos_user, 'editar-venta')) { ?>
                            <li class="nav-item">
                                <a href="/home/pages/ventas/editar.php" class="nav-link"><i class="fas fa-edit nav-icon"></i><p>Editar Venta</p></a>
                            </li>
                            <?php } ?>
                        </ul>
                    </li>
                    <?php } ?>

                    <?php if(Tiene_permiso($permisos_user, 'crear-compra') || Tiene_permiso($permisos_user, 'editar-compra') || Tiene_permiso($permisos_user, 'eliminar-compra')) { ?>
                    <li class="nav-item has-treeview">
                        <a href="#" class="nav-link">
                            <i class="nav-icon fas fa-shopping-cart"></i>
                            <p>Compras <i class="fas fa-angle-left right"></i></p>
                        </a>
                        <ul class="nav nav-treeview">
                            <?php if(Tiene_permiso($permisos_user, 'crear-compra')) { ?>
                            <li class="nav-item">
                                <a href="/home/pages/compras/crear.php" class="nav-link"><i class="fas fa-plus nav-icon"></i><p>Crear Compra</p></a>
                            </li>
                            <?php } ?>
                            <?php if(Tiene_permiso($permisos_user, 'editar-compra')) { ?>
                            <li class="nav-item">
                                <a href="/home/pages/compras/editar.php" class="nav-link"><i class="fas fa-edit nav-icon"></i><p>Editar Compra</p></a>
                            </li>
                            <?php } ?>
                        </ul>
                    </li>
                    <?php } ?>

                    <?php if(Tiene_permiso($permisos_user, 'ver-inventario') || Tiene_permiso($permisos_user, 'modificar-inventario')) { ?>
                    <li class="nav-item has-treeview">
                        <a href="#" class="nav-link">
                            <i class="nav-icon fas fa-boxes"></i>
                            <p>Inventario <i class="fas fa-angle-left right"></i></p>
                        </a>
                        <ul class="nav nav-treeview">
                            <?php if(Tiene_permiso($permisos_user, 'ver-inventario')) { ?>
                            <li class="nav-item">
                                <a href="/home/pages/inventario/ver.php" class="nav-link"><i class="fas fa-eye nav-icon"></i><p>Ver Inventario</p></a>
                            </li>
                            <?php } ?>
                            <?php if(Tiene_permiso($permisos_user, 'modificar-inventario')) { ?>
                            <li class="nav-item">
                                <a href="/home/pages/inventario/modificar.php" class="nav-link"><i class="fas fa-edit nav-icon"></i><p>Modificar Inventario</p></a>
                            </li>
                            <?php } ?>
                        </ul>
                    </li>
                    <?php } ?>

                    <!-- PERSONAS -->
                    <li class="nav-header">PERSONAS</li>
                    <?php if(Tiene_permiso($permisos_user, 'ver-garantia')) { ?>
                    <li class="nav-item has-treeview">
                        <a href="#" class="nav-link">
                            <i class="nav-icon fas fa-shield-alt"></i>
                            <p>Clientes <i class="fas fa-angle-left right"></i></p>
                        </a>
                        <ul class="nav nav-treeview">
                            <li class="nav-item">
                                <a href="/home/pages/clientes/clientes.php" class="nav-link"><i class="fas fa-file-alt nav-icon"></i><p>Lista de Clientes</p></a>
                            </li>
                            <li class="nav-item">
                                <a href="/home/pages/clientes/seguimiento.php" class="nav-link"><i class="fas fa-file-alt nav-icon"></i><p>Seguimiento</p></a>
                            </li>
                        </ul>
                    </li>
                    <?php } ?>

                    <?php if(Tiene_permiso($permisos_user, 'ver-reparaciones')) { ?>
                    <li class="nav-item has-treeview">
                        <a href="#" class="nav-link">
                            <i class="nav-icon fas fa-tools"></i>
                            <p>Reparaciones <i class="fas fa-angle-left right"></i></p>
                        </a>
                        <ul class="nav nav-treeview">
                            <li class="nav-item">
                                <a href="/home/pages/reparaciones/listado_reparaciones.php" class="nav-link"><i class="fas fa-list nav-icon"></i><p>Listado de Reparaciones</p></a>
                            </li>
                        </ul>
                    </li>
                    <?php } ?>

                    <?php if(Tiene_permiso($permisos_user, 'ver-garantia')) { ?>
                    <li class="nav-item">
                        <a href="/home/pages/form_garantia.php" class="nav-link">
                            <i class="nav-icon fas fa-file-alt"></i><p>Iniciar Garantía</p>
                        </a>
                    </li>
                    <?php } ?>

                    <?php if(Tiene_permiso($permisos_user, 'ver-empleados')) { ?>
                    <li class="nav-item has-treeview">
                        <a href="#" class="nav-link">
                            <i class="nav-icon fas fa-user"></i><p>Empleados <i class="fas fa-angle-left right"></i></p>
                        </a>
                        <ul class="nav nav-treeview">
                            <li class="nav-item">
                                <a href="/home/pages/empleados/lista.php" class="nav-link"><i class="fas fa-list nav-icon"></i><p>Listado</p></a>
                            </li>
                            <li class="nav-item">
                                <a href="/home/pages/empleados/nuevo.php" class="nav-link"><i class="fas fa-user-plus nav-icon"></i><p>Nuevo</p></a>
                            </li>
                        </ul>
                    </li>
                    <?php } ?>

                    <?php if(Tiene_permiso($permisos_user, 'ver-usuarios')) { ?>
                    <li class="nav-item">
                        <a href="/home/pages/usuarios/listado_users.php" class="nav-link">
                            <i class="nav-icon fas fa-users-cog"></i><p>Usuarios</p>
                        </a>
                    </li>
                    <?php } ?>

                    <!-- HERRAMIENTAS -->
                    <li class="nav-header">HERRAMIENTAS</li>
                    <?php if(Tiene_permiso($permisos_user, 'ver-tareas')) { ?>
                    <li class="nav-item has-treeview">
                        <a href="#" class="nav-link"><i class="nav-icon fas fa-tasks"></i><p>Tareas <i class="fas fa-angle-left right"></i></p></a>
                        <ul class="nav nav-treeview">
                            <?php if(Tiene_permiso($permisos_user, 'crear-tarea')) { ?>
                            <li class="nav-item"><a href="/home/pages/tares/crear.php" class="nav-link"><i class="fas fa-plus nav-icon"></i><p>Crear</p></a></li>
                            <?php } ?>
                            <?php if(Tiene_permiso($permisos_user, 'ver-tareas')) { ?>
                            <li class="nav-item"><a href="/home/pages/tares/lista.php" class="nav-link"><i class="fas fa-list nav-icon"></i><p>Lista</p></a></li>
                            <?php } ?>
                        </ul>
                    </li>
                    <?php } ?>

                    <?php if(Tiene_permiso($permisos_user, 'ver-correos')) { ?>
                    <li class="nav-item has-treeview">
                        <a href="#" class="nav-link"><i class="nav-icon fas fa-envelope"></i><p>Correos <i class="fas fa-angle-left right"></i></p></a>
                        <ul class="nav nav-treeview">
                            <?php if(Tiene_permiso($permisos_user, 'asignar-correos')) { ?>
                            <li class="nav-item"><a href="/home/pages/correos/asignacion_correos.php" class="nav-link"><i class="fas fa-user-plus nav-icon"></i><p>Asignación</p></a></li>
                            <?php } ?>
                            <?php if(Tiene_permiso($permisos_user, 'ver-tipos-correo')) { ?>
                            <li class="nav-item"><a href="/home/pages/correos/tipos_correo.php" class="nav-link"><i class="fas fa-tags nav-icon"></i><p>Tipos</p></a></li>
                            <?php } ?>
                        </ul>
                    </li>
                    <?php } ?>

                    <?php if(Tiene_permiso($permisos_user, 'ver-reportes') || Tiene_permiso($permisos_user, 'exportar-reportes')) { ?>
                    <li class="nav-item has-treeview">
                        <a href="#" class="nav-link"><i class="nav-icon fas fa-chart-line"></i><p>Reportes <i class="fas fa-angle-left right"></i></p></a>
                        <ul class="nav nav-treeview">
                            <?php if(Tiene_permiso($permisos_user, 'ver-reportes')) { ?>
                            <li class="nav-item"><a href="/home/pages/reportes/ver.php" class="nav-link"><i class="fas fa-eye nav-icon"></i><p>Ver Reportes</p></a></li>
                            <?php } ?>
                            <?php if(Tiene_permiso($permisos_user, 'exportar-reportes')) { ?>
                            <li class="nav-item"><a href="/home/pages/reportes/exportar.php" class="nav-link"><i class="fas fa-download nav-icon"></i><p>Exportar</p></a></li>
                            <?php } ?>
                        </ul>
                    </li>
                    <?php } ?>

                    <!-- ADMIN -->
                    <li class="nav-header">ADMINISTRACIÓN</li>
                    <?php if(Tiene_permiso($permisos_user, 'ver_permiso')) { ?>
                    <li class="nav-item has-treeview">
                        <a href="#" class="nav-link"><i class="nav-icon fas fa-user-shield"></i><p>Permisos <i class="fas fa-angle-left right"></i></p></a>
                        <ul class="nav nav-treeview">
                            <li class="nav-item"><a href="/home/pages/permisos_view.php" class="nav-link"><i class="fas fa-users nav-icon"></i><p>Permisos para Grupos</p></a></li>
                            <li class="nav-item"><a href="/home/pages/admin_permisos.php" class="nav-link"><i class="fas fa-user-cog nav-icon"></i><p>Administrar Permisos</p></a></li>
                        </ul>
                    </li>
                    <?php } ?>

                    <?php if(Tiene_permiso($permisos_user, 'ver-icloud')) { ?>
                    <li class="nav-item">
                        <a href="/home/pages/permisos_view.php" class="nav-link"><i class="nav-icon fas fa-cloud"></i><p>iCloud</p></a>
                    </li>
                    <?php } ?>

                    <?php if(Tiene_permiso($permisos_user, 'modificar-configuracion') || Tiene_permiso($permisos_user, 'gestionar-roles') || Tiene_permiso($permisos_user, 'gestionar-permisos')) { ?>
                    <li class="nav-item has-treeview">
                        <a href="#" class="nav-link"><i class="nav-icon fas fa-cogs"></i><p>Configuración <i class="fas fa-angle-left right"></i></p></a>
                        <ul class="nav nav-treeview">
                            <?php if(Tiene_permiso($permisos_user, 'modificar-configuracion')) { ?>
                            <li class="nav-item"><a href="/home/pages/configuracion/modificar.php" class="nav-link"><i class="fas fa-edit nav-icon"></i><p>Modificar</p></a></li>
                            <?php } ?>
                            <?php if(Tiene_permiso($permisos_user, 'gestionar-roles')) { ?>
                            <li class="nav-item"><a href="/home/pages/configuracion/roles.php" class="nav-link"><i class="fas fa-users nav-icon"></i><p>Roles</p></a></li>
                            <?php } ?>
                        </ul>
                    </li>
                    <?php } ?>

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
      // Inicializar ids para persistencia
      $('.nav-item.has-treeview').each(function(index) {
          $(this).attr('data-menu-id', 'menu-' + index);
      });

      // Añadir toggles accesibles a cada item con submenú
      $('.nav-item.has-treeview').each(function() {
          var $item = $(this);
          var $link = $item.children('a').first();

          // Crear botón toggle solo si no existe
          if ($link.find('.tree-toggle').length === 0) {
              var $toggle = $('<button>', {
                  'class': 'tree-toggle',
                  'type': 'button',
                  'aria-expanded': 'false',
                  'aria-label': 'Expandir menú'
              }).html('<i class="fas fa-angle-left"></i>');
              // insertar al final del enlace para posicionar a la derecha
              $link.append($toggle);
          }
      });

      // Cargar estado de menús abiertos desde localStorage
      var openMenus = JSON.parse(localStorage.getItem('openMenus') || '[]');
      openMenus.forEach(function(id) {
          var $m = $('.nav-item.has-treeview[data-menu-id="' + id + '"]');
          if ($m.length) {
              $m.addClass('menu-open');
              $m.find('ul.nav-treeview').show();
              $m.find('.tree-toggle').attr('aria-expanded', 'true');
          }
      });

      // Detección de la ruta actual para marcar link activo y abrir padres
      var path = window.location.pathname;
      $('a.nav-link').each(function() {
          var href = $(this).attr('href');
          if (href && href !== '#' && path.indexOf(href) !== -1) {
              $(this).addClass('active');
              var $parent = $(this).closest('.nav-item.has-treeview');
              $parent.addClass('menu-open');
              $parent.find('ul.nav-treeview').show();
              $parent.find('.tree-toggle').attr('aria-expanded', 'true');
              // asegurar que quede guardado en openMenus
              var id = $parent.attr('data-menu-id');
              if (id && openMenus.indexOf(id) === -1) {
                  openMenus.push(id);
              }
          }
      });
      localStorage.setItem('openMenus', JSON.stringify(openMenus));

      // Función para actualizar localStorage cuando se abren/cerran menús
      function updateOpenMenus(id, opened) {
          var arr = JSON.parse(localStorage.getItem('openMenus') || '[]');
          if (opened) {
              if (arr.indexOf(id) === -1) arr.push(id);
          } else {
              var i = arr.indexOf(id);
              if (i !== -1) arr.splice(i, 1);
          }
          localStorage.setItem('openMenus', JSON.stringify(arr));
      }

      // Manejo de clicks en toggles (accesible)
      $(document).on('click', '.tree-toggle', function(e) {
          e.stopPropagation();
          var $btn = $(this);
          var $parent = $btn.closest('.nav-item');
          var $submenu = $parent.find('ul.nav-treeview').first();
          var id = $parent.attr('data-menu-id');
          if ($parent.hasClass('menu-open')) {
              $submenu.slideUp(200, function() {
                  $parent.removeClass('menu-open');
                  $btn.attr('aria-expanded', 'false');
                  if (id) updateOpenMenus(id, false);
              });
          } else {
              // cerrar otros abiertos
              $('.nav-item.has-treeview.menu-open').not($parent).each(function() {
                  var $other = $(this);
                  $other.find('ul.nav-treeview').slideUp(200, function() {
                      $other.removeClass('menu-open');
                      $other.find('.tree-toggle').attr('aria-expanded', 'false');
                      updateOpenMenus($other.attr('data-menu-id'), false);
                  });
              });
              $submenu.slideDown(200, function() {
                  $parent.addClass('menu-open');
                  $btn.attr('aria-expanded', 'true');
                  if (id) updateOpenMenus(id, true);
              });
          }
      });

      // Click en el enlace principal: si href === '#' toggle, si tiene ruta real, navegar
      $('.nav-item.has-treeview > a.nav-link').on('click', function(e) {
          var href = $(this).attr('href');
          // si es un enlace 'placeholder', evitar navegar y alternar
          if (!href || href.trim() === '#' ) {
              e.preventDefault();
              $(this).find('.tree-toggle').trigger('click');
          }
      });

      // Navegación por teclado: Enter / Space para toggles, flechas para moverse
      $(document).on('keydown', '.tree-toggle, .nav-link', function(e) {
          var $focused = $(this);
          if (e.key === 'Enter' || e.key === ' ') {
              e.preventDefault();
              if ($focused.hasClass('tree-toggle')) {
                  $focused.trigger('click');
              } else if ($focused.is('a.nav-link')) {
                  $focused.trigger('click');
              }
          } else if (e.key === 'ArrowDown' || e.key === 'ArrowUp') {
              e.preventDefault();
              // crear lista de elementos navegables
              var $items = $('.nav-sidebar .nav-link:visible');
              var idx = $items.index($focused);
              if (e.key === 'ArrowDown' && idx < $items.length - 1) {
                  $items.eq(idx + 1).focus();
              } else if (e.key === 'ArrowUp' && idx > 0) {
                  $items.eq(idx - 1).focus();
              }
          } else if (e.key === 'ArrowRight') {
              // abrir menú si existe
              if ($(this).closest('.nav-item.has-treeview').length) {
                  $(this).closest('.nav-item').find('.tree-toggle').first().trigger('click');
              }
          } else if (e.key === 'ArrowLeft') {
              // cerrar menú si existe
              var $parentMenu = $(this).closest('.nav-item.has-treeview.menu-open');
              if ($parentMenu.length) {
                  $parentMenu.find('.tree-toggle').first().trigger('click');
              }
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

  /* Estilos para el botón toggle que añade control visual sin romper el layout */
  .tree-toggle {
      background: transparent;
      border: none;
      color: inherit;
      float: right;
      width: 28px;
      height: 28px;
      display: inline-flex;
      align-items: center;
      justify-content: center;
      cursor: pointer;
      padding: 0;
      margin-left: 8px;
  }
  .tree-toggle:focus {
      outline: 2px solid #80bdff;
      outline-offset: 2px;
  }
  .tree-toggle i {
      transition: transform 0.2s ease;
  }
  .nav-item.menu-open > a .tree-toggle i {
      transform: rotate(-90deg); /* indicar abierto */
  }

  /* Ocultar el icono toggle cuando la barra lateral está colapsada / en modo mini (AdminLTE) */
  .sidebar-mini .nav-sidebar .tree-toggle,
  body.sidebar-collapse .nav-sidebar .tree-toggle {
      display: none !important;
      pointer-events: none;
  }

  /* Pequeño ajuste para que los enlaces puedan recibir focus claramente */
  .nav-sidebar .nav-link {
      outline: none;
  }
  .nav-sidebar .nav-link:focus {
      background-color: rgba(0,123,255,0.1);
      color: inherit !important;
  }

  /* Mantener el comportamiento visual previo para active */
  .nav-sidebar .nav-link.active {
      background-color: #007bff !important;
      color: #ffffff !important;
  }
</style>
