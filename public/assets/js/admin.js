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

document.querySelectorAll('[data-notif]').forEach((wrap) => {
    const btn = wrap.querySelector('[data-notif-toggle]');
    const panel = wrap.querySelector('[data-notif-panel]');
    if (!btn || !panel) return;

    const close = () => {
        panel.hidden = true;
        btn.setAttribute('aria-expanded', 'false');
    };

    btn.addEventListener('click', (e) => {
        e.stopPropagation();
        const open = panel.hidden;
        panel.hidden = !open;
        btn.setAttribute('aria-expanded', open ? 'true' : 'false');
    });

    document.addEventListener('click', (e) => {
        if (!wrap.contains(e.target)) close();
    });

    document.addEventListener('keydown', (e) => {
        if (e.key === 'Escape') close();
    });
});
