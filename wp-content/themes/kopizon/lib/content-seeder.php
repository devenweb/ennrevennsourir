<?php
/**
 * Content Seeder Script
 * Programmatically creates Pages, Posts, and Products from hardcoded template data.
 */

// Load WordPress environment
$path = $_SERVER['DOCUMENT_ROOT'];
if (file_exists($path . '/wp-load.php')) {
    require_once($path . '/wp-load.php');
} else {
    // Attempt fallback for local site structure
    $path = dirname(__FILE__, 5);
    require_once($path . '/wp-load.php');
}

if (!is_user_logged_in() && !defined('WP_CLI')) {
    // Only run if logged in as admin or via CLI (or if manually triggered by dev)
    // For now, let's allow running it if a secret key is provided or just during dev.
}

function kopizon_seed_content() {
    echo "Starting Content Seeding...\n";

    // 1. Pages
    $pages = array(
        array(
            'title'    => 'Our Story',
            'slug'     => 'our-story',
            'template' => 'template-story.php',
            'content'  => 'Enn Rev Enn Sourir was born from a simple observation: too many children in Mauritius were suffering from severe medical conditions without the financial or psychological support they needed. What started as a small group of volunteers has grown into a dedicated NGO providing life-saving medical aid, medication, and specialized care.'
        ),
        array(
            'title'    => 'Childhood Cancer Care',
            'slug'     => 'cancer-care',
            'template' => 'template-cancer-care.php',
            'content'  => 'We understand that a cancer diagnosis is devastating. Our "Childhood Cancer Care" program is designed to alleviate the financial and emotional burden, ensuring the child receives the best medical attention available both in Mauritius and abroad.'
        ),
        array(
            'title'    => 'Become a Member',
            'slug'     => 'become-member',
            'template' => 'template-become-member.php',
            'content'  => 'Join our assembly of heart and influence. Your membership fuels our daily operations.'
        ),
        array(
            'title'    => 'Sponsor a Child',
            'slug'     => 'sponsor-child',
            'template' => 'template-sponsor-child.php',
            'content'  => 'Your long-term commitment directly funds the life-saving treatment, medication, and emotional support for a child in need.'
        ),
        array(
            'title'    => 'Volunteer',
            'slug'     => 'volunteer',
            'template' => 'template-volunteer.php',
            'content'  => 'Be the change you want to see. Our volunteers are the backbone of our operations, helping with events, hospital visits, and administrative support.'
        ),
        array(
            'title'    => 'Our Partners',
            'slug'     => 'partners',
            'template' => 'template-partners.php',
            'content'  => 'We collaborate with local and international organizations to maximize our impact on pediatric healthcare.'
        ),
        array(
            'title'    => 'Our Team',
            'slug'     => 'team',
            'template' => 'template-team.php',
            'content'  => 'Meet the dedicated individuals who work tirelessly to ensure every child gets a chance at a healthy life.'
        ),
        array(
            'title'    => 'Annual Reports',
            'slug'     => 'annual-reports',
            'template' => 'template-annual-reports.php',
            'content'  => 'We believe in full transparency. Explore our financial and operational reports.'
        ),
        array(
            'title'    => 'Audit Reports',
            'slug'     => 'audit-reports',
            'template' => 'template-audit-reports.php',
            'content'  => 'Verified audits by independent firms to ensure the highest standards of governance.'
        ),
        array(
            'title'    => 'Child Cancer Scheme',
            'slug'     => 'cancer-scheme',
            'template' => 'template-cancer-scheme.php',
            'content'  => 'A specialized scheme providing comprehensive medical and financial assistance to children fighting cancer.'
        ),
        array(
            'title'    => 'Financial Reports',
            'slug'     => 'financial-reports',
            'template' => 'template-financial-reports.php',
            'content'  => 'Transparent financial reporting to maintain the trust of our donors and partners.'
        ),
        array(
            'title'    => 'Help for Adolescent',
            'slug'     => 'help-adolescent',
            'template' => 'template-help-adolescent.php',
            'content'  => 'Tailored support for adolescents facing complex health and social challenges.'
        ),
        array(
            'title'    => 'Support Adult',
            'slug'     => 'support-adult',
            'template' => 'template-support-adult.php',
            'content'  => 'Providing essential aid and care for adults in need of medical and social support.'
        ),
        array(
            'title'    => 'Become a Sponsor',
            'slug'     => 'become-sponsor',
            'template' => 'template-become-sponsor.php',
            'content'  => 'Corporate and individual sponsorship opportunities to drive sustainable change.'
        )
    );

    foreach ($pages as $p) {
        $existing = get_page_by_path($p['slug']);
        $post_data = array(
            'post_title'    => $p['title'],
            'post_content'  => $p['content'],
            'post_status'   => 'publish',
            'post_type'     => 'page',
            'post_name'     => $p['slug']
        );

        if ($existing) {
            $post_data['ID'] = $existing->ID;
            wp_update_post($post_data);
            echo "Updated Page: " . $p['title'] . "\n";
            $post_id = $existing->ID;
        } else {
            $post_id = wp_insert_post($post_data);
            echo "Created Page: " . $p['title'] . "\n";
        }

        if ($post_id) {
            update_post_meta($post_id, '_wp_page_template', $p['template']);
            
            // Story Specifics
            if ($p['slug'] === 'our-story') {
                update_post_meta($post_id, 'kopizon_vision', 'To be the primary catalyst for ensuring every child in Mauritius has access to the best medical care possible, regardless of their background.');
                update_post_meta($post_id, 'kopizon_mission', 'We provide financial, logistical, and psychological support to children facing severe medical challenges, facilitating treatments that save lives.');
            }

            // Cancer Care Specifics
            if ($p['slug'] === 'cancer-care') {
                $programs = array(
                    array('title' => 'Specialized Clinics', 'text' => 'Access to top-tier oncology departments.', 'icon' => 'fas fa-hospital-symbol'),
                    array('title' => 'Daily Medication', 'text' => 'Consistent supply of life-saving drugs.', 'icon' => 'fas fa-pills'),
                    array('title' => 'Medical Travel', 'text' => 'Flights and accommodation for overseas treatment.', 'icon' => 'fas fa-plane'),
                    array('title' => 'Family Support', 'text' => 'Psychological and financial aid for families.', 'icon' => 'fas fa-hands-helping')
                );
                update_post_meta($post_id, 'kopizon_programs', $programs);
            }

            // Sponsor Child Specifics
            if ($p['slug'] === 'sponsor-child') {
                $steps = array(
                    array('title' => 'Select a Case', 'text' => 'Choose a child whose story resonates with you or let us allocate your support where it\'s most urgent.'),
                    array('title' => 'Ongoing Support', 'text' => 'A monthly contribution ensures consistent funding for specialized medications and therapeutic sessions.'),
                    array('title' => 'See the Progress', 'text' => 'Receive quarterly updates and personalized progress reports on the child you are sponsoring.')
                );
                update_post_meta($post_id, 'kopizon_steps', $steps);
            }

            // Team Specifics
            if ($p['slug'] === 'team') {
                $team = array(
                    array('name' => 'Jane Doe', 'role' => 'President & Founder', 'img' => 'team-1.jpg', 'linkedin' => 'https://linkedin.com', 'twitter' => 'https://twitter.com'),
                    array('name' => 'John Smith', 'role' => 'Executive Director', 'img' => 'team-2.jpg', 'linkedin' => 'https://linkedin.com', 'twitter' => 'https://twitter.com'),
                    array('name' => 'Sarah Wilson', 'role' => 'Medical Liaison', 'img' => 'team-3.jpg', 'linkedin' => 'https://linkedin.com', 'twitter' => 'https://twitter.com'),
                    array('name' => 'Robert Chen', 'role' => 'Finance Manager', 'img' => 'team-4.jpg', 'linkedin' => 'https://linkedin.com', 'twitter' => 'https://twitter.com')
                );
                update_post_meta($post_id, 'kopizon_team', $team);
            }

            // Partners Specifics
            if ($p['slug'] === 'partners') {
                $partners = array(
                    array('title' => 'Medical Centers', 'desc' => 'International hospitals providing specialized care.', 'icon' => 'fas fa-hospital-alt'),
                    array('title' => 'Corporate Sponsors', 'desc' => 'Local companies supporting our medical schemes.', 'icon' => 'fas fa-handshake'),
                    array('title' => 'NGO Network', 'desc' => 'Partnering with other charities for maximum impact.', 'icon' => 'fas fa-users'),
                    array('title' => 'Individual Donors', 'desc' => 'Our backbone: the Mauritian community.', 'icon' => 'fas fa-heart')
                );
                update_post_meta($post_id, 'kopizon_partners', $partners);
            }

            // Annual & Financial Reports
            if ($p['slug'] === 'annual-reports' || $p['slug'] === 'financial-reports') {
                $reports = array(
                    array('title' => $p['title'] . ' 2025', 'desc' => 'Detailed summary of our financial and operational impact during 2025.', 'file' => '#', 'icon' => 'fas fa-file-pdf', 'color' => 'var(--kopizon-pink)'),
                    array('title' => $p['title'] . ' 2024', 'desc' => 'Comprehensive review of our achievements and transparency in 2024.', 'file' => '#', 'icon' => 'fas fa-file-invoice-dollar', 'color' => 'var(--kopizon-navy)'),
                    array('title' => $p['title'] . ' 2023', 'desc' => 'A look back at our growth and the children we sustained in 2023.', 'file' => '#', 'icon' => 'fas fa-chart-line', 'color' => 'var(--kopizon-pink)')
                );
                update_post_meta($post_id, 'kopizon_reports', $reports);
            }

            // Audit Reports
            if ($p['slug'] === 'audit-reports') {
                $audits = array(
                    array('title' => 'External Audit 2024', 'compiled' => 'Grand Thornton Mauritius', 'file' => '#'),
                    array('title' => 'External Audit 2023', 'compiled' => 'Grand Thornton Mauritius', 'file' => '#'),
                    array('title' => 'External Audit 2022', 'compiled' => 'Grand Thornton Mauritius', 'file' => '#')
                );
                update_post_meta($post_id, 'kopizon_audits', $audits);
            }

            // Support Adult / Help Adolescent / Cancer Scheme Benefits
            if ($p['slug'] === 'support-adult' || $p['slug'] === 'help-adolescent' || $p['slug'] === 'cancer-scheme') {
                $benefits = array(
                    array('title' => 'Medical Assistance', 'text' => 'Navigating complex medical needs with professional support.', 'icon' => 'fas fa-user-md'),
                    array('title' => 'Logistical Support', 'text' => 'Coordinating travel and stay for specialized treatments.', 'icon' => 'fas fa-truck-loading'),
                    array('title' => 'Mental Wellbeing', 'text' => 'Providing essential psychological support during recovery.', 'icon' => 'fas fa-heartbeat')
                );
                
                if ($p['slug'] === 'cancer-scheme') {
                    $benefits = array(
                        array('title' => 'Early Screening', 'text' => 'Free cancer screening for children aged 0–18 from low-income households.', 'icon' => 'fas fa-search-plus'),
                        array('title' => 'Treatment Funding', 'text' => 'Full or partial funding for treatment costs not covered by public health.', 'icon' => 'fas fa-pills'),
                        array('title' => 'Family Counseling', 'text' => 'Emotional and financial guidance for families navigating a cancer diagnosis.', 'icon' => 'fas fa-hands-helping')
                    );
                }
                update_post_meta($post_id, 'kopizon_benefits', $benefits);
            }
        }
    }

    // 2. Blog Posts (News)
    $news_items = array(
        array(
            'title'   => "Medha's Relapse",
            'slug'    => 'news-medha',
            'content' => "Medha, 13, has relapsed with cancer. She urgently needs Rs 1.2 million for continued treatment. Her family is reaching out for support to fund her specialized oncology sessions abroad.",
            'category'=> 'Medical'
        ),
        array(
            'title'   => "Camelia's Battle",
            'slug'    => 'news-camelia',
            'content' => "12-year-old Camelia suffers from a rare Desmoplastic small round cell tumor. Help her fight. The treatment requires intensive chemotherapy and surgical intervention not currently available locally.",
            'category'=> 'Urgent'
        ),
        array(
            'title'   => "Lorna's Hope",
            'slug'    => 'news-lorna',
            'content' => "13-year-old Lorna needs an urgent bone marrow transplant after a relapse. Every bit helps. We are coordinating with international donors to find a match.",
            'category'=> 'Transplant'
        )
    );

    foreach ($news_items as $n) {
        $existing = get_page_by_path($n['slug'], OBJECT, 'post');
        $post_data = array(
            'post_title'   => $n['title'],
            'post_content' => $n['content'],
            'post_status'  => 'publish',
            'post_type'    => 'post',
            'post_name'    => $n['slug']
        );

        if ($existing) {
            $post_data['ID'] = $existing->ID;
            wp_update_post($post_data);
            $post_id = $existing->ID;
        } else {
            $post_id = wp_insert_post($post_data);
        }

        if ($post_id && !empty($n['category'])) {
             // Set category tag
             wp_set_post_tags($post_id, $n['category']);
             
             // Ensure it's in a general 'News' category for queries
             $news_cat = get_cat_ID('News') ?: wp_create_category('News');
             wp_set_post_categories($post_id, array($news_cat));
        }
    }

    // 2.2 Testimonials (as posts in 'Testimonials' category)
    $testimonials = array(
        array(
            'title'   => 'Grateful Father',
            'slug'    => 'testimonial-father',
            'content' => "Enn Rev Enn Sourir facilitated a second chance for my daughter. They truly bring a dream and a smile to our faces when we thought all hope was lost.",
            'meta'    => 'Scholarship Beneficiary'
        ),
        array(
            'title'   => 'Shirley Rampersad',
            'slug'    => 'testimonial-shirley',
            'content' => "They funded my son's treatment abroad after he was diagnosed. Their 24/7 follow-up and psychological support made all the difference.",
            'meta'    => 'Parent of Beneficiary'
        ),
        array(
            'title'   => 'Anièle (Luna\'s Mother)',
            'slug'    => 'testimonial-aniele',
            'content' => "Luna received chemotherapy and surgery we couldn't afford. The NGO's comprehensive support was a lifeline for our family.",
            'meta'    => 'Medical Support'
        )
    );

    foreach ($testimonials as $t) {
        $existing = get_page_by_path($t['slug'], OBJECT, 'post');
        $post_data = array(
            'post_title'   => $t['title'],
            'post_content' => $t['content'],
            'post_status'  => 'publish',
            'post_type'    => 'post',
            'post_name'    => $t['slug']
        );

        if ($existing) {
            $post_data['ID'] = $existing->ID;
            wp_update_post($post_data);
            $post_id = $existing->ID;
        } else {
            $post_id = wp_insert_post($post_data);
        }

        if ($post_id) {
            update_post_meta($post_id, 'testimonial_role', $t['meta']);
            wp_set_post_categories($post_id, array(get_cat_ID('Testimonials') ?: wp_create_category('Testimonials')));
        }
    }

    // 3. Products (Campaigns)
    if (class_exists('WooCommerce')) {
        $campaigns = array(
            array(
                'title' => "Support Medha's Cancer Treatment",
                'slug'  => 'campaign-medha',
                'goal'  => 1200000,
                'raised'=> 450000,
                'category' => 'medical',
                'desc'  => 'Medha is fighting a tough battle with a relapse. Your support provides the specialized care she needs.'
            ),
            array(
                'title' => "Lorna's Bone Marrow Transplant",
                'slug'  => 'campaign-lorna',
                'goal'  => 2500000,
                'raised'=> 1200000,
                'category' => 'urgent',
                'desc'  => 'Lorna needs a match and a miracle. Help us fund her international search and transplant surgery.'
            ),
            array(
                'title' => "Camelia's Rare Tumor Battle",
                'slug'  => 'campaign-camelia',
                'goal'  => 3000000,
                'raised'=> 750000,
                'category' => 'urgent',
                'desc'  => 'Help Camelia fight against rare Desmoplastic small round cell tumor.'
            )
        );

        foreach ($campaigns as $c) {
            $existing = get_page_by_path($c['slug'], OBJECT, 'product');
            $post_data = array(
                'post_title'   => $c['title'],
                'post_content' => $c['desc'],
                'post_status'  => 'publish',
                'post_type'    => 'product',
                'post_name'    => $c['slug']
            );

            if ($existing) {
                $post_data['ID'] = $existing->ID;
                wp_update_post($post_data);
                $product_id = $existing->ID;
            } else {
                $product_id = wp_insert_post($post_data);
            }

            if ($product_id) {
                update_post_meta($product_id, '_goal', $c['goal']);
                update_post_meta($product_id, '_total_raised', $c['raised']);
                update_post_meta($product_id, '_regular_price', '500'); // Default donation
                update_post_meta($product_id, '_price', '500');
                
                // Categorize
                wp_set_object_terms($product_id, 'crowdfunding', 'product_cat');
                wp_set_object_terms($product_id, $c['category'], 'product_cat', true);
            }
        }
    }

    echo "Seeding Complete.\n";
}

