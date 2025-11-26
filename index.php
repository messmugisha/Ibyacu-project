<?php
require 'conn.php';

$productsResult = $conn->query("
    SELECT p.product_id, p.product_name, p.description, p.price, p.quantity, p.image, u.username
    FROM pdts p
    JOIN users u ON p.user_id = u.user_id
    ORDER BY p.product_id DESC
");

$marketProducts = $productsResult ? $productsResult->fetch_all(MYSQLI_ASSOC) : [];
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <base target="_self">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ArtisanCraft - Handmade Arts & Crafts Marketplace</title>
    <meta name="description" content="Connect with artisans and discover unique handmade arts and crafts. Buy directly from creators worldwide.">
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
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
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=Playfair+Display:wght@400;500;600;700&display=swap" rel="stylesheet">
</head>

<body class="min-h-screen bg-light font-body text-dark">
    <!-- Header with Navigation -->
    <header class="bg-white shadow-lg sticky top-0 z-50">
        <div class="container mx-auto px-4">
            <!-- Top Bar with Language & Cart -->
            <div class="flex justify-between items-center py-2 border-b">
                <div class="flex items-center space-x-4">
                    <!-- Language Switcher -->
                    <div class="relative group">
                        <button class="flex items-center space-x-1 text-gray-600 hover:text-primary transition-colors">
                            <i class="fas fa-globe"></i>
                            <span>Kiny</span>
                            <i class="fas fa-chevron-down text-xs"></i>
                        </button>
                        <div class="absolute hidden group-hover:block bg-white shadow-lg rounded-lg mt-2 w-32">
                            <button class="block w-full text-left px-4 py-2 hover:bg-gray-100 transition-colors">Ikinyarwanda</button>
                            <button class="block w-full text-left px-4 py-2 hover:bg-gray-100 transition-colors">Icyongereza</button>
                            <button class="block w-full text-left px-4 py-2 hover:bg-gray-100 transition-colors">Igifaransa</button>
                        </div>
                    </div>
                </div>

                <div class="flex items-center space-x-4">
                    <!-- Cart Viewer -->
                    <button id="cartButton" class="relative flex items-center space-x-1 text-gray-600 hover:text-primary transition-colors">
                        <i class="fas fa-shopping-cart text-xl"></i>
                        <span class="cart-count bg-primary text-white rounded-full w-5 h-5 text-xs flex items-center justify-center">0</span>
                    </button>

                    <!-- Auth Buttons -->
                    <div class="flex items-center space-x-2">
                        <button id="loginBtn" class="px-4 py-2 text-gray-600 hover:text-primary transition-colors">Injira</button>
                        <button id="registerBtn" class="px-4 py-2 bg-primary text-white rounded-lg hover:bg-secondary transition-colors">Iyandikishe</button>
                    </div>
                </div>
            </div>

            <!-- Main Navigation -->
            <nav class="flex justify-between items-center py-4">
                <!-- Logo -->
                <div class="flex items-center">
                    <h1 class="text-2xl font-display font-bold text-dark"><b>Ibyacu</b></h1>
                </div>

                <!-- Navigation Links -->
                <div class="hidden md:flex items-center space-x-8">
                    <a href="#" class="nav-link text-gray-700 hover:text-primary transition-colors font-medium">Ahabanza</a>
                    <a href="#" class="nav-link text-gray-700 hover:text-primary transition-colors font-medium">Ibicuruzwa</a>
                    <div class="relative group">
                        <button class="flex items-center space-x-1 text-gray-700 hover:text-primary transition-colors font-medium">
                            Ibyiciro
                            <i class="fas fa-chevron-down text-xs"></i>
                        </button>
                        <div class="absolute hidden group-hover:block bg-white shadow-lg rounded-lg mt-2 w-48">
                            <a href="#" class="block px-4 py-2 hover:bg-gray-100 transition-colors">Ibibumbano n’inyongeramusaruro</a>
                            <a href="#" class="block px-4 py-2 hover:bg-gray-100 transition-colors">Imyenda n’udushingwe</a>
                            <a href="#" class="block px-4 py-2 hover:bg-gray-100 transition-colors">Ibikoresho by’imbaho</a>
                            <a href="#" class="block px-4 py-2 hover:bg-gray-100 transition-colors">Imitako</a>
                            <a href="#" class="block px-4 py-2 hover:bg-gray-100 transition-colors">Ishusho n’Igishushanyo</a>
                        </div>
                    </div>
                    <a href="#" class="nav-link text-gray-700 hover:text-primary transition-colors font-medium">Abahanzi</a>
                    <a href="#" class="nav-link text-gray-700 hover:text-primary transition-colors font-medium">Twandikire</a>
                </div>

                <!-- Search Bar -->
                <div class="hidden md:flex items-center">
                    <div class="relative">
                        <input type="text" placeholder="Shakisha ibihangano..." class="pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-primary transition-colors w-64">
                        <i class="fas fa-search absolute left-3 top-3 text-gray-400"></i>
                    </div>
                </div>

                <!-- Mobile Menu Button -->
                <button class="md:hidden text-gray-700">
                    <i class="fas fa-bars text-xl"></i>
                </button>
            </nav>
        </div>
    </header>

    <!-- Main Content -->
    <main>
        <!-- Hero Section -->
        <section class="bg-gradient-to-r from-primary to-secondary text-white py-20">
            <div class="container mx-auto px-4 text-center">
                <h2 class="text-4xl md:text-6xl font-display font-bold mb-6">Menya ibihangano by’umwimerere</h2>
                <p class="text-xl mb-8 max-w-2xl mx-auto">Hura n’abahanzi b’isi yose, ugire ibicuruzwa byakozwe n’intoki byuzuye umwimerere n’urukundo.</p>
                <div class="flex flex-col sm:flex-row justify-center space-y-4 sm:space-y-0 sm:space-x-4">
                    <button class="bg-accent text-dark px-8 py-3 rounded-lg font-semibold hover:bg-opacity-90 transition-colors">Gura Ubu</button>
                    <button class="border-2 border-white text-white px-8 py-3 rounded-lg font-semibold hover:bg-white hover:text-dark transition-colors">Ba Umuhanzi</button>
                </div>
            </div>
        </section>


        <!-- Featured Products Section -->
        <section class="py-16">
            <div class="container mx-auto px-4">
                <h3 class="text-3xl font-display font-bold text-center text-dark mb-12">Ibicuruzwa Bihari </h3>
                
                <div class="relative">
               <span class="absolute top-2 left-2 bg-primary text-white text-xs font-bold px-3 py-1 rounded-full shadow">
                  NEW
               </span>
                </div>
                <div id="pr
                oductsGrid" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-8"> <?php if (!$marketProducts): ?> <p class="col-span-1 sm:col-span-2 lg:col-span-4 text-center text-gray-500 py-12"> Nta bicuruzwa byashyizweho ubu. </p> <?php else: ?> <?php foreach ($marketProducts as $product): ?> <?php $imageSrc = !empty($product['image']) ? $product['image'] : 'https://via.placeholder.com/300x300?text=ArtisanCraft';
                                                                                                                                                                                                                                                                                                                            $priceValue = (float) $product['price'];
                                                                                                                                                                                                                                                                                                                            $artisanName = $product['username'] ?? 'Artisan'; ?> <div class="bg-white rounded-lg shadow-md overflow-hidden hover:shadow-lg transition-shadow border border-light"> <img src="<?= htmlspecialchars($imageSrc); ?>" alt="<?= htmlspecialchars($product['product_name']); ?>" class="w-full h-48 object-cover" loading="lazy">
                                <div class="p-4">
                                    <div class="flex justify-between items-start mb-2">
                                        <h4 class="font-semibold text-dark text-lg"><?= htmlspecialchars($product['product_name']); ?></h4> <span class="text-primary font-bold"> <?= number_format($priceValue, 2); ?> FRW </span>
                                    </div>
                                    <p class="text-gray-600 text-sm mb-2"><span class="font-bold font-2xl  " style="font-size: 17px;">Umucuruzi: </span><?= htmlspecialchars($artisanName); ?></p>
                                    <p class="text-gray-500 text-sm mb-3 h-12 overflow-hidden text-ellipsis"> <span class="font-bold font-2xl  " style="font-size: 17px;">Ubusobanuro: </span> <?= htmlspecialchars($product['description']); ?> <br> </p>
                                    <div class="flex space-x-2"> <button class="add-to-cart flex-1 bg-primary text-white py-2 rounded-lg hover:bg-secondary transition-colors" data-id="<?= (int) $product['product_id']; ?>" data-name="<?= htmlspecialchars($product['product_name'], ENT_QUOTES); ?>" data-price="<?= $priceValue; ?>" data-image="<?= htmlspecialchars($imageSrc, ENT_QUOTES); ?>"> Shyira mu gikapu </button> <button class="w-10 h-10 bg-gray-100 rounded-lg flex items-center justify-center hover:bg-gray-200 transition-colors"> <i class="far fa-heart text-gray-600"></i> </button> </div>
                                </div>
                            </div>

                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            </div>
        </section>

        <!-- Categories Section -->
        <section class="py-16 bg-white">
            <div class="container mx-auto px-4">
                <h3 class="text-3xl font-display font-bold text-center text-dark mb-12">Sura ushingiye ku byiciro</h3>
                <div class="grid grid-cols-2 md:grid-cols-5 gap-6">
                    <?php
                    $categories = [
                        ['icon' => 'fa-vase', 'label' => 'Ibibumbano'],
                        ['icon' => 'fa-tshirt', 'label' => 'Imyenda'],
                        ['icon' => 'fa-gem', 'label' => 'Imitako'],
                        ['icon' => 'fa-palette', 'label' => 'Ubugeni'],
                        ['icon' => 'fa-hammer', 'label' => 'Ibikoresho by’imbaho'],
                    ];
                    foreach ($categories as $category):
                    ?>
                        <div class="category-card text-center p-6 rounded-lg bg-light hover:bg-accent transition-colors cursor-pointer">
                            <div class="w-16 h-16 bg-primary rounded-full flex items-center justify-center mx-auto mb-4">
                                <i class="fas <?= $category['icon']; ?> text-white text-2xl"></i>
                            </div>
                            <h4 class="font-semibold text-dark"><?= $category['label']; ?></h4>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </section>
    </main>

    <!-- Footer -->
    <footer class="bg-dark text-white py-12">
        <div class="container mx-auto px-4">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-8">
                <div>
                    <h4 class="text-xl font-display font-bold mb-4">ArtisanCraft</h4>
                    <p class="text-gray-300">Duhuza abahanzi n’abaguzi ku isi. Shakisha ibihangano by’umwimerere kandi uteze imbere abahanzi bigenga.</p>
                </div>
                <div>
                    <h5 class="font-semibold mb-4">Amasano yihuse</h5>
                    <ul class="space-y-2">
                        <li><a href="#" class="text-gray-300 hover:text-white transition-colors">Ibyerekeye Twebwe</a></li>
                        <li><a href="#" class="text-gray-300 hover:text-white transition-colors">Twandikire</a></li>
                        <li><a href="#" class="text-gray-300 hover:text-white transition-colors">Ibibazo bikunze kubazwa</a></li>
                        <li><a href="#" class="text-gray-300 hover:text-white transition-colors">Amakuru yo kohereza</a></li>
                    </ul>
                </div>
                <div>
                    <h5 class="font-semibold mb-4">Ku bahanzi</h5>
                    <ul class="space-y-2">
                        <li><a href="#" class="text-gray-300 hover:text-white transition-colors">Gurisha kuri ArtisanCraft</a></li>
                        <li><a href="#" class="text-gray-300 hover:text-white transition-colors">Ikibaho cy’umucuruzi</a></li>
                        <li><a href="#" class="text-gray-300 hover:text-white transition-colors">Ibiciro</a></li>
                        <li><a href="#" class="text-gray-300 hover:text-white transition-colors">Ubufasha n’ibikoresho</a></li>
                    </ul>
                </div>
                <div>
                    <h5 class="font-semibold mb-4">Guma tuvugana</h5>
                    <div class="flex space-x-4 mb-4">
                        <a href="#" class="text-gray-300 hover:text-white transition-colors"><i class="fab fa-facebook text-xl"></i></a>
                        <a href="#" class="text-gray-300 hover:text-white transition-colors"><i class="fab fa-instagram text-xl"></i></a>
                        <a href="#" class="text-gray-300 hover:text-white transition-colors"><i class="fab fa-pinterest text-xl"></i></a>
                    </div>
                    <p class="text-gray-300">Iyandikishe kuri buriya butumwa</p>
                    <div class="flex mt-2">
                        <input type="email" placeholder="Imeyili yawe" class="px-3 py-2 bg-gray-700 text-white rounded-l-lg focus:outline-none w-full">
                        <button class="bg-primary px-4 py-2 rounded-r-lg hover:bg-secondary transition-colors">Iyandikishe</button>
                    </div>
                </div>
            </div>
            <div class="border-t border-gray-700 mt-8 pt-8 text-center text-gray-300">
                <p>&copy; 2025 ArtisanCraft. Uburenganzira bwose burabitswe.</p>
            </div>
        </div>
    </footer>

    <!-- Modals -->
    <!-- Login Modal -->
    <div id="loginModal" class="fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center z-50 p-4">
        <div class="bg-white rounded-lg max-w-md w-full p-6">
            <div class="flex justify-between items-center mb-6">
                <h3 class="text-2xl font-display font-bold text-dark">Injira</h3>
                <button class="close-modal text-gray-500 hover:text-dark">
                    <i class="fas fa-times text-xl"></i>
                </button>
            </div>
            <form method="post" action="logic.php">
                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Amazina</label>
                        <input type="text" required name="username" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-primary transition-colors">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Ijambo ry'ibanga</label>
                        <input type="password" required name="key" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-primary transition-colors">
                    </div>
                    <button type="submit" name="login" class="w-full bg-primary text-white py-3 rounded-lg hover:bg-secondary transition-colors font-semibold">Injira</button>
                </div>
            </form>
            <div class="text-center mt-4">
                <p class="text-gray-600">Nta konti ufite? <button class="text-primary hover:underline switch-to-register">Iyandikishe hano</button></p>
            </div>
        </div>
    </div>

    <!-- Register Modal -->
    <div id="registerModal" class="fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center z-50 p-4">
        <div class="bg-white rounded-lg max-w-md w-full p-6 max-h-[80vh] overflow-y-auto">
            <div class="flex justify-between items-center mb-6">
                <h3 class="text-2xl font-display font-bold text-dark">Hanga konte</h3>
                <button class="close-modal text-gray-500 hover:text-dark">
                    <i class="fas fa-times text-xl"></i>
                </button>
            </div>
            <form action="logic.php" method="post">
                <div class="space-y-4">
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Amazina</label>
                            <input type="text" required name="username" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-primary transition-colors">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Imeyili</label>
                            <input type="email" name="email" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-primary transition-colors">
                        </div>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Aho utuye</label>
                        <input type="text" required name="location" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-primary transition-colors">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Telefone</label>
                        <input type="text" required name="contacts" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-primary transition-colors">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Ibyerekeye wowe</label>
                        <input type="text" required name="bio" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-primary transition-colors">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Ubwoko</label>
                        <input type="text" required name="type" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-primary transition-colors">
                    </div>
                    <div class="grid grid-cols-1 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Ijambo ry'ibanga</label>
                            <input type="password" required name="password" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-primary transition-colors">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Emeza ijambo ry'ibanga</label>
                            <input type="password" required name="confirm_password" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-primary transition-colors">
                        </div>
                    </div>
                    <button type="submit" name="register" class="w-full bg-primary text-white py-3 rounded-lg hover:bg-secondary transition-colors font-semibold">Iyandikishe</button>
                </div>
            </form>
            <div class="text-center mt-4">
                <p class="text-gray-600">Ufite konte? <button class="text-primary hover:underline switch-to-login">Injira hano</button></p>
            </div>
        </div>
    </div>

    <!-- Cart Modal -->
    <div id="cartModal" class="fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center z-50 p-4">
        <div class="bg-white rounded-lg max-w-2xl w-full p-6">
            <div class="flex justify-between items-center mb-6">
                <h3 class="text-2xl font-display font-bold text-dark">Igikapu cyawe</h3>
                <button class="close-modal text-gray-500 hover:text-dark">
                    <i class="fas fa-times text-xl"></i>
                </button>
            </div>
            <div id="cartItems" class="space-y-4 max-h-96 overflow-y-auto">
                <!-- Cart items will be dynamically loaded here -->
            </div>
            <div class="border-t pt-4 mt-4">
                <div class="flex justify-between items-center mb-4">
                    <span class="text-lg font-semibold">Igiteranyo:</span>
                    <span class="text-lg font-semibold" id="cartTotal">$0.00</span>
                </div>
                <button class="w-full bg-primary text-white py-3 rounded-lg hover:bg-secondary transition-colors font-semibold mb-4" id="checkoutBtn">Komeza wishyure</button>
                <form id="paymentForm" class="space-y-3 hidden">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Uburyo bwo kwishyura</label>
                        <select name="provider" id="paymentProvider" class="w-full border rounded-lg px-3 py-2 focus:outline-none focus:border-primary" required>
                            <option value="">-- Hitamo --</option>
                            <option value="MTN MoMo">MTN MoMo</option>
                            <option value="Airtel Money">Airtel Money</option>
                            <option value="Phone Payment">Phone Payment</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Numero ya telefone</label>
                        <input type="tel" name="phone" id="paymentPhone" pattern="[0-9]{10}" maxlength="10" class="w-full border rounded-lg px-3 py-2 focus:outline-none focus:border-primary" placeholder="07xxxxxxxx" required>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Amazina y’umwishyuzi</label>
                        <input type="text" name="payer_name" id="paymentName" class="w-full border rounded-lg px-3 py-2 focus:outline-none focus:border-primary" placeholder="Amazina yawe" required>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Amafaranga</label>
                        <input type="text" id="paymentAmount" class="w-full border rounded-lg px-3 py-2 bg-gray-100" readonly>
                    </div>
                    <button type="submit" class="w-full bg-green-600 text-white py-3 rounded-lg hover:bg-green-700 transition-colors font-semibold">Emeza kwishyura</button>
                    <p class="text-xs text-gray-500 text-center">Uzakira ubutumwa bwa MoMo cyangwa Airtel bwo kubyemeza.</p>
                </form>
            </div>
        </div>
    </div>

    <script>
        const cart = [];
        let cartTotalValue = 0;

        function updateCartDisplay() {
            const cartCount = document.querySelector('.cart-count');
            const cartItems = document.getElementById('cartItems');
            const cartTotal = document.getElementById('cartTotal');

            if (cartCount) {
                cartCount.textContent = cart.reduce((sum, item) => sum + item.quantity, 0);
            }

            if (!cartItems || !cartTotal) {
                return;
            }

            if (cart.length === 0) {
                cartItems.innerHTML = '<p class="text-center text-gray-500 py-8">Igikapu cyawe kiracyari ubusa</p>';
                cartTotal.textContent = "$0.00";
                return;
            }

            cartItems.innerHTML = cart.map(item => `
                <div class="flex items-center space-x-4 border-b pb-4">
                    <img src="${item.image}" alt="${item.name}" class="w-16 h-16 object-cover rounded">
                    <div class="flex-1">
                        <h5 class="font-semibold">${item.name}</h5>
                        <p class="text-gray-600 text-sm">${(item.price * item.quantity).toFixed(2)}Frw</p>
                    </div>
                    <div class="flex items-center space-x-2">
                        <button class="decrease-quantity w-8 h-8 bg-gray-100 rounded flex items-center justify-center" data-id="${item.id}">-</button>
                        <span>${item.quantity}</span>
                        <button class="increase-quantity w-8 h-8 bg-gray-100 rounded flex items-center justify-center" data-id="${item.id}">+</button>
                        <button class="remove-item text-red-500 hover:text-red-700 ml-2" data-id="${item.id}">
                            <i class="fas fa-trash"></i>
                        </button>
                    </div>
                </div>
            `).join('');

            const total = cart.reduce((sum, item) => sum + (item.price * item.quantity), 0);
            cartTotal.textContent = `$${total.toFixed(2)}`;
            cartTotalValue = total;
            const paymentAmount = document.getElementById('paymentAmount');
            if (paymentAmount) {
                paymentAmount.value = `${total.toFixed(2)}FRW`;
            }
        }

        function setupModals() {
            const modals = {
                login: document.getElementById('loginModal'),
                register: document.getElementById('registerModal'),
                cart: document.getElementById('cartModal')
            };

            const openers = {
                login: document.getElementById('loginBtn'),
                register: document.getElementById('registerBtn'),
                cart: document.getElementById('cartButton')
            };

            Object.entries(openers).forEach(([key, button]) => {
                if (button && modals[key]) {
                    button.addEventListener('click', (event) => {
                        event.preventDefault();
                        Object.values(modals).forEach(modal => modal && modal.classList.add('hidden'));
                        modals[key].classList.remove('hidden');
                        modals[key].classList.add('flex');
                    });
                }
            });

            document.querySelectorAll('.close-modal').forEach(closer => {
                closer.addEventListener('click', () => {
                    Object.values(modals).forEach(modal => {
                        if (modal) {
                            modal.classList.add('hidden');
                            modal.classList.remove('flex');
                        }
                    });
                });
            });

            const toRegister = document.querySelector('.switch-to-register');
            const toLogin = document.querySelector('.switch-to-login');

            if (toRegister && modals.login && modals.register) {
                toRegister.addEventListener('click', () => {
                    modals.login.classList.add('hidden');
                    modals.login.classList.remove('flex');
                    modals.register.classList.remove('hidden');
                    modals.register.classList.add('flex');
                });
            }

            if (toLogin && modals.login && modals.register) {
                toLogin.addEventListener('click', () => {
                    modals.register.classList.add('hidden');
                    modals.register.classList.remove('flex');
                    modals.login.classList.remove('hidden');
                    modals.login.classList.add('flex');
                });
            }

            Object.values(modals).forEach(modal => {
                if (!modal) return;
                modal.addEventListener('click', (event) => {
                    if (event.target === modal) {
                        modal.classList.add('hidden');
                        modal.classList.remove('flex');
                    }
                });
            });
        }

        function setupCartInteractions() {
            document.addEventListener('click', (event) => {
                const addButton = event.target.closest('.add-to-cart');
                if (addButton) {
                    event.preventDefault();
                    const productId = parseInt(addButton.dataset.id, 10);
                    if (Number.isNaN(productId)) {
                        return;
                    }

                    const existing = cart.find(item => item.id === productId);
                    if (existing) {
                        existing.quantity += 1;
                    } else {
                        cart.push({
                            id: productId,
                            name: addButton.dataset.name,
                            price: parseFloat(addButton.dataset.price || '0'),
                            image: addButton.dataset.image || 'https://via.placeholder.com/80',
                            quantity: 1
                        });
                    }
                    updateCartDisplay();
                    return;
                }

                const increaseButton = event.target.closest('.increase-quantity');
                if (increaseButton) {
                    const productId = parseInt(increaseButton.dataset.id, 10);
                    const item = cart.find(product => product.id === productId);
                    if (item) {
                        item.quantity += 1;

                        updateCartDisplay();
                    }
                    return;
                }

                const decreaseButton = event.target.closest('.decrease-quantity');
                if (decreaseButton) {
                    const productId = parseInt(decreaseButton.dataset.id, 10);
                    const itemIndex = cart.findIndex(product => product.id === productId);
                    if (itemIndex > -1) {
                        if (cart[itemIndex].quantity > 1) {
                            cart[itemIndex].quantity -= 1;
                        } else {
                            cart.splice(itemIndex, 1);
                        }
                        updateCartDisplay();
                    }
                    return;
                }

                const removeButton = event.target.closest('.remove-item');
                if (removeButton) {
                    const productId = parseInt(removeButton.dataset.id, 10);
                    const itemIndex = cart.findIndex(product => product.id === productId);
                    if (itemIndex > -1) {
                        cart.splice(itemIndex, 1);
                        updateCartDisplay();
                    }
                }
            });
        }

        document.addEventListener('DOMContentLoaded', () => {
            setupModals();
            setupCartInteractions();
            updateCartDisplay();
            const checkoutBtn = document.getElementById('checkoutBtn');
            const paymentForm = document.getElementById('paymentForm');
            const paymentProvider = document.getElementById('paymentProvider');
            const paymentPhone = document.getElementById('paymentPhone');
            const paymentName = document.getElementById('paymentName');

            if (checkoutBtn && paymentForm) {
                checkoutBtn.addEventListener('click', (event) => {
                    event.preventDefault();
                    if (cart.length === 0) {
                        alert('Ongeramo ibicuruzwa mu gikapu mbere yo kwishyura.');
                        return;
                    }
                    paymentForm.classList.toggle('hidden');
                });
            }

            if (paymentForm) {
                paymentForm.addEventListener('submit', (event) => {
                    event.preventDefault();
                    if (cart.length === 0) {
                        alert('Igikapu cyawe kiracyari ubusa.');
                        return;
                    }
                    if (!paymentProvider.value || !paymentPhone.value || !paymentName.value) {
                        alert('Reba neza amakuru yo kwishyura.');
                        return;
                    }
                    alert(`Murakoze! Twakiriye icyifuzo cyo kwishyura ukoresheje ${paymentProvider.value} kuri nimero ${paymentPhone.value} kingana na $${cartTotalValue.toFixed(2)}.`);
                    paymentForm.classList.add('hidden');
                });
            }
        });
    </script>
</body>

</html>