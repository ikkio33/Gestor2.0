<?php
include '../../conexion.php';

$inicio = $_GET['fecha_inicio'] ?? null;
$fin = $_GET['fecha_fin'] ?? null;
$meson_id = $_GET['meson_id'] ?? null;
$materia_id = $_GET['materia_id'] ?? null;

$condiciones = [];
if ($inicio && $fin) {
  $condiciones[] = "t.created_at BETWEEN '$inicio 00:00:00' AND '$fin 23:59:59'";
}
if ($meson_id) {
  $condiciones[] = "t.meson_id = $meson_id";
}
if ($materia_id) {
  $condiciones[] = "t.materia_id = $materia_id";
}

$where = count($condiciones) ? "WHERE " . implode(" AND ", $condiciones) : "";

$sql = "
  SELECT s.nombre, COUNT(*) as cantidad
  FROM turnos t
  INNER JOIN servicios s ON t.servicio_id = s.id
  $where
  GROUP BY s.id
  ORDER BY cantidad DESC
";

$res = $conn->query($sql);
$data = [];

while ($row = $res->fetch_assoc()) {
  $data[] = [
    'nombre' => $row['nombre'],
    'cantidad' => (int)$row['cantidad']
  ];
}

header('Content-Type: application/json');
echo json_encode($data);
