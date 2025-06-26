<?php
defined( 'ABSPATH' ) || exit();

/**
 * @var LP_Order $order
 */
if ( ! isset( $order ) ) {
    echo esc_html__( 'Invalid order', 'learnpress' );
    return;
}
?>

<h3><?php esc_html_e( 'Transaction Details', 'learnpress' ); ?></h3>

<div class="invoice-wrapper new-invoice-wrapper-cls">
    <div class="custom-table-wrapper">
        <h3>Invoice</h3>
        <table class="lp-list-table order-table-details">
            <thead>
                <tr>
                    <th class="course-name"><?php esc_html_e( 'Course Name', 'learnpress' ); ?></th>
                    <th class="course-total"><?php esc_html_e( 'Total Amount', 'learnpress' ); ?></th>
                </tr>
            </thead>

            <tbody>
                <?php
                $items = $order->get_items();
                if ( $items ) {
                    $currency_symbol = learn_press_get_currency_symbol( $order->get_currency() );

                    foreach ( $items as $item_id => $item ) {
                        if ( ! isset( $item['course_id'] ) ) {
                            continue;
                        }

                        $price = (float) $item['subtotal'];
                        $price_display = ( $price <= 0 ) ? __( 'Free', 'learnpress' ) : learn_press_format_price( $price, $currency_symbol );

                        if ( apply_filters( 'learn-press/order/item-visible', true, $item ) ) {
                            $course = learn_press_get_course( $item['course_id'] );
                            ?>
                            <tr class="<?php echo esc_attr( apply_filters( 'learn-press/order/item-class', 'order-item', $item, $order ) ); ?>">
                                <td class="course-name">
                                    <?php
                                    echo apply_filters(
                                        'learn-press/order/item-name',
                                        sprintf(
                                            '<a href="%s">%s</a>',
                                            esc_url_raw( get_permalink( $item['course_id'] ) ),
                                            esc_html( $item['name'] )
                                        ),
                                        $item,
                                        $order
                                    );
                                    ?>
                                </td>

                                <td class="course-total">
                                    <?php
                                    echo '<span class="course-price">' . wp_kses_post( $price_display ) . '</span>';
                                    ?>
                                </td>
                            </tr>
                            <?php
                        }
                    }
                }
                ?>
                <?php do_action( 'learn-press/order/items-table', $order ); ?>
            </tbody>

            <tfoot>
                <tr>
                    <th scope="row"><?php esc_html_e( 'Subtotal', 'learnpress' ); ?></th>
                    <td><?php echo esc_html( $order->get_formatted_order_subtotal() ); ?></td>
                </tr>

                <?php do_action( 'learn-press/order/items-table-foot', $order ); ?>

                <tr>
                    <th scope="row"><?php esc_html_e( 'Total', 'learnpress' ); ?></th>
                    <td><?php echo esc_html( $order->get_formatted_order_total() ); ?></td>
                </tr>
            </tfoot>
        </table>
    </div>

    <!-- Transaction Details Display -->
    <div class="transaction-details">
        <?php
        $user = $order->get_user();
    	if ( $user && ! is_wp_error( $user ) ) {
    		$user_name = $user->display_name;
    	} 
    	if( $user->display_name == '' && empty( $user->display_name)){
    		$current_user = wp_get_current_user();
    		$user_name = ( $current_user && ! empty( $current_user->display_name ) ) ? $current_user->display_name : esc_html__( 'Guest', 'learnpress' );
    	}    
    	$transaction_id = $order->get_order_key();
        $payment_method = $order->get_payment_method_title();
        $payment_status = ucfirst( $order->get_status() );
        $order_date = get_post_field( 'post_date', $order->get_id() );
        $formatted_date = $order_date ? date( 'F j, Y', strtotime( $order_date ) ) : esc_html__( 'Date not available', 'learnpress' );

        // Assume subject and duration are course meta. You may need to replace meta keys if needed.
        $course_subject = '';
        $course_duration = '';

        if ( $items ) {
            foreach ( $items as $item ) {
                if ( isset( $item['course_id'] ) ) {
                    $course_id = $item['course_id'];
                    $course_subject = get_post_meta( $course_id, '_lp_subject', true ); // Example meta key
                    $course_duration = get_post_meta( $course_id, '_lp_duration', true ); // Example meta key
                }
            }
        }

        // Discounts and final amount
        $subtotal = $order->get_subtotal();
        $total = $order->get_total();
        $discount = $subtotal > $total ? ( ( ( $subtotal - $total ) / $subtotal ) * 100 ) : 0;
        ?>
        <p><strong><?php esc_html_e( 'Student Name:', 'learnpress' ); ?></strong> <?php echo esc_html( $user_name ); ?></p>
        <p><strong><?php esc_html_e( 'Transaction ID:', 'learnpress' ); ?></strong> <?php echo esc_html( $transaction_id ); ?></p>
        <p><strong><?php esc_html_e( 'Payment Method:', 'learnpress' ); ?></strong> <?php
            if ( (float) $order->get_total() == 0 ) {
                echo esc_html__( 'Free', 'learnpress' );
            } else {
                echo esc_html( $payment_method );
            }
        ?></p>
        <p><strong><?php esc_html_e( 'Payment Status:', 'learnpress' ); ?></strong> <?php echo esc_html( $payment_status ); ?></p>
        <p><strong><?php esc_html_e( 'Date:', 'learnpress' ); ?></strong> <?php echo esc_html( $formatted_date ); ?></p>

        <?php if ( $items ) {
            foreach ( $items as $item ) {
                if ( isset( $item['course_id'] ) ) {
                    $course = get_the_title( $item['course_id'] );
                    ?>
                    <?php
                    if ( $course_subject ) {
                        echo '<p><strong>' . esc_html__( 'Subject:', 'learnpress' ) . '</strong> ' . esc_html( $course_subject ) . '</p>';
                    }
                    if ( $course_duration ) {
                        echo '<p><strong>' . esc_html__( 'Course Duration:', 'learnpress' ) . '</strong> ' . esc_html( $course_duration ) . '</p>';
                    }
                }
            }
        } ?>

        <p><strong><?php esc_html_e( 'Amount:', 'learnpress' ); ?></strong> <?php echo esc_html( learn_press_format_price( $subtotal ) ); ?></p>

        <?php if ( $discount > 0 ) : ?>
            <p><strong><?php esc_html_e( 'Discount:', 'learnpress' ); ?></strong> <?php echo esc_html( number_format( $discount, 2 ) ) . '%'; ?></p>
        <?php endif; ?>

        <p><strong><?php esc_html_e( 'Final Amount:', 'learnpress' ); ?></strong> <?php echo esc_html( learn_press_format_price( $total ) ); ?></p>
        
    </div>

