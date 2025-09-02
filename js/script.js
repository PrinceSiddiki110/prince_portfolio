// Enhanced script: theme, navigation, reveal, filters, modals, form handling
// DOM Elements
const body = document.body;
const themeToggle = document.getElementById('themeToggle');
const navToggle = document.getElementById('nav-toggle');
const navMenu = document.getElementById('nav-menu');
const mobileNavOverlay = document.getElementById('mobile-nav-overlay');
const navLinks = document.querySelectorAll('.nav-link');
const revealEls = document.querySelectorAll('.reveal');
const filters = document.querySelectorAll('.filter');
const projectsGrid = document.getElementById('projectsGrid');
const modals = document.querySelectorAll('.modal');
const yearSpan = document.getElementById('year');
const contactForm = document.getElementById('contactForm');
const copyEmailBtn = document.getElementById('copyEmail');
const yearEl = document.getElementById('year');

// Theme toggle functionality
if (themeToggle) {
  themeToggle.addEventListener('click', () => {
    const currentTheme = body.getAttribute('data-theme');
    const newTheme = currentTheme === 'light' ? 'dark' : 'light';
    body.setAttribute('data-theme', newTheme);
    themeToggle.setAttribute('aria-pressed', newTheme === 'dark');
    localStorage.setItem('theme', newTheme);
    
    // Add a small animation effect
    themeToggle.style.transform = 'scale(0.95)';
    setTimeout(() => {
      themeToggle.style.transform = '';
    }, 150);
  });

  // Load saved theme
  const savedTheme = localStorage.getItem('theme') || 'light';
  body.setAttribute('data-theme', savedTheme);
  themeToggle.setAttribute('aria-pressed', savedTheme === 'dark');
}

// Mobile navigation functionality
if (navToggle && navMenu) {
  navToggle.addEventListener('click', (e) => {
    e.stopPropagation();
    toggleMobileNav();
  });
}

// Function to toggle mobile navigation
function toggleMobileNav() {
  navMenu.classList.toggle('active');
  navToggle.classList.toggle('active');
  mobileNavOverlay.classList.toggle('active');
  
  // Update aria-expanded attribute
  const isOpen = navMenu.classList.contains('active');
  navToggle.setAttribute('aria-expanded', isOpen);
  
  // Prevent body scroll when nav is open
  if (isOpen) {
    document.body.style.overflow = 'hidden';
  } else {
    document.body.style.overflow = '';
  }
}

// Close mobile nav when clicking nav links
navLinks.forEach(link => {
  link.addEventListener('click', () => {
    if (window.innerWidth <= 768) {
      closeMobileNav();
    }
  });
});

// Function to close mobile navigation
function closeMobileNav() {
  navMenu.classList.remove('active');
  navToggle.classList.remove('active');
  mobileNavOverlay.classList.remove('active');
  navToggle.setAttribute('aria-expanded', 'false');
  document.body.style.overflow = '';
}

// Close mobile nav when clicking overlay
if (mobileNavOverlay) {
  mobileNavOverlay.addEventListener('click', () => {
    closeMobileNav();
  });
}

// Close mobile nav on window resize
window.addEventListener('resize', () => {
  if (window.innerWidth > 768) {
    closeMobileNav();
  }
});

// Close mobile nav on escape key
document.addEventListener('keydown', (e) => {
  if (e.key === 'Escape' && navMenu && navMenu.classList.contains('active')) {
    closeMobileNav();
  }
});

// Smooth scrolling for navigation links
navLinks.forEach(link => {
  link.addEventListener('click', (e) => {
    e.preventDefault();
    const targetId = link.getAttribute('href').substring(1);
    const targetSection = document.getElementById(targetId);
    
    if (targetSection) {
      targetSection.scrollIntoView({ behavior: 'smooth' });
      
      // Update active link
      navLinks.forEach(l => l.classList.remove('active'));
      link.classList.add('active');
    }
  });
});

// Update active link on scroll
window.addEventListener('scroll', () => {
  const sections = document.querySelectorAll('.section');
  const scrollPos = window.scrollY + 100;

  sections.forEach(section => {
    const sectionTop = section.offsetTop;
    const sectionHeight = section.offsetHeight;
    const sectionId = section.getAttribute('id');
    
    if (scrollPos >= sectionTop && scrollPos < sectionTop + sectionHeight) {
      navLinks.forEach(link => {
        link.classList.remove('active');
        if (link.getAttribute('href') === '#' + sectionId) {
          link.classList.add('active');
        }
      });
    }
  });
});

// Reveal animations
const observerOptions = {
  threshold: 0.1,
  rootMargin: '0px 0px -50px 0px'
};

const observer = new IntersectionObserver((entries) => {
  entries.forEach(entry => {
    if (entry.isIntersecting) {
      entry.target.classList.add('revealed');
    }
  });
}, observerOptions);

document.querySelectorAll('.reveal').forEach(el => {
  observer.observe(el);
});

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

// Contact form submission
contactForm?.addEventListener('submit', (e)=>{
  e.preventDefault();
  
  const submitBtn = contactForm.querySelector('button[type="submit"]');
  const originalText = submitBtn.textContent;
  
  // Show loading state
  submitBtn.disabled = true;
  submitBtn.classList.add('loading');
  submitBtn.textContent = 'Sending...';
  
  // Get form data
  const formData = new FormData(contactForm);
  
  // Submit form
  fetch(contactForm.action, {
    method: 'POST',
    body: formData
  })
  .then(response => {
    if (response.ok) {
      // Show success message
      showFormMessage('Message sent successfully! I\'ll get back to you soon.', 'success');
      contactForm.reset();
    } else {
      throw new Error('Failed to send message');
    }
  })
  .catch(error => {
    console.error('Error:', error);
    showFormMessage('Failed to send message. Please try again.', 'error');
  })
  .finally(() => {
    // Reset button state
    submitBtn.disabled = false;
    submitBtn.classList.remove('loading');
    submitBtn.textContent = originalText;
  });
});

// Function to show form messages
function showFormMessage(message, type) {
  // Remove existing messages
  const existingMessage = contactForm.querySelector('.success-message, .error-message');
  if (existingMessage) {
    existingMessage.remove();
  }
  
  // Create new message
  const messageDiv = document.createElement('div');
  messageDiv.className = `${type}-message`;
  messageDiv.textContent = message;
  
  // Insert before form actions
  const formActions = contactForm.querySelector('.form-actions');
  contactForm.insertBefore(messageDiv, formActions);
  
  // Auto-remove after 5 seconds
  setTimeout(() => {
    if (messageDiv.parentNode) {
      messageDiv.remove();
    }
  }, 5000);
}

// Copy email
copyEmailBtn?.addEventListener('click', ()=>{
  navigator.clipboard?.writeText(contactForm.dataset.destination || 'your-email@example.com').then(()=>{
    copyEmailBtn.textContent = 'Copied!'; setTimeout(()=>copyEmailBtn.textContent='Copy Email',1500);
  });
});

// animated skillbars: set widths after load
document.addEventListener('DOMContentLoaded', ()=>{
  document.querySelectorAll('.bar-fill').forEach(b=>{
    const w = getComputedStyle(b).getPropertyValue('--p') || '60%';
    b.style.width = w.trim();
  });
  yearEl.textContent = new Date().getFullYear();
});