(() => {
    const nav = document.getElementById('siteNav');
    const onScroll = () => nav?.classList.toggle('is-scrolled', window.scrollY > 24);
    onScroll();
    window.addEventListener('scroll', onScroll, { passive: true });

    const toggle = document.getElementById('navToggle');
    const links = document.getElementById('navLinks');
    toggle?.addEventListener('click', () => links?.classList.toggle('is-open'));

    const slides = [...document.querySelectorAll('.hero-slide')];
    const dotsWrap = document.getElementById('heroDots');
    const titleEl = document.getElementById('heroTitle');
    const subEl = document.getElementById('heroSub');
    const progress = document.getElementById('heroProgress');
    let index = 0;
    let timer;

    const paintCopy = () => {
        const slide = slides[index];
        if (!slide) return;
        if (titleEl && slide.dataset.title) titleEl.textContent = slide.dataset.title;
        if (subEl && slide.dataset.sub) subEl.textContent = slide.dataset.sub;
        if (progress) {
            progress.style.animation = 'none';
            progress.offsetHeight;
            progress.style.animation = '';
        }
    };

    const armSlide = (el) => {
        if (!el || el.style.backgroundImage || !el.dataset.bg) return;
        el.style.backgroundImage = `url("${el.dataset.bg}")`;
    };
    const go = (next) => {
        if (!slides.length) return;
        slides[index].classList.remove('is-active');
        index = (next + slides.length) % slides.length;
        armSlide(slides[index]);
        armSlide(slides[(index + 1) % slides.length]);
        slides[index].classList.add('is-active');
        dotsWrap?.querySelectorAll('button').forEach((d, i) => d.classList.toggle('is-active', i === index));
        paintCopy();
    };

    if (dotsWrap && slides.length) {
        slides.forEach((_, i) => {
            const b = document.createElement('button');
            b.type = 'button';
            b.addEventListener('click', () => { go(i); restart(); });
            if (i === 0) b.classList.add('is-active');
            dotsWrap.appendChild(b);
        });
    }

    const restart = () => {
        clearInterval(timer);
        timer = setInterval(() => go(index + 1), 5500);
    };
    document.querySelector('[data-hero="prev"]')?.addEventListener('click', () => { go(index - 1); restart(); });
    document.querySelector('[data-hero="next"]')?.addEventListener('click', () => { go(index + 1); restart(); });
    if (slides.length > 1) {
        armSlide(slides[1]);
        restart();
    }
    document.addEventListener('visibilitychange', () => {
        if (document.hidden) clearInterval(timer);
        else if (slides.length > 1) restart();
    });

    const authModal = document.getElementById('authModal');
    const openAuth = (tab) => {
        if (!authModal) return;
        authModal.classList.add('is-open');
        authModal.hidden = false;
        setTab(tab || 'login');
    };
    const closeAuth = () => {
        authModal?.classList.remove('is-open');
        if (authModal) authModal.hidden = true;
    };
    const setTab = (tab) => {
        document.querySelectorAll('[data-auth-tab]').forEach((b) => b.classList.toggle('is-active', b.dataset.authTab === tab));
        const loginForm = document.getElementById('formLogin');
        const registerForm = document.getElementById('formRegister');
        if (loginForm) loginForm.hidden = tab !== 'login';
        if (registerForm) registerForm.hidden = tab !== 'register';
    };
    document.querySelectorAll('[data-open-auth]').forEach((el) => {
        el.addEventListener('click', () => openAuth(el.dataset.openAuth));
    });
    document.querySelectorAll('[data-close-auth]').forEach((el) => el.addEventListener('click', closeAuth));
    document.querySelectorAll('[data-auth-tab]').forEach((el) => el.addEventListener('click', () => setTab(el.dataset.authTab)));
    authModal?.addEventListener('click', (e) => { if (e.target === authModal) closeAuth(); });

    const measureModal = document.getElementById('measureModal');
    const desc = document.getElementById('measureDescripcion');
    const openMeasure = (producto) => {
        if (!measureModal) return;
        if (desc && producto && producto !== '1') {
            if (!desc.value || desc.value.startsWith('Me interesa:')) {
                desc.value = 'Me interesa: ' + producto;
            }
        }
        measureModal.classList.add('is-open');
        measureModal.hidden = false;
    };
    const closeMeasure = () => {
        measureModal?.classList.remove('is-open');
        if (measureModal) measureModal.hidden = true;
    };
    document.querySelectorAll('[data-open-measure]').forEach((el) => {
        el.addEventListener('click', () => openMeasure(el.getAttribute('data-open-measure')));
    });
    document.querySelectorAll('[data-close-measure]').forEach((el) => el.addEventListener('click', closeMeasure));
    measureModal?.addEventListener('click', (e) => { if (e.target === measureModal) closeMeasure(); });

    const buyModal = document.getElementById('buyModal');
    const buyState = { price: 0 };
    const cop = (n) => '$' + Math.round(Number(n) || 0).toLocaleString('es-CO');
    const paintBuyTotal = () => {
        const qty = Math.max(1, parseInt(document.getElementById('buyCantidad')?.value || '1', 10));
        const total = document.getElementById('buyTotal');
        if (total) total.value = cop(buyState.price * qty);
    };
    const openBuy = (btn) => {
        if (!buyModal || !btn) return;
        const id = btn.getAttribute('data-buy-id') || '';
        const name = btn.getAttribute('data-buy-name') || '';
        const price = parseFloat(btn.getAttribute('data-buy-price') || '0') || 0;
        const stock = Math.max(1, parseInt(btn.getAttribute('data-buy-stock') || '1', 10));
        const image = btn.getAttribute('data-buy-image') || '';
        buyState.price = price;
        const idInput = document.getElementById('buyProductoId');
        const nameEl = document.getElementById('buyNombre');
        const priceEl = document.getElementById('buyPrecioTxt');
        const stockEl = document.getElementById('buyStockTxt');
        const qty = document.getElementById('buyCantidad');
        const foto = document.getElementById('buyFoto');
        if (idInput) idInput.value = id;
        if (nameEl) nameEl.textContent = name;
        if (priceEl) priceEl.textContent = cop(price);
        if (stockEl) stockEl.textContent = stock + (stock === 1 ? ' disponible' : ' disponibles');
        if (qty) {
            qty.max = String(stock);
            qty.value = '1';
        }
        if (foto) foto.style.backgroundImage = image ? `url('${image}')` : '';
        paintBuyTotal();
        buyModal.classList.add('is-open');
        buyModal.hidden = false;
    };
    const closeBuy = () => {
        buyModal?.classList.remove('is-open');
        if (buyModal) buyModal.hidden = true;
    };
    document.querySelectorAll('[data-open-buy]').forEach((el) => {
        el.addEventListener('click', () => openBuy(el));
    });
    document.querySelectorAll('[data-close-buy]').forEach((el) => el.addEventListener('click', closeBuy));
    buyModal?.addEventListener('click', (e) => { if (e.target === buyModal) closeBuy(); });
    document.getElementById('buyCantidad')?.addEventListener('input', paintBuyTotal);

    document.addEventListener('keydown', (e) => {
        if (e.key !== 'Escape') return;
        if (authModal?.classList.contains('is-open')) closeAuth();
        if (measureModal?.classList.contains('is-open')) closeMeasure();
        if (buyModal?.classList.contains('is-open')) closeBuy();
    });

    const params = new URLSearchParams(window.location.search);
    if (params.has('cotizar')) {
        openMeasure(params.get('cotizar'));
    }

    const lightbox = document.getElementById('lightbox');
    const lightboxImg = document.getElementById('lightboxImg');
    const openLightbox = (src, alt) => {
        if (!lightbox || !lightboxImg) return;
        lightboxImg.src = src;
        lightboxImg.alt = alt || '';
        lightbox.classList.add('is-open');
        lightbox.hidden = false;
    };
    const closeLightbox = () => {
        lightbox?.classList.remove('is-open');
        if (lightbox) lightbox.hidden = true;
    };
    document.querySelectorAll('[data-gallery-src]').forEach((btn) => {
        btn.addEventListener('click', () => openLightbox(btn.dataset.gallerySrc, btn.dataset.galleryAlt));
    });
    document.querySelectorAll('[data-close-lightbox]').forEach((btn) => btn.addEventListener('click', closeLightbox));
    lightbox?.addEventListener('click', (e) => { if (e.target === lightbox) closeLightbox(); });
    document.addEventListener('keydown', (e) => {
        if (e.key === 'Escape') closeLightbox();
    });
    document.querySelectorAll('.flash-wrap').forEach((el) => {
        setTimeout(() => {
            el.classList.add('is-leaving');
            setTimeout(() => el.remove(), 400);
        }, 3000);
    });
})();
// ============================================
// MODAL DE TRATAMIENTO DE DATOS - CARPIN ART
// ============================================