</div>

<!-- Download Invoice Button -->
        <?php
        $order_id = $order->get_id();
        $invoice_url = home_url('/lp-download-invoice/' . $order_id);
        ?>
        <div class="new-order-downloaded-here">
        <p class="paragraph-type-cls">
            <strong><?php esc_html_e('Download Invoice:', 'learnpress'); ?></strong>
            <button type="button" class="lp-download-invoice-btn new-order-details-cls-download"><?php esc_html_e('Download Invoice (PDF)', 'learnpress'); ?></button>
        </p>
        </div>

<?php do_action( 'learn-press/order/after-table-details', $order ); ?>

<!-- Load html2canvas and jsPDF -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>

<script type="text/javascript">
    jQuery(function($){
        $('.lp-download-invoice-btn').on('click', function () {
            const doc = window.jspdf.jsPDF();
            const element = document.querySelector('.invoice-wrapper'); // Your invoice wrapper

            html2canvas(element).then(canvas => {
                const imgData = canvas.toDataURL('image/png');
                const imgProps = doc.getImageProperties(imgData);
                const pdfWidth = doc.internal.pageSize.getWidth();
                const pdfHeight = (imgProps.height * pdfWidth) / imgProps.width;

                doc.addImage(imgData, 'PNG', 0, 0, pdfWidth, pdfHeight);
                doc.save('invoice-<?php echo $order->get_id(); ?>.pdf');
            });
        });
    });

</script>
