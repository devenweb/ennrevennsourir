<?php
/**
 * Template Name: Become a Sponsor
 */
get_header();
?>

<main class="py-5" style="background: #fcfcfc;">
    <div class="container py-5">
        <h1 class="font-weight-bold text-center mb-5" style="color: var(--kopizon-navy);">Corporate &amp; Individual Sponsorship</h1>
        <div class="row align-items-center">
            <div class="col-lg-6 pr-lg-5">
                <p class="lead">Partnering with Enn Rev Enn Sourir is a powerful way for your brand or family to make a lasting impact on childhood cancer care in Mauritius.</p>
                <ul class="list-unstyled mt-4">
                    <li class="mb-3"><i class="fas fa-check-circle mr-2" style="color: var(--kopizon-pink);"></i> Brand visibility at events and campaigns</li>
                    <li class="mb-3"><i class="fas fa-check-circle mr-2" style="color: var(--kopizon-pink);"></i> Tax-deductible contributions</li>
                    <li class="mb-3"><i class="fas fa-check-circle mr-2" style="color: var(--kopizon-pink);"></i> Impact reports and recognition</li>
                    <li class="mb-3"><i class="fas fa-check-circle mr-2" style="color: var(--kopizon-pink);"></i> Certificate of sponsorship</li>
                </ul>
            </div>
            <div class="col-lg-6 mt-4 mt-lg-0">
                <div class="card p-5 border-0 shadow-sm" style="border-radius: 20px;">
                    <h4 class="font-weight-bold mb-4" style="color: var(--kopizon-pink);">Sponsorship Interest</h4>
                    <form method="post" action="#">
                        <?php wp_nonce_field('become_sponsor_form', 'sponsor_nonce'); ?>
                        <input type="text" class="form-control mb-3" name="org_name" placeholder="Company/Individual Name" required>
                        <input type="email" class="form-control mb-3" name="email" placeholder="Contact Email" required>
                        <input type="tel" class="form-control mb-3" name="phone" placeholder="Phone Number">
                        <select class="form-control mb-3" name="package">
                            <option value="">Select Sponsorship Package</option>
                            <option>Gold — Rs 50,000+</option>
                            <option>Silver — Rs 25,000+</option>
                            <option>Bronze — Rs 10,000+</option>
                            <option>Custom Amount</option>
                        </select>
                        <button type="submit" class="btn btn-primary btn-block py-3" style="background: var(--kopizon-navy); border: none; border-radius: 10px; font-weight: 700; letter-spacing: 1px;">REQUEST SPONSORSHIP KIT</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</main>

<?php get_footer(); ?>
