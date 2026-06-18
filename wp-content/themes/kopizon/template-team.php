<?php
/**
 * Template Name: Our Team
 */
get_header();
?>

<main>
    <!-- Inner Hero -->
    <section class="inner-hero py-5" style="background: var(--kopizon-navy);">
        <div class="container text-center py-5">
            <h1 class="font-weight-bold text-white" style="font-size: 48px;"><?php the_title(); ?></h1>
            <div class="lead text-white-50"><?php the_content(); ?></div>
        </div>
    </section>

    <!-- Team Grid -->
    <section class="py-5 bg-white">
        <div class="container">
            <div class="row">
                <?php 
                $team = get_post_meta( get_the_ID(), 'kopizon_team', true );
                if ( $team && is_array($team) ) :
                    foreach ( $team as $member ) : ?>
                    <div class="col-lg-3 col-md-6 mb-4">
                        <div class="card border-0 shadow-sm overflow-hidden text-center" style="border-radius: 20px;">
                            <img src="<?php echo get_template_directory_uri(); ?>/assets/images/<?php echo esc_attr($member['img']); ?>" alt="<?php echo esc_attr($member['name']); ?>" class="card-img-top">
                            <div class="card-body py-4">
                                <h4 class="font-weight-bold mb-1"><?php echo esc_html($member['name']); ?></h4>
                                <p class="small font-weight-bold mb-3" style="color: var(--kopizon-pink); text-transform: uppercase;"><?php echo esc_html($member['role']); ?></p>
                                <div class="social-links">
                                    <?php if ( ! empty( $member['linkedin'] ) ) : ?>
                                        <a href="<?php echo esc_url( $member['linkedin'] ); ?>" class="text-muted mx-2" target="_blank"><i class="fab fa-linkedin"></i></a>
                                    <?php endif; ?>
                                    <?php if ( ! empty( $member['twitter'] ) ) : ?>
                                        <a href="<?php echo esc_url( $member['twitter'] ); ?>" class="text-muted mx-2" target="_blank"><i class="fab fa-twitter"></i></a>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; else : ?>
                    <p class="text-muted w-100 text-center">Define team members in the page editor.</p>
                <?php endif; ?>
            </div>
        </div>
    </section>

    <!-- CTA Section -->
    <section class="py-5" style="background: var(--kopizon-pink);">
        <div class="container text-center py-4">
            <h2 class="text-white font-weight-bold mb-4">Want to make a difference?</h2>
            <a href="<?php echo esc_url( home_url('/volunteer/') ); ?>" class="btn btn-light rounded-pill px-5 btn-lg font-weight-bold" style="color: var(--kopizon-pink);">Join Our Team</a>
        </div>
    </section>
</main>

<?php get_footer(); ?>
