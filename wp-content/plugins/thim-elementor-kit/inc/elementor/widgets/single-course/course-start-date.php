<?php

namespace Elementor;

use Elementor\Utils;
use Elementor\Controls_Manager;
use Elementor\Group_Control_Typography;
use Elementor\Group_Control_Text_Shadow;

class Thim_Ekit_Widget_Course_Start_Date extends Widget_Base {

	public function get_name() {
		return 'thim-ekits-course-start-date';
	}

	public function get_title() {
		return esc_html__( 'Course Start Date', 'thim-elementor-kit' );
	}

	public function get_icon() {
		return 'eicon-calendar';
	}

	public function get_categories() {
		return array( \Thim_EL_Kit\Elementor::CATEGORY_SINGLE_COURSE );
	}

	protected function register_controls() {

		$this->start_controls_section(
			'section_content',
			[
				'label' => esc_html__( 'Content', 'thim-elementor-kit' ),
			]
		);

		$this->add_control(
			'tag_name',
			[
				'label' => esc_html__( 'HTML Tag', 'thim-elementor-kit' ),
				'type' => Controls_Manager::SELECT,
				'options' => [
					'h1' => 'H1',
					'h2' => 'H2',
					'h3' => 'H3',
					'h4' => 'H4',
					'h5' => 'H5',
					'h6' => 'H6',
					'div' => 'div',
					'span' => 'span',
					'p' => 'p',
				],
				'default' => 'p',
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_style',
			[
				'label' => esc_html__( 'Style', 'thim-elementor-kit' ),
				'tab' => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_control(
			'text_color',
			[
				'label' => esc_html__( 'Text Color', 'thim-elementor-kit' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .thim-ekit-single-course__start-date' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'typography',
				'selector' => '{{WRAPPER}} .thim-ekit-single-course__start-date',
			]
		);

		$this->add_group_control(
			Group_Control_Text_Shadow::get_type(),
			[
				'name' => 'text_shadow',
				'selector' => '{{WRAPPER}} .thim-ekit-single-course__start-date',
			]
		);

		$this->end_controls_section();
	}

	public function render() {
		global $post;
		$settings = $this->get_settings_for_display();

		// You can adjust this key if you're using a different one.
		$start_date = get_post_meta( $post->ID, '_lp_start_date', true );

		if ( ! empty( $start_date ) ) {
			echo '<div class="thim-ekit-single-course__start-date">';
			echo '<' . Utils::validate_html_tag( $settings['tag_name'] ) . '>';
			echo esc_html__( 'Start Date: ', 'thim-elementor-kit' ) . esc_html( date_i18n( get_option( 'date_format' ), strtotime( $start_date ) ) );
			echo '</' . Utils::validate_html_tag( $settings['tag_name'] ) . '>';
			echo '</div>';
		}
	}
}
