<?php
include 'config.php';
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['name'];
    $description = $_POST['description'];
    $price = $_POST['price'];
    $category = $_POST['category'];
    $image_url = $_POST['image_url'];

    $stmt = $conn->prepare("INSERT INTO products (name, description, price, category, image_url) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param('ssdss', $name, $description, $price, $category, $image_url);

    if ($stmt->execute()) {
        $success_message = "Товар успешно добавлен!";
    } else {
        $error_message = "Ошибка при добавлении товара: " . $conn->error;
    }
    $stmt->close();
}
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Добавить товар</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <header>
        <div class="container">
            <h1>Добавить новый товар</h1>
            <nav>
                <a href="index.php" class="nav-link">Главная</a>
                <a href="cart.php" class="nav-link">Корзина</a>
                <a href="add_product.php" class="nav-link">Добавить продукт</a>
            </nav>
        </div>
    </header>
    <div class="container">
        <?php if (isset($success_message)) { ?>
            <div class="success-message"><?php echo $success_message; ?></div>
        <?php } ?>
        <?php if (isset($error_message)) { ?>
            <div class="error-message"><?php echo $error_message; ?></div>
        <?php } ?>
        <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>" class="add-product-form">
            <div class="form-group">
                <label for="name">Название:</label>
                <input type="text" id="name" name="name" required>
            </div>
            <div class="form-group">
                <label for="description">Описание:</label>
                <textarea id="description" name="description" required></textarea>
            </div>
            <div class="form-group">
                <label for="price">Цена:</label>
                <input type="number" id="price" name="price" step="0.01" required>
            </div>
            <div class="form-group">
                <label for="category">Категория:</label>
                <input type="text" id="category" name="category" required>
            </div>
            <div class="form-group">
                <label for="image_url">URL изображения:</label>
                <input type="text" id="image_url" name="image_url" required>
            </div>
            <input type="submit" value="Добавить продукт" class="btn">
            <a href="index.php" class="btn">Вернуться к списку</a>
        </form>
    </div>
</body>
</html>
