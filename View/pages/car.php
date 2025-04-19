<?php
include_once "../../Config/connection.php"; // Conexión a la base de datos
include_once "../Templates/cabeza.php"; // Encabezado de la página
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

?>
<style>
    input[type=number]::-webkit-inner-spin-button,
    input[type=number]::-webkit-outer-spin-button {
        -webkit-appearance: none;
        margin: 0;
    }

    input[type=number] {
        -moz-appearance: textfield;
    }

    input.update-quantity {
        width: 50px;
        text-align: center;
        padding: 4px;
        font-size: 16px;
        border: 1px solid #ccc;
        border-radius: 5px;
    }

    .quantity-controls {
        display: flex;
        align-items: center;
        gap: 5px;
    }

    .quantity-controls button {
        background-color: #eee;
        border: none;
        padding: 4px 10px;
        font-size: 16px;
        cursor: pointer;
        border-radius: 4px;
        transition: background-color 0.2s ease;
    }

    .quantity-controls button:hover {
        background-color: #ddd;
    }
</style>

<main>
    <h1>Carrito de Compras</h1>

    <div class="cart-container">
        <div class="cart-items">
            <h2 class="cart-header">Productos en tu carrito</h2>
            <table class="cart-table">
                <thead>
                    <tr>
                        <th>Producto</th>
                        <th>Precio</th>
                        <th>Cantidad</th>
                        <th>Total</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $total = 0;
                    if (!empty($_SESSION['car'])) {
                        foreach ($_SESSION['car'] as $product_id => $item) {
                            $subtotal = $item['price'] * $item['quantity'];
                            $total += $subtotal;
                            echo "<tr>
                                    <td>{$item['product_name']}</td>
                                    <td>$ {$item['price']}</td>
                                    <td>
                                        <div class='quantity-controls'>
                                            <button class='decrease'>-</button>
                                            <input type='number' class='update-quantity' data-id='{$item['product_id']}' value='{$item['quantity']}' min='1'>
                                            <button class='increase'>+</button>

                                        </div>
                                    </td>
                                    <td class='subtotal'>$ {$subtotal}</td>
                                    <td>
                                        <form action='../../Controller/carrito_crud.php' method='POST'>
                                            <input type='hidden' name='action' value='remove'>
                                            <input type='hidden' name='product_id' value='{$item['product_id']}'>
                                            <button type='submit' class='remove-item'>🗑️</button>
                                        </form>
                                    </td>
                                </tr>
                                ";
                        }
                    } else {
                        echo "<tr><td colspan='5'>El carrito está vacío</td></tr>";
                    }
                    ?>
                </tbody>
            </table>
        </div>

        <div class="order-summary">
            <h2 class="summary-header">Resumen del Pedido</h2>
            <div class="summary-row">
                <span>Subtotal:</span>
                <span class="subtotal-amount">$ <?php echo number_format($total, 2); ?></span>
            </div>
            <div class="summary-row">
                <span>IVA (19%):</span>
                <span class="iva-amount">$ <?php echo number_format($total * 0.19, 2); ?></span>
            </div>
            <div class="summary-total">
                <span>Total:</span>
                <span class="total-amount">$ <?php echo number_format($total * 1.19, 2); ?></span>
            </div>
            <?php if (!empty($_SESSION['car'])): ?>
                <form action="pago.php" method="POST">
                    <h3>🛒 Información de Envío</h3>

                    <label for="direccion_envio">Dirección de Envío:</label>
                    <input type="text" name="direccion_envio" required>

                    <label for="telefono_envio">Teléfono:</label>
                    <input type="text" name="telefono_envio" required>

                    <button type="submit" class="checkout-button" style="background-color: #009ee3; color: white; padding: 10px 20px; border: none; border-radius: 5px;">
                        Pagar con MercadoPago
                    </button>
                </form>
            <?php endif; ?>
        </div>
    </div>
</main>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
    $(document).ready(function() {
        function actualizarCarrito(input) {
            var productId = input.data("id");
            var newQuantity = input.val();
            var row = input.closest("tr");

            $.ajax({
                url: "../../Controller/carrito_crud.php",
                type: "POST",
                data: {
                    action: "update",
                    product_id: productId,
                    quantity: newQuantity
                },
                success: function(response) {
                    var data = JSON.parse(response);

                    // Animar actualización de subtotal y totales
                    row.find(".subtotal").fadeOut(150, function() {
                        $(this).text("$ " + data.subtotal.toFixed(2)).fadeIn(150);
                    });
                    $(".subtotal-amount").fadeOut(150, function() {
                        $(this).text("$ " + data.total.toFixed(2)).fadeIn(150);
                    });
                    $(".iva-amount").fadeOut(150, function() {
                        $(this).text("$ " + (data.total * 0.19).toFixed(2)).fadeIn(150);
                    });
                    $(".total-amount").fadeOut(150, function() {
                        $(this).text("$ " + (data.total * 1.19).toFixed(2)).fadeIn(150);
                    });
                }
            });
        }

        // Evento al cambiar manualmente la cantidad
        $(".update-quantity").on("change", function() {
            actualizarCarrito($(this));
        });

        // Botón +
        $(".increase").on("click", function() {
            var input = $(this).siblings(".update-quantity");
            var val = parseInt(input.val());
            input.val(val + 1).trigger("change");
        });

        // Botón -
        $(".decrease").on("click", function() {
            var input = $(this).siblings(".update-quantity");
            var val = parseInt(input.val());
            if (val > 1) {
                input.val(val - 1).trigger("change");
            }
        });
    });
</script>



<?php
include_once "../Templates/pie.php"; // Pie de página
?>