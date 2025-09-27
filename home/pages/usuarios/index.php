<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Pruebas - Listado de Usuarios</title>
  <style>
    body { font-family: Arial, sans-serif; margin: 24px; background:#f7f7f7; color:#222; }
    .container { max-width:1000px; margin:0 auto; background:#fff; padding:20px; border-radius:6px; box-shadow:0 2px 6px rgba(0,0,0,0.08); }
    h1 { margin-top:0; font-size:20px; }
    table { width:100%; border-collapse:collapse; margin-top:12px; }
    th, td { padding:8px 10px; border:1px solid #e1e1e1; text-align:left; }
    th { background:#f0f0f0; }
    .actions { text-align:right; }
    .btn { display:inline-block; padding:6px 10px; border-radius:4px; text-decoration:none; color:#fff; font-size:13px; }
    .btn-edit { background:#17a2b8; }
    .btn-delete { background:#dc3545; margin-left:6px; }
    .note { font-size:13px; color:#555; margin-top:8px; }
  </style>
</head>
<body>
  <div class="container">
    <h1>Pruebas - Listado de Usuarios</h1>
    <p class="note">Este archivo es una página HTML básica para pruebas. No realiza operaciones en base de datos.</p>

    <table id="usuariosTable" aria-label="Listado de usuarios">
      <thead>
        <tr>
          <th>ID</th>
          <th>Nombre</th>
          <th>Email</th>
          <th>Rol</th>
          <th class="actions">Acciones</th>
        </tr>
      </thead>
      <tbody>
        <tr>
          <td>1</td>
          <td>María López</td>
          <td>maria.lopez@example.com</td>
          <td>Admin</td>
          <td class="actions">
            <a href="#" class="btn btn-edit" data-id="1">Editar</a>
            <a href="#" class="btn btn-delete" data-id="1">Eliminar</a>
          </td>
        </tr>
        <tr>
          <td>2</td>
          <td>Carlos Pérez</td>
          <td>carlos.perez@example.com</td>
          <td>Usuario</td>
          <td class="actions">
            <a href="#" class="btn btn-edit" data-id="2">Editar</a>
            <a href="#" class="btn btn-delete" data-id="2">Eliminar</a>
          </td>
        </tr>
        <tr>
          <td>3</td>
          <td>Ana Gómez</td>
          <td>ana.gomez@example.com</td>
          <td>Usuario</td>
          <td class="actions">
            <a href="#" class="btn btn-edit" data-id="3">Editar</a>
            <a href="#" class="btn btn-delete" data-id="3">Eliminar</a>
          </td>
        </tr>
      </tbody>
    </table>

    <p class="note">Usar los botones para simular acciones. Esta página está pensada para pruebas locales y visuales.</p>
  </div>

  <script>
    // Manejo simple de botones para pruebas
    document.addEventListener('click', function (e) {
      var target = e.target;
      if (target.matches('.btn-edit')) {
        e.preventDefault();
        var id = target.getAttribute('data-id');
        alert('Simulación: editar usuario ID ' + id);
      }
      if (target.matches('.btn-delete')) {
        e.preventDefault();
        var id = target.getAttribute('data-id');
        var ok = confirm('¿Desea eliminar el usuario ID ' + id + '? (simulación)');
        if (ok) {
          alert('Simulación: usuario ID ' + id + ' eliminado (no real).');
        }
      }
    });
  </script>
</body>
</html>