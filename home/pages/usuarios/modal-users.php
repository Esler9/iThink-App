<?php
// ====== modal-users.php ======
if (session_status() === PHP_SESSION_NONE) session_start();

// Asegurar $conn disponible (ajusta la ruta si es necesario)
if (!isset($conn)) {
  $try = @include_once __DIR__ . "/../../../conexion.php";
  // Si no existe, $conn deberá existir en la página que incluye este archivo.
}

// CSRF token
if (empty($_SESSION['csrf_token'])) {
  $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}
$CSRF = $_SESSION['csrf_token'];

$flash_message = '';
$flash_success = null;

// Helpers
function bad($m){ return ['ok'=>false,'msg'=>$m]; }
function good($m){ return ['ok'=>true,'msg'=>$m]; }
function require_conn($conn){
  if (!isset($conn) || !is_object($conn)) return bad('Sin conexión a la base de datos ($conn).');
  return good('ok');
}

// Manejo de formularios sin AJAX (mismo archivo)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['form_action'])) {
  // CSRF
  if (empty($_POST['csrf_token']) || !hash_equals($_SESSION['csrf_token'] ?? '', $_POST['csrf_token'])) {
    $flash_message = 'CSRF inválido.';
    $flash_success = false;
  } else {
    $chk = require_conn($conn);
    if (!$chk['ok']) {
      $flash_message = $chk['msg'];
      $flash_success = false;
    } else {
      $action = $_POST['form_action'];

      if ($action === 'create_user') {
        $user        = trim($_POST['User'] ?? '');
        $codEmp      = trim($_POST['Cod_Empleado'] ?? '');
        $email       = trim($_POST['email'] ?? '');
        $emailActive = isset($_POST['email_active']) ? (int)$_POST['email_active'] : 0;
        $idGroup     = ($_POST['id_group_user'] === '' ? null : (int)$_POST['id_group_user']);
        $codTienda   = trim($_POST['cod_tienda'] ?? '');
        $state       = isset($_POST['State']) ? (int)$_POST['State'] : 1;
        $password    = $_POST['Password'] ?? '';

        if ($user === '' || $password === '') {
          $flash_message = 'Usuario y contraseña son obligatorios.';
          $flash_success = false;
        } elseif ($email !== '' && !filter_var($email, FILTER_VALIDATE_EMAIL)) {
          $flash_message = 'Email inválido.';
          $flash_success = false;
        } else {
          $hash = password_hash($password, PASSWORD_DEFAULT);
          $sql = "INSERT INTO usuarios (user, cod_empleado, email, email_active, id_group_user, cod_tienda, state, password)
                  VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
          if ($stmt = mysqli_prepare($conn, $sql)) {
            // s s s i i s i s
            // user, cod_empleado, email, email_active, id_group_user, cod_tienda, state, password
            mysqli_stmt_bind_param($stmt, "sssii sis",
              /*s*/ $user,
              /*s*/ $codEmp,
              /*s*/ $email,
              /*i*/ $emailActive,
              /*i*/ $idGroup,
              /*s*/ $codTienda,
              /*i*/ $state,
              /*s*/ $hash
            );
            // Nota: el espacio en "sssii sis" es para lectura; PHP lo permite. Si tu PHP falla, cambia por "sssii sis" => "sssii sis" sin espacios extra o usa "sssii sis" exacto.
            $ok = @mysqli_stmt_execute($stmt);
            $err = mysqli_error($conn);
            mysqli_stmt_close($stmt);

            if ($ok) {
              $flash_message = 'Usuario creado correctamente.';
              $flash_success = true;
            } else {
              $flash_message = 'No se pudo crear el usuario. ' . ($err ?: '');
              $flash_success = false;
            }
          } else {
            $flash_message = 'Error al preparar INSERT.';
            $flash_success = false;
          }
        }
      }

      if ($action === 'edit_user') {
        $codigo      = (int)($_POST['Codigo'] ?? 0);
        $user        = trim($_POST['User'] ?? '');
        $codEmp      = trim($_POST['Cod_Empleado'] ?? '');
        $email       = trim($_POST['email'] ?? '');
        $emailActive = isset($_POST['email_active']) ? (int)$_POST['email_active'] : 0;
        $idGroup     = ($_POST['id_group_user'] === '' ? null : (int)$_POST['id_group_user']);
        $codTienda   = trim($_POST['cod_tienda'] ?? '');
        $state       = isset($_POST['State']) ? (int)$_POST['State'] : 1;
        $password    = $_POST['Password'] ?? '';

        if ($codigo <= 0) {
          $flash_message = 'Código inválido.';
          $flash_success = false;
        } elseif ($user === '') {
          $flash_message = 'El nombre de usuario es obligatorio.';
          $flash_success = false;
        } elseif ($email !== '' && !filter_var($email, FILTER_VALIDATE_EMAIL)) {
          $flash_message = 'Email inválido.';
          $flash_success = false;
        } else {
          if ($password !== '') {
            $hash = password_hash($password, PASSWORD_DEFAULT);
            $sql = "UPDATE usuarios SET user=?, cod_empleado=?, email=?, email_active=?, id_group_user=?, cod_tienda=?, state=?, password=? WHERE codigo=?";
            if ($stmt = mysqli_prepare($conn, $sql)) {
              mysqli_stmt_bind_param($stmt, "sssii sisi",
                /*s*/ $user,
                /*s*/ $codEmp,
                /*s*/ $email,
                /*i*/ $emailActive,
                /*i*/ $idGroup,
                /*s*/ $codTienda,
                /*i*/ $state,
                /*s*/ $hash,
                /*i*/ $codigo
              );
              $ok = @mysqli_stmt_execute($stmt);
              $err = mysqli_error($conn);
              mysqli_stmt_close($stmt);
            } else { $ok = false; $err = 'Error prepare UPDATE (con password)'; }
          } else {
            $sql = "UPDATE usuarios SET user=?, cod_empleado=?, email=?, email_active=?, id_group_user=?, cod_tienda=?, state=? WHERE codigo=?";
            if ($stmt = mysqli_prepare($conn, $sql)) {
              mysqli_stmt_bind_param($stmt, "sssii sii",
                /*s*/ $user,
                /*s*/ $codEmp,
                /*s*/ $email,
                /*i*/ $emailActive,
                /*i*/ $idGroup,
                /*s*/ $codTienda,
                /*i*/ $state,
                /*i*/ $codigo
              );
              $ok = @mysqli_stmt_execute($stmt);
              $err = mysqli_error($conn);
              mysqli_stmt_close($stmt);
            } else { $ok = false; $err = 'Error prepare UPDATE'; }
          }

          if ($ok) {
            $flash_message = 'Usuario actualizado.';
            $flash_success = true;
          } else {
            $flash_message = 'No se pudo actualizar. ' . ($err ?: '');
            $flash_success = false;
          }
        }
      }

      if ($action === 'delete_user') {
        $codigo = (int)($_POST['Codigo'] ?? 0);
        if ($codigo <= 0) {
          $flash_message = 'Código inválido.';
          $flash_success = false;
        } else {
          $sql = "DELETE FROM usuarios WHERE codigo = ?";
          if ($stmt = mysqli_prepare($conn, $sql)) {
            mysqli_stmt_bind_param($stmt, "i", $codigo);
            $ok = @mysqli_stmt_execute($stmt);
            $err = mysqli_error($conn);
            mysqli_stmt_close($stmt);

            if ($ok) {
              $flash_message = 'Usuario eliminado.';
              $flash_success = true;
            } else {
              $flash_message = 'No se pudo eliminar. ' . ($err ?: '');
              $flash_success = false;
            }
          } else {
            $flash_message = 'Error prepare DELETE.';
            $flash_success = false;
          }
        }
      }
    }
  }

  // Opcional: evitar resubmit con F5 (PRG suave sin salir de la misma página)
  // Comentado para mantener exactamente "sin salir":
  // if ($flash_success !== null) {
  //   header("Location: " . $_SERVER['REQUEST_URI']);
  //   exit;
  // }
}
?>

