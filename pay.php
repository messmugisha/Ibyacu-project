<?php
session_start();

$defaultTotal = isset($_GET['total']) ? max(0, (float) $_GET['total']) : 0;
$defaultPhone = $_SESSION['user_phone'] ?? '';
$defaultName  = $_SESSION['username'] ?? '';

$providers = [
    [
        'id'      => 'mtn',
        'label'   => 'MTN MoMo',
        'number'  => '*182#',
        'color'   => 'bg-yellow-100 border-yellow-300',
        'accent'  => 'text-yellow-700',
        'details' => 'Quick mobile transfers across Rwanda.',
        'logo'    => 'https://upload.wikimedia.org/wikipedia/commons/thumb/5/5d/MTN_Group_logo.svg/2560px-MTN_Group_logo.svg.png',
    ],
    [
        'id'      => 'airtel',
        'label'   => 'Airtel Money',
        'number'  => '*182#',
        'color'   => 'bg-red-100 border-red-300',
        'accent'  => 'text-red-700',
        'details' => 'Pay from any Airtel Money wallet instantly.',
        'logo'    => 'https://upload.wikimedia.org/wikipedia/commons/thumb/2/29/Airtel_logo.svg/2560px-Airtel_logo.svg.png',
    ],
    [
        'id'      => 'visa',
        'label'   => 'Card / Visa',
        'number'  => 'Web Checkout',
        'color'   => 'bg-blue-100 border-blue-300',
        'accent'  => 'text-blue-700',
        'details' => 'Secure debit & credit card payments.',
        'logo'    => 'https://upload.wikimedia.org/wikipedia/commons/4/41/Visa_Logo.png',
    ],
];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Ibyacu - Pay Securely</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
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
<body class="bg-gray-50 min-h-screen">
    <header class="bg-white shadow-sm">
        <div class="max-w-6xl mx-auto px-6 py-4 flex justify-between items-center">
            <div>
                <p class="text-xs uppercase tracking-widest text-gray-500">Secure checkout</p>
                <h1 class="text-2xl font-semibold text-gray-900">Twishyure Umutekano</h1>
            </div>
            <a href="index.php" class="text-sm text-primary border px-4 py-2 rounded-md hover:bg-gray-100">← Back to shop</a>
        </div>
    </header>

    <main class="max-w-6xl mx-auto px-6 py-10 grid grid-cols-1 lg:grid-cols-2 gap-8">
        <section class="space-y-6">
            <div class="bg-white shadow rounded-2xl p-6">
                <h2 class="text-lg font-semibold mb-4">1. Hitamo uburyo bwo kwishyura</h2>
                <div class="space-y-4" id="providerCards">
                    <?php foreach ($providers as $provider): ?>
                        <label class="flex items-center gap-4 p-4 border rounded-xl cursor-pointer transition hover:shadow <?=
                            htmlspecialchars($provider['color']);
                        ?>" data-provider="<?= htmlspecialchars($provider['id']); ?>">
                            <input type="radio" name="provider" value="<?= htmlspecialchars($provider['id']); ?>" class="hidden">
                            <img src="<?= htmlspecialchars($provider['logo']); ?>" alt="<?= htmlspecialchars($provider['label']); ?>" class="h-10 object-contain">
                            <div>
                                <p class="font-semibold text-gray-900"><?= htmlspecialchars($provider['label']); ?></p>
                                <p class="text-sm text-gray-500"><?= htmlspecialchars($provider['details']); ?></p>
                            </div>
                            <span class="ml-auto text-xs font-medium px-2 py-1 rounded-full border <?= htmlspecialchars($provider['accent']); ?>">
                                <?= htmlspecialchars($provider['number']); ?>
                            </span>
                        </label>
                    <?php endforeach; ?>
                </div>
            </div>

            <div class="bg-white shadow rounded-2xl p-6">
                <h2 class="text-lg font-semibold mb-4">2. Uzuza amakuru ya Mobile Money</h2>
                <form id="paymentDetails" class="space-y-4">
                    <div>
                        <label class="block text-sm text-gray-600 mb-1">Izina ry'umwishyuzi</label>
                        <input type="text" name="payer_name" value="<?= htmlspecialchars($defaultName); ?>" class="w-full border rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-gray-900" placeholder="Amazina yawe" required>
                    </div>
                    <div>
                        <label class="block text-sm text-gray-600 mb-1">Numero ya telefoni</label>
                        <input type="tel" name="phone" value="<?= htmlspecialchars($defaultPhone); ?>" pattern="[0-9]{10}" maxlength="10" class="w-full border rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-gray-900" placeholder="07xxxxxxxx" required>
                    </div>
                    <div>
                        <label class="block text-sm text-gray-600 mb-1">Amafaranga uri kwishyura (USD)</label>
                        <input type="number" step="0.01" min="1" name="amount" value="<?= htmlspecialchars(number_format($defaultTotal, 2, '.', '')); ?>" class="w-full border rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-gray-900" required>
                    </div>
                    <div>
                        <label class="block text-sm text-gray-600 mb-1">Icyo uri kwishyura</label>
                        <textarea name="note" rows="2" class="w-full border rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-gray-900" placeholder="Urugero: Ibicuruzwa 3 by'ubukorikori" required></textarea>
                    </div>
                    <button type="submit" class="w-full bg-gray-900 text-white py-3 rounded-lg text-sm font-semibold">Andikira MoMo Push</button>
                </form>
            </div>
        </section>

        <section class="space-y-6">
            <div class="bg-white shadow rounded-2xl p-6">
                <h2 class="text-lg font-semibold mb-4">3. Ibisobanuro by'ubwishyu</h2>
                <div class="space-y-4 text-sm text-gray-600">
                    <div class="flex justify-between">
                        <span>Umubare w'ibicuruzwa</span>
                        <span id="summaryNote">-</span>
                    </div>
                    <div class="flex justify-between">
                        <span>Umubare w'amafaranga</span>
                        <span>$<span id="summaryAmount">0.00</span></span>
                    </div>
                    <div class="flex justify-between">
                        <span>Serivisi (2.5%)</span>
                        <span>$<span id="summaryFees">0.00</span></span>
                    </div>
                    <div class="flex justify-between font-semibold text-gray-900 text-base">
                        <span>Igiteranyo cyose</span>
                        <span>$<span id="summaryTotal">0.00</span></span>
                    </div>
                    <hr>
                    <div class="flex justify-between text-sm">
                        <span>Uburyo bwo kwishyura</span>
                        <span id="summaryProvider">Nta na bumwe</span>
                    </div>
                </div>
            </div>

            <div class="bg-white shadow rounded-2xl p-6 space-y-4">
                <h2 class="text-lg font-semibold">4. Uko byifashe ubu</h2>
                <div class="space-y-2 text-sm" id="statusLog">
                    <p class="text-gray-500">Tegereza utange igitekerezo cyawe…</p>
                </div>
                <button id="simulateBtn" class="w-full border border-gray-900 text-gray-900 py-3 rounded-lg font-semibold disabled:opacity-40" disabled>Simulate Payment</button>
            </div>
        </section>
    </main>

    <script>
        const providerCards = document.querySelectorAll('#providerCards label');
        const paymentForm = document.getElementById('paymentDetails');
        const summaryAmount = document.getElementById('summaryAmount');
        const summaryFees = document.getElementById('summaryFees');
        const summaryTotal = document.getElementById('summaryTotal');
        const summaryNote = document.getElementById('summaryNote');
        const summaryProvider = document.getElementById('summaryProvider');
        const statusLog = document.getElementById('statusLog');
        const simulateBtn = document.getElementById('simulateBtn');

        let selectedProvider = '';

        function formatNumber(value) {
            return Number(value || 0).toFixed(2);
        }

        function updateSummary() {
            const formData = new FormData(paymentForm);
            const amount = parseFloat(formData.get('amount')) || 0;
            const fees = amount * 0.025;
            const total = amount + fees;

            summaryAmount.textContent = formatNumber(amount);
            summaryFees.textContent = formatNumber(fees);
            summaryTotal.textContent = formatNumber(total);
            summaryNote.textContent = formData.get('note') || '-';
            summaryProvider.textContent = selectedProvider || 'Nta na bumwe';

            simulateBtn.disabled = !(selectedProvider && amount > 0 && formData.get('phone'));
        }

        providerCards.forEach(card => {
            card.addEventListener('click', () => {
                providerCards.forEach(c => c.classList.remove('ring-2', 'ring-gray-900'));
                const radio = card.querySelector('input[type="radio"]');
                radio.checked = true;
                selectedProvider = card.dataset.provider?.toUpperCase() || '';
                card.classList.add('ring-2', 'ring-gray-900');
                updateSummary();
                logStatus(`✔️ Wahisemo ${card.textContent.trim()}`);
            });
        });

        paymentForm.addEventListener('input', updateSummary);
        paymentForm.addEventListener('submit', (event) => {
            event.preventDefault();
            updateSummary();
            logStatus('📨 Twohereje MoMo push kuri telefoni yawe. Emeza ubwishyu.');
        });

        function logStatus(message) {
            const time = new Date().toLocaleTimeString();
            const entry = document.createElement('p');
            entry.className = 'text-gray-700';
            entry.textContent = `[${time}] ${message}`;
            statusLog.prepend(entry);
        }

        simulateBtn.addEventListener('click', () => {
            simulateBtn.disabled = true;
            logStatus('🔄 Kwemeza… Tegereza amasegonda make.');
            setTimeout(() => {
                logStatus('✅ Kwishyura byagenze neza! Murakoze gukoresha Ibyacu.');
            }, 2000);
        });

        updateSummary();
    </script>
</body>
</html>

