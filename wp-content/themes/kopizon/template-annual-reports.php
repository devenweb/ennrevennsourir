<?php
/**
 * Template Name: Annual Reports
 */
get_header();
?>

<main>
    <!-- Hero -->
    <section class="py-5" style="background: var(--kopizon-light-grey);">
        <div class="container text-center py-5">
            <h1 class="font-weight-bold" style="font-size: 48px; color: var(--kopizon-dark);">Transparency &amp; Impact</h1>
            <p class="lead text-muted">We believe in full transparency. Explore our financial and operational reports.</p>
        </div>
    </section>

    <!-- Reports Grid -->
    <section class="py-5 bg-white">
        <div class="container">
            <div class="row">
                <?php 
                $reports = get_post_meta( get_the_ID(), 'kopizon_reports', true );
                if ( $reports && is_array($reports) ) :
                    foreach ( $reports as $report ) : ?>
                    <div class="col-lg-4 mb-4">
                        <div class="p-5 shadow-sm text-center bg-white" style="border-radius: 20px; border-bottom: 4px solid <?php echo esc_attr($report['color']); ?>;">
                            <i class="<?php echo esc_attr($report['icon']); ?> fa-4x mb-4" style="color: <?php echo esc_attr($report['color'] === 'var(--kopizon-pink)' ? 'var(--kopizon-pink)' : 'var(--kopizon-navy)'); ?>;"></i>
                            <h4 class="font-weight-bold"><?php echo esc_html($report['title']); ?></h4>
                            <p class="small text-muted mb-4"><?php echo esc_html($report['desc']); ?></p>
                            <a href="<?php echo esc_url($report['file']); ?>" class="btn btn-outline-dark rounded-pill px-4 btn-sm font-weight-bold">DOWNLOAD PDF</a>
                        </div>
                    </div>
                <?php endforeach; else : ?>
                    <p class="text-muted w-100 text-center">No reports found. Please add them in the page editor.</p>
                <?php endif; ?>
            </div>
        </div>
    </section>
</main>

<?php get_footer(); ?>
