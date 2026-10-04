    </main>
    <div class="toast-container" id="toast-container"></div>
    <footer class="site-footer">
        <div class="container footer-inner">
            <p class="copyright">© 2026 BookMart</p>
            <p class="footer-contact">
                <span class="footer-label">للتواصل:</span>
                <a href="tel:+966550000000" class="footer-phone">+966 55 000 0000</a>
            </p>
            <p class="footer-contact-en" dir="ltr" lang="en">Contact: <a href="tel:+966550000000">+966 55 000 0000</a></p>
        </div>
    </footer>
    <?php if (!defined('BOOKMART_ASSET_VER')) { require_once __DIR__ . '/asset_version.php'; } ?>
    <script src="<?php echo isset($base) ? $base : ''; ?>assets/js/main.js?v=<?php echo htmlspecialchars(BOOKMART_ASSET_VER, ENT_QUOTES, 'UTF-8'); ?>"></script>
</body>
</html>
