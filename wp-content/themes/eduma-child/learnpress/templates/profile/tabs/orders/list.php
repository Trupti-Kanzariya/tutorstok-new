<?php
/**
 * Template for displaying list orders in orders tab of user profile page.
 *
 * This template can be overridden by copying it to yourtheme/learnpress/orders/list.php.
 *
 * @author   ThimPress
 * @package  Learnpress/Templates
 * @version  4.0.1
 */

use LearnPress\Helpers\Template;

defined( 'ABSPATH' ) || exit();

$profile = LP_Profile::instance();

$query_orders = $profile->query_orders( array( 'fields' => 'ids' ) );
if ( ! $query_orders->get_items() ) {
	Template::print_message( __( 'No orders!', 'learnpress' ), 'info' );
	return;
}
?>

<h3 class="profile-heading"><?php esc_html_e( 'Transactions', 'learnpress' ); ?></h3>

<div class="custom-table-wrapper">
<table class="lp-list-table profile-list-orders profile-list-table ">
	<thead>
		<tr class="order-row">
			<th class="column-order-number"><?php esc_html_e( 'Transaction ID', 'learnpress' ); ?></th>
			<th class="column-order-total"><?php esc_html_e( 'Total Amount', 'learnpress' ); ?></th>
			<th class="column-order-status"><?php esc_html_e( 'Status', 'learnpress' ); ?></th>
			<th class="column-order-date"><?php esc_html_e( 'Date', 'learnpress' ); ?></th>
			<th class="column-order-actions"><?php esc_html_e( 'Action', 'learnpress' ); ?></th>
		</tr>
	</thead>

	<tbody>
		<?php
		foreach ( $query_orders->get_items() as $order_id ) {
			$order = learn_press_get_order( $order_id );
			?>

			<tr class="order-row">
				<td class="column-order-number">
					<a href="<?php echo esc_html( $order->get_view_order_url() ); ?>">
						<?php echo esc_html( $order->get_order_number() ); ?>
					</a>
				</td>
				<td class="column-order-total"><?php echo esc_html( $order->get_formatted_order_total() ); ?></td>
				<td class="column-order-status">
					<span class="lp-label label-<?php echo esc_attr( $order->get_status() ); ?>">
						<?php echo wp_kses_post( $order->get_order_status_html() ); ?>
					</span>
				</td>
				<td class="column-order-date">
				    <?php
				    $order_date_obj = $order->get_data( 'order_date' );
				    if ( $order_date_obj && method_exists( $order_date_obj, 'getTimestamp' ) ) {
				        echo esc_html( date( 'd-m-Y', $order_date_obj->getTimestamp() ) );
				    } else {
				        echo esc_html__( 'N/A', 'text-domain' );
				    }
				    ?>
				</td>

				<td class="column-order-actions">
					<?php
					$actions = $order->get_profile_order_actions();

					if ( $actions ) {
						foreach ( $actions as $action ) {
							printf( '<a href="%s">%s</a>', esc_url_raw( $action['url'] ), $action['text'] );
						}
					}
					?>
				</td>
			</tr>
		<?php } ?>
	</tbody>

	<tfoot>
		<tr class="list-table-nav">
			<td colspan="3" class="nav-text"><?php echo esc_html( $query_orders->get_offset_text() ); ?></td>
			<td colspan="2" class="nav-pages"><?php $query_orders->get_nav_numbers( true ); ?></td>
		</tr>
	</tfoot>
</table>
</div>