<?php
/**
 * The template for displaying 404 pages (not found)
 */

get_header();
?>

<main id="primary" class="site-main py-5 mt-5">
    <div class="container text-center py-5">
        <h1 class="display-1 font-weight-bold" style="color: var(--kopizon-pink);">404</h1>
        <h2 class="mb-4">Page Not Found</h2>
        <p class="lead text-muted mb-5">Oops! The page you are looking for does not exist or has been moved.</p>
        <a href="<?php echo esc_url( home_url( '/' ) ); ?>" class="btn btn-primary rounded-pill px-5 py-3" style="background: var(--kopizon-blue); border: none;">Back to Home</a>
    </div>
</main>

<?php
get_footer();
