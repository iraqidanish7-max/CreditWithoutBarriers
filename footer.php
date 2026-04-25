<?php
// footer.php
// Include at the bottom of your .php pages: <?php include 'footer.php'; ?>

<footer class="site-footer bg-dark text-light py-4">
  <!-- decorative wave -->
  <svg class="footer-wave" viewBox="0 0 1440 120" preserveAspectRatio="none" aria-hidden="true">
    <path d="M0,32L48,42.7C96,53,192,75,288,96C384,117,480,139,576,128C672,117,768,71,864,42.7C960,13,1056,3,1152,13.3C1248,24,1344,56,1392,72L1440,88L1440,120L1392,120C1344,120,1248,120,1152,120C1056,120,960,120,864,120C768,120,672,120,576,120C480,120,384,120,288,120C192,120,96,120,48,120L0,120Z"
          fill="url(#g)" fill-opacity="0.12"></path>
    <defs>
      <linearGradient id="g" x1="0" x2="1">
        <stop offset="0%" stop-color="#6a11cb"/>
        <stop offset="100%" stop-color="#2575fc"/>
      </linearGradient>
    </defs>
  </svg>

  <div class="container d-flex justify-content-between align-items-center">
    <div>© <strong>CreditWithoutBarriers</strong> — <?php echo date('Y'); ?></div>
    <div class="muted small">Contact: support@example.com</div>
  </div>
</footer>

<!-- SCRIPTS: Bootstrap + site scripts (keeps pages DRY) -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"></script>

<script>
/* Mobile menu toggle */
const mobileBtn = document.getElementById('mobileMenuBtn');
const mobileMenu = document.getElementById('mobileMenu');
mobileBtn && mobileBtn.addEventListener('click', () => {
  mobileMenu.classList.toggle('open');
});

/* Ripple effect for buttons */
document.querySelectorAll('.btn').forEach(btn => {
  btn.addEventListener('click', function(e) {
    const rect = this.getBoundingClientRect();
    const size = Math.max(rect.width, rect.height);
    const x = e.clientX - rect.left - size/2;
    const y = e.clientY - rect.top - size/2;
    const ripple = document.createElement('span');
    ripple.className = 'ripple';
    ripple.style.width = ripple.style.height = size + 'px';
    ripple.style.left = x + 'px';
    ripple.style.top = y + 'px';
    this.appendChild(ripple);
    setTimeout(() => ripple.remove(), 600);
  });
});

/* Scroll reveal for cards */
(function(){
  const items = Array.from(document.querySelectorAll('.card, .feature-card'));
  items.forEach(el => el.classList.add('reveal'));
  if ('IntersectionObserver' in window) {
    const obs = new IntersectionObserver((entries, o) => {
      entries.forEach(e => {
        if (e.isIntersecting) {
          e.target.classList.add('is-revealed');
          o.unobserve(e.target);
        }
      });
    }, {root: null, rootMargin: '0px 0px -10% 0px', threshold: 0.12});
    items.forEach(i => obs.observe(i));
  } else {
    items.forEach(i => i.classList.add('is-revealed'));
  }
})();

/* Navbar darken on scroll */
(function(){
  const hdr = document.querySelector('.site-header');
  if (!hdr) return;
  const threshold = 60;
  function onScroll(){
    if (window.scrollY > threshold) hdr.classList.add('scrolled');
    else hdr.classList.remove('scrolled');
  }
  onScroll();
  window.addEventListener('scroll', onScroll, {passive:true});
})();

/* Animated border decorator (applies to first few cards) */
(function(){
  document.querySelectorAll('.feature-card, .card.p-4').forEach((el, idx) => {
    if (idx < 6) el.classList.add('animated-border');
  });
})();

/* Particles background (lightweight) */
(function(){
  const canvas = document.createElement('canvas');
  canvas.id = 'particles-canvas';
  document.body.appendChild(canvas);
  const ctx = canvas.getContext('2d');
  let w = canvas.width = innerWidth;
  let h = canvas.height = innerHeight;
  const particles = [];
  const count = Math.floor(Math.min(80, (w*h) / 70000));
  function rand(min, max){ return Math.random()*(max-min)+min; }
  for(let i=0;i<count;i++){
    particles.push({
      x: rand(0,w), y: rand(0,h),
      r: rand(0.6,2.6),
      vx: rand(-0.2,0.2), vy: rand(-0.05,0.05),
      alpha: rand(0.06,0.22)
    });
  }
  function resize(){ w = canvas.width = innerWidth; h = canvas.height = innerHeight; }
  window.addEventListener('resize', resize, {passive:true});
  function draw(){
    ctx.clearRect(0,0,w,h);
    for(const p of particles){
      p.x += p.vx; p.y += p.vy;
      if (p.x < -10) p.x = w+10; if (p.x > w+10) p.x = -10;
      if (p.y < -10) p.y = h+10; if (p.y > h+10) p.y = -10;
      ctx.beginPath();
      ctx.arc(p.x, p.y, p.r, 0, Math.PI*2);
      ctx.fillStyle = 'rgba(37,99,235,' + (p.alpha*0.8) + ')';
      ctx.fill();
    }
    requestAnimationFrame(draw);
  }
  requestAnimationFrame(draw);
})();

/* auto-apply active-link by URL (optional; server-side highlight preferred) */
(function(){
  const path = location.pathname.split('/').pop() || 'index.php';
  document.querySelectorAll('nav .nav-link').forEach(a => {
    const href = a.getAttribute('href');
    if(!href) return;
    if (href === path || (href === 'index.php' && path === '')) a.classList.add('active-link');
    else a.classList.remove('active-link');
  });
})();
</script>
<script>
(function(){
  // delegate: find all password toggles on the page
  document.querySelectorAll('.pwd-toggle-btn').forEach(function(btn){
    btn.addEventListener('click', function(e){
      var targetSelector = btn.getAttribute('data-target');
      if (!targetSelector) return;
      var input = document.querySelector(targetSelector);
      if (!input) return;

      var isPassword = (input.type === 'password');
      // toggle input type
      input.type = isPassword ? 'text' : 'password';

      // update aria & pressed state
      btn.setAttribute('aria-pressed', String(isPassword));
      btn.setAttribute('aria-label', isPassword ? 'Hide password' : 'Show password');

      // swap icon visibility
      var eyeOpen = btn.querySelector('.eye-open');
      var eyeClosed = btn.querySelector('.eye-closed');
      if (eyeOpen && eyeClosed) {
        if (isPassword) {
          eyeOpen.style.display = 'none';
          eyeClosed.style.display = '';
        } else {
          eyeOpen.style.display = '';
          eyeClosed.style.display = 'none';
        }
      }
    });
  });
})();
</script>
