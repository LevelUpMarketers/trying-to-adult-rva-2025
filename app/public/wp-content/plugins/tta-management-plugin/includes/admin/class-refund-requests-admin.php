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
            9
        );
    }

    public function render_page() {
        echo '<div class="wrap"><h1>' . esc_html__( 'Refund Requests', 'tta' ) . '</h1>';
        $rows = tta_get_refund_requests();
        if ( $rows ) {
            foreach ( $rows as $r ) {
                $attendees = tta_get_refund_request_attendees( $r['transaction_id'], $r['event_id'] );
                $summary   = tta_get_member_history_summary( $r['member_id'] );

                $member = esc_html( $r['member_name'] );
                $event  = esc_html( $r['event_name'] );
                $url    = $r['event_url'] ? '<a href="' . esc_url( $r['event_url'] ) . '">' . $event . '</a>' : $event;

                echo '<details class="tta-refund-request"><summary>' . esc_html( date_i18n( 'F j, Y g:i a', strtotime( $r['date'] ) ) ) . ' - ' . $member . '</summary>';
                echo '<div class="tta-refund-details">';
                echo '<p><strong>' . esc_html__( 'Event:', 'tta' ) . '</strong> ' . $url . '</p>';
                echo '<p><strong>' . esc_html__( 'Reason:', 'tta' ) . '</strong> ' . esc_html( $r['reason'] ) . '</p>';
                echo '<p><em>' . sprintf( esc_html__( 'Past refund requests: %d&nbsp;|&nbsp; Cancellations: %d &nbsp;|&nbsp; No-shows: %d', 'tta' ), $summary['refunds'], $summary['cancellations'], $summary['no_show'] ) . '</em></p>';

                if ( $attendees ) {
                    echo '<div class="tta-wl-info-wrapper">';
                    echo '<table class="tta-wl-info-table"><thead><tr>';
                    echo '<th><span class="tta-tooltip-icon" data-tooltip="' . esc_attr__( 'Attendee first and last name.', 'tta' ) . '"><img src="' . esc_url( TTA_PLUGIN_URL . 'assets/images/admin/question.svg' ) . '" alt="?"></span>' . esc_html__( 'Name', 'tta' ) . '</th>';
                    echo '<th><span class="tta-tooltip-icon" data-tooltip="' . esc_attr__( 'Attendee email address.', 'tta' ) . '"><img src="' . esc_url( TTA_PLUGIN_URL . 'assets/images/admin/question.svg' ) . '" alt="?"></span>' . esc_html__( 'Email', 'tta' ) . '</th>';
                    echo '<th><span class="tta-tooltip-icon" data-tooltip="' . esc_attr__( 'Phone number provided at checkout.', 'tta' ) . '"><img src="' . esc_url( TTA_PLUGIN_URL . 'assets/images/admin/question.svg' ) . '" alt="?"></span>' . esc_html__( 'Phone', 'tta' ) . '</th>';
                    echo '<th><span class="tta-tooltip-icon" data-tooltip="' . esc_attr__( 'Amount charged for this ticket.', 'tta' ) . '"><img src="' . esc_url( TTA_PLUGIN_URL . 'assets/images/admin/question.svg' ) . '" alt="?"></span>' . esc_html__( 'Paid', 'tta' ) . '</th>';
                    echo '<th><span class="tta-tooltip-icon" data-tooltip="' . esc_attr__( 'Specify a partial refund amount.', 'tta' ) . '"><img src="' . esc_url( TTA_PLUGIN_URL . 'assets/images/admin/question.svg' ) . '" alt="?"></span>' . esc_html__( 'Refund $', 'tta' ) . '</th>';
                    echo '<th><span class="tta-tooltip-icon" data-tooltip="' . esc_attr__( 'Available actions for the attendee.', 'tta' ) . '"><img src="' . esc_url( TTA_PLUGIN_URL . 'assets/images/admin/question.svg' ) . '" alt="?"></span>' . esc_html__( 'Actions', 'tta' ) . '</th>';
                    echo '</tr></thead><tbody>';

                    $tx_row = reset( $attendees );
                    if ( $tx_row ) {
                        echo '<tr class="tta-transaction-group"><td colspan="6" style="background:#f9f9f9;font-weight:bold;">' . sprintf( esc_html__( 'Transaction ID %s - %s', 'tta' ), esc_html( $tx_row['gateway_id'] ), esc_html( mysql2date( 'n/j/Y g:i a', $tx_row['created_at'] ) ) ) . '</td></tr>';
                    }

                    foreach ( $attendees as $a ) {
                        $paid = $a['amount_paid'] ? sprintf( esc_html__( '$%s', 'tta' ), number_format_i18n( $a['amount_paid'], 2 ) ) : '&ndash;';
                        echo '<tr data-attendee-id="' . esc_attr( $a['id'] ) . '">';
                        echo '<td>' . esc_html( trim( $a['first_name'] . ' ' . $a['last_name'] ) ) . '</td>';
                        echo '<td>' . esc_html( $a['email'] ) . '</td>';
                        echo '<td>' . esc_html( $a['phone'] ) . '</td>';
                        echo '<td>' . $paid . '</td>';
                        echo '<td><input type="number" class="tta-refund-amount" step="0.01" style="width:70px" placeholder="' . esc_attr__( 'Full', 'tta' ) . '"></td>';
                        echo '<td><button type="button" class="tta-refund-cancel-attendee" data-attendee="' . esc_attr( $a['id'] ) . '">' . esc_html__( 'Refund & Cancel Attendance', 'tta' ) . '</button> ';
                        echo '<button type="button" class="tta-refund-keep-attendee" data-attendee="' . esc_attr( $a['id'] ) . '">' . esc_html__( 'Refund & Keep Attendance', 'tta' ) . '</button> ';
                        echo '<button type="button" class="tta-cancel-attendee" data-attendee="' . esc_attr( $a['id'] ) . '">' . esc_html__( 'Cancel Attendance (No Refund)', 'tta' ) . '</button></td>';
                        echo '</tr>';
                    }

                    echo '</tbody></table></div></div></details>';
                }
            }
        } else {
            echo '<p>' . esc_html__( 'No refund requests found.', 'tta' ) . '</p>';
        }
        echo '</div>';
    }
}

TTA_Refund_Requests_Admin::get_instance();
