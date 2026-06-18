<?php
/**
 * Kopizon Theme Customizer
 */

function kopizon_customize_register( $wp_customize ) {
    // Panel
    $wp_customize->add_panel( 'kopizon_theme_settings', array(
        'title'    => __( 'Kopizon Theme Settings', 'kopizon' ),
        'priority' => 30,
    ) );

    // --- Section: Contact Info ---
    $wp_customize->add_section( 'kopizon_contact_section', array(
        'title' => __( 'Header & Contact', 'kopizon' ),
        'panel' => 'kopizon_theme_settings',
    ) );

    $wp_customize->add_setting( 'kopizon_phone', array( 'default' => '+230 460 2500' ) );
    $wp_customize->add_control( 'kopizon_phone', array( 'label' => 'Phone Number', 'section' => 'kopizon_contact_section', 'type' => 'text' ) );

    $wp_customize->add_setting( 'kopizon_email', array( 'default' => 'info@ennrevennsourir.org' ) );
    $wp_customize->add_control( 'kopizon_email', array( 'label' => 'Email', 'section' => 'kopizon_contact_section', 'type' => 'text' ) );

    $wp_customize->add_setting( 'kopizon_facebook', array( 'default' => '#' ) );
    $wp_customize->add_control( 'kopizon_facebook', array( 'label' => 'Facebook Link', 'section' => 'kopizon_contact_section', 'type' => 'text' ) );

    $wp_customize->add_setting( 'kopizon_instagram', array( 'default' => '#' ) );
    $wp_customize->add_control( 'kopizon_instagram', array( 'label' => 'Instagram Link', 'section' => 'kopizon_contact_section', 'type' => 'text' ) );

    $wp_customize->add_setting( 'kopizon_linkedin', array( 'default' => '#' ) );
    $wp_customize->add_control( 'kopizon_linkedin', array( 'label' => 'LinkedIn Link', 'section' => 'kopizon_contact_section', 'type' => 'text' ) );

    $wp_customize->add_setting( 'kopizon_youtube', array( 'default' => '#' ) );
    $wp_customize->add_control( 'kopizon_youtube', array( 'label' => 'YouTube Link', 'section' => 'kopizon_contact_section', 'type' => 'text' ) );

    // --- Section: Hero Slider ---
    $wp_customize->add_section( 'kopizon_hero_section', array(
        'title' => __( 'Hero Slider', 'kopizon' ),
        'panel' => 'kopizon_theme_settings',
    ) );

    for ($i = 1; $i <= 3; $i++) {
        $wp_customize->add_setting( "kopizon_hero_img_$i" );
        $wp_customize->add_control( new WP_Customize_Image_Control( $wp_customize, "kopizon_hero_img_$i", array(
            'label'    => "Slide $i Image",
            'section'  => 'kopizon_hero_section',
        ) ) );

        $wp_customize->add_setting( "kopizon_hero_title_$i", array( 'default' => '' ) );
        $wp_customize->add_control( "kopizon_hero_title_$i", array(
            'label'   => "Slide $i Title",
            'section' => 'kopizon_hero_section',
            'type'    => 'text',
        ) );
    }

    // --- Section: Impact Strip (Top Stats) ---
    $wp_customize->add_section( 'kopizon_impact_section', array(
        'title' => __( 'Impact Strip (5 Stats)', 'kopizon' ),
        'panel' => 'kopizon_theme_settings',
    ) );

    for ($i = 1; $i <= 5; $i++) {
        $wp_customize->add_setting( "kopizon_impact_num_$i", array( 'default' => '' ) );
        $wp_customize->add_control( "kopizon_impact_num_$i", array( 'label' => "Stat $i Number", 'section' => 'kopizon_impact_section' ) );

        $wp_customize->add_setting( "kopizon_impact_label_$i", array( 'default' => '' ) );
        $wp_customize->add_control( "kopizon_impact_label_$i", array( 'label' => "Stat $i Label", 'section' => 'kopizon_impact_section' ) );
        
        $wp_customize->add_setting( "kopizon_impact_icon_$i", array( 'default' => 'fas fa-star' ) );
        $wp_customize->add_control( "kopizon_impact_icon_$i", array( 'label' => "Stat $i Icon Class", 'section' => 'kopizon_impact_section' ) );
    }

    // --- Section: Main Counters ---
    $wp_customize->add_section( 'kopizon_counter_section', array(
        'title' => __( 'Main Counters (4 Stats)', 'kopizon' ),
        'panel' => 'kopizon_theme_settings',
    ) );

    for ($i = 1; $i <= 4; $i++) {
        $wp_customize->add_setting( "kopizon_counter_num_$i", array( 'default' => '' ) );
        $wp_customize->add_control( "kopizon_counter_num_$i", array( 'label' => "Counter $i Number", 'section' => 'kopizon_counter_section' ) );

        $wp_customize->add_setting( "kopizon_counter_label_$i", array( 'default' => '' ) );
        $wp_customize->add_control( "kopizon_counter_label_$i", array( 'label' => "Counter $i Label", 'section' => 'kopizon_counter_section' ) );

        $wp_customize->add_setting( "kopizon_counter_icon_$i", array( 'default' => 'fas fa-star' ) );
        $wp_customize->add_control( "kopizon_counter_icon_$i", array( 'label' => "Counter $i Icon Class", 'section' => 'kopizon_counter_section' ) );
    }

    // --- Section: About Section ---
    $wp_customize->add_section( 'kopizon_about_section', array(
        'title' => __( 'About Section', 'kopizon' ),
        'panel' => 'kopizon_theme_settings',
    ) );

    $wp_customize->add_setting( 'kopizon_about_title', array( 'default' => 'From the first idea to the final outcome' ) );
    $wp_customize->add_control( 'kopizon_about_title', array( 'label' => 'Title', 'section' => 'kopizon_about_section', 'type' => 'text' ) );

    $wp_customize->add_setting( 'kopizon_about_desc', array( 'default' => '' ) );
    $wp_customize->add_control( 'kopizon_about_desc', array( 'label' => 'Description', 'section' => 'kopizon_about_section', 'type' => 'textarea' ) );

    $wp_customize->add_setting( 'kopizon_about_years', array( 'default' => '10' ) );
    $wp_customize->add_control( 'kopizon_about_years', array( 'label' => 'Years of Experience', 'section' => 'kopizon_about_section', 'type' => 'text' ) );

    $wp_customize->add_setting( 'kopizon_about_img' );
    $wp_customize->add_control( new WP_Customize_Image_Control( $wp_customize, 'kopizon_about_img', array(
        'label'    => 'About Image',
        'section'  => 'kopizon_about_section',
    ) ) );

    // --- Section: Split Panel Cards ---
    $wp_customize->add_section( 'kopizon_split_section', array(
        'title' => __( 'Split Panel Cards', 'kopizon' ),
        'panel' => 'kopizon_theme_settings',
    ) );

    for ($i = 1; $i <= 3; $i++) {
        // Icon
        $wp_customize->add_setting( "kopizon_spc_icon_$i", array( 'default' => 'fas fa-star' ) );
        $wp_customize->add_control( "kopizon_spc_icon_$i", array( 'label' => "Card $i Icon Class", 'section' => 'kopizon_split_section', 'type' => 'text' ) );
        // Title
        $wp_customize->add_setting( "kopizon_spc_title_$i" );
        $wp_customize->add_control( "kopizon_spc_title_$i", array( 'label' => "Card $i Title", 'section' => 'kopizon_split_section', 'type' => 'text' ) );
        // Description
        $wp_customize->add_setting( "kopizon_spc_desc_$i" );
        $wp_customize->add_control( "kopizon_spc_desc_$i", array( 'label' => "Card $i Description", 'section' => 'kopizon_split_section', 'type' => 'textarea' ) );
    }

    // --- Section: Why Choose Us ---
    $wp_customize->add_section( 'kopizon_why_section', array(
        'title' => __( 'Why Choose Us', 'kopizon' ),
        'panel' => 'kopizon_theme_settings',
    ) );

    $wp_customize->add_setting( 'kopizon_why_eyebrow', array( 'default' => 'Why Choose Us' ) );
    $wp_customize->add_control( 'kopizon_why_eyebrow', array( 'label' => 'Eyebrow', 'section' => 'kopizon_why_section', 'type' => 'text' ) );
    $wp_customize->add_setting( 'kopizon_why_heading', array( 'default' => 'Why choose Enn Rev Enn Sourir' ) );
    $wp_customize->add_control( 'kopizon_why_heading', array( 'label' => 'Heading', 'section' => 'kopizon_why_section', 'type' => 'text' ) );

    for ($i = 1; $i <= 2; $i++) {
        $wp_customize->add_setting( "kopizon_why_feat_title_$i" );
        $wp_customize->add_control( "kopizon_why_feat_title_$i", array( 'label' => "Feature $i Title", 'section' => 'kopizon_why_section', 'type' => 'text' ) );
        $wp_customize->add_setting( "kopizon_why_feat_desc_$i" );
        $wp_customize->add_control( "kopizon_why_feat_desc_$i", array( 'label' => "Feature $i Desc", 'section' => 'kopizon_why_section', 'type' => 'textarea' ) );
    }

    $wp_customize->add_setting( 'kopizon_why_img' );
    $wp_customize->add_control( new WP_Customize_Image_Control( $wp_customize, 'kopizon_why_img', array(
        'label'    => 'Why Image',
        'section'  => 'kopizon_why_section',
    ) ) );

    // --- Section: Homepage Gallery ---
    $wp_customize->add_section( 'kopizon_gallery_section', array(
        'title' => __( 'Homepage Gallery', 'kopizon' ),
        'panel' => 'kopizon_theme_settings',
    ) );

    for ($i = 1; $i <= 5; $i++) {
        $wp_customize->add_setting( "kopizon_gallery_img_$i" );
        $wp_customize->add_control( new WP_Customize_Image_Control( $wp_customize, "kopizon_gallery_img_$i", array(
            'label'    => "Gallery Image $i",
            'section'  => 'kopizon_gallery_section',
        ) ) );
    }

    // --- Section: Patients Needs ---
    $wp_customize->add_section( 'kopizon_needs_section', array(
        'title' => __( 'Patients Needs', 'kopizon' ),
        'panel' => 'kopizon_theme_settings',
    ) );

    $wp_customize->add_setting( 'kopizon_needs_eyebrow', array( 'default' => 'How to help' ) );
    $wp_customize->add_control( 'kopizon_needs_eyebrow', array( 'label' => 'Eyebrow', 'section' => 'kopizon_needs_section', 'type' => 'text' ) );
    $wp_customize->add_setting( 'kopizon_needs_heading', array( 'default' => 'Get our sick patients needs' ) );
    $wp_customize->add_control( 'kopizon_needs_heading', array( 'label' => 'Heading', 'section' => 'kopizon_needs_section', 'type' => 'text' ) );

    for ($i = 1; $i <= 4; $i++) {
        $wp_customize->add_setting( "kopizon_needs_item_$i" );
        $wp_customize->add_control( "kopizon_needs_item_$i", array( 'label' => "Need $i", 'section' => 'kopizon_needs_section', 'type' => 'text' ) );
    }

    $wp_customize->add_setting( 'kopizon_needs_form_title', array( 'default' => 'Make a Donation' ) );
    $wp_customize->add_control( 'kopizon_needs_form_title', array( 'label' => 'Form Title', 'section' => 'kopizon_needs_section', 'type' => 'text' ) );

    for ($i = 1; $i <= 2; $i++) {
        $wp_customize->add_setting( "kopizon_needs_stat_num_$i" );
        $wp_customize->add_control( "kopizon_needs_stat_num_$i", array( 'label' => "Stat $i Number", 'section' => 'kopizon_needs_section', 'type' => 'text' ) );
        $wp_customize->add_setting( "kopizon_needs_stat_label_$i" );
        $wp_customize->add_control( "kopizon_needs_stat_label_$i", array( 'label' => "Stat $i Label", 'section' => 'kopizon_needs_section', 'type' => 'text' ) );
    }

    // --- Section: Global Headings ---
    $wp_customize->add_section( 'kopizon_headings_section', array(
        'title' => __( 'Section Headings', 'kopizon' ),
        'panel' => 'kopizon_theme_settings',
    ) );

    $wp_customize->add_setting( 'kopizon_testi_eyebrow', array( 'default' => 'Testimonials' ) );
    $wp_customize->add_control( 'kopizon_testi_eyebrow', array( 'label' => 'Testimonials Eyebrow', 'section' => 'kopizon_headings_section', 'type' => 'text' ) );
    $wp_customize->add_setting( 'kopizon_testi_heading', array( 'default' => "What they're talking about?" ) );
    $wp_customize->add_control( 'kopizon_testi_heading', array( 'label' => 'Testimonials Heading', 'section' => 'kopizon_headings_section', 'type' => 'text' ) );

    $wp_customize->add_setting( 'kopizon_news_eyebrow', array( 'default' => 'Blog & News' ) );
    $wp_customize->add_control( 'kopizon_news_eyebrow', array( 'label' => 'News Eyebrow', 'section' => 'kopizon_headings_section', 'type' => 'text' ) );
    $wp_customize->add_setting( 'kopizon_news_heading', array( 'default' => 'Latest news & articles' ) );
    $wp_customize->add_control( 'kopizon_news_heading', array( 'label' => 'News Heading', 'section' => 'kopizon_headings_section', 'type' => 'text' ) );

    // --- Section: Footer CTA ---
    $wp_customize->add_section( 'kopizon_fcta_section', array(
        'title' => __( 'Footer CTA', 'kopizon' ),
        'panel' => 'kopizon_theme_settings',
    ) );

    $wp_customize->add_setting( 'kopizon_fcta_heading', array( 'default' => 'Ready to make a donation for kids?' ) );
    $wp_customize->add_control( 'kopizon_fcta_heading', array( 'label' => 'Heading', 'section' => 'kopizon_fcta_section', 'type' => 'text' ) );
    $wp_customize->add_setting( 'kopizon_fcta_desc', array( 'default' => 'Every small donation counts. Help us reach our goal today.' ) );
    $wp_customize->add_control( 'kopizon_fcta_desc', array( 'label' => 'Description', 'section' => 'kopizon_fcta_section', 'type' => 'textarea' ) );

    // --- Additional Footer Settings ---
    $wp_customize->add_setting( 'kopizon_whatsapp', array( 'default' => 'https://wa.me/23059099219' ) );
    $wp_customize->add_control( 'kopizon_whatsapp', array( 'label' => 'WhatsApp Link', 'section' => 'kopizon_contact_section', 'type' => 'text' ) );
    $wp_customize->add_setting( 'kopizon_address', array( 'default' => 'Port-Louis, Mauritius' ) );
    $wp_customize->add_control( 'kopizon_address', array( 'label' => 'Address', 'section' => 'kopizon_contact_section', 'type' => 'text' ) );
    $wp_customize->add_setting( 'kopizon_newsletter_desc', array( 'default' => 'Enter your email address to receive the latest updates on our projects and the children you have helped.' ) );
    $wp_customize->add_control( 'kopizon_newsletter_desc', array( 'label' => 'Newsletter Description', 'section' => 'kopizon_fcta_section', 'type' => 'textarea' ) );
    // --- Section: Support Section ---
    $wp_customize->add_section( 'kopizon_support_section', array(
        'title' => __( 'Support Section', 'kopizon' ),
        'panel' => 'kopizon_theme_settings',
    ) );
    $wp_customize->add_setting( 'kopizon_support_eyebrow', array( 'default' => 'Raised to Help Kids' ) );
    $wp_customize->add_control( 'kopizon_support_eyebrow', array( 'label' => 'Eyebrow', 'section' => 'kopizon_support_section', 'type' => 'text' ) );
    $wp_customize->add_setting( 'kopizon_support_heading', array( 'default' => 'Support a child' ) );
    $wp_customize->add_control( 'kopizon_support_heading', array( 'label' => 'Heading', 'section' => 'kopizon_support_section', 'type' => 'text' ) );

    // --- Section: New Patients ---
    $wp_customize->add_section( 'kopizon_new_patients_section', array(
        'title' => __( 'New Patients Section', 'kopizon' ),
        'panel' => 'kopizon_theme_settings',
    ) );
    $wp_customize->add_setting( 'kopizon_new_patients_eyebrow', array( 'default' => 'New Campaigns' ) );
    $wp_customize->add_control( 'kopizon_new_patients_eyebrow', array( 'label' => 'Eyebrow', 'section' => 'kopizon_new_patients_section', 'type' => 'text' ) );
    $wp_customize->add_setting( 'kopizon_new_patients_heading', array( 'default' => 'New Patients Support' ) );
    $wp_customize->add_control( 'kopizon_new_patients_heading', array( 'label' => 'Heading', 'section' => 'kopizon_new_patients_section', 'type' => 'text' ) );

    // Additional About Benefit
    $wp_customize->add_setting( 'kopizon_about_eyebrow', array( 'default' => 'About Our Charity' ) );
    $wp_customize->add_control( 'kopizon_about_eyebrow', array( 'label' => 'Eyebrow', 'section' => 'kopizon_about_section', 'type' => 'text' ) );
    $wp_customize->add_setting( 'kopizon_about_benefit_title', array( 'default' => 'Join our journey' ) );
    $wp_customize->add_control( 'kopizon_about_benefit_title', array( 'label' => 'Benefit Title', 'section' => 'kopizon_about_section', 'type' => 'text' ) );
    $wp_customize->add_setting( 'kopizon_about_benefit_desc', array( 'default' => 'Making a difference in the world starts with one step.' ) );
    $wp_customize->add_control( 'kopizon_about_benefit_desc', array( 'label' => 'Benefit Description', 'section' => 'kopizon_about_section', 'type' => 'textarea' ) );

    // Additional Split Panel
    $wp_customize->add_setting( 'kopizon_split_eyebrow', array( 'default' => "We're Focused on Results" ) );
    $wp_customize->add_control( 'kopizon_split_eyebrow', array( 'label' => 'Split Eyebrow', 'section' => 'kopizon_split_section', 'type' => 'text' ) );
    $wp_customize->add_setting( 'kopizon_split_heading', array( 'default' => "The new way of managing that's making waves — for our Child Cancer Scheme." ) );
    $wp_customize->add_control( 'kopizon_split_heading', array( 'label' => 'Split Heading', 'section' => 'kopizon_split_section', 'type' => 'textarea' ) );
}
add_action( 'customize_register', 'kopizon_customize_register' );
