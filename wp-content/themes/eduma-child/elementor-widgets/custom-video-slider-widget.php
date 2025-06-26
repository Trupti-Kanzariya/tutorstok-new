<?php
// File: custom-video-slider-widget.php

if ( ! defined( 'ABSPATH' ) ) exit;

use Elementor\Widget_Base;
use Elementor\Controls_Manager;
use Elementor\Repeater;

class Elementor_Video_Slider_Widget extends Widget_Base {

    public function get_name() {
        return 'video_slider';
    }

    public function get_title() {
        return __('Video Slider', 'elementor');
    }

    public function get_icon() {
        return 'eicon-media-play';
    }

    public function get_categories() {
        return ['general'];
    }
    
    public function get_script_depends() {
        return ['swiper'];
    }

    public function get_style_depends() {
        return ['swiper'];
    }

    protected function register_controls() {

        // Section: Custom First Slide
        $this->start_controls_section('custom_text_slide_section', [
            'label' => __('Custom First Slide (Text/Image)', 'elementor'),
        ]);

        $this->add_control('custom_image', [
            'label' => __('Custom Slide Image', 'elementor'),
            'type' => Controls_Manager::MEDIA,
            'default' => [
                'url' => get_template_directory_uri() . '/img/home-logo.svg',
            ],
        ]);

        $this->add_control('custom_heading', [
            'label' => __('Custom Slide Heading', 'elementor'),
            'type' => Controls_Manager::TEXTAREA,
            'default' => 'TutorsTok<br />Learning that hits different',
            'dynamic' => ['active' => true],
        ]);

        $this->end_controls_section();

        // Section: Repeater Slides
        $this->start_controls_section('slides_section', [
            'label' => __('Video Slides', 'elementor'),
        ]);

        $repeater = new Repeater();

        $repeater->add_control('video_source', [
            'label' => __('Video Source', 'elementor'),
            'type' => Controls_Manager::MEDIA,
            'media_type' => 'video',
        ]);

        $repeater->add_control('title', [
            'label' => __('Title', 'elementor'),
            'type' => Controls_Manager::TEXTAREA,
            'default' => __('Slide Title', 'elementor'),
        ]);

        $repeater->add_control('description', [
            'label' => __('Description', 'elementor'),
            'type' => Controls_Manager::TEXTAREA,
            'default' => __('Slide Description', 'elementor'),
        ]);

        $this->add_control('slides', [
            'label' => __('Video Slides', 'elementor'),
            'type' => Controls_Manager::REPEATER,
            'fields' => $repeater->get_controls(),
            'default' => [],
        ]);

        $this->end_controls_section();
    }

