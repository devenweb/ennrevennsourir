<?php
/**
 * Template Name: Childhood Cancer Care
 */
get_header();
?>

<main>
    <!-- Hero -->
    <section class="py-5" style="background: #fff0f5;">
        <div class="container py-5 text-center">
            <h1 class="font-weight-bold" style="font-size: 48px; color: var(--kopizon-pink);">Childhood Cancer Care</h1>
            <p class="lead text-muted">A dedicated program for children diagnosed with cancer, providing end-to-end support.</p>
        </div>
    </section>

    <!-- Content -->
    <section class="py-5 bg-white">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-lg-6">
                    <h2 class="font-weight-bold mb-4"><?php the_title(); ?></h2>
                    <div class="mission-text mb-4" style="font-size: 18px; line-height: 1.8;">
                        <?php the_content(); ?>
                    </div>
                    <div class="row mt-5">
                        <?php 
                        $programs = get_post_meta( get_the_ID(), 'kopizon_programs', true );
                        if ( $programs && is_array( $programs ) ) :
                            foreach ( $programs as $prog ) : ?>
                            <div class="col-md-6 mb-4">
                                <h5 class="font-weight-bold">
                                    <i class="<?php echo esc_attr( $prog['icon'] ); ?> mr-2" style="color: var(--kopizon-pink);"></i> 
                                    <?php echo esc_html( $prog['title'] ); ?>
                                </h5>
                                <p class="small text-muted"><?php echo esc_html( $prog['text'] ); ?></p>
                            </div>
                        <?php endforeach; else : ?>
                            <p class="text-muted">Explore our programs in the page editor.</p>
                        <?php endif; ?>
                    </div>
                    <a href="<?php echo esc_url( home_url('/donation/') ); ?>" class="btn btn-primary rounded-pill px-5 mt-2" style="background: var(--kopizon-pink); border: none; font-weight: 700;">DONATE TO THIS PROGRAM</a>
                </div>
                <div class="col-lg-6 mt-4 mt-lg-0">
                    <?php if ( has_post_thumbnail() ) : ?>
                        <?php the_post_thumbnail( 'large', array( 'class' => 'img-fluid rounded shadow-lg' ) ); ?>
                    <?php else : ?>
                        <img src="<?php echo get_template_directory_uri(); ?>/assets/images/cancer-care.jpg" class="img-fluid rounded shadow-lg" alt="Cancer Care">
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </section>
</main>

<?php get_footer(); ?>
