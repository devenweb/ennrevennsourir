<?php
/**
 * Template Name: Audit Reports
 */
get_header();
?>

<main class="py-5 bg-white">
    <div class="container py-5 text-center">
        <h1 class="font-weight-bold mb-4" style="color: var(--kopizon-navy);">Audit Reports</h1>
        <p class="text-muted mb-5">Verified audits by independent firms to ensure the highest standards of governance.</p>

        <div class="row justify-content-center">
            <div class="col-lg-10">
                <div class="card border-0 shadow-sm p-4" style="border-radius: 20px;">
                    <?php 
                    $audits = get_post_meta( get_the_ID(), 'kopizon_audits', true );
                    if ( $audits && is_array($audits) ) :
                        $count = count($audits);
                        $i = 0;
                        foreach ( $audits as $audit ) : 
                            $i++;
                            $border_class = ($i < $count) ? 'border-bottom' : '';
                        ?>
                        <div class="d-flex justify-content-between align-items-center py-3 <?php echo $border_class; ?>">
                            <div class="d-flex align-items-center">
                                <i class="fas fa-file-contract fa-2x mr-4" style="color: var(--kopizon-navy);"></i>
                                <div class="text-left">
                                    <h5 class="font-weight-bold mb-0"><?php echo esc_html($audit['title']); ?></h5>
                                    <p class="small text-muted mb-0">Compiled by <?php echo esc_html($audit['compiled']); ?></p>
                                </div>
                            </div>
                            <a href="<?php echo esc_url($audit['file']); ?>" class="btn btn-primary btn-sm rounded-pill px-4" style="background: var(--kopizon-pink); border: none;">VIEW REPORT</a>
                        </div>
                    <?php endforeach; else : ?>
                        <p class="text-muted text-center py-4">No audit reports found. Please add them in the page editor.</p>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</main>

<?php get_footer(); ?>
