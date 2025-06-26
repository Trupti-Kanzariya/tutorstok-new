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
        ]);

        $repeater->add_control('slide_desc', [
            'label' => __('Slide Description', 'tutorstok'),
            'type' => Controls_Manager::TEXTAREA,
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
        <style>
            /* Include your CSS styles here or enqueue as a separate file */
            .swiper {
                border-radius: 20px;
            }
            .swiper-slide {
                display: flex;
                justify-content: space-between;
                align-items: center;
                height: 100%;
                border-radius: 20px;
            }
            .video-box {
                width: 50%;
                height: 100%;
                overflow: hidden;
                border-radius: 20px;
                opacity: 0;
                transition: opacity 0.8s ease;
            }
            .swiper-slide-active .video-box {
                opacity: 1;
            }
            .video-box video {
                width: 100%;
                height: 100%;
                object-fit: cover;
            }
            .text-box {
                width: 50%;
                max-width: 560px;
                padding: 20px;
                color: #fff;
                opacity: 0;
                transform: translateY(50px);
                transition: all 0.8s ease;
            }
            .swiper-slide-active .text-box {
                opacity: 1;
                transform: translateY(0);
            }
            .slide-title {
                font-size: 2em;
                margin-bottom: 10px;
            }
            .slide-desc {
                font-size: 1.2em;
            }
        </style>

        <div class="swiper banner-slider">
            <div class="swiper-wrapper">
                <?php foreach ($settings['slides'] as $slide): ?>
                    <div class="swiper-slide">
                        <?php if (!empty($slide['video_url'])): ?>
                            <div class="video-box">
                                <video autoplay muted loop>
                                    <source src="<?php echo esc_url($slide['video_url']); ?>" type="video/mp4">
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
        <?php
    }
}