<?php if ($flash_success !== null): ?>
  <div class="alert alert-<?php echo $flash_success ? 'success' : 'danger'; ?> mt-2">
    <?php echo htmlspecialchars($flash_message); ?>
  </div>
<?php endif; ?>

<!-- ======================= Ver Usuario ======================= -->
<div class="modal fade" id="viewUserModal" tabindex="-1" role="dialog" aria-labelledby="viewUserLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
      <div class="modal-header bg-info">
        <h5 class="modal-title" id="viewUserLabel">Ver Usuario</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Cerrar"><span aria-hidden="true">&times;</span></button>
      </div>
      <div class="modal-body">
        <table class="table table-borderless">
          <tr><th>Código</th><td id="v_codigo"></td></tr>
          <tr><th>User</th><td id="v_user"></td></tr>
          <tr><th>Cod Empleado</th><td id="v_cod_empleado"></td></tr>
          <tr><th>Email</th><td id="v_email"></td></tr>
          <tr><th>Email verificado</th><td id="v_email_active"></td></tr>
          <tr><th>Grupo</th><td id="v_grupo"></td></tr>
          <tr><th>Tienda</th><td id="v_tienda"></td></tr>
          <tr><th>Estado</th><td id="v_state"></td></tr>
        </table>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
      </div>
    </div>
  </div>
