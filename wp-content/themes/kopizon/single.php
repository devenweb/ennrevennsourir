<?php
/**
 * The template for displaying all single posts (Patients/Campaigns)
 */

get_header();

while ( have_posts() ) :
    the_post();
    ?>

    <!-- Breadcrumb -->
    <section class="py-4 bg-light">
        <div class="container">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb bg-transparent p-0 m-0">
                    <li class="breadcrumb-item"><a href="<?php echo esc_url( home_url( '/' ) ); ?>">Home</a></li>
                    <li class="breadcrumb-item active" aria-current="page"><?php the_title(); ?></li>
                </ol>
            </nav>
        </div>
    </section>

    <!-- Top Campaign Header Section -->
    <section class="py-5">
        <div class="container">
            <div class="row">
                <!-- Left: Main Image -->
                <div class="col-lg-6 mb-4 mb-lg-0">
                    <div class="position-relative rounded-lg overflow-hidden shadow-sm">
                        <?php if ( has_post_thumbnail() ) : ?>
                            <?php the_post_thumbnail( 'large', array( 'class' => 'img-fluid w-100' ) ); ?>
                        <?php else : ?>
                            <img src="<?php echo get_template_directory_uri(); ?>/assets/images/cancer-care.jpg" class="img-fluid w-100" alt="<?php the_title(); ?>">
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Right: Stats & Quick Donate -->
                <div class="col-lg-6 pl-lg-5">
                    <div class="campaign-header-content">
                        <?php 
                        $categories = get_the_category();
                        $cat_name = !empty($categories) ? $categories[0]->name : 'Medical';
                        $is_campaign = (get_post_type() === 'product' || in_category('campaigns'));
                        
                        $goal = get_post_meta(get_the_ID(), '_goal', true);
                        $raised = get_post_meta(get_the_ID(), '_total_raised', true);
                        $percent = ($goal && $goal > 0) ? min(100, round(($raised / $goal) * 100)) : 0;
                        ?>
                        <span class="badge badge-primary px-3 py-2 mb-3" style="background: var(--kopizon-pink); border: none;"><?php echo esc_html($cat_name); ?></span>
                        <h1 class="font-weight-bold mb-4" style="font-size: 36px;"><?php the_title(); ?></h1>
                        
                        <?php if ($is_campaign) : ?>
                        <div class="campaign-stats-grid">
                            <div class="stat-box">
                                <h4>Rs <?php echo number_format((float)$raised, 0, '.', ','); ?></h4>
                                <p>Raised</p>
                            </div>
                            <div class="stat-box">
                                <h4><?php echo number_format((float)$goal, 0, '.', ','); ?></h4>
                                <p>Goal (Rs)</p>
                            </div>
                            <div class="stat-box">
                                <h4><?php echo $percent; ?>%</h4>
                                <p>Progress</p>
                            </div>
                        </div>

                        <div class="progress-container-fancy">
                            <div class="progress-info">
                                <span>Impact Progress: <?php echo $percent; ?>%</span>
                            </div>
                            <div class="progress-bar-wrap">
                                <div class="progress-bar-fill" style="width: <?php echo $percent; ?>%;"></div>
                            </div>
                        </div>

                        <div class="donation-quick-select mt-4">
                            <button class="quick-amt-btn">Rs 500</button>
                            <button class="quick-amt-btn active">Rs 1500</button>
                            <button class="quick-amt-btn">Rs 5000</button>
                        </div>

                        <div class="donation-input-group">
                            <div class="amt-input-wrap">
                                <span>Rs</span>
                                <input type="number" value="1500">
                            </div>
                            <button class="btn-back-campaign" style="background: var(--kopizon-pink);">Support Now</button>
                        </div>
                        <?php else : ?>
                            <div class="news-meta-simple mb-4">
                                <span class="text-muted mr-3"><i class="far fa-calendar-alt mr-1"></i> <?php echo get_the_date(); ?></span>
                                <span class="text-muted"><i class="far fa-user mr-1"></i> By <?php the_author(); ?></span>
                            </div>
                            <div class="news-excerpt lead text-muted italic">
                                <?php echo get_the_excerpt(); ?>
                            </div>
                        <?php endif; ?>

                        <div class="campaign-author mt-4">
                            <img src="<?php echo get_template_directory_uri(); ?>/assets/images/team-1.jpg" alt="Author">
                            <div class="author-info">
                                <h5>By Enn Rev Enn Sourir</h5>
                                <p>Verified Charity | Impact Driven</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Main Content Tabs -->
    <section class="py-5 border-top">
        <div class="container">
            <div class="row">
                <!-- Left: Content area -->
                <div class="col-lg-8 pr-lg-5">
                    <div class="detail-tabs-nav">
                        <span class="tab-link active">Story</span>
                        <span class="tab-link">Updates</span>
                        <span class="tab-link">Backer List</span>
                        <span class="tab-link">Reviews (0)</span>
                    </div>

                    <div class="tab-content-area">
                        <div class="story-content">
                            <h3 class="font-weight-bold mb-4">Story</h3>
                            <div class="story-text">
                                <?php the_content(); ?>
                            </div>

                            <div class="row mt-5 mb-5">
                                <div class="col-md-6 mb-3">
                                    <img src="<?php echo get_template_directory_uri(); ?>/assets/images/heart-surgery.jpg" class="img-fluid rounded shadow-sm" alt="Care">
                                </div>
                                <div class="col-md-6 mb-3">
                                    <img src="<?php echo get_template_directory_uri(); ?>/assets/images/rare-diseases.jpg" class="img-fluid rounded shadow-sm" alt="Support">
                                </div>
                            </div>

                            <h3 class="font-weight-bold mb-3">Why Donate Our Products</h3>
                            <ul class="list-unstyled">
                                <li class="mb-2"><i class="fas fa-check-circle text-success mr-2"></i> 100% of funds go to the medical treatment.</li>
                                <li class="mb-2"><i class="fas fa-check-circle text-success mr-2"></i> Verified medical cases by our foundation.</li>
                                <li class="mb-2"><i class="fas fa-check-circle text-success mr-2"></i> Regular updates and transparent reporting.</li>
                            </ul>
                        </div>
                    </div>
                </div>

                <!-- Right: Rewards Sidebar -->
                <div class="col-lg-4">
                    <?php if ($is_campaign) : ?>
                        <h3 class="rewards-title">Support Options</h3>
                        
                        <div class="reward-card">
                            <div class="reward-price">One-time Gift</div>
                            <img src="<?php echo get_template_directory_uri(); ?>/assets/images/cancer-care.jpg" class="reward-img" alt="Reward">
                            <h4>Essential Medical Fund</h4>
                            <p>Contribute to the general medical fund that supports urgent cases and surgical supplies for children in need.</p>
                            <button class="btn-select-reward" style="background: var(--kopizon-navy);">Select Amount</button>
                        </div>
                    <?php else : ?>
                        <div class="sidebar-widget mb-5">
                            <h3 class="rewards-title">Recent News</h3>
                            <ul class="list-unstyled">
                                <?php 
                                $recent = new WP_Query(array('posts_per_page' => 3, 'post__not_in' => array(get_the_ID())));
                                while($recent->have_posts()) : $recent->the_post(); ?>
                                    <li class="mb-3">
                                        <a href="<?php the_permalink(); ?>" class="text-dark font-weight-bold d-block"><?php the_title(); ?></a>
                                        <small class="text-muted"><?php echo get_the_date(); ?></small>
                                    </li>
                                <?php endwhile; wp_reset_postdata(); ?>
                            </ul>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </section>

    <?php
endwhile;

get_footer();
