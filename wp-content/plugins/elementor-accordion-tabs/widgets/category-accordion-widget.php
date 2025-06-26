<?php
if (!defined('ABSPATH')) exit; // Exit if accessed directly

class Elementor_Custom_Accordion_Widget extends \Elementor\Widget_Base {
	public function get_name() {
		return 'custom_accordion';
	}

	public function get_title() {
		return __('Custom Accordion', 'plugin-name');
	}

	public function get_icon() {
		return 'eicon-accordion';
	}

	public function get_categories() {
		return ['general'];
	}

	protected function register_controls() {
		$this->start_controls_section(
			'content_section',
			[
				'label' => __('Accordion Content', 'plugin-name'),
				'tab' => \Elementor\Controls_Manager::TAB_CONTENT,
			]
		);

		$repeater = new \Elementor\Repeater();

		$repeater->add_control(
			'accordion_title',
			[
				'label' => __('Title', 'plugin-name'),
				'type' => \Elementor\Controls_Manager::TEXT,
				'default' => __('Accordion Title', 'plugin-name'),
			]
		);

		$repeater->add_control(
			'accordion_subtitle',
			[
				'label' => __('Subtitle', 'plugin-name'),
				'type' => \Elementor\Controls_Manager::TEXT,
				'default' => __('Accordion Subtitle', 'plugin-name'),
			]
		);
		$repeater->add_control(
			'accordion_openings',
			[
				'label' => __('Openings', 'plugin-name'),
				'type' => \Elementor\Controls_Manager::TEXT,
				'default' => __('Accordion Openings', 'plugin-name'),
			]
		);


		$repeater->add_control(
			'accordion_description',
			[
				'label' => __('Description', 'plugin-name'),
				'type' => \Elementor\Controls_Manager::TEXTAREA,
				'default' => __('Accordion description goes here...', 'plugin-name'),
			]
		);

		$repeater->add_control(
			'apply_button_text',
			[
				'label' => __('Button Text', 'plugin-name'),
				'type' => \Elementor\Controls_Manager::TEXT,
				'default' => __('Apply Now', 'plugin-name'),
			]
		);

		$repeater->add_control(
			'apply_button_url',
			[
				'label' => __('Button Link', 'plugin-name'),
				'type' => \Elementor\Controls_Manager::URL,
				'default' => ['url' => '#'],
			]
		);

		$repeater->add_control(
			'apply_button_icon',
			[
				'label' => __('Button Icon', 'plugin-name'),
				'type' => \Elementor\Controls_Manager::ICONS,
				'default' => [
					'value' => 'fas fa-arrow-right',
					'library' => 'fa-solid',
				],
			]
		);

		$this->add_control(
			'accordion_items',
			[
				'label' => __('Accordion Items', 'plugin-name'),
				'type' => \Elementor\Controls_Manager::REPEATER,
				'fields' => $repeater->get_controls(),
				'title_field' => '{{{ accordion_title }}} - {{{ accordion_subtitle }}}',
			]
		);
		$this->add_control(
			'accordion_items',
			[
				'label' => __('Accordion Items', 'plugin-name'),
				'type' => \Elementor\Controls_Manager::REPEATER,
				'fields' => $repeater->get_controls(),
				'title_field' => '{{{ accordion_title }}} - {{{ accordion_openings }}}',
			]
		);

		$this->end_controls_section();

        // STYLE CONTROLS
		$this->start_controls_section(
			'style_section',
			[
				'label' => __('Accordion Style', 'plugin-name'),
				'tab' => \Elementor\Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_control(
			'accordion_bg_color',
			[
				'label' => __('Background Color', 'plugin-name'),
				'type' => \Elementor\Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .custom-accordion' => 'background-color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'accordion_border_color',
			[
				'label' => __('Border Color', 'plugin-name'),
				'type' => \Elementor\Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .custom-accordion' => 'border: 1px solid {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			\Elementor\Group_Control_Typography::get_type(),
			[
				'name' => 'title_typography',
				'label' => __('Title Typography', 'plugin-name'),
				'selector' => '{{WRAPPER}} .accordion-title',
			]
		);

		$this->add_control(
			'title_color',
			[
				'label' => __('Title Color', 'plugin-name'),
				'type' => \Elementor\Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .accordion-title' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			\Elementor\Group_Control_Typography::get_type(),
			[
				'name' => 'subtitle_typography',
				'label' => __('Subtitle Typography', 'plugin-name'),
				'selector' => '{{WRAPPER}} .accordion-subtitle',
			]
		);

		$this->add_control(
			'subtitle_color',
			[
				'label' => __('Subtitle Color', 'plugin-name'),
				'type' => \Elementor\Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .accordion-subtitle' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'button_bg_color',
			[
				'label' => __('Button Background Color', 'plugin-name'),
				'type' => \Elementor\Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .apply-now-btn' => 'background-color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'button_text_color',
			[
				'label' => __('Button Text Color', 'plugin-name'),
				'type' => \Elementor\Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .apply-now-btn' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			\Elementor\Group_Control_Typography::get_type(),
			[
				'name' => 'button_typography',
				'label' => __('Button Typography', 'plugin-name'),
				'selector' => '{{WRAPPER}} .apply-now-btn',
			]
		);



		$this->end_controls_section();
	}

	protected function render() {
		$settings = $this->get_settings_for_display();
		?>
		<section class="what-we-are-looking-for">
			<div class="main-part-here-start">
				<div class="accordion">
					<?php foreach ($settings['accordion_items'] as $index => $item): ?>
						<div class="accordion-item">
							<div class="main-accordion-response">
								<div class="accordion-header">
									<div class="breaking-cls-adding">
										<h3><?php echo esc_html($item['accordion_title']); ?></h3>
										<div class="new-title-cls-adding">
											<span class="job-info">
												<?php echo wp_kses_post($item['accordion_subtitle']); ?>
											</span>
											<span class="openings-info">
												<?php echo wp_kses_post($item['accordion_openings']); ?>
											</span>
										</div>
									</div>
									<div class="apply-now-here-cls-adding">
										<button class="apply-btn">
											<?php echo esc_html($item['apply_button_text']); ?>
											<span class="apply-now-cls-adding">
												<?php \Elementor\Icons_Manager::render_icon($item['apply_button_icon'], ['aria-hidden' => 'true']); ?>
											</span>
										</button>
									</div>
									<div class="accordion-icon">+</div>
								</div>
							</div>
							<div class="accordion-content">
								<?php echo wp_kses_post($item['accordion_description']); ?>
							</div>
						</div>
					<?php endforeach; ?>
				</div>
			</div>
		</section>
		<!-- MODAL STRUCTURE (UPDATED) -->
		<div class="modal-overlay modal-overtype-cls-adding-here" id="modal">
			<div class="modal-content">
				<div class="modal-header">
					<h6>Apply Now</h6>
					<button class="modal-close" id="modalClose">
						<span class="apply-nowcross-btn-cls-adding"
						><svg
						width="24"
						height="24"
						viewBox="0 0 24 24"
						fill="none"
						xmlns="http://www.w3.org/2000/svg"
						>
						<path
						d="M20.0195 20L4.01953 4"
						stroke="white"
						stroke-width="1.5"
						stroke-miterlimit="10"
						stroke-linecap="round"
						stroke-linejoin="round"
						/>
						<path
						d="M20.0195 4L4.01953 20"
						stroke="white"
						stroke-width="1.5"
						stroke-miterlimit="10"
						stroke-linecap="round"
						stroke-linejoin="round"
						/>
					</svg>
				</span>
			</button>
		</div>
		<div class="modal-body">
			

			<div class="newmodal-popup-login" id="newmodal-popup-login">
				
				
				<?php echo do_shortcode( '[contact-form-7 id="a814616" title="Career Form"]' ); ?>	
				<div class="form-loader" style="display: none;">
			
					<img class="loader-images" src="<?php echo get_home_url(); ?>/wp-content/uploads/2025/04/pause-gIF-image-ezgif.com-gif-maker.gif" alt="Loading..." />
				</div>						
			</div>
		</div>
	</div>
</div>
<?php
}

}
