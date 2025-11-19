// Main script for Ibyacu frontend: products, cart, auth, language

const STORAGE_KEYS = { CART: 'ibyacu_cart', PRODUCTS: 'ibyacu_products', USER_PRODUCTS: 'ibyacu_user_products', USER: 'ibyacu_user' };

const sampleProducts = [
    { id: 'p1', title: 'Handwoven Basket', price: 25.0, category: 'Textiles', img: 'https://picsum.photos/seed/basket/600/400' },
    { id: 'p2', title: 'Clay Mug', price: 18.5, category: 'Ceramics', img: 'https://picsum.photos/seed/mug/600/400' },
    { id: 'p3', title: 'Beaded Necklace', price: 42.0, category: 'Jewelry', img: 'https://picsum.photos/seed/necklace/600/400' },
    { id: 'p4', title: 'Carved Spoon', price: 12.0, category: 'Woodwork', img: 'https://picsum.photos/seed/spoon/600/400' }
];

function $(sel) { return document.querySelector(sel); }
function $all(sel) { return Array.from(document.querySelectorAll(sel)); }

function loadProducts() {
    const stored = localStorage.getItem(STORAGE_KEYS.PRODUCTS);
    if (stored) return JSON.parse(stored);
    localStorage.setItem(STORAGE_KEYS.PRODUCTS, JSON.stringify(sampleProducts));
    return sampleProducts;
}

function renderProducts(list = null, containerId = 'productGrid'){
    const products = list || loadProducts();
    const grid = document.getElementById(containerId);
    if (!grid) return;
    grid.innerHTML = '';
    products.forEach(p => {
        const card = document.createElement('div');
        card.className = 'product-card';
        card.innerHTML = `
            <img src="${p.img}" alt="${p.title}">
            <h4>${p.title}</h4>
            <div class="meta"><div class="price">$${p.price.toFixed(2)}</div><div class="text-muted">${p.category}</div></div>
            <div class="actions">
                <button class="btn" data-id="${p.id}" onclick="addToCart('${p.id}')">Add to cart</button>
                <a class="btn" href="products.html">View</a>
            </div>
        `;
        grid.appendChild(card);
    });
}

function getCart(){
    return JSON.parse(localStorage.getItem(STORAGE_KEYS.CART) || '[]');
}

function saveCart(cart){
    localStorage.setItem(STORAGE_KEYS.CART, JSON.stringify(cart));
    updateCartCount();
    renderCartItems();
}

function addToCart(id){
    const products = loadProducts();
    const product = products.find(p=>p.id===id);
    if(!product) return alert('Product not found');
    const cart = getCart();
    const item = cart.find(i=>i.id===id);
    if(item) item.qty++;
    else cart.push({ id: product.id, title: product.title, price: product.price, img: product.img, qty: 1 });
    saveCart(cart);
}

function updateCartCount(){
    const countEl = document.getElementById('cartCount');
    if (!countEl) return;
    const total = getCart().reduce((s,i)=>s + i.qty, 0);
    countEl.textContent = total;
}

function renderCartItems(){
    const container = document.getElementById('cartItems');
    if(!container) return;
    const cart = getCart();
    container.innerHTML = '';
    if(cart.length===0){ container.innerHTML = '<div class="text-muted">Cart is empty</div>'; document.getElementById('cartTotal').textContent='0.00'; return; }
    let total = 0;
    cart.forEach(i=>{
        total += i.price * i.qty;
        const el = document.createElement('div');
        el.className = 'cart-item';
        el.innerHTML = `
            <img src="${i.img}" alt="${i.title}">
            <div style="flex:1">
                <div style="font-weight:600">${i.title}</div>
                <div class="text-muted">$${i.price.toFixed(2)} x ${i.qty}</div>
            </div>
            <div style="display:flex;flex-direction:column;gap:6px">
                <button onclick="changeQty('${i.id}',1)">+</button>
                <button onclick="changeQty('${i.id}',-1)">-</button>
            </div>
        `;
        container.appendChild(el);
    });
    document.getElementById('cartTotal').textContent = total.toFixed(2);
}

function changeQty(id, delta){
    const cart = getCart();
    const item = cart.find(i=>i.id===id);
    if(!item) return;
    item.qty += delta;
    if(item.qty<=0){ const idx = cart.indexOf(item); cart.splice(idx,1); }
    saveCart(cart);
}

