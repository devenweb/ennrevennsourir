<?php
/**
 * Template Name: Financial Reports
 */
get_header();
?>

<main class="py-5 bg-white">
    <div class="container py-5">
        <div class="row">
            <div class="col-lg-8">
                <h1 class="font-weight-bold mb-4" style="color: var(--kopizon-navy);">Financial Reports</h1>
                <p class="text-muted mb-5">Access our detailed financial statements to see how we manage and allocate the generous contributions from our donors.</p>

                <div class="table-responsive border p-4" style="border-radius: 20px;">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th class="border-0">Report Period</th>
                                <th class="border-0">Type</th>
                                <th class="border-0 text-right">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php 
                            $reports = get_post_meta( get_the_ID(), 'kopizon_reports', true );
                            if ( $reports && is_array($reports) ) :
                                foreach ( $reports as $report ) : ?>
                                <tr>
                                    <td class="align-middle font-weight-bold"><?php echo esc_html($report['title']); ?></td>
                                    <td class="align-middle text-muted">Financial Statement</td>
                                    <td class="text-right"><a href="<?php echo esc_url($report['file']); ?>" class="btn btn-link font-weight-bold" style="color: var(--kopizon-pink);"><i class="fas fa-download mr-2"></i>PDF</a></td>
                                </tr>
                            <?php endforeach; else : ?>
                                <tr><td colspan="3" class="text-center text-muted py-4">No reports found.</td></tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="col-lg-4 mt-4 mt-lg-0">
                <div class="p-5" style="background: var(--kopizon-navy); color: white; border-radius: 25px;">
                    <h4 class="font-weight-bold mb-4">Our Commitment</h4>
                    <p class="small mb-4" style="color: rgba(255,255,255,0.6);">"Trust is the bridge between our mission and your generosity. We ensure every Mauritian Rupee is accounted for and spent effectively on child wellness."</p>
                    <hr style="border-color: rgba(255,255,255,0.1);">
                    <p class="font-weight-bold small mb-0">Finance Committee</p>
                    <p class="small" style="color: rgba(255,255,255,0.6);">Enn Rev Enn Sourir</p>
                </div>
            </div>
        </div>
    </div>
</main>

<?php get_footer(); ?>
