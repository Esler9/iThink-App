<?php

// Simple endpoints para listados usados por los modales (grupos / tiendas)
header('Content-Type: application/json; charset=utf-8');

if (session_status() == PHP_SESSION_NONE) session_start();

// Ajusta la ruta si es necesario (igual que en listado_users.php)
include_once("../../../conexion.php");

$action = isset($_REQUEST['action']) ? $_REQUEST['action'] : '';

if (!$conn) {
  echo json_encode(['error' => 'No DB connection']);
  exit;
}

switch ($action) {
  case 'list_groups':
    $out = [];
    $sql = "SELECT codigo, nombre_grupo FROM grupo_user ORDER BY nombre_grupo ASC";
    if ($res = mysqli_query($conn, $sql)) {
      while ($r = mysqli_fetch_assoc($res)) {
        $out[] = ['codigo' => $r['codigo'], 'nombre' => $r['nombre_grupo']];
      }
    } else {
      http_response_code(500);
      echo json_encode(['error' => mysqli_error($conn)]);
      exit;
    }
    echo json_encode($out);
    break;

  case 'list_tiendas':
    $out = [];
    $sql = "SELECT cod_tienda, nombre FROM tienda ORDER BY nombre ASC";
    if ($res = mysqli_query($conn, $sql)) {
      while ($r = mysqli_fetch_assoc($res)) {
        $out[] = ['cod_tienda' => $r['cod_tienda'], 'nombre' => $r['nombre']];
      }
    } else {
      http_response_code(500);
      echo json_encode(['error' => mysqli_error($conn)]);
      exit;
    }
    echo json_encode($out);
    break;

  default:
    http_response_code(400);
    echo json_encode(['error' => 'Acción no válida']);
    break;
}
?>