<?php
if (!defined('ABSPATH')) {
    exit;
}

class Elementor_Our_Partners_Widget extends \Elementor\Widget_Base {
    public function get_name() {
        return 'our_partners';
    }

    public function get_title() {
        return esc_html__('Our Partners', 'our-partners');
    }

    public function get_icon() {
        return 'eicon-bullet-list';
    }

    public function get_categories() {
        return ['basic'];
    }

    protected function register_controls() {
        // Content Section
        $this->start_controls_section(
            'content_section',
            [
                'label' => esc_html__('Partners List', 'our-partners'),
                'tab' => \Elementor\Controls_Manager::TAB_CONTENT,
            ]
        );

        $this->add_control(
            'header_text',
            [
                'label' => esc_html__('Header Text', 'our-partners'),
                'type' => \Elementor\Controls_Manager::TEXT,
                'default' => 'Our Partners',
                'placeholder' => esc_html__('Enter header text', 'our-partners'),
            ]
        );

        $this->add_control(
            'match_height',
            [
                'label' => esc_html__('Match Height With Other Widgets', 'our-partners'),
                'type' => \Elementor\Controls_Manager::SWITCHER,
                'default' => '',
                'label_on' => esc_html__('Yes', 'our-partners'),
                'label_off' => esc_html__('No', 'our-partners'),
            ]
        );

        $repeater = new \Elementor\Repeater();

        $repeater->add_control(
            'partner_name',
            [
                'label' => esc_html__('Partner Name', 'our-partners'),
                'type' => \Elementor\Controls_Manager::TEXT,
                'default' => '',
                'label_block' => true,
            ]
        );

        $repeater->add_control(
            'partner_link',
            [
                'label' => esc_html__('Partner Link', 'our-partners'),
                'type' => \Elementor\Controls_Manager::URL,
                'placeholder' => 'https://your-link.com',
                'show_external' => true,
                'default' => [
                    'url' => '',
                    'is_external' => true,
                    'nofollow' => true,
                ],
                'label_block' => true,
            ]
        );

        $repeater->add_control(
            'partner_icon',
            [
                'label' => esc_html__('Icon', 'our-partners'),
                'type' => \Elementor\Controls_Manager::ICONS,
                'default' => [
                    'value' => 'fas fa-star',
                    'library' => 'solid',
                ],
            ]
        );

        $this->add_control(
            'partners_list',
            [
                'label' => esc_html__('Partners', 'our-partners'),
                'type' => \Elementor\Controls_Manager::REPEATER,
                'fields' => $repeater->get_controls(),
                'default' => [
                    [
                        'partner_name' => 'APOLLO HOSPITALS ENTERPRISES LTD',
                        'partner_icon' => ['value' => 'fas fa-hospital'],
                    ],
                    [
                        'partner_name' => 'ONG STEP BY STEP',
                        'partner_icon' => ['value' => 'fas fa-walking'],
                    ],
                    [
                        'partner_name' => 'SIOP AFRICA',
                        'partner_icon' => ['value' => 'fas fa-globe-africa'],
                    ],
                    [
                        'partner_name' => 'CHU REUNION',
                        'partner_icon' => ['value' => 'fas fa-clinic-medical'],
                    ],
                    [
                        'partner_name' => 'GLOBAL EAGLE',
                        'partner_icon' => ['value' => 'fas fa-eagle'],
                    ],
                ],
                'title_field' => '{{{ partner_name }}}',
            ]
        );

        $this->end_controls_section();

        // Style Section
        $this->start_controls_section(
            'style_section',
            [
                'label' => esc_html__('Styles', 'our-partners'),
                'tab' => \Elementor\Controls_Manager::TAB_STYLE,
            ]
        );

        $this->add_control(
            'header_background_color',
            [
                'label' => esc_html__('Header Background Color', 'our-partners'),
                'type' => \Elementor\Controls_Manager::COLOR,
                'default' => '#F06292',
                'selectors' => [
                    '{{WRAPPER}} .our-partners-header' => 'background-color: {{VALUE}}',
                ],
            ]
        );

        $this->add_control(
            'header_text_color',
            [
                'label' => esc_html__('Header Text Color', 'our-partners'),
                'type' => \Elementor\Controls_Manager::COLOR,
                'default' => '#FFFFFF',
                'selectors' => [
                    '{{WRAPPER}} .our-partners-header' => 'color: {{VALUE}}',
                ],
            ]
        );

        $this->add_control(
            'icon_color',
            [
                'label' => esc_html__('Icon Color', 'our-partners'),
                'type' => \Elementor\Controls_Manager::COLOR,
                'default' => '#F06292',
                'selectors' => [
                    '{{WRAPPER}} .partner-icon' => 'color: {{VALUE}}',
                ],
            ]
        );

        $this->end_controls_section();
    }

    protected function render() {
        $settings = $this->get_settings_for_display();
        $match_height = $settings['match_height'] === 'yes' ? 'match-height' : '';
        ?>
        <div class="our-partners-widget <?php echo esc_attr($match_height); ?>">
            <div class="our-partners-header">
                <?php echo esc_html($settings['header_text']); ?>
            </div>
            <div class="our-partners-list">
                <?php foreach ($settings['partners_list'] as $index => $partner) : 
                    $target = $partner['partner_link']['is_external'] ? ' target="_blank"' : '';
                    $nofollow = $partner['partner_link']['nofollow'] ? ' rel="nofollow"' : '';
                    $link = $partner['partner_link']['url'];
                    ?>
                    <?php if ($link) : ?>
                        <a href="<?php echo esc_url($link); ?>"<?php echo $target . $nofollow; ?>>
                    <?php endif; ?>
                    <div class="partner-item" data-item="<?php echo esc_attr($index + 1); ?>">
                        <?php if (!empty($partner['partner_icon']['value'])) : ?>
                            <span class="partner-icon">
                                <i class="<?php echo esc_attr($partner['partner_icon']['value']); ?>"></i>
                            </span>
                        <?php endif; ?>
                        <span class="partner-name"><?php echo esc_html($partner['partner_name']); ?></span>
                    </div>
                    <?php if ($link) : ?>
                        </a>
                    <?php endif; ?>
                <?php endforeach; ?>
            </div>
        </div>
        <?php if ($match_height === 'match-height') : ?>
        <script>
        jQuery(document).ready(function($) {
            function matchHeight() {
                $('.match-height').matchHeight();
            }
            matchHeight();
            $(window).on('resize', matchHeight);
        });
        </script>
        <?php endif;
    }
}