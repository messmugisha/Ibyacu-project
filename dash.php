<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit();
}

require 'conn.php';

$userId = (int) $_SESSION['user_id'];

$userStmt = $conn->prepare("
    SELECT username, email, location, contacts, bio
    FROM users
    WHERE user_id = ?
");
$userStmt->bind_param('i', $userId);
$userStmt->execute();
$user = $userStmt->get_result()->fetch_assoc();
$userStmt->close();

$productsStmt = $conn->prepare("
    SELECT product_id, product_name, description, price, quantity, image
    FROM pdts
    WHERE user_id = ?
    ORDER BY product_id DESC
");
$productsStmt->bind_param('i', $userId);
$productsStmt->execute();
$productsResult = $productsStmt->get_result();
$products = $productsResult->fetch_all(MYSQLI_ASSOC);
$productsStmt->close();

$totalProducts = count($products);
$totalStock = array_sum(array_map(static fn($product) => (int) $product['quantity'], $products));
$portfolioValue = array_sum(array_map(static fn($product) => (float) $product['price'] * (int) $product['quantity'], $products));

$editProduct = null;
if (isset($_GET['edit'])) {
    $editId = (int) $_GET['edit'];
    $editStmt = $conn->prepare("
        SELECT product_id, product_name, description, price, quantity, image
        FROM pdts
        WHERE product_id = ? AND user_id = ?
        LIMIT 1
    ");
    $editStmt->bind_param('ii', $editId, $userId);
    $editStmt->execute();
    $editProduct = $editStmt->get_result()->fetch_assoc() ?: null;
    $editStmt->close();
}

$flash = $_SESSION['product_flash'] ?? null;
unset($_SESSION['product_flash']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Ibyacu - Dashboard</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        primary: "#8B4513",
                        secondary: "#D2691E",
                        accent: "#F4A460",
                        dark: "#2C1810",
                        light: "#FAF3E0"
                    },
                    fontFamily: {
                        'display': ['Playfair Display', 'serif'],
                        'body': ['Inter', 'sans-serif']
                    }
                }
            }
        }
    </script>
