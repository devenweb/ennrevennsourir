<?php
/**
 * Template Name: Help an Adolescent
 */
get_header();
?>

<main>
    <!-- Hero -->
    <section class="py-5" style="background: #f8f9fa;">
        <div class="container py-5 text-center">
            <h1 class="font-weight-bold" style="font-size: 48px; color: var(--kopizon-navy);">Supporting Adolescents</h1>
            <p class="lead text-muted">Specialized care and guidance for young adults battling health challenges.</p>
        </div>
    </section>

    <!-- Content -->
    <section class="py-5 bg-white">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-lg-6">
                    <img src="<?php echo get_template_directory_uri(); ?>/assets/images/rare-diseases.jpg" class="img-fluid rounded shadow-lg" alt="Adolescents">
                </div>
                <div class="col-lg-6 pl-lg-5 mt-4 mt-lg-0">
                    <h2 class="font-weight-bold mb-4" style="color: var(--kopizon-pink);">Beyond Medical Care</h2>
                    <p class="text-muted mb-4" style="font-size: 18px; line-height: 1.8;">Adolescence is a critical stage of life, and battling a chronic illness during this time adds immense mental and emotional strain. Our program provides:</p>
                    <ul class="list-unstyled">
                        <?php 
                        $benefits = get_post_meta( get_the_ID(), 'kopizon_benefits', true );
                        if ( $benefits && is_array($benefits) ) :
                            foreach ( $benefits as $benefit ) : ?>
                            <li class="mb-3"><i class="fas fa-check-circle mr-2 text-success"></i> <?php echo esc_html($benefit['title']); ?>: <?php echo esc_html($benefit['text']); ?></li>
                        <?php endforeach; else : ?>
                            <li class="text-muted">No specific programs defined yet.</li>
                        <?php endif; ?>
                    </ul>
                    <a href="<?php echo esc_url( home_url('/donation/') ); ?>" class="btn btn-primary rounded-pill px-5 mt-4" style="background: var(--kopizon-pink); border: none; font-weight: 700;">SUPPORT THIS PROGRAM</a>
                </div>
            </div>
        </div>
    </section>
</main>

<?php get_footer(); ?>
