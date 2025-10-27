<?php
// php/cart_add.php
session_start();
require_once("../config/db.php");

$id = isset($_POST['id']) ? (int)$_POST['id'] : 0;
if ($id <= 0) {
  echo json_encode(['ok'=>false,'msg'=>'ID inválido']); exit; }

$stmt = $pdo->prepare("SELECT id,nombre,precio,imagen FROM productos WHERE id=?");
$stmt->execute([$id]);
$prod = $stmt->fetch(PDO::FETCH_ASSOC);
if (!$prod) { 
  echo json_encode(['ok'=>false,'msg'=>'Producto no existe']); exit; }

if (!isset($_SESSION['cart'])) $_SESSION['cart'] = [];
if (!isset($_SESSION['cart'][$id])) {
  $_SESSION['cart'][$id] = [
    'id'      => (int)$prod['id'],
    'nombre'  => $prod['nombre'],
    'precio'  => (float)$prod['precio'],
    'cantidad'=> 1,
    'imagen'  => $prod['imagen']
  ];
} else {
  $_SESSION['cart'][$id]['cantidad']++;
}

echo json_encode([
  'ok'=>true,
  'count'=>array_sum(array_column($_SESSION['cart'],'cantidad'))
]);
