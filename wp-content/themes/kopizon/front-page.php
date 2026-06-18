<?php
/**
 * The template for displaying the front page
 */

get_header();
?>

<section class="mockup-hero" id="hero-slider" style="background-image: url('<?php echo esc_url( get_theme_mod('kopizon_hero_bg', get_template_directory_uri() . '/assets/images/hero-bg.jpg') ); ?>');">
    <div class="container">
        <div class="hero-content">
            <h1 id="hero-title"><?php echo esc_html( get_theme_mod('kopizon_hero_title', 'A Dream and a Smile for every child') ); ?></h1>
            <div class="hero-btns">
                <a href="<?php echo esc_url( get_theme_mod('kopizon_hero_btn1_link', home_url( '/donation' )) ); ?>" class="btn btn-primary"
                    style="background: var(--kopizon-blue); border: none;"><?php echo esc_html( get_theme_mod('kopizon_hero_btn1_label', 'View Projects') ); ?></a>
                <a href="<?php echo esc_url( get_theme_mod('kopizon_hero_btn2_link', home_url( '/donation' )) ); ?>" class="btn btn-light ml-3"><?php echo esc_html( get_theme_mod('kopizon_hero_btn2_label', 'Donate Now') ); ?></a>
            </div>
        </div>
    </div>
</section>

<section class="impact-strip">
    <div class="impact-strip-inner">
        <?php for ($i = 1; $i <= 5; $i++) : 
            $num = get_theme_mod("kopizon_impact_num_$i");
            $label = get_theme_mod("kopizon_impact_label_$i");
            $icon = get_theme_mod("kopizon_impact_icon_$i", 'fas fa-star');
            $colors = ['#e91e8c', '#3b82f6', '#10b981', '#f59e0b', '#8b5cf6'];
            if ($num || $label) :
        ?>
            <div class="impact-stat-item">
                <div class="impact-icon-ring" style="--ring-color: <?php echo $colors[$i-1]; ?>;">
                    <i class="<?php echo esc_attr($icon); ?>"></i>
                </div>
                <div class="impact-stat-body">
                    <span class="impact-number"><?php echo esc_html($num); ?></span>
                    <span class="impact-label"><?php echo esc_html($label); ?></span>
                </div>
            </div>
            <?php if ($i < 5) echo '<div class="impact-divider"></div>'; ?>
        <?php endif; endfor; ?>
    </div>
</section>

