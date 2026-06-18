<?php
/**
 * Template Name: Child Cancer Scheme
 */
get_header();
?>

<main class="py-5" style="background: #fffafa;">
    <div class="container py-5">
        <h1 class="font-weight-bold text-center mb-5" style="color: var(--kopizon-pink);">Child Cancer Scheme</h1>
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <p class="lead text-center mb-5">Our specialized scheme ensures that children from underprivileged backgrounds have access to life-critical cancer screening and early treatment.</p>
            </div>
        </div>
        <div class="row">
            <?php 
            $benefits = get_post_meta( get_the_ID(), 'kopizon_benefits', true );
            if ( $benefits && is_array($benefits) ) :
                foreach ( $benefits as $benefit ) : ?>
                <div class="col-md-4 mb-4">
                    <div class="p-4 text-center border-0 shadow-sm bg-white" style="border-radius: 15px;">
                        <i class="<?php echo esc_attr($benefit['icon']); ?> fa-3x mb-3" style="color: var(--kopizon-pink);"></i>
                        <h5 class="font-weight-bold"><?php echo esc_html($benefit['title']); ?></h5>
                        <p class="small text-muted"><?php echo esc_html($benefit['text']); ?></p>
                    </div>
                </div>
            <?php endforeach; else : ?>
                <p class="text-muted w-100 text-center py-4">No scheme benefits defined. Please add them in the page editor.</p>
            <?php endif; ?>
        </div>
        <div class="text-center mt-5">
            <a href="<?php echo esc_url( home_url('/donation/') ); ?>" class="btn btn-primary rounded-pill px-5 py-3" style="background: var(--kopizon-pink); border: none; font-weight: 700; font-size: 16px;">SUPPORT THE CANCER SCHEME</a>
        </div>
    </div>
</main>

<?php get_footer(); ?>
