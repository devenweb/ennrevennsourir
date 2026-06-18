<?php
/**
 * Template Name: Contact Page
 */

get_header();

while ( have_posts() ) :
    the_post();
    ?>

    <!-- Page Header Banner -->
    <section class="page-header-banner" style="background-image: url('<?php echo get_template_directory_uri(); ?>/assets/images/hero-bg.jpg');">
        <div class="container">
            <div class="page-header-content">
                <h1><?php the_title(); ?></h1>
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="<?php echo esc_url( home_url( '/' ) ); ?>">Home</a></li>
                        <li class="breadcrumb-item active" aria-current="page"><?php the_title(); ?></li>
                    </ol>
                </nav>
            </div>
        </div>
    </section>

    <main class="py-5 bg-white">
        <div class="container">
            <div class="row">
                <!-- Contact Info -->
                <div class="col-lg-6 mb-5">
                    <h2 style="font-weight: 800; margin-bottom: 30px;">Get in Touch</h2>
                    <p class="text-muted mb-5">Have questions about our campaigns or want to start your own? Our team is here to help you every step of the way.</p>

                    <div class="contact-info-block d-flex mb-4">
                        <div class="icon mr-3" style="font-size: 24px; color: var(--kopizon-pink);"><i class="fas fa-map-marker-alt"></i></div>
                        <div>
                            <h6 class="font-weight-bold">Our Location</h6>
                            <p class="text-muted">50 Alma Street Vallee Pitot, Port Louis, Mauritius</p>
                        </div>
                    </div>
                    <div class="contact-info-block d-flex mb-4">
                        <div class="icon mr-3" style="font-size: 24px; color: var(--kopizon-pink);"><i class="fas fa-phone"></i></div>
                        <div>
                            <h6 class="font-weight-bold">Phone Number</h6>
                            <p class="text-muted">(+230) 5909 92 19</p>
                        </div>
                    </div>
                    <div class="contact-info-block d-flex">
                        <div class="icon mr-3" style="font-size: 24px; color: var(--kopizon-pink);"><i class="fas fa-envelope"></i></div>
                        <div>
                            <h6 class="font-weight-bold">Email Support</h6>
                            <p class="text-muted">info@ennrevennsourir.org</p>
                        </div>
                    </div>

                    <!-- Socials in Contact Page -->
                    <div class="contact-socials mt-5">
                        <h6 class="font-weight-bold mb-3">Follow Us</h6>
                        <div class="footer-socials">
                            <a href="https://www.facebook.com/1rev1sourir" target="_blank" rel="noopener" style="background:#f0f2f5; color:#333; width:40px; height:40px; border-radius:50%; display:inline-flex; align-items:center; justify-content:center; margin-right:10px;"><i class="fab fa-facebook-f"></i></a>
                            <a href="https://www.linkedin.com/in/enn-rev-enn-sourir-1116861b5/" target="_blank" rel="noopener" style="background:#f0f2f5; color:#333; width:40px; height:40px; border-radius:50%; display:inline-flex; align-items:center; justify-content:center; margin-right:10px;"><i class="fab fa-linkedin-in"></i></a>
                            <a href="https://www.instagram.com/ennrevennsourir/" target="_blank" rel="noopener" style="background:#f0f2f5; color:#333; width:40px; height:40px; border-radius:50%; display:inline-flex; align-items:center; justify-content:center; margin-right:10px;"><i class="fab fa-instagram"></i></a>
                        </div>
                    </div>
                </div>

                <!-- Contact Form -->
                <div class="col-lg-6">
                    <div class="card border-0 shadow-lg p-5" style="border-radius: 20px; background: #fff;">
                        <h4 class="font-weight-bold mb-4">Send us a Message</h4>
                        <div class="contact-form-wp">
                            <?php 
                            // If Contact Form 7 or similar is used, the user can paste shortcode in the content.
                            // Otherwise we show a mockup or the content itself.
                            if ( empty( get_the_content() ) ) : ?>
                                <form>
                                    <div class="form-group mb-3"><input type="text" class="form-control rounded-pill px-4 py-3" placeholder="Your Name" style="background: #f8f9fa; border: none;"></div>
                                    <div class="form-group mb-3"><input type="email" class="form-control rounded-pill px-4 py-3" placeholder="Email Address" style="background: #f8f9fa; border: none;"></div>
                                    <div class="form-group mb-3"><input type="text" class="form-control rounded-pill px-4 py-3" placeholder="Subject" style="background: #f8f9fa; border: none;"></div>
                                    <div class="form-group mb-4"><textarea class="form-control px-4 py-3" rows="5" placeholder="Your Message" style="border-radius: 15px; background: #f8f9fa; border: none;"></textarea></div>
                                    <button class="btn btn-primary btn-block rounded-pill py-3" style="background: var(--kopizon-pink); border: none; font-weight: 700;">Send Message</button>
                                </form>
                            <?php else : 
                                the_content();
                            endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <!-- Map Section -->
    <div class="contact-map mt-5">
        <iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3744.156543256789!2d57.50123456789012!3d-20.16543212345678!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x217c504ca1aaaaab%3A0x1234567890abcdef!2sAlma%20St%2C%20Port%20Louis%2C%20Mauritius!5e0!3m2!1sen!2smu!4v1234567890123!5m2!1sen!2smu" width="100%" height="450" style="border:0;" allowfullscreen="" loading="lazy"></iframe>
    </div>

    <?php
endwhile;

get_footer();
