<?php
/**
 * Template Name: Support an Adult
 */
get_header();
?>

<main>
    <!-- Hero -->
    <section class="py-5" style="background: var(--kopizon-navy); color: white;">
        <div class="container py-5 text-center">
            <h1 class="font-weight-bold" style="font-size: 48px;">Support for Adults</h1>
            <p class="lead text-white-50">Empowering families by supporting the breadwinners during health crises.</p>
        </div>
    </section>

    <!-- Content -->
    <section class="py-5 bg-white">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-lg-6 pr-lg-5">
                    <h2 class="font-weight-bold mb-4" style="color: var(--kopizon-pink);">Holistic Family Support</h2>
                    <p class="text-muted mb-4" style="font-size: 18px; line-height: 1.8;">When a parent or guardian faces a life-threatening illness, the entire family structure is at risk. Our adult support program extends our mission to ensure the stability of the household.</p>
                    <?php 
                    $benefits = get_post_meta( get_the_ID(), 'kopizon_benefits', true );
                    if ( $benefits && is_array($benefits) ) :
                        foreach ( $benefits as $benefit ) : ?>
                        <div class="d-flex mb-4">
                            <i class="<?php echo esc_attr($benefit['icon']); ?> fa-3x mr-4" style="color: var(--kopizon-pink);"></i>
                            <div>
                                <h5 class="font-weight-bold"><?php echo esc_html($benefit['title']); ?></h5>
                                <p class="small text-muted"><?php echo esc_html($benefit['text']); ?></p>
                            </div>
                        </div>
                    <?php endforeach; else : ?>
                        <p class="text-muted">No specific benefits defined for this program.</p>
                    <?php endif; ?>
                    <a href="<?php echo esc_url( home_url('/donation/') ); ?>" class="btn btn-primary rounded-pill px-5 mt-4" style="background: var(--kopizon-pink); border: none; font-weight: 700;">DONATE TO THIS FUND</a>
                </div>
                <div class="col-lg-6 mt-4 mt-lg-0">
                    <img src="<?php echo get_template_directory_uri(); ?>/assets/images/heart-surgery.jpg" class="img-fluid rounded shadow-lg" alt="Adult Support">
                </div>
            </div>
        </div>
    </section>
</main>

<?php get_footer(); ?>
