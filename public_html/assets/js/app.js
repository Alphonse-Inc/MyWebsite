(function () {
  const reduce = window.matchMedia('(prefers-reduced-motion: reduce)').matches;
  const fine = window.matchMedia('(pointer: fine)').matches;
  const root = document.documentElement;

  const preloader = document.getElementById('preloader');
  if (preloader) {
    window.addEventListener('load', function () {
      preloader.classList.add('is-done');
      setTimeout(function () { preloader.remove(); }, reduce ? 0 : 500);
    });
  }

  document.querySelectorAll('[data-theme-toggle]').forEach(function (button) {
    button.addEventListener('click', function () {
      const next = root.classList.contains('light') ? 'dark' : 'light';
      root.classList.toggle('light', next === 'light');
      root.classList.toggle('dark', next !== 'light');
      root.style.colorScheme = next;
      localStorage.setItem('theme', next);
      const meta = document.querySelector('meta[name="theme-color"]');
      if (meta) meta.setAttribute('content', next === 'light' ? '#F6FAF8' : '#070B0A');
    });
  });

  document.querySelectorAll('[data-typed]').forEach(function (node) {
    let titles = [];
    try { titles = JSON.parse(node.getAttribute('data-typed') || '[]'); } catch (e) { titles = []; }
    const target = node.querySelector('[data-typed-text]');
    if (!target || titles.length === 0) return;
    if (reduce) { target.textContent = titles[0]; return; }
    let index = 0;
    let char = 0;
    let deleting = false;
    function tick() {
      const word = titles[index];
      char += deleting ? -1 : 1;
      target.textContent = word.slice(0, char);
      let delay = deleting ? 40 : 90;
      if (!deleting && char === word.length) { delay = 1400; deleting = true; }
      if (deleting && char === 0) { deleting = false; index = (index + 1) % titles.length; delay = 280; }
      setTimeout(tick, delay);
    }
    tick();
  });

  const media = document.querySelector('[data-hero-media] video');
  if (media) {
    const saveData = navigator.connection && navigator.connection.saveData;
    if (!saveData && !reduce) {
      const src = media.getAttribute('data-src');
      if (src) {
        media.src = src;
        media.play().catch(function () {});
      }
    }
  }

  if (fine && !reduce && !window.matchMedia('(hover: none)').matches) {
    root.classList.add('custom-cursor');
    const dot = document.querySelector('.cursor-dot');
    const ring = document.querySelector('.cursor-ring');
    window.addEventListener('mousemove', function (event) {
      if (!dot || !ring) return;
      dot.style.transform = 'translate(' + event.clientX + 'px,' + event.clientY + 'px)';
      ring.style.transform = 'translate(' + (event.clientX - 13) + 'px,' + (event.clientY - 13) + 'px)';
    });
  }

  document.querySelectorAll('[data-magnetic]').forEach(function (button) {
    if (reduce || !fine) return;
    button.addEventListener('mousemove', function (event) {
      const box = button.getBoundingClientRect();
      const x = event.clientX - (box.left + box.width / 2);
      const y = event.clientY - (box.top + box.height / 2);
      button.style.transform = 'translate(' + (x * 0.15) + 'px,' + (y * 0.2) + 'px)';
    });
    button.addEventListener('mouseleave', function () { button.style.transform = ''; });
  });

  document.querySelectorAll('[data-tilt]').forEach(function (card) {
    if (reduce || !fine) return;
    card.addEventListener('mousemove', function (event) {
      const box = card.getBoundingClientRect();
      const px = (event.clientX - box.left) / box.width;
      const py = (event.clientY - box.top) / box.height;
      card.style.transform = 'perspective(900px) rotateX(' + ((0.5 - py) * 6) + 'deg) rotateY(' + ((px - 0.5) * 8) + 'deg)';
    });
    card.addEventListener('mouseleave', function () { card.style.transform = ''; });
  });

  if (window.gsap && window.ScrollTrigger && !reduce) {
    gsap.registerPlugin(ScrollTrigger);
    gsap.utils.toArray('.reveal').forEach(function (el) {
      gsap.from(el, { y: 24, autoAlpha: 0, duration: 0.7, ease: 'power2.out', scrollTrigger: { trigger: el, start: 'top 88%' } });
    });
    gsap.utils.toArray('[data-parallax]').forEach(function (el) {
      gsap.to(el, { y: 40, ease: 'none', scrollTrigger: { trigger: el, scrub: true } });
    });
    document.querySelectorAll('[data-bar]').forEach(function (bar) {
      const value = Number(bar.getAttribute('data-bar') || 0);
      gsap.to(bar, { width: value + '%', duration: 1, ease: 'power2.out', scrollTrigger: { trigger: bar, start: 'top 90%' } });
    });
    document.querySelectorAll('[data-ring]').forEach(function (ring) {
      const value = Number(ring.getAttribute('data-ring') || 0);
      ring.style.setProperty('--value', '0');
      gsap.to(ring, { '--value': value, duration: 1.1, scrollTrigger: { trigger: ring, start: 'top 90%' } });
    });
    document.querySelectorAll('[data-count]').forEach(function (node) {
      const target = Number(node.getAttribute('data-count') || 0);
      const state = { n: 0 };
      gsap.to(state, {
        n: target, duration: 1.2, ease: 'power1.out',
        scrollTrigger: { trigger: node, start: 'top 90%' },
        onUpdate: function () { node.textContent = String(Math.round(state.n)); }
      });
    });
    const line = document.querySelector('.timeline');
    if (line) {
      gsap.from(line, { scaleY: 0, transformOrigin: 'top', duration: 1, ease: 'none', scrollTrigger: { trigger: line, start: 'top 80%', end: 'bottom 60%', scrub: true } });
    }
  } else {
    document.querySelectorAll('[data-bar]').forEach(function (bar) { bar.style.width = (bar.getAttribute('data-bar') || 0) + '%'; });
    document.querySelectorAll('[data-count]').forEach(function (node) { node.textContent = node.getAttribute('data-count') || '0'; });
  }

  window.showToast = function (message, kind) {
    const stack = document.getElementById('toasts');
    if (!stack || !message) return;
    const item = document.createElement('div');
    item.className = 'toast' + (kind === 'error' ? ' is-error' : '');
    item.textContent = message;
    stack.appendChild(item);
    setTimeout(function () { item.remove(); }, 4200);
  };
  document.querySelectorAll('[data-toast]').forEach(function (node) {
    if (node.textContent.trim() !== '') showToast(node.textContent.trim(), node.getAttribute('data-toast'));
  });

  document.querySelectorAll('[data-contact-form]').forEach(function (form) {
    form.addEventListener('submit', function (event) {
      event.preventDefault();
      const status = form.querySelector('[data-form-status]');
      const button = form.querySelector('button[type="submit"]');
      const label = button ? button.querySelector('[data-label]') : null;
      const original = label ? label.textContent : '';
      if (label) label.textContent = 'Sending…';
      if (button) button.disabled = true;
      fetch(form.action, {
        method: 'POST',
        body: new FormData(form),
        headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' }
      }).then(function (response) {
        return response.json().then(function (data) { return { ok: response.ok, data: data }; });
      }).then(function (result) {
        if (status) status.textContent = result.data.message || '';
        showToast(result.data.message || '', result.ok ? 'success' : 'error');
        if (result.ok) form.reset();
      }).catch(function () {
        if (status) status.textContent = 'The message could not be sent.';
        showToast('The message could not be sent.', 'error');
      }).finally(function () {
        if (label) label.textContent = original;
        if (button) button.disabled = false;
      });
    });
  });

  if (window.Swiper && document.querySelector('.project-swiper')) {
    const gallery = new Swiper('.project-swiper', {
      loop: false,
      pagination: { el: '.project-swiper .swiper-pagination', clickable: true },
      navigation: { nextEl: '.project-swiper .swiper-button-next', prevEl: '.project-swiper .swiper-button-prev' }
    });
    const dialog = document.getElementById('lightbox');
    let light = null;
    if (dialog) {
      light = new Swiper('.lightbox-swiper', {
        navigation: { nextEl: '.lightbox-swiper .swiper-button-next', prevEl: '.lightbox-swiper .swiper-button-prev' },
        keyboard: { enabled: true }
      });
      document.querySelectorAll('[data-lightbox-open]').forEach(function (button) {
        button.addEventListener('click', function () {
          const index = Number(button.getAttribute('data-lightbox-open') || 0);
          if (typeof dialog.showModal === 'function') dialog.showModal();
          light.slideTo(index, 0);
        });
      });
      dialog.querySelector('[data-lightbox-close]')?.addEventListener('click', function () { dialog.close(); });
      dialog.addEventListener('cancel', function (event) { event.preventDefault(); dialog.close(); });
      dialog.addEventListener('click', function (event) { if (event.target === dialog) dialog.close(); });
    }
    gallery.on('slideChange', function () {
      if (light && dialog && dialog.open) light.slideTo(gallery.activeIndex);
    });
  }

})();
