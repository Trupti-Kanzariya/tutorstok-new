<?php
/**
 * LearnPress Coming Soon Hook Template
 *
 * @since 4.1.4
 * @version 1.0.0
 */

namespace LP_Addon_Coming_Soon;

use LearnPress\Helpers\Singleton;
use LearnPress\Helpers\Template;
use LearnPress\Models\CourseModel;
use LP_Addon_Course_Review;
use LP_Addon_Course_Review_Preload;

class CourseRatingTemplate {
	use Singleton;

	public $addon;

	public function init() {
		$this->addon = LP_Addon_Course_Review_Preload::$addon;
		$this->hooks();
	}

	/**
	 * Hooks
	 */
	public function hooks() {
	}

	/**
	 * Add course rating to course archive page
	 *
	 * @param CourseModel $course
	 * @param $rated
	 *
	 * @return string
	 */
	public function html_rated_star( CourseModel $course, $rated ): string {
		$percent = min( 100, (float) $rated * 20 );

		$html_item = '';
		for ( $i = 1; $i <= 5; $i++ ) {
			$p            = $i * 20;
			$r            = max( $p <= $percent ? 100 : ( $percent - ( $i - 1 ) * 20 ) * 5, 0 );
			$section_item = [
				'wrapper'     => '<div class="review-star">',
				'far'         => sprintf(
					'<em class="far lp-review-svg-star">%s</em>',
					LP_Addon_Course_Review::get_svg_star()
				),
				'fas'         => sprintf(
					'<em class="fas lp-review-svg-star" style="width:%d;">%s</em>',
					"$r%",
					LP_Addon_Course_Review::get_svg_star()
				),
				'wrapper_end' => '</div>',
			];

			$html_item .= Template::combine_components( $section_item );
		}

		$section = [
			'wrapper'     => '<div class="review-stars-rated">',
			'item'        => $html_item,
			'wrapper_end' => '</div>',
		];

		return Template::combine_components( $section );
	}

	/**
	 * Add course rating to course archive page
	 *
	 * @param CourseModel $course
	 *
	 * @return string
	 */
	public function html_average_rating( CourseModel $course ): string {
		$average = $this->addon->get_average_rated( $course );

		$html = sprintf(
			'<span class="lp-course-rating-average">%s</span>',
			$average
		);

		return $html;
	}
}