// Cart panel controls
document.addEventListener('click', (e)=>{
    if(e.target.matches('#cartBtn') || e.target.closest('#cartBtn')){
        const panel = document.getElementById('cartPanel');
        panel.setAttribute('aria-hidden','false');
        renderCartItems();
    }
    if(e.target.matches('#closeCart')){
        document.getElementById('cartPanel').setAttribute('aria-hidden','true');
    }
});

// Checkout placeholder
document.addEventListener('click',(e)=>{
    if(e.target.matches('#checkoutBtn')){
        alert('Checkout placeholder — integrate payment gateway later.');
    }
});

// Auth modal
function showAuth(){ document.getElementById('authModal').setAttribute('aria-hidden','false'); }
function hideAuth(){ document.getElementById('authModal').setAttribute('aria-hidden','true'); }
document.addEventListener('click',(e)=>{
    if(e.target.matches('#authBtn')) showAuth();
    if(e.target.matches('.modal-close')) hideAuth();
    if(e.target.matches('#showLogin')){ $('#loginForm').classList.remove('hidden'); $('#registerForm').classList.add('hidden'); }
    if(e.target.matches('#showRegister')){ $('#loginForm').classList.add('hidden'); $('#registerForm').classList.remove('hidden'); }
});

// Simple login/register that stores a local user (mock)
document.addEventListener('submit',(e)=>{
    if(e.target.matches('#registerForm')){
        e.preventDefault();
        const f = e.target;
        const user = { name:f.name.value, email:f.email.value, seller: f.seller.checked };
        localStorage.setItem(STORAGE_KEYS.USER, JSON.stringify(user));
        alert('Registered locally');
        hideAuth();
    }
    if(e.target.matches('#loginForm')){
        e.preventDefault();
        const f = e.target;
        const stored = JSON.parse(localStorage.getItem(STORAGE_KEYS.USER) || 'null');
        if(stored && stored.email === f.email.value){ alert('Logged in (mock)'); hideAuth(); }
        else alert('No local user found with that email (register first)');
    }
});

// Language switching (EN/FR) simple dictionary
const i18n = {
    en: { heroTitle: 'Handmade arts & crafts — connect with artisans', heroDesc: 'Buy directly from creators. Secure payments.', shopNow: 'Shop Now', sellNow: 'Sell Your Work', categories: 'Shop by Category', featured: 'Featured Products', contact: 'Contact', yourCart: 'Your Cart', checkout: 'Checkout', login: 'Login', register: 'Register', loginBtn: 'Login', registerBtn: 'Register' },
    fr: { heroTitle: 'Arts et artisanat — connectez-vous aux artisans', heroDesc: 'Achetez directement auprès des créateurs. Paiements sécurisés.', shopNow: 'Acheter', sellNow: 'Vendre', categories: 'Par catégorie', featured: 'Produits en vedette', contact: 'Contact', yourCart: 'Votre panier', checkout: 'Payer', login: 'Connexion', register: 'Inscription', loginBtn: 'Connexion', registerBtn: 'Inscription' }
};

function applyLang(lang){
    $all('[data-i18n]').forEach(el=>{ const key = el.getAttribute('data-i18n'); if(i18n[lang] && i18n[lang][key]) el.textContent = i18n[lang][key]; });
    $all('.lang').forEach(b=>b.classList.toggle('active', b.dataset.lang===lang));
}

document.addEventListener('click',(e)=>{
    if(e.target.matches('.lang')) applyLang(e.target.dataset.lang);
});

// Dashboard: allow sellers to add products (persist to USER_PRODUCTS)
function addUserProduct(p){
    const list = JSON.parse(localStorage.getItem(STORAGE_KEYS.USER_PRODUCTS) || '[]');
    list.push(p); localStorage.setItem(STORAGE_KEYS.USER_PRODUCTS, JSON.stringify(list));
    // also add to global products list
    const all = loadProducts(); all.push(p); localStorage.setItem(STORAGE_KEYS.PRODUCTS, JSON.stringify(all));
}

// Category filter
document.addEventListener('click',(e)=>{
    if(e.target.matches('.category')){
        const cat = e.target.dataset.category;
        const all = loadProducts();
        renderProducts(all.filter(p=>p.category===cat));
    }
});

// Initialization
document.addEventListener('DOMContentLoaded', ()=>{
    renderProducts();
    updateCartCount();
    // initial language en
    applyLang('en');
    // render products on products page if present
    if(document.getElementById('productsList')){
        renderProducts(loadProducts(),'productsList');
    }
});

// Expose a couple functions to global scope for inline handlers
window.addToCart = addToCart;
window.changeQty = changeQty;
window.addUserProduct = addUserProduct;

//tailwind configtailwind
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