<?php
/**
 * Template Name: Sponsor a Child
 */
get_header();
?>

<main>
    <!-- Impact Hero -->
    <section class="py-5" style="background: #00153a; color: white; position: relative; overflow: hidden;">
        <div class="container py-5 text-center">
            <h1 class="font-weight-bold" style="font-size: 52px; letter-spacing: -1px;">Sponsor a Child</h1>
            <p class="lead text-white-50" style="max-width: 800px; margin: 0 auto;">Your long-term commitment directly funds the life-saving treatment, medication, and emotional support for a child in need.</p>
        </div>
    </section>

    <!-- Program Details -->
    <section class="py-5 bg-white">
        <div class="container">
            <div class="row align-items-center mb-5">
                <!-- How It Works -->
                <div class="col-lg-6">
                    <h2 class="font-weight-bold mb-4" style="color: var(--kopizon-pink);">How Sponsorship Works</h2>
                    <ul class="list-unstyled">
                        <?php 
                        $steps = get_post_meta( get_the_ID(), 'kopizon_steps', true );
                        if ( $steps && is_array($steps) ) :
                            $i = 1;
                            foreach ( $steps as $step ) : ?>
                            <li class="mb-4 d-flex align-items-start">
                                <div class="bg-light p-3 rounded-circle mr-3" style="min-width: 60px; height: 60px; display: flex; align-items: center; justify-content: center; color: var(--kopizon-pink); font-size: 24px; font-weight: 700;"><?php echo $i++; ?></div>
                                <div>
                                    <h5 class="font-weight-bold"><?php echo esc_html($step['title']); ?></h5>
                                    <p class="text-muted"><?php echo esc_html($step['text']); ?></p>
                                </div>
                            </li>
                        <?php endforeach; else : ?>
                            <p class="text-muted">Define steps in the page editor.</p>
                        <?php endif; ?>
                    </ul>
                </div>

                <!-- Sponsorship Packages -->
                <div class="col-lg-6">
                    <div class="p-5" style="background: #f8f9fa; border-radius: 30px; border: 2px dashed #ddd;">
                        <h3 class="font-weight-bold mb-4 text-center">Sponsorship Packages</h3>
                        <div class="row">
                            <?php 
                            $packages = get_post_meta( get_the_ID(), 'kopizon_packages', true );
                            if ( $packages && is_array($packages) ) :
                                foreach ( $packages as $pkg ) : ?>
                                <div class="col-md-6 mb-3">
                                    <div class="bg-white p-4 rounded shadow-sm text-center">
                                        <h2 class="font-weight-bold mb-0" style="color: <?php echo esc_attr($pkg['color']); ?>;"><?php echo esc_html($pkg['amount']); ?></h2>
                                        <p class="small text-muted font-weight-bold"><?php echo esc_html($pkg['type']); ?></p>
                                        <hr>
                                        <p class="small text-muted"><?php echo esc_html($pkg['desc']); ?></p>
                                    </div>
                                </div>
                            <?php endforeach; else : ?>
                                <div class="col-12 text-center text-muted">No sponsorship packages defined.</div>
                            <?php endif; ?>
                        </div>
                        <a href="<?php echo esc_url( home_url('/donation/') ); ?>" class="btn btn-primary btn-block rounded-pill py-3 mt-4 font-weight-bold" style="background: var(--kopizon-pink); border: none;">START SPONSORING TODAY</a>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Children Seeking Sponsors — Dynamic WP_Query -->
    <section class="py-5 bg-light">
        <div class="container">
            <div class="section-heading mb-5">
                <span>Active Cases</span>
                <h2>Children Seeking Sponsors</h2>
            </div>

            <?php
            $campaigns = new WP_Query( array(
                'post_type'      => 'product',
                'posts_per_page' => 6,
                'post_status'    => 'publish',
                'tax_query'      => array(
                    array(
                        'taxonomy' => 'product_cat',
                        'field'    => 'slug',
                        'terms'    => array( 'crowdfunding', 'campaigns' ),
                        'operator' => 'IN',
                    ),
                ),
            ) );

            if ( $campaigns->have_posts() ) : ?>
                <div class="campaign-grid">
                    <?php while ( $campaigns->have_posts() ) : $campaigns->the_post();
                        $goal           = get_post_meta( get_the_ID(), '_goal', true );
                        $total_raised   = get_post_meta( get_the_ID(), '_total_raised', true );
                        $percent        = ( $goal && $goal > 0 ) ? min( 100, round( ( $total_raised / $goal ) * 100 ) ) : 0;
                        $category_terms = get_the_terms( get_the_ID(), 'product_tag' );
                        $cat_label      = ( $category_terms && ! is_wp_error( $category_terms ) ) ? esc_html( $category_terms[0]->name ) : '';
                        $thumb_url      = get_the_post_thumbnail_url( get_the_ID(), 'medium_large' );
                        if ( ! $thumb_url ) {
                            $thumb_url = get_template_directory_uri() . '/assets/images/cancer-care.jpg';
                        }
                    ?>
                        <div class="mockup-campaign-card" onclick="window.location.href='<?php the_permalink(); ?>'" style="cursor: pointer;">
                            <div class="campaign-card-img" style="background-image: url('<?php echo esc_url( $thumb_url ); ?>');">
                                <?php if ( $cat_label ) : ?>
                                    <span class="campaign-category"><?php echo $cat_label; ?></span>
                                <?php endif; ?>
                            </div>
                            <div class="campaign-card-content">
                                <h3><a href="<?php the_permalink(); ?>" class="text-dark text-decoration-none"><?php the_title(); ?></a></h3>
                                <div class="campaign-progress">
                                    <div class="campaign-progress-bar" style="width: <?php echo $percent; ?>%;"></div>
                                </div>
                                <div class="campaign-meta">
                                    <span>Rs <?php echo number_format( (float) $total_raised, 0, '.', ',' ); ?> raised</span>
                                    <span>Goal: Rs <?php echo number_format( (float) $goal, 0, '.', ',' ); ?></span>
                                </div>
                            </div>
                        </div>
                    <?php endwhile; wp_reset_postdata(); ?>
                </div>
            <?php else : ?>
                <p class="text-muted text-center">No active campaigns at this time. <a href="<?php echo esc_url( home_url('/donation/') ); ?>">Make a general donation</a>.</p>
            <?php endif; ?>
        </div>
    </section>
</main>

<?php get_footer(); ?>
