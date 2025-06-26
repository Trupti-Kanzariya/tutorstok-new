<?php

use Elementor\Widget_Base;
use Elementor\Controls_Manager;
use Elementor\Repeater;

if (!defined('ABSPATH')) exit;

class TutorsTok_Slider_Widget extends Widget_Base {

    public function get_name() {
        return 'tutorstok_slider';
    }

    public function get_title() {
        return __('TutorsTok Slider', 'tutorstok');
    }

    public function get_icon() {
        return 'eicon-slider-push';
    }

    public function get_categories() {
        return ['general'];
    }

    protected function register_controls() {
        $repeater = new Repeater();

        $repeater->add_control('video_url', [
            'label' => __('Video URL', 'tutorstok'),
            'type' => Controls_Manager::TEXT,
            'label_block' => true,
        ]);

        $repeater->add_control('slide_title', [
            'label' => __('Slide Title', 'tutorstok'),
            'type' => Controls_Manager::TEXTAREA,
            'rows' => 2,
        ]);

        $repeater->add_control('slide_desc', [
            'label' => __('Slide Description', 'tutorstok'),
            'type' => Controls_Manager::TEXTAREA,
            'rows' => 3,
        ]);

        $this->add_control('slides', [
            'label' => __('Slides', 'tutorstok'),
            'type' => Controls_Manager::REPEATER,
            'fields' => $repeater->get_controls(),
            'title_field' => '{{{ slide_title }}}',
        ]);
    }

    protected function render() {
        $settings = $this->get_settings_for_display();
        ?>
        <div class="swiper banner-slider">
            <div class="swiper-wrapper">
                <?php foreach ($settings['slides'] as $slide) : ?>
                    <div class="swiper-slide">
                        <?php if (!empty($slide['video_url'])) : ?>
                            <div class="video-box">
                                <video autoplay muted loop>
                                    <source src="<?php echo esc_url($slide['video_url']); ?>" type="video/mp4" />
                                </video>
                            </div>
                        <?php endif; ?>
                        <div class="text-box">
                            <div class="slide-title animate-letters">
                                <?php echo wp_kses_post(nl2br($slide['slide_title'])); ?>
                            </div>
                            <div class="slide-desc animate-words">
                                <?php echo wp_kses_post(nl2br($slide['slide_desc'])); ?>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>

        <script>
        document.addEventListener("DOMContentLoaded", function () {
            new Swiper('.banner-slider', {
                loop: true,
                autoplay: {
                    delay: 5000,
                    disableOnInteraction: false
                }
            });
        });
        </script>
        <?php
    }
}
\Elementor\Plugin::instance()->widgets_manager->register_widget_type(new TutorsTok_Slider_Widget());
