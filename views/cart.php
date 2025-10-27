    <?php
    session_start();
    require_once("../config/db.php");
    $cart = $_SESSION['cart'] ?? [];
    $subtotal = 0;
    foreach($cart as $c){ $subtotal += $c['precio']*$c['cantidad']; }
    ?>
    <!DOCTYPE html>
    <html lang="es">
    <head>
    <meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Carrito | Chinos Café</title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <style>
    .cart{padding:50px 7%}
    .tbl{width:100%;border-collapse:collapse;background:#fff;border-radius:12px;overflow:hidden;box-shadow:0 6px 14px rgba(0,0,0,.12)}
    .tbl th,.tbl td{padding:14px;border-bottom:1px solid #eee;text-align:left}
    .qty{display:flex;gap:6px;align-items:center}
    .qty input{width:56px;padding:6px}
    .actions{display:flex;gap:10px;justify-content:flex-end;margin-top:16px}
    .btn{border:0;border-radius:18px;padding:10px 16px;cursor:pointer}
    .btn-prim{background:#2e7d32;color:#fff}
    .btn-sec{background:#8b5e3c;color:#fff}
    .right{text-align:right}
    </style>
    </head>
    <body>
    <?php include("../includes/header.php"); ?>

    <main class="cart">
    <h2>Carrito de compra</h2>
    <?php if(!$cart){ ?>
        <p>Tu carrito está vacío.</p>
    <?php } else { ?>
    <table class="tbl">
        <thead><tr><th>Producto</th><th>Precio</th><th>Cantidad</th><th>Total</th></tr></thead>
        <tbody>
        <?php foreach($cart as $item): $total = $item['precio']*$item['cantidad']; ?>
        <tr data-id="<?= (int)$item['id'] ?>">
            <td>
            <img src="../assets/img/<?= htmlspecialchars($item['imagen']) ?>" alt="" style="width:58px;height:58px;object-fit:cover;border-radius:8px;vertical-align:middle;margin-right:10px">
            <?= htmlspecialchars($item['nombre']) ?>
            </td>
            <td>$<?= number_format($item['precio'],2) ?></td>
            <td class="qty">
            <input type="number" min="0" value="<?= (int)$item['cantidad'] ?>" class="inp-qty">
            </td>
            <td class="right">$<?= number_format($total,2) ?></td>
        </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
    <div class="actions">
        <div style="margin-right:auto;font-weight:700">Subtotal: $<?= number_format($subtotal,2) ?></div>
        <a class="btn btn-sec" href="./tienda.php">Seguir comprando</a>
        <a class="btn btn-prim" href="./checkout.php">Finalizar pedido</a>
    </div>
    <?php } ?>
    </main>

    <script>
    document.querySelectorAll('.inp-qty').forEach(i=>{
    i.addEventListener('change', async e=>{
        const tr  = e.target.closest('tr');
        const id  = tr.dataset.id;
        const qty = e.target.value;
        const resp = await fetch('../php/cart_update.php',{
        method:'POST',
        headers:{'Content-Type':'application/x-www-form-urlencoded'},
        body:'id='+encodeURIComponent(id)+'&qty='+encodeURIComponent(qty)
        });
        if(resp.ok) location.reload();
    });
    });
    </script>

    <?php include("../includes/footer.php"); ?>
    </body>
    </html>
