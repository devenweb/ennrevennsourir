<?php
/**
 * Template Name: Become a Member
 */
get_header();
?>

<main class="py-5">
    <div class="container py-5 text-center">
        <h1 class="font-weight-bold mb-4"><?php the_title(); ?></h1>
        <div class="text-muted mb-5">
            <?php the_content(); ?>
        </div>
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <div class="card border-0 shadow-lg p-5" style="border-radius: 20px;">
                    <h4 class="font-weight-bold mb-4">Membership Application</h4>
                    <form method="post" action="#">
                        <?php wp_nonce_field('become_member_form', 'member_nonce'); ?>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <input type="text" class="form-control" name="full_name" placeholder="Full Name" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <input type="email" class="form-control" name="email" placeholder="Email" required>
                            </div>
                        </div>
                        <div class="mb-3">
                            <input type="tel" class="form-control" name="phone" placeholder="Phone Number">
                        </div>
                        <div class="mb-3">
                            <textarea class="form-control" name="motivation" rows="3" placeholder="Why do you want to become a member?"></textarea>
                        </div>
                        <button type="submit" class="btn btn-primary btn-block py-3 mt-4" style="background: var(--kopizon-pink); border: none; border-radius: 10px; font-weight: 700; letter-spacing: 1px;">SUBMIT APPLICATION</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</main>

<?php get_footer(); ?>
