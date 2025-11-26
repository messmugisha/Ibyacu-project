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
    <!-- Tailwind -->
    <link href="./tailwind/src/output.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link rel="shortcut icon" href="./design/logo.png" type="image/x-icon">

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

    <!-- MOBILE TOP NAV -->
    <header class="md:hidden bg-white px-4 py-3 shadow flex justify-between items-center sticky top-0 z-30">
        <h1 class="text-lg font-semibold">Murakaza neza, <?= htmlspecialchars($user['username']); ?></h1>
        <button id="menuBtn" class="text-gray-700 text-2xl">☰</button>
    </header>

    <div class="flex h-screen overflow-hidden">

        <!-- SIDEBAR -->
        <aside id="sidebar"
            class="w-64 bg-white shadow-lg fixed md:static inset-y-0 left-0 transform -translate-x-full md:translate-x-0 transition-transform duration-300 z-40">
            <div class="p-6 border-b">
                <h2 class="text-2xl font-bold text-gray-900">Mucunguzu</h2>
                <p class="text-sm text-gray-500 mt-1"><?= htmlspecialchars($user['username']); ?></p>
            </div>

            <nav class="p-4 space-y-2">
                <a href="index.php" class="block px-4 py-2 rounded hover:bg-gray-200 smooth">🏠 Gusubira Ahabanza</a>
                <a href="#profile" class="block px-4 py-2 rounded hover:bg-gray-200 smooth">👤 Konti yawe</a>
                <a href="#reports" class="block px-4 py-2 rounded hover:bg-gray-200 smooth">📊 Raporo</a>
                <a href="#products" id="pdts" class="block px-4 py-2 rounded hover:bg-gray-200 smooth">📦Ibicuruzwa Washizeho</a>
                <a href="logout.php" class="block px-4 py-2 rounded bg-red-500 text-white text-center"><i class="fas fa-sign-out-alt text-white-600"></i>
                    Sohoka</a>
            </nav>
            <script>
                function showSection(sectionId) {
                    // All sections
                    const sections = ['products', 'profile'];

                    // Hide all
                    sections.forEach(id => {
                        document.getElementById(id).style.display = 'none';
                    });

                    // Show selected
                    document.getElementById(sectionId).style.display = 'block';

                    // Smooth scroll to top
                    window.scrollTo({
                        top: 0,
                        behavior: 'smooth'
                    });
                }

                // Default page load → Show products only
                showSection('products');
            </script>
        </aside>

        <!-- MAIN AREA -->
        <main id="main" class="flex-1 overflow-y-auto">

            <header class="hidden md:flex bg-white px-6 py-4 shadow justify-between items-center sticky top-0 z-20">
                <h1 class="text-xl font-semibold">Murakaza neza, <?= htmlspecialchars($user['username']); ?></h1>
                <span><a href="logout.php" class="px-4 py-2 bg-red-500 text-white rounded"><i class="fas fa-sign-out text-white-50"></i> Sohoka</a></span>
            </header>

            <div class="p-6 space-y-8">

                <!-- FLASH MESSAGE -->
                <?php if ($flash): ?>
                    <div class="<?= $flash['status'] === 'success' ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700'; ?> px-4 py-3 rounded">
                        <?= htmlspecialchars($flash['message']); ?>
                    </div>
                <?php endif; ?>

                <!-- STAT CARDS -->
                <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-6">
                    <div class="p-6 bg-white shadow rounded-lg">
                        <h3 class="text-lg font-bold mb-2">Ibicuruzwa Wamaze Gushyiraho</h3>
                        <p class="text-3xl font-bold text-indigo-600"><?= $totalProducts; ?></p>
                    </div>
                    <div class="p-6 bg-white shadow rounded-lg">
                        <h3 class="text-lg font-bold mb-2">Ibyaguzwe Kumunsi</h3>
                        <p class="text-3xl font-bold text-green-600"><?= $totalStock; ?></p>
                    </div>
                    <div class="p-6 bg-white shadow rounded-lg">
                        <h3 class="text-lg font-bold mb-2">Agaciro k’Ibicuruzwa</h3>
                        <p class="text-3xl font-bold text-blue-500"><?= number_format($portfolioValue, 2); ?> FRW</p>
                    </div>
                </div>

                <!-- PRODUCTS SECTION -->
                <section id="products" class="bg-white rounded-lg shadow p-6 scroll-mt-20">
                    <div class="flex justify-between items-center mb-4">
                        <h2 class="text-xl font-semibold">📦 Ibicuruzwa Byanjye</h2>
                        <?php if ($editProduct): ?>
                            <a href="dash.php" class="px-4 py-2 bg-gray-200 text-gray-700 rounded-lg">Hagarika Guhindura</a>
                        <?php endif; ?>
                    </div>

                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">

                        <!-- FORM -->
                        <form action="logic.php" id="input" method="post" enctype="multipart/form-data"
                            class="bg-gray-50 p-4 rounded-lg space-y-4">

                            <h3 class="text-lg font-semibold">
                                <?= $editProduct ? 'Hindura Igicuruzwa' : 'Ongeraho Igicuruzwa Gishya'; ?>
                            </h3>

                            <div>
                                <label class="block text-sm font-medium text-gray-700">Izina ry'Igicuruzwa</label>
                                <input type="text" name="product_name" required
                                    value="<?= htmlspecialchars($editProduct['product_name'] ?? ''); ?>"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-lg outline-none focus:border-gray-400">
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700">Ibisobanuro</label>
                                <textarea name="description" rows="3" required
                                    class="w-full px-3 py-2 border border-gray-300 rounded-lg outline-none focus:border-gray-400"><?= htmlspecialchars($editProduct['description'] ?? ''); ?></textarea>
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Igiciro ($)</label>
                                    <input type="number" min="0" step="0.01" name="price" required
                                        value="<?= htmlspecialchars($editProduct['price'] ?? ''); ?>"
                                        class="w-full px-3 py-2 border border-gray-300 rounded-lg outline-none focus:border-gray-400">
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Umubare</label>
                                    <input type="number" min="0" name="quantity" required
                                        value="<?= htmlspecialchars($editProduct['quantity'] ?? 0); ?>"
                                        class="w-full px-3 py-2 border border-gray-300 rounded-lg outline-none focus:border-gray-400">
                                </div>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700">Ifoto y’Igicuruzwa</label>
                                <input type="file" name="image" accept="image/*"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-lg bg-white">
                                <p class="text-xs text-gray-500">jpg, png, webp kugeza 2MB</p>

                                <?php if (!empty($editProduct['image'])): ?>
                                    <img src="<?= htmlspecialchars($editProduct['image']); ?>" class="mt-3 w-24 h-24 object-cover rounded">
                                <?php endif; ?>
                            </div>

                            <?php if ($editProduct): ?>
                                <input type="hidden" name="product_id" value="<?= (int) $editProduct['product_id']; ?>">
                                <button type="submit" name="update_product" class="btn-primary">Hindura</button>
                            <?php else: ?>
                                <button
                                    class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg shadow-md transition" name="create_product">
                                    Ongeraho Igicuruzwa
                                </button>
                                <button id="reset" type="reset" class="px-4 py-2 bg-gray-300 hover:bg-gray-400 text-gray-700 rounded-lg shadow-md transition ml-2">Siba</button>
                            <?php endif; ?>
                            <script>
                                document.getElementById('reset').addEventListener('click', function() {
                                    // Clear image preview
                                    const all = document.getElementById('form');
                                    if (all) {
                                        all.remove();
                                    }
                                    const imgPreview = this.parentElement.querySelector('img');
                                    if (imgPreview) {
                                        imgPreview.remove();
                                    }
                                });
                            </script>
                        </form>


                        <!-- TABLE -->
                        <div id="table" class="overflow-x-auto">
                            <table class="w-full border-collapse">
                                <thead>
                                    <tr class="bg-gray-100 text-left">
                                        <th class="p-3">Ifoto</th>
                                        <th class="p-3">Igicuruzwa</th>
                                        <th class="p-3">Igiciro</th>
                                        <th class="p-3">Umubare</th>
                                        <th class="p-3 text-center">Ibikorwa</th>
                                    </tr>
                                </thead>

                                <tbody>
                                    <?php if (!$products): ?>
                                        <tr>
                                            <td colspan="5" class="p-4 text-center text-gray-500">Nta bicuruzwa bihari.</td>
                                        </tr>
                                    <?php endif; ?>

                                    <?php foreach ($products as $product): ?>
                                        <tr class="border-b">
                                            <td class="p-3">
                                                <?php if ($product['image']): ?>
                                                    <img src="<?= htmlspecialchars($product['image']); ?>" class="w-16 h-16 rounded object-cover">
                                                <?php else: ?>
                                                    <span class="text-gray-400 text-sm">Nta foto</span>
                                                <?php endif; ?>
                                            </td>

                                            <td class="p-3">
                                                <p class="font-semibold"><?= htmlspecialchars($product['product_name']); ?></p>
                                                <p class="text-sm text-gray-500"><?= htmlspecialchars($product['description']); ?></p>
                                            </td>

                                            <td class="p-3">$<?= number_format($product['price'], 2); ?></td>
                                            <td class="p-3"><?= (int)$product['quantity']; ?></td>

                                            <td class="p-3 text-center space-x-2">
                                                <a href="dash.php?edit=<?= $product['product_id']; ?>"
                                                    class="px-3 py-1 bg-blue-500 text-white rounded">
                                                    Hindura
                                                </a>

                                                <form action="logic.php" method="post" class="inline-block"
                                                    onsubmit="return confirm('Ushaka gusiba iki gicuruzwa?');">
                                                    <input type="hidden" name="product_id" value="<?= $product['product_id']; ?>">
                                                    <button type="submit" name="delete_product"
                                                        class="px-3 py-1 bg-red-500 text-white rounded">
                                                        Siba
                                                    </button>
                                                </form>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>

                            </table>
                        </div>
                    </div>

                </section>

                <!-- PROFILE -->
                <section id="profile" class="bg-white rounded-lg shadow p-6 scroll-mt-20">
                    <h2 class="text-xl font-semibold mb-4">👤 Konti Yawe</h2>

                    <div class="space-y-2 text-gray-700">
                        <p><span class="font-semibold">Izina ukoresha:</span> <?= htmlspecialchars($user['username']); ?></p>
                        <p><span class="font-semibold">Email:</span> <?= htmlspecialchars($user['email']); ?></p>
                        <p><span class="font-semibold">Aho utuye:</span> <?= htmlspecialchars($user['location']); ?></p>
                        <p><span class="font-semibold">Telefone:</span> <?= htmlspecialchars($user['contacts']); ?></p>
                        <p><span class="font-semibold">Bio:</span> <?= htmlspecialchars($user['bio']); ?></p>
                    </div>
                </section>

            </div>
        </main>
    </div>

    <script>
        // Smooth scrolling
        document.querySelectorAll('.smooth').forEach(link => {
            link.addEventListener('click', e => {
                const target = document.querySelector(link.getAttribute('href'));
                if (target) {
                    e.preventDefault();
                    window.scrollTo({
                        top: target.offsetTop - 80,
                        behavior: 'smooth'
                    });
                }
            });
        });

        // Mobile menu toggle
        document.getElementById('menuBtn')?.addEventListener('click', () => {
            document.getElementById('sidebar').classList.toggle('-translate-x-full');
        });
    </script>

</body>


</html>