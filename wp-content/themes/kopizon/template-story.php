<?php
/**
 * Template Name: Our Story Page
 */

get_header();

while ( have_posts() ) :
    the_post();
    ?>

    <!-- Page Header Banner -->
    <?php 
    $banner_bg = get_the_post_thumbnail_url( get_the_ID(), 'full' ) ?: get_template_directory_uri() . '/assets/images/hero-bg.jpg';
    ?>
    <section class="page-header-banner" style="background-image: url('<?php echo esc_url( $banner_bg ); ?>');">
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

    <!-- Story Content -->
    <section class="py-5 bg-white">
        <div class="container">
            <div class="row align-items-center mb-5">
                <div class="col-lg-6 mb-4 mb-lg-0">
                    <?php if ( has_post_thumbnail() ) : ?>
                        <?php the_post_thumbnail( 'large', array( 'class' => 'img-fluid rounded-lg shadow' ) ); ?>
                    <?php else : ?>
                        <img src="<?php echo get_template_directory_uri(); ?>/assets/images/experience-main.jpg" class="img-fluid rounded-lg shadow" alt="Experience">
                    <?php endif; ?>
                </div>
                <div class="col-lg-6 pl-lg-5">
                    <h2 class="font-weight-bold mb-4" style="font-size: 36px;"><?php the_title(); ?></h2>
                    <div class="story-text">
                        <?php the_content(); ?>
                    </div>
                </div>
            </div>

            <!-- Vision & Mission -->
            <div class="row py-5 mt-5">
                <div class="col-md-6 mb-4">
                    <div class="p-5 rounded-lg shadow-sm h-100" style="background: #f8f9fa; border-left: 5px solid var(--kopizon-pink);">
                        <div class="icon mb-4" style="font-size: 32px; color: var(--kopizon-pink);"><i class="fas fa-eye"></i></div>
                        <h4 class="font-weight-bold mb-3">Our Vision</h4>
                        <p class="text-muted"><?php echo esc_html( get_post_meta( get_the_ID(), 'kopizon_vision', true ) ?: 'To be the primary catalyst for ensuring every child in Mauritius has access to the best medical care possible.' ); ?></p>
                    </div>
                </div>
                <div class="col-md-6 mb-4">
                    <div class="p-5 rounded-lg shadow-sm h-100" style="background: #f8f9fa; border-left: 5px solid var(--kopizon-blue);">
                        <div class="icon mb-4" style="font-size: 32px; color: var(--kopizon-blue);"><i class="fas fa-bullseye"></i></div>
                        <h4 class="font-weight-bold mb-3">Our Mission</h4>
                        <p class="text-muted"><?php echo esc_html( get_post_meta( get_the_ID(), 'kopizon_mission', true ) ?: 'We provide financial, logistical, and psychological support to children facing severe medical challenges.' ); ?></p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Team Section Preview or similar can be added here -->

    <?php
endwhile;

get_footer();
