<?php
include 'config.php';
session_start();

// Remove product from cart
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['remove_from_cart'])) {
    $product_id = $_POST['product_id'];
    if (($key = array_search($product_id, $_SESSION['cart'])) !== false) {
        unset($_SESSION['cart'][$key]);
    }
}

// Get product details from cart
$cart_products = [];
if (!empty($_SESSION['cart'])) {
    $cart_ids = implode(',', array_fill(0, count($_SESSION['cart']), '?'));
    $cart_sql = "SELECT id, name, description, price, category, image_url FROM products WHERE id IN ($cart_ids)";
    $stmt = $conn->prepare($cart_sql);
    $stmt->bind_param(str_repeat('i', count($_SESSION['cart'])), ...$_SESSION['cart']);
    $stmt->execute();
    $result = $stmt->get_result();
    while ($row = $result->fetch_assoc()) {
        $cart_products[] = $row;
    }
    $stmt->close();
}
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Корзина</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <header>
        <div class="container">
            <h1>Корзина</h1>
            <nav>
                <a href="index.php" class="nav-link">Главная</a>
                <a href="cart.php" class="nav-link">Корзина (<?php echo count($_SESSION['cart']); ?>)</a>
            </nav>
        </div>
    </header>
    <div class="container">
        <h2>Выбранные товары</h2>
        <?php if (!empty($cart_products)) { ?>
            <div class="cart-list">
                <?php foreach ($cart_products as $product) { ?>
                    <div class="cart-item">
                        <img src="<?php echo htmlspecialchars($product['image_url']); ?>" alt="<?php echo htmlspecialchars($product['name']); ?>" class="product-image">
                        <div class="product-details">
                            <h3><?php echo htmlspecialchars($product['name']); ?></h3>
                            <p><?php echo htmlspecialchars($product['description']); ?></p>
                            <p>Цена: <?php echo htmlspecialchars($product['price']); ?> руб.</p>
                            <p>Категория: <?php echo htmlspecialchars($product['category']); ?></p>
                            <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>">
                                <input type="hidden" name="product_id" value="<?php echo htmlspecialchars($product['id']); ?>">
                                <input type="submit" name="remove_from_cart" value="Убрать" class="btn">
                            </form>
                        </div>
                    </div>
                <?php } ?>
            </div>
        <?php } else { ?>
            <p>Ваша корзина пуста.</p>
        <?php } ?>
    </div>
</body>
</html>
