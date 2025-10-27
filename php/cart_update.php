<?php
// php/cart_update.php
session_start();

$id  = isset($_POST['id'])  ? (int)$_POST['id']  : 0;
$qty = isset($_POST['qty']) ? (int)$_POST['qty'] : 0;

if (!isset($_SESSION['cart'][$id]) || $qty < 0) {
  http_response_code(400);
  echo 'bad request';
  exit;
}

if ($qty === 0) {
  unset($_SESSION['cart'][$id]);
} else {
  $_SESSION['cart'][$id]['cantidad'] = $qty;
}

echo 'ok';