    protected function render() { ?>
         <style>
            .tutors-slider .swiper {
                border-radius: 20px;
            }

            .tutors-slider .swiper-slide {
                display: flex;
                align-items: center;
                justify-content: space-between;
                height: 100%;
                border-radius: 20px;
            }

            .tutors-slider .first {
                background: rgba(0, 147, 97, 1);
            }

            .tutors-slider .second {
                background: rgba(224, 82, 121, 1);
            }

            .tutors-slider .third {
                background: rgba(33, 34, 34, 1);
            }

            .tutors-slider .third .text-box .slide-title, .tutors-slider .third .text-box .slide-desc{
                color: rgba(128, 255, 214, 1) !important;
            }

            .tutors-slider .four {
                background: rgba(33, 34, 34, 1);
            }

            .tutors-slider .four .slide-title .letter {
                color: rgba(128, 255, 214, 1);
            }

            .tutors-slider .video-box {
                width: 50%;
                height: 100%;
                overflow: hidden;
                border-radius: 20px;
                opacity: 0;
                transition: opacity 0.8s ease;
            }

            .tutors-slider .swiper-slide-active .video-box {
                opacity: 1;
            }

            .tutors-slider .video-box video {
                width: 100%;
                height: 100%;
                object-fit: cover;
            }

            .tutors-slider .text-box {
                width: 50%;
                max-width: 560px;
                height: 100%;
                padding: 20px 40px;
                display: flex;
                flex-direction: column;
                justify-content: center;
                align-items: flex-start;
                color: #fff;
                opacity: 0;
                transform: translateY(50px);
                transition: all 0.8s ease;
            }

            .tutors-slider .swiper-slide-active .text-box {
                opacity: 1;
                transform: translateY(0);
            }

            .tutors-slider .banner-slider.swiper {
                height: 400px;
            }

            .tutors-slider {
                max-width: 1310px;
                height: 400px;
                overflow: hidden;
                position: relative;
                width: 100%;
            }

            .tutors-slider .banner-slider.swiper .swiper-wrapper {
                transition-timing-function: ease-in-out;
            }

            .tutors-slider .banner-slider.swiper .swiper-slide {
                display: flex;
                align-items: center;
                justify-content: space-between;
                height: 100%;
                border-radius: 20px;
                flex-shrink: 0;
            }

            .tutors-slider .banner-slider.swiper .swiper-slide .video-box {
                width: 50%;
                position: relative;
                overflow: hidden;
                border-radius: 20px;
                opacity: 0;
                transition: opacity 0.8s ease;
                height: 100%;
            }

            .tutors-slider .banner-slider.swiper .swiper-slide-active .video-box {
                opacity: 1;
            }

            .tutors-slider .banner-slider.swiper .swiper-slide .video-box video {
                width: 100%;
                height: 100%;
                object-fit: cover;
                border-radius: 20px;
            }

            .tutors-slider .banner-slider.swiper .swiper-slide.four .text-box {
                width: 100%;
                max-width: 100%;
                text-align: center;
                align-items: center;
                justify-content: center;
            }

            .tutors-slider .banner-slider.swiper .swiper-slide .text-box {
                display: flex;
                flex-direction: column;
                justify-content: center;
                align-items: flex-start;
                text-align: left;
                max-width: 560px;
                margin: auto;
                width: 50%;
                height: 100%;
                color: #fff;
                padding: 0px;
                padding-left: 0px;
                opacity: 0;
                transform: translateX(100px);
                transition: all 0.8s ease;
            }

            .tutors-slider .banner-slider.swiper .swiper-slide-active .text-box {
                opacity: 1;
                transform: translateX(0);
            }

            .tutors-slider .banner-slider.swiper .swiper-slide .text-box .slide-title {
                font-size: 80px;
                line-height: 84px;
                font-weight: 600;
                color: rgba(255, 255, 255, 1);
                font-family: "Urbanist", Sans-serif;
                margin-bottom: 20px;
                animation: fadeInUp 1s ease forwards;
            }

            .tutors-slider .banner-slider.swiper .swiper-slide.second .text-box .slide-title {
                font-size: 60px;
                line-height: 64px;
            }

            .tutors-slider .banner-slider.swiper .swiper-slide .text-box .slide-desc {
                font-size: 28px;
                font-weight: 400;
                line-height: 36px;
                animation: fadeInUp 1.3s ease forwards;
                font-family: "Urbanist", Sans-serif;
            }

            @keyframes fadeInUp {
                to {
                opacity: 1;
                transform: translateY(0);
                }
            }

            .tutors-slider .animate-letters .letter {
                display: inline-block;
                transform: translateX(30px) scaleY(0);
                transform-origin: bottom right;
                opacity: 0;
                animation: stretchInLetter 0.4s ease forwards;
                animation-delay: var(--delay);
            }

            @keyframes stretchInLetter {
                to {
                transform: translateX(0) scaleY(1);
                opacity: 1;
                }
            }

            .tutors-slider .animate-words .word {
                display: inline-block;
                opacity: 0;
                transform: translateY(20px);
                animation: fadeUpWord 0.4s ease forwards;
                animation-delay: var(--delay);
            }

            @keyframes fadeUpWord {
                to {
                opacity: 1;
                transform: translateY(0);
                }
            }

            .tutors-slider .logo-type-all-here-tutorstok .tutors-cls-adding-here {
                height: auto;
                max-height: 90px;
                margin-bottom: 14px;
            }

            @media only screen and (max-width: 1024px) {
                .tutors-slider .banner-slider.swiper .swiper-slide .text-box .slide-title {
                font-size: 50px;
                line-height: 54px;
                }

                .tutors-slider .banner-slider.swiper .swiper-slide.second .text-box .slide-title {
                font-size: 35px;
                line-height: 40px;
                }

                .tutors-slider .banner-slider.swiper .swiper-slide .text-box .slide-desc {
                font-size: 20px;
                line-height: 24px;
                }

                .tutors-slider .banner-slider.swiper .swiper-slide .text-box {
                padding-left: 30px;
                padding-right: 20px;
                }
            }

            @media only screen and (max-width: 767px) {
                .tutors-slider .banner-slider.swiper .swiper-slide .text-box .slide-title {
                font-size: 24px;
                line-height: 28px;
                }

                .tutors-slider .banner-slider.swiper .swiper-slide.second .text-box .slide-title {
                font-size: 20px;
                line-height: 24px;
                }

                .tutors-slider .banner-slider.swiper .swiper-slide .text-box .slide-desc {
                font-size: 16px;
                line-height: 20px;
                }

                .tutors-slider .banner-slider.swiper .swiper-slide .text-box {
                max-width: 100%;
                width: 100%;
                justify-content: center;
                align-items: center;
                text-align: center;
                }

                .tutors-slider .banner-slider.swiper .swiper-slide {
                flex-direction: column;
                }

                .tutors-slider .banner-slider.swiper .swiper-slide .video-box {
                width: 100%;
                }
            }
        </style>
        <?php $settings = $this->get_settings_for_display();

        if (empty($settings['slides']) && empty($settings['custom_heading']) && empty($settings['custom_image']['url'])) {
            return;
        }

        echo '<div class="tutors-slider"><div class="swiper banner-slider"><div class="swiper-wrapper">';

        // ✅ Video Slides Loop
        foreach ($settings['slides'] as $slide) {
            echo '<div class="swiper-slide">';
            echo '<div class="video-box">';
            echo '<video autoplay muted loop>';
            echo '<source src="' . esc_url($slide['video_source']['url']) . '" type="video/mp4" />';
            echo '</video></div>';
            echo '<div class="text-box">';
            echo '<div class="slide-title animate-letters">' . wp_kses_post($slide['title']) . '</div>';
            echo '<div class="slide-desc animate-words">' . wp_kses_post($slide['description']) . '</div>';
            echo '</div></div>';
        }

        // ✅ Custom First Slide
        if (!empty($settings['custom_heading']) || !empty($settings['custom_image']['url'])) {
            echo '<div class="swiper-slide custom-text-slide">';
            echo '<div class="text-box">';
            if (!empty($settings['custom_image']['url'])) {
                echo '<div class="logo-type-all-here-tutorstok">';
                echo '<img src="' . esc_url($settings['custom_image']['url']) . '" alt="custom-logo" class="tutors-cls-adding-here" />';
                echo '</div>';
            }
            echo '<div class="slide-title animate-letters">' . wp_kses_post($settings['custom_heading']) . '</div>';
            echo '</div></div>';
        }

        echo '</div></div></div>';
    }
}
