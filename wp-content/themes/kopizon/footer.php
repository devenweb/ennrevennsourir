<?php
/**
 * The template for displaying the footer
 */
?>
    <footer class="deep-footer">
        <div class="container">
            <div class="row">
                <div class="col-lg-4 mb-5 mb-lg-0">
                    <?php
                    if ( has_custom_logo() ) {
                        the_custom_logo();
                    } else {
                        echo '<img src="' . get_template_directory_uri() . '/assets/images/logo-web.png" alt="' . get_bloginfo( 'name' ) . '" style="max-height: 65px; margin-bottom: 30px;">';
                    }
                    ?>
                    <p class="mb-4"><?php echo esc_html( get_bloginfo( 'description' ) ); ?></p>
                    <ul class="footer-contact list-unstyled">
                        <li><i class="fas fa-phone"></i> <?php echo esc_html( get_theme_mod('kopizon_phone', '(+230) 5 909 9219') ); ?></li>
                        <li><i class="fas fa-map-marker-alt"></i> <?php echo esc_html( get_theme_mod('kopizon_address', 'Port-Louis, Mauritius') ); ?></li>
                        <li><i class="fas fa-envelope"></i> <?php echo esc_html( get_theme_mod('kopizon_email', get_option('admin_email')) ); ?></li>
                    </ul>
                </div>
                <div class="col-lg-4 mb-5 mb-lg-0 px-lg-5">
                    <h4>Important links</h4>
                    <?php
                    wp_nav_menu( array(
                        'theme_location' => 'footer',
                        'container'      => false,
                        'menu_class'     => 'footer-links list-unstyled',
                        'fallback_cb'    => 'kopizon_footer_menu_fallback',
                    ) );
                    ?>
                </div>
                <div class="col-lg-4">
                    <h4>Subscribe to our newsletter</h4>
                    <p class="mb-4"><?php echo esc_html( get_theme_mod('kopizon_newsletter_desc', 'Enter your email address to receive the latest updates on our projects and the children you have helped.') ); ?></p>
                    <div class="footer-newsletter-form">
                        <input type="email" class="form-control" placeholder="Email Address">
                        <button class="footer-newsletter-btn">Send</button>
                    </div>

                    <h4 class="mt-5">Follow us</h4>
                    <div class="footer-socials">
                        <a href="<?php echo esc_url( get_theme_mod( 'kopizon_facebook', '#' ) ); ?>" target="_blank" rel="noopener"><i class="fab fa-facebook-f"></i></a>
                        <a href="<?php echo esc_url( get_theme_mod( 'kopizon_linkedin', '#' ) ); ?>" target="_blank" rel="noopener"><i class="fab fa-linkedin-in"></i></a>
                        <a href="<?php echo esc_url( get_theme_mod( 'kopizon_instagram', '#' ) ); ?>" target="_blank" rel="noopener"><i class="fab fa-instagram"></i></a>
                        <a href="<?php echo esc_url( get_theme_mod( 'kopizon_youtube', '#' ) ); ?>" target="_blank" rel="noopener"><i class="fab fa-youtube"></i></a>
                        <a href="<?php echo esc_url( get_theme_mod( 'kopizon_whatsapp', '#' ) ); ?>" target="_blank" rel="noopener"><i class="fab fa-whatsapp"></i></a>
                    </div>
                </div>
            </div>
            <div class="copyright-bar">
                <p>&copy; <?php echo date('Y'); ?> <?php bloginfo( 'name' ); ?>. All rights reserved.</p>
            </div>
        </div>
    </footer>

    <?php wp_footer(); ?>
</body>
</html>