document.addEventListener("DOMContentLoaded", function () {
    const abrir = document.getElementById("btnTratamientoDatos");
    const modal = document.getElementById("modalTratamientoDatos");
    const cerrar = document.getElementById("cerrarTratamientoDatos");
    const aceptar = document.getElementById("btnAceptarTratamientoDatos");

    // Evitar errores si esta vista no contiene el modal.
    if (!abrir || !modal || !cerrar || !aceptar) {
        return;
    }

    function abrirModal(event) {
        if (event) {
            event.preventDefault();
        }

        modal.hidden = false;
        modal.setAttribute("aria-hidden", "false");
        document.body.style.overflow = "hidden";

        cerrar.focus();
    }

    function cerrarModal() {
        modal.hidden = true;
        modal.setAttribute("aria-hidden", "true");
        document.body.style.overflow = "";

        abrir.focus();
    }

    // Abrir desde el menú.
    abrir.addEventListener("click", abrirModal);

    // Cerrar con la X.
    cerrar.addEventListener("click", cerrarModal);

    // Cerrar con el botón Entendido.
    aceptar.addEventListener("click", cerrarModal);

    // Cerrar al hacer clic fuera de la ventana.
    modal.addEventListener("click", function (event) {
        if (event.target === modal) {
            cerrarModal();
        }
    });

    // Cerrar con la tecla Escape.
    document.addEventListener("keydown", function (event) {
        if (event.key === "Escape" && !modal.hidden) {
            cerrarModal();
        }
    });
});
