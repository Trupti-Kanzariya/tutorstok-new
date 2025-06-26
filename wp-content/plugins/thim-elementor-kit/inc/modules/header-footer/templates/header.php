<!DOCTYPE html>
<html <?php
language_attributes(); ?>>
<head>
	<meta charset="<?php
	bloginfo( 'charset' ); ?>">
	<meta name="viewport" content="width=device-width, initial-scale=1.0"/>
	<?php
	if ( ! current_theme_supports( 'title-tag' ) ) : ?>
		<title>
			<?php
			echo wp_get_document_title(); ?>
		</title>
	<?php
	endif; ?>
	<?php
	wp_head(); ?>
</head>
<body <?php
body_class(); ?>>
<?php
wp_body_open(); ?>
<?php
do_action( 'thim_ekit/header_footer/template/before_header' ); ?>
<div class="thim-ekit__header">
	<div <?php do_action( 'thim_ekit/modules/header_footer/template/attributes', 'header' ); ?>>
		<?php
		if ( function_exists( 'learn_press_is_profile' ) && learn_press_is_profile() ) {
			
			echo \Elementor\Plugin::instance()->frontend->get_builder_content_for_display( 24650 );
		} else {
			
			do_action( 'thim_ekit/modules/header_footer/template/header' );
		}
		?>
	</div>
</div>

<?php
do_action( 'thim_ekit/header_footer/template/after_header' ); ?>