</div>

<!-- ======================= Crear Usuario ======================= -->
<div class="modal fade" id="createUserModal" tabindex="-1" role="dialog" aria-labelledby="createUserLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <form id="formCreateUser" method="post" action="">
      <div class="modal-content">
        <div class="modal-header bg-success">
          <h5 class="modal-title" id="createUserLabel">Crear Usuario</h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Cerrar"><span aria-hidden="true">&times;</span></button>
        </div>
        <div class="modal-body">
          <input type="hidden" name="form_action" value="create_user">
          <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($CSRF); ?>">

          <div class="form-group">
            <label for="c_user">User</label>
            <input type="text" class="form-control" id="c_user" name="User" required>
          </div>
          <div class="form-group">
            <label for="c_cod_empleado">Cod Empleado</label>
            <input type="text" class="form-control" id="c_cod_empleado" name="Cod_Empleado">
          </div>
          <div class="form-group">
            <label for="c_email">Email</label>
            <input type="email" class="form-control" id="c_email" name="email">
          </div>
          <div class="form-group">
            <label for="c_email_active">Email verificado</label>
            <input type="hidden" name="email_active" value="0">
            <div class="custom-control custom-switch">
              <input type="checkbox" class="custom-control-input" id="c_email_active" name="email_active" value="1">
              <label class="custom-control-label" for="c_email_active">Enviar correos / Verificado</label>
            </div>
          </div>
          <div class="form-group">
            <label for="c_id_group">Grupo</label>
            <select class="form-control" id="c_id_group" name="id_group_user">
              <option value="">-- Seleccione --</option>
              <?php
              if (isset($conn) && $conn) {
                $rg = @mysqli_query($conn,"SELECT codigo,nombre_grupo FROM grupo_user ORDER BY codigo");
                if($rg){ while($gr = @mysqli_fetch_assoc($rg)){
                  echo '<option value="'.htmlspecialchars($gr['codigo']).'">'.htmlspecialchars($gr['nombre_grupo']).'</option>';
                }}
              }
              ?>
            </select>
          </div>
          <div class="form-group">
            <label for="c_cod_tienda">Tienda</label>
            <select class="form-control" id="c_cod_tienda" name="cod_tienda">
              <option value="">-- Seleccione --</option>
              <?php
              if (isset($conn) && $conn) {
                $rt = @mysqli_query($conn,"SELECT cod_tienda,nombre FROM tienda ORDER BY nombre");
                if($rt){ while($t = @mysqli_fetch_assoc($rt)){
                  echo '<option value="'.htmlspecialchars($t['cod_tienda']).'">'.htmlspecialchars($t['nombre']).'</option>';
                }}
              }
              ?>
            </select>
          </div>
          <div class="form-group">
            <label for="c_state">Estado</label>
            <select class="form-control" id="c_state" name="State">
              <option value="1">Activo</option>
              <option value="0">Inactivo</option>
            </select>
          </div>
          <div class="form-group">
            <label for="c_password">Contraseña</label>
            <input type="password" class="form-control" id="c_password" name="Password" required>
          </div>
        </div>
        <div class="modal-footer">
          <button type="submit" class="btn btn-success">Crear</button>
          <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
        </div>
      </div>
    </form>
  </div>
</div>

