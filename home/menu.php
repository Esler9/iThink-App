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
                    <!-- Dashboard (siempre primero) -->
                    <li class="nav-item">
                        <a href="/home/dashboard.php" class="nav-link" id="menu-dashboard">
                            <i class="nav-icon fas fa-tachometer-alt"></i>
                            <p>Dashboard</p>
                        </a>
                    </li>

                    <!-- Grupo: Servicio Técnico -->
                    <?php /* Incluye reparaciones, garantías, liberaciones, tareas, horarios e iCloud */ ?>
                    <?php if(Tiene_permiso($permisos_user, 'ver-reparaciones') || Tiene_permiso($permisos_user, 'ver-garantia') || true) { ?>
                    <li class="nav-item has-treeview">
                        <a href="#" class="nav-link">
                            <i class="nav-icon fas fa-tools"></i>
                            <p>
                                Servicio Técnico
                                <i class="fas fa-angle-left right"></i>
                            </p>
                        </a>
                        <ul class="nav nav-treeview">
                            <?php if(Tiene_permiso($permisos_user, 'ver-reparaciones')) { ?>
                            <li class="nav-item">
                                <a href="/home/pages/reparaciones/listado_reparaciones.php" class="nav-link">
                                    <i class="fas fa-list nav-icon"></i>
                                    <p>Listado de Reparaciones</p>
                                </a>
                            </li>
                            <?php } ?>
                            <?php if(Tiene_permiso($permisos_user, 'ver-garantia')) { ?>
                            <li class="nav-item">
                                <a href="/home/pages/form_garantia.php" class="nav-link">
                                    <i class="fas fa-file-alt nav-icon"></i>
                                    <p>Iniciar Garantía</p>
                                </a>
                            </li>
                            <?php } ?>

                            <!-- Liberaciones (alto uso) -->
                            <li class="nav-item has-treeview">
                                <a href="#" class="nav-link">
                                    <i class="fas fa-file-alt nav-icon"></i>
                                    <p>Liberaciones <i class="fas fa-angle-left right"></i></p>
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

                            <!-- Tareas -->
                            <?php if(Tiene_permiso($permisos_user, 'ver-tareas')) { ?>
                            <li class="nav-item">
                                <a href="/home/pages/tares/lista.php" class="nav-link">
                                    <i class="fas fa-tasks nav-icon"></i>
                                    <p>Tareas</p>
                                </a>
                            </li>
                            <?php } ?>

                            <!-- Horarios -->
                            <?php if(Tiene_permiso($permisos_user, 'ver-horario')) { ?>
                            <li class="nav-item">
                                <a href="/home/pages/horarios/configurar.php" class="nav-link">
                                    <i class="fas fa-clock nav-icon"></i>
                                    <p>Horarios</p>
                                </a>
                            </li>
                            <?php } ?>

                            <!-- iCloud movido aquí -->
                            <?php if(Tiene_permiso($permisos_user, 'ver-icloud')) { ?>
                            <li class="nav-item">
                                <a href="/home/pages/guardar_icloud.php" class="nav-link">
                                    <i class="fas fa-cloud nav-icon"></i>
                                    <p>iCloud</p>
                                </a>
                            </li>
                            <?php } ?>
                        </ul>
                    </li>
                    <?php } ?>

                    <!-- Grupo: Ventas -->
                    <?php if(Tiene_permiso($permisos_user, 'crear-venta') || Tiene_permiso($permisos_user, 'ver-guias') || Tiene_permiso($permisos_user, 'ver-inventario')) { ?>
                    <li class="nav-item has-treeview">
                        <a href="#" class="nav-link">
                            <i class="nav-icon fas fa-dollar-sign"></i>
                            <p>
                                Ventas
                                <i class="fas fa-angle-left right"></i>
                            </p>
                        </a>
                        <ul class="nav nav-treeview">
                            <!-- Ventas -->
                            <?php if(Tiene_permiso($permisos_user, 'crear-venta')) { ?>
                            <li class="nav-item">
                                <a href="/home/pages/ventas/crear.php" class="nav-link">
                                    <i class="fas fa-plus nav-icon"></i>
                                    <p>Crear Venta</p>
                                </a>
                            </li>
                            <?php } ?>
                            <?php if(Tiene_permiso($permisos_user, 'ver-inventario')) { ?>
                            <li class="nav-item">
                                <a href="/home/pages/inventario/ver.php" class="nav-link">
                                    <i class="fas fa-boxes nav-icon"></i>
                                    <p>Inventario</p>
                                </a>
                            </li>
                            <?php } ?>
                            <!-- Guías -->
                            <?php if(Tiene_permiso($permisos_user, 'ver-guias')) { ?>
                            <li class="nav-item">
                                <a href="/home/pages/guias/listado_guias.php" class="nav-link">
                                    <i class="fas fa-shipping-fast nav-icon"></i>
                                    <p>Guías</p>
                                </a>
                            </li>
                            <?php } ?>
                        </ul>
                    </li>
                    <?php } ?>

                    <!-- Grupo: Administración -->
                    <?php if(Tiene_permiso($permisos_user, 'ver-empleados') || Tiene_permiso($permisos_user, 'ver-usuarios') || Tiene_permiso($permisos_user, 'ver_permiso') || Tiene_permiso($permisos_user, 'ver-icloud') || Tiene_permiso($permisos_user, 'ver-correos')) { ?>
                    <li class="nav-item has-treeview">
                        <a href="#" class="nav-link">
                            <i class="nav-icon fas fa-user-tie"></i>
                            <p>
                                Administración
                                <i class="fas fa-angle-left right"></i>
                            </p>
                        </a>
                        <ul class="nav nav-treeview">
                            <!-- Empleados -->
                            <?php if(Tiene_permiso($permisos_user, 'ver-empleados')) { ?>
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
                            <?php } ?>

                            <!-- Usuarios -->
                            <?php if(Tiene_permiso($permisos_user, 'ver-usuarios')) { ?>
                            <li class="nav-item">
                                <a href="/home/pages/usuarios/listado_users.php" class="nav-link">
                                    <i class="fas fa-users-cog nav-icon"></i>
                                    <p>Listado de Usuarios</p>
                                </a>
                            </li>
                            <?php } ?>

                            <!-- Permisos -->
                            <?php if(Tiene_permiso($permisos_user, 'ver_permiso')) { ?>
                            <li class="nav-item">
                                <a href="/home/pages/permisos_view.php" class="nav-link">
                                    <i class="fas fa-user-shield nav-icon"></i>
                                    <p>Permisos Por roles</p>
                                </a>
                            </li>
                            <?php } ?>

                            <!-- iCloud -->
                            <?php if(Tiene_permiso($permisos_user, 'ver-icloud')) { ?>
                            <li class="nav-item">
                                <a href="/home/pages/guardar_icloud.php" class="nav-link">
                                    <i class="fas fa-cloud nav-icon"></i>
                                    <p>iCloud</p>
                                </a>
                            </li>
                            <?php } ?>

                            <!-- Correos -->
                            <?php if(Tiene_permiso($permisos_user, 'ver-correos')) { ?>
                            <li class="nav-item">
                                <a href="/home/pages/correos/asignacion_correos.php" class="nav-link">
                                    <i class="fas fa-envelope nav-icon"></i>
                                    <p>Correos</p>
                                </a>
                            </li>
                            <?php } ?>
                        </ul>
                    </li>
                    <?php } ?>

                    <!-- Grupo: Configuraciones -->
                    <?php if(Tiene_permiso($permisos_user, 'modificar-configuracion') || Tiene_permiso($permisos_user, 'gestionar-roles') || Tiene_permiso($permisos_user, 'gestionar-permisos')) { ?>
                    <li class="nav-item has-treeview">
                        <a href="#" class="nav-link">
                            <i class="nav-icon fas fa-cogs"></i>
                            <p>
                                Configuraciones
                                <i class="fas fa-angle-left right"></i>
                            </p>
                        </a>
                        <ul class="nav nav-treeview">
                            <?php if(Tiene_permiso($permisos_user, 'modificar-configuracion')) { ?>
                            <li class="nav-item">
                                <a href="/home/pages/configuracion/modificar.php" class="nav-link">
                                    <i class="fas fa-edit nav-icon"></i>
                                    <p>Modificar Configuración</p>
                                </a>
                            </li>
                            <?php } ?>
                            <?php if(Tiene_permiso($permisos_user, 'gestionar-roles')) { ?>
                            <li class="nav-item">
                                <a href="/home/pages/configuracion/roles.php" class="nav-link">
                                    <i class="fas fa-users nav-icon"></i>
                                    <p>Gestionar Roles</p>
                                </a>
                            </li>
                            <?php } ?>
                            <?php if(Tiene_permiso($permisos_user, 'gestionar-permisos')) { ?>
                            <li class="nav-item">
                                <a href="/home/pages/admin_permisos.php" class="nav-link">
                                    <i class="fas fa-user-shield nav-icon"></i>
                                    <p>Gestionar Permisos</p>
                                </a>
                            </li>
                            <?php } ?>
                        </ul>
                    </li>
                    <?php } ?>

                    <!-- Nuevo Grupo: Reportes (separado de Configuraciones) -->
                    <?php if(Tiene_permiso($permisos_user, 'ver-reportes') || Tiene_permiso($permisos_user, 'exportar-reportes')) { ?>
                    <li class="nav-item has-treeview">
                        <a href="#" class="nav-link">
                            <i class="nav-icon fas fa-chart-line"></i>
                            <p>
                                Reportes
                                <i class="fas fa-angle-left right"></i>
                            </p>
                        </a>
                        <ul class="nav nav-treeview">
                            <?php if(Tiene_permiso($permisos_user, 'ver-reportes')) { ?>
                            <li class="nav-item">
                                <a href="/home/pages/reportes/ver.php" class="nav-link">
                                    <i class="fas fa-eye nav-icon"></i>
                                    <p>Ver Reportes</p>
                                </a>
                            </li>
                            <?php } ?>
                            <?php if(Tiene_permiso($permisos_user, 'exportar-reportes')) { ?>
                            <li class="nav-item">
                                <a href="/home/pages/reportes/exportar.php" class="nav-link">
                                    <i class="fas fa-download nav-icon"></i>
                                    <p>Exportar Reportes</p>
                                </a>
                            </li>
                            <?php } ?>
                        </ul>
                    </li>
                    <?php } ?>

                    <!-- Opciones recomendadas (más enfocadas a la app) -->
                    <li class="nav-header">Extras</li>
                    <!-- Inventario (placeholder si no tiene permiso aparece igual como próximo) -->
                    <li class="nav-item">
                        <a href="/home/pages/inventario/ver.php" class="nav-link">
                            <i class="nav-icon fas fa-boxes"></i>
                            <p>Inventario <span class="right text-muted">Próximamente</span></p>
                        </a>
                    </li>
                    <!-- Proveedores -->
                    <li class="nav-item">
                        <a href="/home/pages/proveedores/index.php" class="nav-link">
                            <i class="nav-icon fas fa-truck"></i>
                            <p>Proveedores <span class="right text-muted">Próximamente</span></p>
                        </a>
                    </li>
                    <!-- Documentación / Ayuda -->
                    <li class="nav-item">
                        <a href="/home/pages/documentacion/index.php" class="nav-link">
                            <i class="nav-icon fas fa-book"></i>
                            <p>Documentación <span class="right text-muted">Ayuda</span></p>
                        </a>
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

