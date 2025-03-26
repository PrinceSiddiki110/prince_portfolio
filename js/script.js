// Enhanced script: theme, sidebar, navigation, reveal, filters, modals, form handling
const body = document.body;
const themeToggle = document.getElementById('themeToggle');
const sidebar = document.getElementById('sidebar');
const mobileMenu = document.getElementById('mobileMenu');
const navLinks = document.querySelectorAll('[data-link]');
const revealEls = document.querySelectorAll('.reveal');
const filters = document.querySelectorAll('.filter');
const projectsGrid = document.getElementById('projectsGrid');
const modals = document.querySelectorAll('.modal');
const contactForm = document.getElementById('contactForm');
const copyEmailBtn = document.getElementById('copyEmail');
const contactQuick = document.getElementById('contactQuick');
const yearEl = document.getElementById('year');

// Persist theme preference
const userTheme = localStorage.getItem('theme') || (window.matchMedia && window.matchMedia('(prefers-color-scheme: dark)').matches ? 'dark' : 'light');
body.setAttribute('data-theme', userTheme);

// Toggle theme with smooth transition
themeToggle?.addEventListener('click', () => {
  const next = body.getAttribute('data-theme') === 'dark' ? 'light' : 'dark';
  body.setAttribute('data-theme', next);
  localStorage.setItem('theme', next);
  themeToggle.setAttribute('aria-pressed', next === 'dark');
});

// Mobile sidebar open/close
mobileMenu?.addEventListener('click', () => sidebar.classList.toggle('open'));

// Close sidebar when clicking nav (mobile)
navLinks.forEach(l => l.addEventListener('click', () => sidebar.classList.remove('open')));

// Smooth scroll and active link on click
navLinks.forEach(link => {
  link.addEventListener('click', (e) => {
    e.preventDefault();
    const href = link.getAttribute('href');
    document.querySelector(href).scrollIntoView({behavior:'smooth', block:'start'});
    navLinks.forEach(n => n.classList.remove('active'));
    link.classList.add('active');
    sidebar.classList.remove('open');
  });
});

// Intersection observer for reveal
const obs = new IntersectionObserver((entries)=>{
  entries.forEach(en=>{
    if(en.isIntersecting){ en.target.classList.add('visible'); obs.unobserve(en.target); }
  });
},{threshold:0.18});
revealEls.forEach(el=>obs.observe(el));

// Filters for projects
filters.forEach(f=>f.addEventListener('click', ()=>{
  filters.forEach(b=>b.classList.remove('active'));
  f.classList.add('active');
  const filter = f.dataset.filter;
  const cards = projectsGrid.querySelectorAll('.project-card');
  cards.forEach(c=>{
    const type = c.dataset.type;
    if(filter==='all' || type===filter) c.style.display='grid'; else c.style.display='none';
  });
}));

// Modals open/close
document.querySelectorAll('[data-open]').forEach(btn=>{
  btn.addEventListener('click', ()=>{
    const id = btn.dataset.open;
    const modal = document.getElementById(id);
    if(modal){ modal.setAttribute('aria-hidden','false'); modal.style.display='flex'; document.body.style.overflow='hidden'; }
  });
});
document.querySelectorAll('[data-close]').forEach(b=>b.addEventListener('click', ()=>{
  const modal = b.closest('.modal'); if(modal){ modal.setAttribute('aria-hidden','true'); modal.style.display='none'; document.body.style.overflow=''; }
}));
modals.forEach(m=>m.addEventListener('click', e=>{ if(e.target===m){ m.setAttribute('aria-hidden','true'); m.style.display='none'; document.body.style.overflow=''; }}));

// Contact form -> mailto
contactForm?.addEventListener('submit', (e)=>{
  e.preventDefault();
  const to = contactForm.dataset.destination || 'your-email@example.com';
  const from = document.getElementById('fromEmail').value.trim();
  const subj = document.getElementById('subject').value.trim();
  const msg = document.getElementById('message').value.trim();
  const subject = encodeURIComponent(subj || 'Contact from portfolio');
  const bodyText = encodeURIComponent(msg + '\n\nFrom: ' + from);
  window.location.href = `mailto:${to}?subject=${subject}&body=${bodyText}`;
});

// Copy email
copyEmailBtn?.addEventListener('click', ()=>{
  navigator.clipboard?.writeText(contactForm.dataset.destination || 'your-email@example.com').then(()=>{
    copyEmailBtn.textContent = 'Copied!'; setTimeout(()=>copyEmailBtn.textContent='Copy Email',1500);
  });
});

contactQuick?.addEventListener('click', ()=>{ document.querySelector('#contact').scrollIntoView({behavior:'smooth'}); });

// animated skillbars: set widths after load
document.addEventListener('DOMContentLoaded', ()=>{
  document.querySelectorAll('.bar-fill').forEach(b=>{
    const w = getComputedStyle(b).getPropertyValue('--p') || '60%';
    b.style.width = w.trim();
  });
  yearEl.textContent = new Date().getFullYear();
});