<?php
/**
 * زر تبديل الوضع الليلي/النهاري — يُستخدم في المتجر والإدارة.
 * $theme_toggle_class: فئات إضافية اختيارية (مثل theme-toggle--compact)
 */
$tt_class = isset($theme_toggle_class) ? trim($theme_toggle_class) : '';
?>
<button type="button" class="theme-toggle<?php echo $tt_class !== '' ? ' ' . htmlspecialchars($tt_class, ENT_QUOTES, 'UTF-8') : ''; ?>" id="theme-toggle" aria-label="تبديل الوضع الليلي" title="الوضع الليلي / النهاري">
    <span class="theme-toggle-track" aria-hidden="true">
        <span class="theme-toggle-thumb"></span>
    </span>
    <span class="theme-toggle-label">
        <svg class="theme-toggle-icon theme-toggle-icon-moon" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><path d="M21 12.79A9 9 0 1 1 11.21 3 7 7 0 0 0 21 12.79z"/></svg>
        <svg class="theme-toggle-icon theme-toggle-icon-sun" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><circle cx="12" cy="12" r="4"/><path d="M12 2v2M12 20v2M4.93 4.93l1.41 1.41M17.66 17.66l1.41 1.41M2 12h2M20 12h2M4.93 19.07l1.41-1.41M17.66 6.34l1.41-1.41"/></svg>
    </span>
</button>
