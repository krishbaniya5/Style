const navToggle = document.getElementById("navToggle");
const navMenu = document.getElementById("navMenu");

if (navToggle && navMenu) {
    navToggle.addEventListener("click", () => {
        navMenu.classList.toggle("active");
    });
}

const productCards = document.querySelectorAll(".product-card");
const productDetailSection = document.getElementById("productDetail");
const productsSection = document.getElementById("products");
const featuredSection = document.getElementById("featured");

const detailImage = document.getElementById("detailImage");
const detailTitle = document.getElementById("detailTitle");
const detailPrice = document.getElementById("detailPrice");
const detailDescription = document.getElementById("detailDescription");

const hiddenName = document.getElementById("detailProductName");
const hiddenPrice = document.getElementById("detailProductPrice");
const hiddenQty = document.getElementById("detailQuantity");

const backToProducts = document.getElementById("backToProducts");

productCards.forEach(card => {
    const viewBtn = card.querySelector(".view-detail-btn");
    if (viewBtn) {
        viewBtn.addEventListener("click", () => {
            const { name, price, desc, image } = card.dataset;
            detailImage.src = image;
            detailTitle.textContent = name;
            detailPrice.textContent = "$" + price;
            detailDescription.textContent = desc;
            hiddenName.value = name;
            hiddenPrice.value = price;
            productsSection.style.display = "none";
            featuredSection.style.display = "none";
            productDetailSection.style.display = "block";
            window.scrollTo(0, 0);
        });
    }
});

if (backToProducts) {
    backToProducts.addEventListener("click", () => {
        productDetailSection.style.display = "none";
        productsSection.style.display = "block";
        featuredSection.style.display = "block";
    });
}

const sizeButtons = document.querySelectorAll(".size-btn");
let selectedSize = "";

sizeButtons.forEach(btn => {
    btn.addEventListener("click", () => {
        sizeButtons.forEach(b => b.classList.remove("active"));
        btn.classList.add("active");
        selectedSize = btn.dataset.size;
    });
});

const quantityInput = document.getElementById("quantityInput");
document.getElementById("increaseQty").addEventListener("click", () => {
    quantityInput.value++;
});
document.getElementById("decreaseQty").addEventListener("click", () => {
    if (quantityInput.value > 1) quantityInput.value--;
});

let cart = [];
const cartBadge = document.querySelector(".cart-count");

function updateCartCount() {
    if (cartBadge) {
        cartBadge.textContent = cart.length;
    }
}

const addToCartBtn = document.createElement("button");
addToCartBtn.id = "addToCart";
addToCartBtn.textContent = "Add to Cart";
addToCartBtn.className = "btn";
document.querySelector(".detail-info").append(addToCartBtn);

addToCartBtn.addEventListener("click", () => {
    if (!selectedSize) {
        alert("Please select a size!");
        return;
    }
    cart.push({
        name: hiddenName.value,
        price: hiddenPrice.value,
        size: selectedSize,
        qty: quantityInput.value,
    });
    updateCartCount();
    alert("Item added to cart!");
});

const buyNowForm = document.getElementById("buyNowForm");
if (buyNowForm) {
    buyNowForm.addEventListener("submit", (e) => {
        e.preventDefault();
        if (!selectedSize) {
            alert("Please select a size to continue!");
            return;
        }
        hiddenQty.value = quantityInput.value;
        const params = new URLSearchParams({
            name: hiddenName.value,
            price: hiddenPrice.value,
            size: selectedSize,
            qty: quantityInput.value
        });
        window.location.href = `checkout.html?${params}`;
    });
}

document.addEventListener("click", (e) => {
    if (e.target.classList.contains("add-to-cart-btn")) {
        e.preventDefault();
        const card = e.target.closest(".product-card");
        const item = {
            name: card.dataset.name,
            price: parseFloat(card.dataset.price),
            size: "M",
            qty: 1
        };
        cart.push(item);
        updateCartCount();
        fetch("add_to_cart.php", {
            method: "POST",
            headers: { "Content-Type": "application/json" },
            body: JSON.stringify(item)
        })
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                alert("Added to cart!");
            } else {
                alert("Please log in first.");
            }
        })
        .catch(err => console.error("Error:", err));
    }
});

const contactForm = document.getElementById("contactForm");
if (contactForm) {
    contactForm.addEventListener("submit", (e) => {
        const name = document.getElementById("name").value.trim();
        const email = document.getElementById("email").value.trim();
        const subject = document.getElementById("subject").value.trim();
        const message = document.getElementById("message").value.trim();
        if (name.length < 3) {
            alert("Name must be at least 3 characters!");
            e.preventDefault();
        } else if (!email.includes("@") || !email.includes(".")) {
            alert("Please enter a valid email!");
            e.preventDefault();
        } else if (subject.length < 3) {
            alert("Subject must be at least 3 characters!");
            e.preventDefault();
        } else if (message.length < 10) {
            alert("Message must be at least 10 characters!");
            e.preventDefault();
        }
    });
}

if (window.location.pathname.includes("login.html")) {
    const loginForm = document.getElementById("loginForm");
    if (loginForm) {
        loginForm.addEventListener("submit", (e) => {
            const username = document.getElementById("username").value.trim();
            const password = document.getElementById("password").value.trim();
            if (username.length < 3) {
                alert("Username must be at least 3 characters!");
                e.preventDefault();
            } else if (password.length < 6) {
                alert("Password must be at least 6 characters!");
                e.preventDefault();
            }
        });
    }
}

const logo = document.getElementById("logo");
if (logo) {
    logo.addEventListener("click", () => {
        window.location.href = "index.html";
    });
}

const shopNowBtn = document.getElementById("shopNowBtn");
if (shopNowBtn) {
    shopNowBtn.addEventListener("click", () => {
        productsSection.scrollIntoView({ behavior: "smooth" });
    });
}