<section class="support-section py-2 bg-white">
    <div class="container">
        <div class="section-heading" style="padding-top: 40px;">
            <span><?php echo esc_html( get_theme_mod('kopizon_support_eyebrow', 'Raised to Help Kids') ); ?></span>
            <h2><?php echo esc_html( get_theme_mod('kopizon_support_heading', 'Support a child') ); ?></h2>
        </div>

        <div class="campaign-grid">
            <?php
            // Dynamic Campaign Query
            $homepage_campaigns = new WP_Query( array(
                'post_type'      => 'product',
                'posts_per_page' => 3,
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

            if ( $homepage_campaigns->have_posts() ) : 
                while ( $homepage_campaigns->have_posts() ) : $homepage_campaigns->the_post();
                    $goal = get_post_meta( get_the_ID(), '_goal', true );
                    $raised = get_post_meta( get_the_ID(), '_total_raised', true );
                    $percent = ( $goal && $goal > 0 ) ? min( 100, round( ( $raised / $goal ) * 100 ) ) : 0;
                    $thumb_url = get_the_post_thumbnail_url( get_the_ID(), 'medium_large' );
                    if ( ! $thumb_url ) $thumb_url = get_template_directory_uri() . '/assets/images/cancer-care.jpg';
                    ?>
                    <div class="mockup-campaign-card" onclick="window.location.href='<?php the_permalink(); ?>'" style="cursor: pointer;">
                        <div class="campaign-card-img" style="background-image: url('<?php echo esc_url( $thumb_url ); ?>');">
                            <span class="campaign-category"><?php echo 100 - $percent; ?>% to go</span>
                        </div>
                        <div class="campaign-card-content">
                            <h3><?php the_title(); ?></h3>
                            <div class="campaign-progress">
                                <div class="campaign-progress-bar" style="width: <?php echo $percent; ?>%;"></div>
                            </div>
                            <div class="campaign-meta">
                                <span>Rs <?php echo number_format( (float) $raised, 0, '.', ',' ); ?> raised</span>
                                <span>Goal: Rs <?php echo number_format( (float) $goal, 0, '.', ',' ); ?></span>
                            </div>
                        </div>
                    </div>
                <?php endwhile; wp_reset_postdata(); 
            else : ?>
                <p class="text-muted w-100 text-center">No active campaigns found. Please add products to the 'campaigns' category.</p>
            <?php endif; ?>
        </div>
        <div class="text-center mt-5">
            <a href="<?php echo esc_url( home_url( '/donation' ) ); ?>" class="btn btn-primary px-5 py-3 rounded-pill" style="background: var(--kopizon-pink); border: none; font-weight: 700;">View All Campaigns</a>
        </div>
    </div>
</section>

<section class="new-patients-section bg-light-grey">
    <div class="container">
        <div class="section-heading">
            <span><?php echo esc_html( get_theme_mod('kopizon_new_patients_eyebrow', 'New Campaigns') ); ?></span>
            <h2><?php echo esc_html( get_theme_mod('kopizon_new_patients_heading', 'New Patients Support') ); ?></h2>
        </div>
        <div class="new-patients-grid">
            <?php
            $new_patients = new WP_Query(array(
                'post_type'      => 'product',
                'posts_per_page' => 3,
                'post_status'    => 'publish',
                'tax_query'      => array(
                    array(
                        'taxonomy' => 'product_cat',
                        'field'    => 'slug',
                        'terms'    => array( 'urgent', 'medical' ),
                        'operator' => 'IN',
                    ),
                ),
            ));
            if ($new_patients->have_posts()) : 
                while ($new_patients->have_posts()) : $new_patients->the_post();
            ?>
                <div class="new-patient-card" onclick="window.location.href='<?php the_permalink(); ?>'">
                    <?php if (has_post_thumbnail()) : ?>
                        <?php the_post_thumbnail('large'); ?>
                    <?php else : ?>
                        <img src="<?php echo get_template_directory_uri(); ?>/assets/images/rare-diseases.jpg">
                    <?php endif; ?>
                    <div class="new-patient-overlay">
                        <h3><?php the_title(); ?></h3>
                        <p><?php echo wp_trim_words(get_the_excerpt(), 15); ?></p>
                    </div>
                </div>
            <?php endwhile; wp_reset_postdata(); else : ?>
                <p class="text-muted w-100 text-center">No urgent campaigns found.</p>
            <?php endif; ?>
        </div>
    </div>
</section>

<section class="experience-section bg-white">
    <div class="container">
        <div class="experience-grid">
            <div class="experience-img-container">
                <img src="<?php echo esc_url( get_theme_mod('kopizon_about_img', get_template_directory_uri() . '/assets/images/experience-main.jpg') ); ?>" class="experience-img-main">
                <div class="experience-badge">
                    <h2><?php echo esc_html( get_theme_mod('kopizon_about_years', '10') ); ?></h2>
                    <p>Years of Experience</p>
                </div>
            </div>
            <div class="experience-text">
                <span class="text-pink font-weight-bold" style="color: var(--kopizon-pink);"><?php echo esc_html( get_theme_mod('kopizon_about_eyebrow', 'About Our Charity') ); ?></span>
                <h2 class="mb-4" style="font-size: 42px; font-weight: 800;"><?php echo esc_html( get_theme_mod('kopizon_about_title', 'From the first idea to the final outcome') ); ?></h2>
                <p class="mb-4"><?php echo nl2br( esc_html( get_theme_mod('kopizon_about_desc', 'Since 2016, Enn Rev Enn Sourir has been dedicated to ensuring all children receive the best medical treatment, regardless of financial barriers...') ) ); ?></p>
                <div class="benefit-box bg-white p-4 rounded mb-4 shadow" style="display: flex; gap: 20px; border-radius:15px;">
                    <i class="fas fa-check-circle" style="color: var(--kopizon-pink); font-size: 24px;"></i>
                    <p class="mb-0"><strong><?php echo esc_html( get_theme_mod('kopizon_about_benefit_title', 'Join our journey') ); ?></strong><br><?php echo esc_html( get_theme_mod('kopizon_about_benefit_desc', 'Making a difference in the world starts with one step.') ); ?></p>
                </div>
            </div>
        </div>
    </div>
</section>

<section class="counter-section">
    <div class="container">
        <div class="counter-grid">
            <?php for ($i = 1; $i <= 4; $i++) : 
                $num = get_theme_mod("kopizon_counter_num_$i");
                $label = get_theme_mod("kopizon_counter_label_$i");
                $icon = get_theme_mod("kopizon_counter_icon_$i", 'fas fa-star');
                if ($num || $label) :
            ?>
                <div class="counter-item">
                    <i class="<?php echo esc_attr($icon); ?>"></i>
                    <span class="counter-number" data-target="<?php echo esc_attr( preg_replace('/[^0-9]/', '', $num) ); ?>">0</span>
                    <span class="counter-suffix"><?php echo esc_html( preg_replace('/[0-9]/', '', $num) ); ?></span>
                    <p><?php echo esc_html($label); ?></p>
                </div>
            <?php endif; endfor; ?>
        </div>
    </div>
</section>

<section class="dark-section">
    <div class="container">
        <div class="dark-grid">
            <div class="dark-text">
                <span class="text-pink font-weight-bold" style="color: var(--kopizon-pink);"><?php echo esc_html( get_theme_mod('kopizon_why_eyebrow', 'Why Choose Us') ); ?></span>
                <h2 class="mb-5" style="font-size: 42px; font-weight: 800;"><?php echo esc_html( get_theme_mod('kopizon_why_heading', 'Why choose Enn Rev Enn Sourir') ); ?></h2>
                
                <?php 
                $why_defaults = array(
                    1 => array('title' => 'Trusted Organization', 'desc' => 'We are a certified charity with over 10 years of experience.'),
                    2 => array('title' => 'Maximum Impact', 'desc' => '100% of your donation goes directly to the cause you choose.')
                );
                for ($i = 1; $i <= 2; $i++) : 
                    $title = get_theme_mod("kopizon_why_feat_title_$i", $why_defaults[$i]['title']);
                    $desc  = get_theme_mod("kopizon_why_feat_desc_$i", $why_defaults[$i]['desc']);
                    if ($title || $desc) :
                ?>
                    <div class="dark-feature-item">
                        <i class="fas fa-check"></i>
                        <div>
                            <h4 class="font-weight-bold"><?php echo esc_html($title); ?></h4>
                            <p><?php echo esc_html($desc); ?></p>
                        </div>
                    </div>
                <?php endif; endfor; ?>
            </div>
            <div class="dark-img">
                <?php $why_img = get_theme_mod('kopizon_why_img'); ?>
                <img src="<?php echo $why_img ? esc_url($why_img) : get_template_directory_uri() . '/assets/images/why-choose.jpg'; ?>" class="w-100 rounded">
            </div>
        </div>
        <div class="dark-gallery">
            <?php for ($i = 1; $i <= 5; $i++) : 
                $img = get_theme_mod("kopizon_gallery_img_$i");
                if ($img) :
            ?>
                <img src="<?php echo esc_url($img); ?>" alt="Gallery">
            <?php elseif (!$img && $i === 1) : // Fallback for 1st image only if none set ?>
                <img src="<?php echo get_template_directory_uri() . '/assets/images/gallery-1.jpg'; ?>" alt="Gallery">
                <img src="<?php echo get_template_directory_uri() . '/assets/images/gallery-2.jpg'; ?>" alt="Gallery">
                <img src="<?php echo get_template_directory_uri() . '/assets/images/gallery-3.jpg'; ?>" alt="Gallery">
                <img src="<?php echo get_template_directory_uri() . '/assets/images/gallery-4.jpg'; ?>" alt="Gallery">
                <img src="<?php echo get_template_directory_uri() . '/assets/images/gallery-5.jpg'; ?>" alt="Gallery">
            <?php break; endif; endfor; ?>
        </div>
    </div>
</section>

<section class="split-panel-section">
    <div class="split-panel-left">
        <span class="split-panel-eyebrow"><?php echo esc_html( get_theme_mod('kopizon_split_eyebrow', "We're Focused on Results") ); ?></span>
        <h2 class="split-panel-heading"><?php echo esc_html( get_theme_mod('kopizon_split_heading', "The new way of managing that's making waves — for our Child Cancer Scheme.") ); ?></h2>
        <a href="<?php echo esc_url( home_url( '/cancer-scheme' ) ); ?>" class="split-panel-btn">Learn More</a>
    </div>
    <div class="split-panel-right" style="background-image: url('<?php echo esc_url( get_theme_mod('kopizon_split_bg', get_template_directory_uri() . '/assets/images/cancer-care.jpg') ); ?>');">
        <div class="split-panel-play">
            <i class="fas fa-play"></i>
        </div>
    </div>
    <div class="split-panel-cards">
        <?php 
        $spc_defaults = array(
            1 => array('title' => 'Psychosocial Support', 'desc' => 'When a child has cancer, the entire family faces emotional, physical, and financial challenges together.', 'icon' => 'fas fa-people-arrows'),
            2 => array('title' => 'Rapid Action', 'desc' => 'Rapid action is taken for suspected cancer patients to ensure timely diagnosis and immediate support.', 'icon' => 'fas fa-bolt'),
            3 => array('title' => 'Psychological Preparation', 'desc' => 'It is necessary to help patients cope effectively with the upcoming medical procedures.', 'icon' => 'fas fa-brain')
        );
        for ($i = 1; $i <= 3; $i++) : 
            $title = get_theme_mod("kopizon_spc_title_$i", $spc_defaults[$i]['title']);
            $desc  = get_theme_mod("kopizon_spc_desc_$i", $spc_defaults[$i]['desc']);
            $icon  = get_theme_mod("kopizon_spc_icon_$i", $spc_defaults[$i]['icon']);
            if ($title || $desc) :
        ?>
            <div class="spc-card">
                <div class="spc-icon"><i class="<?php echo esc_attr($icon); ?>"></i></div>
                <div class="spc-body">
                    <h4><?php echo esc_html($title); ?></h4>
                    <p><?php echo esc_html($desc); ?></p>
                </div>
            </div>
        <?php endif; endfor; ?>
    </div>
</section>

<section class="patients-needs-section bg-light-grey">
    <div class="container">
        <div class="needs-grid">
            <div class="needs-text">
                <span class="text-pink font-weight-bold" style="color: var(--kopizon-pink);"><?php echo esc_html( get_theme_mod('kopizon_needs_eyebrow', 'How to help') ); ?></span>
                <h2 class="mb-4" style="font-size: 36px; font-weight: 800;"><?php echo esc_html( get_theme_mod('kopizon_needs_heading', 'Get our sick patients needs') ); ?></h2>
                <ul class="list-unstyled">
                    <?php 
                    $needs_defaults = array(
                        1 => 'Child Cancer Support',
                        2 => 'Medical help for poor people',
                        3 => 'Education for students',
                        4 => 'Emergency Relief Care'
                    );
                    for ($i = 1; $i <= 4; $i++) : 
                        $item = get_theme_mod("kopizon_needs_item_$i", $needs_defaults[$i]);
                        if ($item) :
                    ?>
                        <li class="mb-3"><i class="fas fa-check-circle mr-2" style="color: var(--kopizon-pink);"></i> <?php echo esc_html($item); ?></li>
                    <?php endif; endfor; ?>
                </ul>
            </div>
            <div class="needs-form-container">
                <h4><?php echo esc_html( get_theme_mod('kopizon_needs_form_title', 'Make a Donation') ); ?></h4>
                <form>
                    <div class="form-group mb-3"><input type="text" class="form-control rounded-pill" placeholder="Name"></div>
                    <div class="form-group mb-3"><input type="email" class="form-control rounded-pill" placeholder="Email"></div>
                    <button class="btn btn-primary btn-block rounded-pill py-3 w-100" style="background: var(--kopizon-blue); border: none;">Donate Now</button>
                </form>
            </div>
            <div class="needs-stats">
                <?php 
                $stats_defaults = array(
                    1 => array('num' => '4,200', 'label' => 'Donations Made', 'icon' => 'fas fa-hand-holding-usd'),
                    2 => array('num' => '8,500', 'label' => 'Active Volunteers', 'icon' => 'fas fa-heart')
                );
                for ($i = 1; $i <= 2; $i++) : 
                    $num = get_theme_mod("kopizon_needs_stat_num_$i", $stats_defaults[$i]['num']);
                    $label = get_theme_mod("kopizon_needs_stat_label_$i", $stats_defaults[$i]['label']);
                    $icon = $stats_defaults[$i]['icon']; // Icon is static for now to keep it simple, or could add set
                    $style = ($i === 2) ? 'style="background: var(--kopizon-blue); color: white;"' : '';
                    $text_class = ($i === 2) ? 'text-white' : '';
                    $subtext_class = ($i === 2) ? 'text-white-50' : 'text-muted';
                    $icon_style = ($i === 2) ? 'style="color: white;"' : '';
                    if ($num || $label) :
                ?>
                    <div class="need-stat-item" <?php echo $style; ?>>
                        <i class="<?php echo esc_attr($icon); ?>" <?php echo $icon_style; ?>></i>
                        <div>
                            <div class="count <?php echo $text_class; ?>"><?php echo esc_html($num); ?></div>
                            <p class="mb-0 <?php echo $subtext_class; ?>"><?php echo esc_html($label); ?></p>
                        </div>
                    </div>
                <?php endif; endfor; ?>
            </div>
        </div>
    </div>
</section>

<section class="testimonials-section bg-white">
    <div class="container">
        <div class="section-heading">
            <span><?php echo esc_html( get_theme_mod('kopizon_testi_eyebrow', 'Testimonials') ); ?></span>
            <h2><?php echo esc_html( get_theme_mod('kopizon_testi_heading', "What they're talking about?") ); ?></h2>
        </div>
        <div class="swiper-container testimonial-slider">
            <div class="swiper-wrapper">
                <?php 
                $testimonials = new WP_Query(array(
                    'category_name' => 'testimonials',
                    'posts_per_page' => 6
                ));
                if ($testimonials->have_posts()) : 
                    while ($testimonials->have_posts()) : $testimonials->the_post();
                    $role = get_post_meta(get_the_ID(), 'testimonial_role', true);
                ?>
                    <div class="swiper-slide">
                        <div class="testimonial-card">
                            <p class="font-italic">"<?php the_excerpt(); ?>"</p>
                            <div class="testimonial-user">
                                <img src="<?php echo has_post_thumbnail() ? get_the_post_thumbnail_url() : get_template_directory_uri() . '/assets/images/user-1.jpg'; ?>">
                                <div class="user-info">
                                    <h4><?php the_title(); ?></h4>
                                    <p><?php echo esc_html($role); ?></p>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endwhile; wp_reset_postdata(); else : ?>
                    <p class="text-center text-muted">Add testimonials in the dashboard.</p>
                <?php endif; ?>
            </div>
            <div class="swiper-pagination"></div>
        </div>
    </div>
</section>

<section class="news-section bg-light-grey">
    <div class="container">
        <div class="section-heading">
            <span><?php echo esc_html( get_theme_mod('kopizon_news_eyebrow', 'Blog & News') ); ?></span>
            <h2><?php echo esc_html( get_theme_mod('kopizon_news_heading', 'Latest news & articles') ); ?></h2>
        </div>
        <div class="swiper-container news-slider">
            <div class="swiper-wrapper">
                <?php 
                $news = new WP_Query(array(
                    'posts_per_page' => 6,
                    'category_name'  => 'news,medical,urgent,transplant' // Or just ignore cat to get latest
                ));
                if ($news->have_posts()) :
                    while ($news->have_posts()) : $news->the_post();
                    $categories = get_the_category();
                    $cat_display = !empty($categories) ? $categories[0]->name : 'UPDATE';
                ?>
                    <div class="swiper-slide">
                        <div class="news-card">
                            <div class="news-img" style="background-image: url('<?php echo has_post_thumbnail() ? get_the_post_thumbnail_url() : get_template_directory_uri() . '/assets/images/news-1.jpg'; ?>');"></div>
                            <div class="p-4">
                                <span style="color: var(--kopizon-pink);"><?php echo esc_html($cat_display); ?></span>
                                <h4 class="mt-2 font-weight-bold"><?php the_title(); ?></h4>
                                <div class="text-muted" style="font-size: 14px; line-height: 1.5;">
                                    <?php echo wp_trim_words(get_the_excerpt(), 20); ?>
                                </div>
                                <a href="<?php the_permalink(); ?>" class="text-pink font-weight-bold d-block mt-3" style="color: var(--kopizon-pink);">Read More</a>
                            </div>
                        </div>
                    </div>
                <?php endwhile; wp_reset_postdata(); else : ?>
                    <p class="text-center text-muted">No news articles found.</p>
                <?php endif; ?>
            </div>
            <div class="swiper-pagination"></div>
        </div>
    </div>
</section>

<div class="footer-cta-blue">
    <div class="container">
        <h2><?php echo esc_html( get_theme_mod('kopizon_fcta_heading', 'Ready to make a donation for kids?') ); ?></h2>
        <p><?php echo esc_html( get_theme_mod('kopizon_fcta_desc', 'Every small donation counts. Help us reach our goal today.') ); ?></p>
    </div>
</div>

<?php
get_footer();