</head>
<body class="bg-gray-100">
<div class="flex h-screen">
    <aside class="w-64 bg-white shadow-lg hidden md:block">
        <div class="p-6 border-b">
            <h2 class="text-2xl font-bold text-gray-900">Dashboard</h2>
            <p class="text-sm text-gray-500 mt-1"><?= htmlspecialchars($user['username'] ?? 'Umuhanzi'); ?></p>
        </div>
        <nav class="p-4 space-y-2">
            <a href="#products" class="block px-4 py-2 rounded hover:bg-gray-200">📦 Products</a>
            <a href="#profile" class="block px-4 py-2 rounded hover:bg-gray-200">👤 Manage Account</a>
            <a href="index.php" class="block px-4 py-2 rounded hover:bg-gray-200">🏠 Back Home</a>
            <a href="logout.php" class="block px-4 py-2 rounded bg-red-500 text-white text-center">🚪 Logout</a>
        </nav>
    </aside>

    <main class="flex-1 overflow-y-auto">
        <header class="bg-white px-6 py-4 shadow flex justify-between items-center">
            <h1 class="text-xl font-semibold">Welcome, <?= htmlspecialchars($user['username'] ?? 'Umuhanzi'); ?></h1>
            <a href="logout.php" class="px-4 py-2 bg-red-500 text-white rounded">Logout</a>
        </header>

        <div class="p-6 space-y-8">
            <?php if ($flash): ?>
                <div class="<?= $flash['status'] === 'success' ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700'; ?> px-4 py-3 rounded">
                    <?= htmlspecialchars($flash['message']); ?>
                </div>
            <?php endif; ?>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <div class="p-6 bg-white shadow rounded-lg">
                    <h3 class="text-lg font-bold mb-2">Products Posted</h3>
                    <p class="text-3xl font-bold text-indigo-600"><?= $totalProducts; ?></p>
                </div>
                <div class="p-6 bg-white shadow rounded-lg">
                    <h3 class="text-lg font-bold mb-2">Items in Stock</h3>
                    <p class="text-3xl font-bold text-green-600"><?= $totalStock; ?></p>
                </div>
                <div class="p-6 bg-white shadow rounded-lg">
                    <h3 class="text-lg font-bold mb-2">Portfolio Value</h3>
                    <p class="text-3xl font-bold text-blue-500">$<?= number_format($portfolioValue, 2); ?></p>
                </div>
            </div>

            <section id="products" class="bg-white rounded-lg shadow p-6">
                <div class="flex justify-between items-center mb-4">
                    <h2 class="text-xl font-semibold">📦 My Products</h2>
                    <?php if ($editProduct): ?>
                        <a href="dash.php" class="px-4 py-2 bg-gray-200 text-gray-700 rounded-lg">Cancel edit</a>
                    <?php endif; ?>
                </div>

                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                    <form action="logic.php" method="post" enctype="multipart/form-data" class="bg-gray-50 p-4 rounded-lg space-y-4">
                        <h3 class="text-lg font-semibold"><?= $editProduct ? 'Update product' : 'Add new product'; ?></h3>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Product name</label>
                            <input
                                type="text"
                                name="product_name"
                                required
                                value="<?= htmlspecialchars($editProduct['product_name'] ?? ''); ?>"
                                class="w-full px-3 py-2 border rounded focus:outline-none focus:ring-1 focus:ring-gray-900">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Description</label>
                            <textarea
                                name="description"
                                rows="3"
                                required
                                class="w-full px-3 py-2 border rounded focus:outline-none focus:ring-1 focus:ring-gray-900"><?= htmlspecialchars($editProduct['description'] ?? ''); ?></textarea>
                        </div>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Price ($)</label>
                                <input
                                    type="number"
                                    min="0"
                                    step="0.01"
                                    name="price"
                                    required
                                    value="<?= htmlspecialchars($editProduct['price'] ?? ''); ?>"
                                    class="w-full px-3 py-2 border rounded focus:outline-none focus:ring-1 focus:ring-gray-900">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Quantity</label>
                                <input
                                    type="number"
                                    min="0"
                                    name="quantity"
                                    required
                                    value="<?= htmlspecialchars($editProduct['quantity'] ?? 0); ?>"
                                    class="w-full px-3 py-2 border rounded focus:outline-none focus:ring-1 focus:ring-gray-900">
                            </div>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Product image</label>
                            <input
                                type="file"
                                name="image"
                                accept="image/*"
                                class="w-full px-3 py-2 border rounded focus:outline-none focus:ring-1 focus:ring-gray-900">
                            <p class="text-xs text-gray-500 mt-1">Accepted: jpg, png, webp up to 2MB.</p>
                            <?php if (!empty($editProduct['image'])): ?>
                                <img src="<?= htmlspecialchars($editProduct['image']); ?>" alt="Preview" class="mt-3 w-24 h-24 object-cover rounded">
                            <?php endif; ?>
                        </div>
                        <?php if ($editProduct): ?>
                            <input type="hidden" name="product_id" value="<?= (int) $editProduct['product_id']; ?>">
                            <button type="submit" name="update_product" class="w-full bg-blue-600 text-white py-2 rounded-lg hover:bg-blue-700 transition">Update product</button>
                        <?php else: ?>
                            <button type="submit" name="create_product" class="w-full bg-gray-900 text-white py-2 rounded-lg hover:bg-black transition">Add product</button>
                        <?php endif; ?>
                    </form>

                    <div class="overflow-x-auto">
                        <table class="w-full border-collapse">
                            <thead>
                            <tr class="bg-gray-100 text-left">
                                <th class="p-3">Image</th>
                                <th class="p-3">Product</th>
                                <th class="p-3">Price</th>
                                <th class="p-3">Stock</th>
                                <th class="p-3 text-center">Actions</th>
                            </tr>
                            </thead>
                            <tbody>
                            <?php if (!$products): ?>
                                <tr>
                                    <td colspan="5" class="p-4 text-center text-gray-500">No products yet. Start by adding one.</td>
                                </tr>
                            <?php endif; ?>
                            <?php foreach ($products as $product): ?>
                                <tr class="border-b">
                                    <td class="p-3">
                                        <?php if (!empty($product['image'])): ?>
                                            <img src="<?= htmlspecialchars($product['image']); ?>" class="w-16 h-16 object-cover rounded" alt="<?= htmlspecialchars($product['product_name']); ?>">
                                        <?php else: ?>
                                            <span class="text-gray-400 text-sm">No image</span>
                                        <?php endif; ?>
                                    </td>
                                    <td class="p-3">
                                        <p class="font-semibold"><?= htmlspecialchars($product['product_name']); ?></p>
                                        <p class="text-sm text-gray-500 overflow-hidden text-ellipsis"><?= htmlspecialchars($product['description']); ?></p>
                                    </td>
                                    <td class="p-3">$<?= number_format((float) $product['price'], 2); ?></td>
                                    <td class="p-3"><?= (int) $product['quantity']; ?></td>
                                    <td class="p-3 text-center space-x-2">
                                        <a href="dash.php?edit=<?= (int) $product['product_id']; ?>" class="px-3 py-1 bg-blue-500 text-white rounded inline-block">Edit</a>
                                        <form action="logic.php" method="post" class="inline-block" onsubmit="return confirm('Delete this product?');">
                                            <input type="hidden" name="product_id" value="<?= (int) $product['product_id']; ?>">
                                            <button type="submit" name="delete_product" class="px-3 py-1 bg-red-500 text-white rounded">Delete</button>
                                        </form>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </section>

            <section id="profile" class="bg-white rounded-lg shadow p-6">
                <h2 class="text-xl font-semibold mb-4">👤 Manage Account</h2>
                <div class="space-y-2 text-gray-700">
                    <p><span class="font-semibold">Username:</span> <?= htmlspecialchars($user['username'] ?? ''); ?></p>
                    <p><span class="font-semibold">Email:</span> <?= htmlspecialchars($user['email'] ?? '—'); ?></p>
                    <p><span class="font-semibold">Location:</span> <?= htmlspecialchars($user['location'] ?? '—'); ?></p>
                    <p><span class="font-semibold">Contacts:</span> <?= htmlspecialchars($user['contacts'] ?? '—'); ?></p>
                    <p><span class="font-semibold">Bio:</span> <?= htmlspecialchars($user['bio'] ?? '—'); ?></p>
                </div>
            </section>
        </div>
    </main>
</div>
</body>
</html>

