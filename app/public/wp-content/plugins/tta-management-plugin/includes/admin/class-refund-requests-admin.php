<?php
if ( ! defined( 'ABSPATH' ) ) { exit; }
class TTA_Refund_Requests_Admin {
    public static function get_instance() {
        static $inst;
        return $inst ?: $inst = new self();
    }

    private function __construct() {
        add_action( 'admin_menu', [ $this, 'register_menu' ] );
    }

    public function register_menu() {
        add_menu_page(
            'TTA Refund Requests',
            'TTA Refund Requests',
            'manage_options',
            'tta-refund-requests',
            [ $this, 'render_page' ],
            'dashicons-money-alt',
            10
        );
    }

    public function render_page() {
        echo '<div class="wrap"><h1>' . esc_html__( 'Refund Requests', 'tta' ) . '</h1>';
        $requests = tta_get_refund_requests();
        if ( $requests ) {
            echo '<table class="widefat fixed striped"><thead><tr>';
            echo '<th>' . esc_html__( 'Requested', 'tta' ) . '</th>';
            echo '<th>' . esc_html__( 'Name', 'tta' ) . '</th>';
            echo '<th>' . esc_html__( 'Event', 'tta' ) . '</th>';
            echo '<th>' . esc_html__( 'Paid', 'tta' ) . '</th>';
            echo '<th>' . esc_html__( 'Actions', 'tta' ) . '</th>';
            echo '</tr></thead><tbody>';

            foreach ( $requests as $req ) {
                $attendees = tta_get_refund_request_attendees( $req['transaction_id'], $req['event_id'], $req['ticket_id'] );
                if ( ! $attendees ) {
                    continue;
                }

                $event_name = esc_html( $req['event_name'] );
                $event_link = $req['event_url'] ? '<a href="' . esc_url( $req['event_url'] ) . '">' . $event_name . '</a>' : $event_name;
                $date = esc_html( date_i18n( 'F j, Y g:i a', strtotime( $req['date'] ) ) );

                foreach ( $attendees as $att ) {
                    $name = esc_html( trim( $att['first_name'] . ' ' . $att['last_name'] ) );
                    $paid = $att['amount_paid'] ? sprintf( esc_html__( '$%s', 'tta' ), number_format_i18n( $att['amount_paid'], 2 ) ) : '&ndash;';
                    echo '<tr data-attendee-id="' . esc_attr( $att['id'] ) . '">';
                    echo '<td>' . $date . '</td>';
                    echo '<td>' . $name . '</td>';
                    echo '<td>' . $event_link . '</td>';
                    echo '<td>' . $paid . '</td>';
                    echo '<td>';
                    echo '<input type="number" class="tta-refund-amount" step="0.01" style="width:70px" placeholder="' . esc_attr__( 'Full', 'tta' ) . '"> ';
                    echo '<button type="button" class="tta-refund-cancel-attendee" data-attendee="' . esc_attr( $att['id'] ) . '">' . esc_html__( 'Refund & Cancel Attendance', 'tta' ) . '</button> ';
                    echo '<button type="button" class="tta-refund-keep-attendee" data-attendee="' . esc_attr( $att['id'] ) . '">' . esc_html__( 'Refund & Keep Attendance', 'tta' ) . '</button> ';
                    echo '<button type="button" class="tta-cancel-attendee" data-attendee="' . esc_attr( $att['id'] ) . '">' . esc_html__( 'Cancel Attendance (No Refund)', 'tta' ) . '</button>';
                    echo '</td>';
                    echo '</tr>';
                }
            }

            echo '</tbody></table>';
        } else {
            echo '<p>' . esc_html__( 'No refund requests found.', 'tta' ) . '</p>';
        }
        echo '</div>';
    }
}

TTA_Refund_Requests_Admin::get_instance();
