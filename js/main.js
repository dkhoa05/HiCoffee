/**
 * Hi Coffee — chỉ xử lý UI cụ thể, không chặn submit/link mặc định.
 */
document.addEventListener("DOMContentLoaded", () => {
    const navToggle = document.getElementById("nav-toggle");
    const primaryNav = document.getElementById("primary-nav");
    const navBackdrop = document.getElementById("nav-backdrop");

    const setNavOpen = (open) => {
        if (!navToggle || !primaryNav) {
            return;
        }
        primaryNav.classList.toggle("is-open", open);
        navToggle.setAttribute("aria-expanded", open ? "true" : "false");
        navToggle.setAttribute("aria-label", open ? "Đóng menu" : "Mở menu");
        document.body.classList.toggle("nav-drawer-open", open);
        if (navBackdrop) {
            navBackdrop.classList.toggle("is-visible", open);
            navBackdrop.setAttribute("aria-hidden", open ? "false" : "true");
        }
    };

    const shopForm = document.getElementById("shop-page-form");
    if (shopForm) {
        shopForm.querySelectorAll(".shop-category-chips .shop-chip__input").forEach((input) => {
            input.addEventListener("change", () => {
                if (typeof shopForm.requestSubmit === "function") {
                    shopForm.requestSubmit();
                } else {
                    shopForm.submit();
                }
            });
        });
    }

    if (navToggle && primaryNav) {
        navToggle.addEventListener("click", () => {
            const next = !primaryNav.classList.contains("is-open");
            setNavOpen(next);
        });

        primaryNav.querySelectorAll("a").forEach((link) => {
            link.addEventListener("click", () => setNavOpen(false));
        });

        if (navBackdrop) {
            navBackdrop.addEventListener("click", () => setNavOpen(false));
        }

        document.addEventListener("keydown", (e) => {
            if (e.key === "Escape") {
                setNavOpen(false);
            }
        });
    }

    const deleteLinks = document.querySelectorAll('a[href*="delete_product.php"]');
    deleteLinks.forEach((link) => {
        link.addEventListener("click", (event) => {
            if (!confirm("Bạn có chắc chắn muốn xóa sản phẩm này không?")) {
                event.preventDefault();
            }
        });
    });

    const quantityInput = document.getElementById("quantity");
    const increaseBtn = document.getElementById("increase-quantity");
    const decreaseBtn = document.getElementById("decrease-quantity");
    const totalPrice = document.getElementById("total-price");

    if (quantityInput && increaseBtn && decreaseBtn && totalPrice) {
        const pricePerUnit = parseInt(totalPrice.getAttribute("data-price"), 10) || 0;

        increaseBtn.addEventListener("click", () => {
            let currentValue = parseInt(quantityInput.value, 10);
            currentValue++;
            quantityInput.value = currentValue;
            totalPrice.textContent = (currentValue * pricePerUnit).toLocaleString("vi-VN") + " VND";
        });

        decreaseBtn.addEventListener("click", () => {
            let currentValue = parseInt(quantityInput.value, 10);
            if (currentValue > 1) {
                currentValue--;
                quantityInput.value = currentValue;
                totalPrice.textContent = (currentValue * pricePerUnit).toLocaleString("vi-VN") + " VND";
            }
        });
    }
});