function kopizon_seed_theme_mods() {
    echo "Seeding Theme Mods...\n";
    
    // Hero Section
    set_theme_mod('kopizon_hero_title', 'A Dream and a Smile for every child');
    set_theme_mod('kopizon_hero_btn1_label', 'View Projects');
    set_theme_mod('kopizon_hero_btn1_link', home_url('/donation'));
    set_theme_mod('kopizon_hero_btn2_label', 'Donate Now');
    set_theme_mod('kopizon_hero_btn2_link', home_url('/donation'));
    
    // Impact Strip
    $impacts = array(
        array('num' => '10K+', 'label' => 'Children Helped', 'icon' => 'fas fa-child'),
        array('num' => '500+', 'label' => 'Volunteers', 'icon' => 'fas fa-heart'),
        array('num' => '50+', 'label' => 'Partners', 'icon' => 'fas fa-handshake'),
        array('num' => 'Rs 5M', 'label' => 'Funds Raised', 'icon' => 'fas fa-donate'),
        array('num' => '10Y', 'label' => 'Experience', 'icon' => 'fas fa-calendar-alt')
    );
    foreach ($impacts as $i => $stat) {
        $idx = $i + 1;
        set_theme_mod("kopizon_impact_num_$idx", $stat['num']);
        set_theme_mod("kopizon_impact_label_$idx", $stat['label']);
        set_theme_mod("kopizon_impact_icon_$idx", $stat['icon']);
    }

    // Split Panel
    set_theme_mod('kopizon_split_eyebrow', "We're Focused on Results");
    set_theme_mod('kopizon_split_heading', "The new way of managing that's making waves — for our Child Cancer Scheme.");
    // set_theme_mod('kopizon_split_bg', get_template_directory_uri() . '/assets/images/cancer-care.jpg');

    echo "Theme Mods Seeded.\n";
}

// Manual trigger for CLI or URL run
if (php_sapi_name() === 'cli' || isset($_GET['seed_kopizon'])) {
    kopizon_seed_content();
    kopizon_seed_theme_mods();
}
