<?php
/**
 * Template Name: Volunteer Page
 */

get_header();

while ( have_posts() ) :
    the_post();
    ?>

    <!-- Page Header Banner -->
    <section class="page-header-banner" style="background-image: url('<?php echo get_template_directory_uri(); ?>/assets/images/hero-bg.jpg'); background-color: var(--kopizon-pink);">
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
        <div class="container" style="max-width: 1400px;">
            <div class="row">
                <div class="col-lg-7 pr-lg-5">
                    <h2 class="font-weight-bold mb-4" style="color: var(--kopizon-navy);">Why Volunteer with Us?</h2>
                    <div class="volunteer-intro mb-5">
                        <?php the_content(); ?>
                    </div>

                    <div class="row mt-5">
                        <?php 
                        $benefits = get_post_meta( get_the_ID(), 'kopizon_benefits', true );
                        if ( $benefits && is_array($benefits) ) :
                            foreach ( $benefits as $benefit ) : ?>
                            <div class="col-md-6 mb-4">
                                <div class="d-flex align-items-start">
                                    <div class="icon-box mr-3" style="color: var(--kopizon-pink); font-size: 24px;"><i class="fas fa-check-circle"></i></div>
                                    <div>
                                        <h5 class="font-weight-bold"><?php echo esc_html($benefit['title']); ?></h5>
                                        <p class="small text-muted"><?php echo esc_html($benefit['text']); ?></p>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; else : ?>
                            <p class="text-muted w-100 px-3">Define volunteer benefits in the page editor.</p>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Registration Form -->
                <div class="col-lg-5">
                    <div class="card border-0 shadow-lg p-5" style="border-radius: 20px; border-top: 5px solid var(--kopizon-pink);">
                        <h3 class="font-weight-bold mb-4">Register as a Volunteer</h3>
                        <div class="volunteer-form-wp">
                            <?php if ( empty( get_the_content() ) ) : ?>
                                <form>
                                    <div class="form-group mb-4">
                                        <label class="font-weight-bold small text-muted">FULL NAME</label>
                                        <input type="text" class="form-control rounded-pill px-4 py-3" style="background:#f8f9fa; border:none;" placeholder="Enter your full name">
                                    </div>
                                    <div class="form-group mb-4">
                                        <label class="font-weight-bold small text-muted">EMAIL ADDRESS</label>
                                        <input type="email" class="form-control rounded-pill px-4 py-3" style="background:#f8f9fa; border:none;" placeholder="email@example.com">
                                    </div>
                                    <div class="form-group mb-4">
                                        <label class="font-weight-bold small text-muted">PHONE NUMBER</label>
                                        <input type="tel" class="form-control rounded-pill px-4 py-3" style="background:#f8f9fa; border:none;" placeholder="+230 5XXX XXXX">
                                    </div>
                                    <button type="submit" class="btn btn-primary btn-block rounded-pill py-3 font-weight-bold" style="background: var(--kopizon-navy); border: none;">SUBMIT APPLICATION</button>
                                </form>
                            <?php else : 
                                // In a real scenario, the user would put a form shortcode here.
                                ?>
                                <div class="text-center p-4">
                                    <i class="fas fa-clipboard-list fa-3x mb-3 text-muted"></i>
                                    <p>Please configure your volunteer registration form in the page editor using a form plugin shortcode.</p>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <?php
endwhile;

get_footer();
