</style>
</head>
<body>
<script>
(function(){
  function syncAdminLayout(){
    var canMatch = typeof window.matchMedia === 'function';
    var hasTouch = (navigator.maxTouchPoints || 0) > 0 || (canMatch && window.matchMedia('(pointer: coarse)').matches);
    var noHover = !canMatch || !window.matchMedia('(hover: hover)').matches;
    var shortSide = Math.min(window.innerWidth || 9999, window.innerHeight || 9999);
    var useTouchLayout = hasTouch && (noHover || shortSide <= 1024);

    document.body.classList.toggle('touch-admin-layout', useTouchLayout);
    if (useTouchLayout) {
      document.body.classList.remove('sidebar-collapsed');
      try { localStorage.removeItem('shashety_sidebar'); } catch (e) {}
    } else if (localStorage.getItem('shashety_sidebar') === 'collapsed') {
      document.body.classList.add('sidebar-collapsed');
    } else {
      document.body.classList.remove('sidebar-collapsed');
    }
  }
  syncAdminLayout();
  window.addEventListener('resize', syncAdminLayout, { passive: true });
})();
</script>
<!-- Netflix-Style Loading Screen -->
<div id="nfx-loader" style="position:fixed;inset:0;background:#0a0a0a;z-index:99999;display:flex;flex-direction:column;align-items:center;justify-content:center;gap:0;transition:opacity .6s ease,visibility .6s ease">
  <span class="nfx-load-indicator" role="status" aria-label="جارٍ تحميل لوحة الإدارة"><i></i><i></i><i></i><i></i><i></i><i></i><i></i><i></i><i></i><i></i><i></i><i></i></span>
