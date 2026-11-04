document.querySelectorAll('[data-live]').forEach((input) => {
    const target = document.querySelector(input.dataset.live);
    if (!target) return;
    const sync = () => { target.textContent = input.value || target.dataset.fallback || ''; };
    input.addEventListener('input', sync);
    sync();
});

document.querySelectorAll('.adm-flash').forEach((el) => {
    setTimeout(() => {
        el.classList.add('is-leaving');
        setTimeout(() => el.remove(), 400);
    }, 3000);
});
