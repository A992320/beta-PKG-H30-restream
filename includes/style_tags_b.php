</style>

<script>
    // مزامنة الثيم النشط من قاعدة البيانات (السيرفر) إلى المتصفح الحالي لتوحيد الثيم في كل الأجهزة
    try {
        localStorage.setItem('shashety_theme', <?= json_encode($settings['active_theme'] ?? 'default') ?>);
        localStorage.setItem('shashety_custom_css', <?= json_encode($settings['custom_css'] ?? '') ?>);
    } catch(e) {}
</script>

<style id="customCssThemeStyle">/* Custom Theme CSS - injected by theme system */</style>

<style>
