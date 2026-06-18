<?php
/**
 * Template Name: Our Partners
 */
get_header();
?>

<main>
    <!-- Inner Hero -->
    <section class="inner-hero py-5" style="background: var(--kopizon-light-grey);">
        <div class="container text-center py-5">
            <h1 class="font-weight-bold" style="font-size: 48px; color: var(--kopizon-dark);">Our Partners</h1>
            <p class="lead text-muted">Collaborating for a better tomorrow.</p>
        </div>
    </section>

    <!-- Partners Grid -->
    <section class="py-5 bg-white">
        <div class="container">
            <div class="section-heading mb-5">
                <span>Corporate &amp; Medical Partners</span>
                <h2>Those Who Help Us Help Others</h2>
            </div>
            <div class="row text-center">
                <?php 
                $partners = get_post_meta( get_the_ID(), 'kopizon_partners', true );
                if ( $partners && is_array($partners) ) :
                    foreach ( $partners as $partner ) : ?>
                    <div class="col-md-3 mb-4">
                        <div class="p-4 border rounded" style="border-radius: 15px;">
                            <i class="<?php echo esc_attr($partner['icon']); ?> fa-3x mb-3" style="color: var(--kopizon-pink);"></i>
                            <h4><?php echo esc_html($partner['title']); ?></h4>
                            <p class="small text-muted"><?php echo esc_html($partner['desc']); ?></p>
                        </div>
                    </div>
                <?php endforeach; else : ?>
                    <p class="text-muted w-100 text-center">Define partners in the page editor.</p>
                <?php endif; ?>
            </div>
        </div>
    </section>
</main>

<?php get_footer(); ?>
