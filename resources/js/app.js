document.documentElement.classList.add('js');

const $ = (s, c = document) => c.querySelector(s);
const $$ = (s, c = document) => [...c.querySelectorAll(s)];

// Theme — persist 'mt-dark' = '1'
const themeBtn = $('[data-theme-toggle]');
const setTheme = (dark) => {
  document.documentElement.classList.toggle('mt-dark', dark);
  themeBtn?.setAttribute('aria-pressed', String(dark));
};
if (localStorage.getItem('mt-dark') === '1' ||
  (!localStorage.getItem('mt-dark') && matchMedia('(prefers-color-scheme: dark)').matches)) {
  setTheme(true);
}
themeBtn?.addEventListener('click', () => {
  const dark = !document.documentElement.classList.contains('mt-dark');
  setTheme(dark);
  localStorage.setItem('mt-dark', dark ? '1' : '0');
});

// One-shot reveals
if ('IntersectionObserver' in window && !matchMedia('(prefers-reduced-motion: reduce)').matches) {
  const io = new IntersectionObserver((entries) => {
    for (const e of entries) if (e.isIntersecting) { e.target.classList.add('is-visible'); io.unobserve(e.target); }
  }, { rootMargin: '0px 0px -8%', threshold: 0.1 });
  $$('[data-reveal]').forEach((el) => io.observe(el));
} else {
  $$('[data-reveal]').forEach((el) => el.classList.add('is-visible'));
}

// Ticker pause/play
const ticker = $('.ticker');
const tickerBtn = $('[data-ticker-toggle]');
tickerBtn?.addEventListener('click', () => {
  const paused = ticker.dataset.paused === '1';
  ticker.dataset.paused = paused ? '0' : '1';
  tickerBtn.setAttribute('aria-pressed', String(!paused));
});

// Category tabs (A11y: roving tabindex)
const tabs = $$('.tab');
const panels = $$('.tab-panel');
tabs.forEach((tab, i) => {
  tab.setAttribute('role', 'tab');
  tab.setAttribute('tabindex', i === 0 ? '0' : '-1');
  tab.addEventListener('click', () => selectTab(i));
  tab.addEventListener('keydown', (e) => {
    const dir = e.key === 'ArrowRight' ? 1 : e.key === 'ArrowLeft' ? -1 : 0;
    if (!dir) return;
    e.preventDefault();
    const next = (i + dir + tabs.length) % tabs.length;
    selectTab(next);
    tabs[next].focus();
  });
});
function selectTab(i) {
  tabs.forEach((t, j) => {
    t.setAttribute('aria-selected', String(j === i));
    t.setAttribute('tabindex', j === i ? '0' : '-1');
  });
  panels.forEach((p, j) => { p.hidden = j !== i; });
}
panels.forEach((p, j) => { p.hidden = j !== 0; });

// Search — filter headline board live, no backend
const searchInput = $('#site-search');
const searchClear = $('[data-search-clear]');
searchInput?.addEventListener('input', () => {
  const q = searchInput.value.trim().toLowerCase();
  $$('.searchable').forEach((el) => {
    el.style.display = el.textContent.toLowerCase().includes(q) ? '' : 'none';
  });
  searchClear.hidden = !q;
});
searchClear?.addEventListener('click', () => {
  searchInput.value = '';
  searchInput.dispatchEvent(new Event('input'));
  searchInput.focus();
});

// Mobile nav
const navToggle = $('[data-nav-toggle]');
const mobileNav = $('[data-mobile-nav]');
navToggle?.addEventListener('click', () => {
  const open = mobileNav.hidden = !mobileNav.hidden;
  navToggle.setAttribute('aria-expanded', String(open));
});

// Newsletter — client-side only, static success state
const newsForm = $('#newsletter-form');
newsForm?.addEventListener('submit', (e) => {
  e.preventDefault();
  const email = new FormData(newsForm).get('email');
  if (!/.+@.+\..+/.test(String(email))) return;
  newsForm.outerHTML = '<p class="newsletter-success" role="status">Terdaftar. Cek email untuk konfirmasi.</p>';
});