<!-- Consolidated: estilos y script de sidebar (elimina duplicados y corrige toggles) -->
<style>
  /* Oculta el icono "right" de AdminLTE para evitar doble indicador */
  .nav-link > .right { display: none; }

  /* Toggle limpio (sin cuadro) */
  .tree-toggle {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    margin-left: .5rem;
    color: rgba(0,0,0,.45);
    background: transparent;
    border: 0;
    padding: 0;
    width: 26px;
    height: 26px;
    cursor: pointer;
    transition: color .18s ease;
    font-size: .85rem;
  }
  .tree-toggle i {
    transition: transform .22s cubic-bezier(.2,.8,.2,1), color .18s ease;
    transform-origin: 50% 50%;
    display: inline-block;
  }
  /* Rotación cuando está abierto (apunta abajo) */
  .nav-item.menu-open > a .tree-toggle i {
    transform: rotate(-90deg);
    color: rgba(0,0,0,.65);
  }

  /* Active / hover */
  .nav-sidebar .nav-link.active {
    background: linear-gradient(90deg, rgba(0,123,255,.12), rgba(0,123,255,.06));
    color: #004085 !important;
    border-left: 3px solid #007bff;
  }
  .nav-sidebar .nav-link:hover {
    background: rgba(0,0,0,.03);
    color: #0056b3;
  }
  /* Submenu smooth visibility */
  .nav-treeview { transition: all .18s ease; display: none; }
  .nav-item.menu-open > .nav-treeview { display: block; }
  /* Dark mode tweaks */
  .main-sidebar.dark-mode .nav-link.active { background: rgba(255,255,255,.06); color: #fff !important; border-left-color: #66b2ff; }
</style>

<script>
  (function($){
    $(function(){
      // asignar ids únicos y crear un único toggle por item con submenú
      $('.nav-item.has-treeview').each(function(i){
          var $item = $(this);
          $item.attr('data-menu-id','menu-'+i);
          var $link = $item.children('a').first();
          if ($link.find('.tree-toggle').length === 0) {
              // usare chevron-left para mantener consistencia y rotar
              var $toggle = $('<span class="tree-toggle" role="button" tabindex="0" aria-expanded="false"><i class="fas fa-chevron-left"></i></span>');
              $link.append($toggle);
          }
      });

      // Cerrar todos los submenús al cargar (salvo los que correspondan al link activo)
      $('.nav-item.has-treeview').removeClass('menu-open');
      $('.nav-item.has-treeview > ul.nav-treeview').hide();
      $('.nav-item.has-treeview > a .tree-toggle').attr('aria-expanded','false');

      // Normalizar ruta y marcar el enlace activo más específico
      function normalizePath(p){ return (p||'').split('?')[0].replace(/\/+$/,''); }
      var path = normalizePath(window.location.pathname);
      var best = null, bestLen = 0;
      $('a.nav-link').each(function(){
          var href = $(this).attr('href');
          if (!href || href === '#') return;
          var h = normalizePath(href);
          if (h && (path === h || path.indexOf(h + '/') === 0)) {
              if (h.length > bestLen) { bestLen = h.length; best = $(this); }
          }
      });
      if (best) {
          $('a.nav-link').removeClass('active');
          best.addClass('active');
          best.parents('.nav-item.has-treeview').each(function(){
              var $p = $(this);
              $p.addClass('menu-open');
              $p.children('a').first().addClass('active');
              $p.find('> ul.nav-treeview').show();
              $p.find('> a .tree-toggle').attr('aria-expanded','true');
          });
      }

      // Función para cerrar hermanos en mismo nivel
      function closeSiblings($item){
          $item.siblings('.nav-item.has-treeview.menu-open').each(function(){
              var $s = $(this);
              $s.removeClass('menu-open');
              $s.find('> a .tree-toggle').attr('aria-expanded','false');
              $s.find('> ul.nav-treeview').stop(true,true).slideUp(180);
          });
      }

      // Manejo de apertura/cierre por toggle (click y teclado)
      $(document).on('click keydown', '.tree-toggle', function(e){
          if (e.type === 'keydown' && e.key !== 'Enter' && e.key !== ' ') return;
          e.preventDefault(); e.stopPropagation();
          var $btn = $(this);
          var $parent = $btn.closest('.nav-item');
          var $submenu = $parent.find('> ul.nav-treeview').first();
          var isOpen = $parent.hasClass('menu-open');
          if (isOpen) {
              $submenu.stop(true,true).slideUp(180, function(){ $parent.removeClass('menu-open'); });
              $btn.attr('aria-expanded','false');
          } else {
              closeSiblings($parent);
              $submenu.stop(true,true).slideDown(200, function(){ $parent.addClass('menu-open'); });
              $btn.attr('aria-expanded','true');
          }
      });

      // Click en label principal con href="#" actúa como toggle
      $(document).on('click', '.nav-item.has-treeview > a.nav-link', function(e){
          var href = $(this).attr('href');
          if (!href || href.trim() === '#') {
              e.preventDefault();
              $(this).find('.tree-toggle').first().trigger('click');
          }
      });

      // Dark mode desde localStorage
      if (localStorage.getItem('darkMode') === 'true') {
          $('.main-sidebar').addClass('dark-mode');
          $('#darkModeSwitch').prop('checked', true);
      }
      $('#darkModeSwitch').on('change', function(){
          var enabled = $(this).is(':checked');
          localStorage.setItem('darkMode', enabled ? 'true' : 'false');
          $('.main-sidebar').toggleClass('dark-mode', enabled);
      });
    });
  })(jQuery);
</script>

<!-- Scripts adicionales para funcionalidades específicas -->
<!-- iCheck -->
<script src="/home/plugins/icheck/icheck.min.js"></script>
<!-- AdminLTE App -->
<script src="/home/dist/js/adminlte.min.js"></script>
<!-- Page specific script -->
<script>
  $(function () {
    //Initialize Select2 Elements
    $('.select2').select2({
      theme: 'bootstrap4'
    });

    //Datemask dd/mm/yyyy
    $('#datemask').inputmask('dd/mm/yyyy', {'placeholder': 'dd/mm/yyyy'});
    //Datemask2 mm/dd/yyyy
    $('#datemask2').inputmask('mm/dd/yyyy', {'placeholder': 'mm/dd/yyyy'});
    //Money Euro
    $('[data-mask]').inputmask();
  });
</script>
