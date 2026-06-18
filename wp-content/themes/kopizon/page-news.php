<?php
/**
 * Template for displaying the News page
 * Auto-applied to any page with slug "news"
 * Customisation: banner uses who_we_are.jpg
 */

get_header();

while ( have_posts() ) :
    the_post();
    ?>

    <!-- Page Header Banner -->
    <section class="page-header-banner" style="background-image: url('<?php echo esc_url( content_url( '/uploads/2024/11/who_we_are.jpg' ) ); ?>');">
        <div class="container">
            <div class="page-header-content">
                <h1 style="padding-top: 100px;"><?php the_title(); ?></h1>
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="<?php echo esc_url( home_url( '/' ) ); ?>">Home</a></li>
                        <li class="breadcrumb-item active" aria-current="page"><?php the_title(); ?></li>
                    </ol>
                </nav>
            </div>
        </div>
    </section>

    <div class="container py-5">
        <div class="row">
            <div class="col-lg-12">
                <article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
                    <div class="entry-content">
                        <?php
                        the_content();

                        wp_link_pages( array(
                            'before' => '<div class="page-links">' . esc_html__( 'Pages:', 'kopizon' ),
                            'after'  => '</div>',
                        ) );
                        ?>
                    </div>
                </article>
            </div>
        </div>
    </div>

    <?php
endwhile;

get_footer();
