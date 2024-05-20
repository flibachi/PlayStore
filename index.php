<?php
include 'config.php';
session_start();

// Initialize cart session
if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

// Add product to cart
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_to_cart'])) {
    $product_id = $_POST['product_id'];
    if (!in_array($product_id, $_SESSION['cart'])) {
        $_SESSION['cart'][] = $product_id;
    }
}

// Delete selected products
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_products'])) {
    $selected_products = $_POST['selected_products'] ?? [];
    $stmt = $conn->prepare("DELETE FROM products WHERE id = ?");
    foreach ($selected_products as $product_id) {
        $stmt->bind_param('i', $product_id);
        $stmt->execute();
    }
    $stmt->close();
}

// Get unique categories from the database
$categories_sql = "SELECT DISTINCT category FROM products";
$categories_result = $conn->query($categories_sql);

// Category filter
$category_filter = $_GET['category'] ?? "";

// Sorting by price
$sort_order = $_GET['sort'] ?? "";
$sort_sql = "";
if ($sort_order == "asc") {
    $sort_sql = "ORDER BY price ASC";
} elseif ($sort_order == "desc") {
    $sort_sql = "ORDER BY price DESC";
}

// Form SQL query to get products with filtering and sorting
$sql = "SELECT id, name, description, price, category, image_url FROM products";
if ($category_filter) {
    $sql .= " WHERE category = ?";
}
$sql .= " $sort_sql";

$stmt = $conn->prepare($sql);
if ($category_filter) {
    $stmt->bind_param('s', $category_filter);
}
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Список игр</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <header>
        <div class="container">
            <h1>Магазин игр</h1>
            <nav>
                <a href="index.php" class="nav-link">Главная</a>
                <a href="cart.php" class="nav-link">Корзина (<?php echo count($_SESSION['cart']); ?>)</a>
            </nav>
        </div>
    </header>
    <div class="container">
        <form method="get" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>" class="filters">
            <div class="filter-item">
                <label for="category-filter">Фильтр по категории:</label>
                <select name="category" id="category-filter">
                    <option value="">Все категории</option>
                    <?php while ($category_row = $categories_result->fetch_assoc()) { ?>
                        <option value="<?php echo htmlspecialchars($category_row['category']); ?>" <?php if ($category_row['category'] == $category_filter) echo "selected"; ?>><?php echo htmlspecialchars($category_row['category']); ?></option>
                    <?php } ?>
                </select>
            </div>
            <div class="filter-item">
                <label for="sort-filter">Сортировка по цене:</label>
                <select name="sort" id="sort-filter">
                    <option value="">По умолчанию</option>
                    <option value="asc" <?php if ($sort_order == "asc") echo "selected"; ?>>По возрастанию</option>
                    <option value="desc" <?php if ($sort_order == "desc") echo "selected"; ?>>По убыванию</option>
                </select>
            </div>
            <input type="submit" value="Применить" class="btn">
        </form>
        <div class="product-list">
            <?php while ($row = $result->fetch_assoc()) { ?>
                <div class="product-card">
                    <img src="<?php echo htmlspecialchars($row['image_url']); ?>" alt="<?php echo htmlspecialchars($row['name']); ?>" class="product-image">
                    <div class="product-details">
                        <h3><?php echo htmlspecialchars($row['name']); ?></h3>
                        <p><?php echo htmlspecialchars($row['description']); ?></p>
                        <p>Категория: <?php echo htmlspecialchars($row['category']); ?></p>
                        <p><?php echo htmlspecialchars($row['price']); ?>р</p>
                        <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>">
                            <input type="hidden" name="product_id" value="<?php echo htmlspecialchars($row['id']); ?>">
                            <input type="submit" name="add_to_cart" value="В корзину" class="btn">
                        </form>
                    </div>
                </div>
            <?php } ?>
        </div>
    </div>
</body>
</html>
