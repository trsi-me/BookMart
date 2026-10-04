<?php
/**
 * تهيئة الوضع الليلي قبل الرسم لتجنب وميض الوضع الفاتح (FOUC).
 * يُضمَّن داخل <head> بعد meta viewport.
 */
?>
<meta name="color-scheme" content="light dark">
<script>
(function(){try{var k='bookmart_theme';var s=localStorage.getItem(k);var dark=false;if(s==='dark')dark=true;else if(s==='light')dark=false;else dark=window.matchMedia('(prefers-color-scheme: dark)').matches;document.documentElement.setAttribute('data-theme',dark?'dark':'light');}catch(e){document.documentElement.setAttribute('data-theme','light');}})();
</script>
