<?php
// Extend session lifetime for user convenience
ini_set('session.gc_maxlifetime', 3600); // 1-hour session lifetime
ini_set('session.cookie_lifetime', 3600);

session_start();


if (isset($_POST['add_to_cart'])) {
    if (isset($_SESSION['cart'])) {
        $products_array_ids = array_column($_SESSION['cart'], "product_id");

        if (!in_array($_POST['product_id'], $products_array_ids)) {
            $product_id = $_POST['product_id'];

            $product_array = array(
                'product_id' => $product_id,
                'product_name' => $_POST['product_name'] ?? 'Unknown Product',
                'product_price' => $_POST['product_price'] ?? 0,
                'product_image' => $_POST['product_image'] ?? 'placeholder.jpg',
                'product_quantity' => $_POST['product_quantity'] ?? 1
            );

            $_SESSION['cart'][$product_id] = $product_array;
        } else {
            echo '<script>alert("Product was already added to cart");</script>';
        }
    } else {
        $product_id = $_POST['product_id'];

        $product_array = array(
            'product_id' => $product_id,
            'product_name' => $_POST['product_name'] ?? 'Unknown Product',
            'product_price' => $_POST['product_price'] ?? 0,
            'product_image' => $_POST['product_image'] ?? 'placeholder.jpg',
            'product_quantity' => $_POST['product_quantity'] ?? 1
        );

        $_SESSION['cart'] = array($product_id => $product_array);
    }
} elseif (isset($_POST['remove_product'])) {
    $product_id = $_POST['product_id'];
    unset($_SESSION['cart'][$product_id]);
} elseif (isset($_POST['edit_quantity'])) {
    $product_id = $_POST['product_id'];
    $product_quantity = $_POST['product_quantity'] ?? 1;
    if (isset($_SESSION['cart'][$product_id])) {
        $_SESSION['cart'][$product_id]['product_quantity'] = $product_quantity;
    }
}

// Calculate the total cart cost and store it in the session
$total = 0;
if (isset($_SESSION['cart']) && !empty($_SESSION['cart'])) {
    foreach ($_SESSION['cart'] as $key => $value) {
        $product_price = $value['product_price'] ?? 0;
        $product_quantity = $value['product_quantity'] ?? 1;
        $subtotal = $product_quantity * $product_price;
        $total += $subtotal;
    }
}
$_SESSION['total'] = $total;
?>

<?php include('layouts/header.php'); ?>

    <!-- Cart Section -->
    <section class="cart container my-5 py-5">
        <div class="container mt-5">
            <h2 class="font-weight-bold">Your Cart</h2>
            <hr>
        </div>
        <table class="table mt-5 pt-5">
            <thead>
                <tr>
                    <th>Product</th>
                    <th>Quantity</th>
                    <th>SubTotal</th>
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($_SESSION['cart'])): ?>
                    <?php foreach ($_SESSION['cart'] as $key => $value): ?>
                        <tr>
                            <td>
                                <div class="product-info d-flex align-items-center">
                                    <img src="assets/imgs/<?php echo htmlspecialchars($value['product_image'] ?? 'placeholder.jpg'); ?>" alt="Product Image" width="100">
                                    <div class="ms-3">
                                        <p><?php echo htmlspecialchars($value['product_name'] ?? 'Unknown Product'); ?></p>
                                        <small><span>Kes</span><?php echo htmlspecialchars($value['product_price'] ?? 0); ?></small>
                                        <form method="POST" action="cart.php">
                                            <input type="hidden" name="product_id" value="<?php echo $key; ?>">
                                            <button type="submit" name="remove_product" class="btn btn-danger btn-sm">Remove</button>
                                        </form>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <form method="POST" action="cart.php">
                                    <input type="hidden" name="product_id" value="<?php echo $key; ?>">
                                    <input type="number" name="product_quantity" value="<?php echo htmlspecialchars($value['product_quantity'] ?? 1); ?>" min="1" class="form-control w-50 d-inline">
                                    <button type="submit" name="edit_quantity" class="btn btn-primary btn-sm">Update</button>
                                </form>
                            </td>
                            <td>Kes <?php echo htmlspecialchars(($value['product_quantity'] ?? 1) * ($value['product_price'] ?? 0)); ?></td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="3" class="text-center">Your cart is empty</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>

        <div class="cart-total">
            <table class="table">
                <tr>
                    <td>SubTotal</td>
                    <td>Kes <?php echo number_format($total, 2); ?></td>
                </tr>
                <tr>
                    <td>Total</td>
                    <td>Kes <?php echo number_format($total, 2); ?></td>
                </tr>
            </table>
        </div>

        <div class="checkout-container text-center">
            <form method="POST" action="checkout.php">
                <input type="hidden" name="total" value="<?php echo htmlspecialchars($total); ?>">
                <input type="submit" class="btn btn-success" value="Checkout" name="checkout">
            </form>
        </div>
    </section>

    <?php include('layouts/footer.php'); ?>