<!-- ======================= Editar Usuario ======================= -->
<div class="modal fade" id="editUserModal" tabindex="-1" role="dialog" aria-labelledby="editUserLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <form id="formEditUser" method="post" action="">
      <input type="hidden" name="form_action" value="edit_user">
      <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($CSRF); ?>">
      <input type="hidden" name="Codigo" id="e_codigo">
      <div class="modal-content">
        <div class="modal-header bg-warning">
          <h5 class="modal-title" id="editUserLabel">Editar Usuario</h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Cerrar"><span aria-hidden="true">&times;</span></button>
        </div>
        <div class="modal-body">
          <div class="form-group">
            <label for="e_user">User</label>
            <input type="text" class="form-control" id="e_user" name="User" required>
          </div>
          <div class="form-group">
            <label for="e_cod_empleado">Cod Empleado</label>
            <input type="text" class="form-control" id="e_cod_empleado" name="Cod_Empleado">
          </div>
          <div class="form-group">
            <label for="e_email">Email</label>
            <input type="email" class="form-control" id="e_email" name="email">
          </div>
          <div class="form-group">
            <label for="e_email_active">Email verificado</label>
            <input type="hidden" name="email_active" value="0">
            <div class="custom-control custom-switch">
              <input type="checkbox" class="custom-control-input" id="e_email_active" name="email_active" value="1">
              <label class="custom-control-label" for="e_email_active">Enviar correos / Verificado</label>
            </div>
          </div>
          <div class="form-group">
            <label for="e_id_group">Grupo</label>
            <select class="form-control" id="e_id_group" name="id_group_user">
              <option value="">-- Seleccione --</option>
              <?php
              if (isset($conn) && $conn) {
                $rg = @mysqli_query($conn,"SELECT codigo,nombre_grupo FROM grupo_user ORDER BY codigo");
                if($rg){ while($gr = @mysqli_fetch_assoc($rg)){
                  echo '<option value="'.htmlspecialchars($gr['codigo']).'">'.htmlspecialchars($gr['nombre_grupo']).'</option>';
                }}
              }
              ?>
            </select>
          </div>
          <div class="form-group">
            <label for="e_cod_tienda">Tienda</label>
            <select class="form-control" id="e_cod_tienda" name="cod_tienda">
              <option value="">-- Seleccione --</option>
              <?php
              if (isset($conn) && $conn) {
                $rt = @mysqli_query($conn,"SELECT cod_tienda,nombre FROM tienda ORDER BY nombre");
                if($rt){ while($t = @mysqli_fetch_assoc($rt)){
                  echo '<option value="'.htmlspecialchars($t['cod_tienda']).'">'.htmlspecialchars($t['nombre']).'</option>';
                }}
              }
              ?>
            </select>
          </div>
          <div class="form-group">
            <label for="e_state">Estado</label>
            <select class="form-control" id="e_state" name="State">
              <option value="1">Activo</option>
              <option value="0">Inactivo</option>
            </select>
          </div>
          <div class="form-group">
            <label for="e_password">Contraseña (dejar vacío para no cambiar)</label>
            <input type="password" class="form-control" id="e_password" name="Password">
          </div>
        </div>
        <div class="modal-footer">
          <button type="submit" class="btn btn-warning">Guardar cambios</button>
          <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
        </div>
      </div>
    </form>
  </div>
</div>

<!-- ======================= Eliminar Usuario ======================= -->
<div class="modal fade" id="deleteUserModal" tabindex="-1" role="dialog" aria-labelledby="deleteUserLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <form id="formDeleteUser" method="post" action="">
      <input type="hidden" name="form_action" value="delete_user">
      <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($CSRF); ?>">
      <input type="hidden" name="Codigo" id="d_codigo">
      <div class="modal-content">
        <div class="modal-header bg-danger">
          <h5 class="modal-title" id="deleteUserLabel">Eliminar Usuario</h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Cerrar"><span aria-hidden="true">&times;</span></button>
        </div>
        <div class="modal-body">
          <p>¿Seguro que deseas eliminar al usuario <strong id="d_user"></strong> (código <span id="d_codigo_txt"></span>)?</p>
          <p class="text-danger small">Esta acción no se puede deshacer.</p>
        </div>
        <div class="modal-footer">
          <button type="submit" class="btn btn-danger">Eliminar</button>
          <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
        </div>
      </div>
    </form>
  </div>
</div>

<!-- ======================= Scripts de apertura/llenado (sin AJAX) ======================= -->
<script>
// Ver
$(document).on('click','.btn-view',function(e){
  e.preventDefault();
  var d = $(this).data();
  $('#v_codigo').text(d.codigo);
  $('#v_user').text(d.user);
  $('#v_cod_empleado').text(d.codEmpleado || '');
  $('#v_email').text(d.email || '');
  $('#v_email_active').text(parseInt(d.emailActive)==1 ? 'Sí' : 'No');
  $('#v_grupo').text(d.grupo || d.idGroup || '');
  $('#v_tienda').text(d.tienda || d.codTienda || '');
  $('#v_state').text(parseInt(d.state)==1 ? 'Activo' : 'Inactivo');
  $('#viewUserModal').modal('show');
});

// Editar
$(document).on('click','.btn-edit',function(e){
  e.preventDefault();
  var d = $(this).data();
  $('#e_codigo').val(d.codigo);
  $('#e_user').val(d.user);
  $('#e_cod_empleado').val(d.codEmpleado || '');
  $('#e_email').val(d.email || '');
  $('#e_email_active').prop('checked', parseInt(d.emailActive)===1);
  $('#e_id_group').val(d.idGroup || '');
  $('#e_cod_tienda').val(d.codTienda || '');
  $('#e_state').val(d.state);
  $('#e_password').val('');
  $('#editUserModal').modal('show');
});

// Eliminar
$(document).on('click','.btn-delete',function(e){
  e.preventDefault();
  var d = $(this).data();
  $('#d_codigo').val(d.codigo);
  $('#d_user').text(d.user);
  $('#d_codigo_txt').text(d.codigo);
  $('#deleteUserModal').modal('show');
});
</script>
