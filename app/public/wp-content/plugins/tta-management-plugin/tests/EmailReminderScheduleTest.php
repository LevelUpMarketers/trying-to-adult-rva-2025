<?php
use PHPUnit\Framework\TestCase;
if ( ! defined( 'ARRAY_A' ) ) { define( 'ARRAY_A', 'ARRAY_A' ); }

class EmailReminderScheduleTest extends TestCase {
    protected function setUp(): void {
        $GLOBALS['scheduled'] = [];
        if ( ! defined( 'ABSPATH' ) ) { define( 'ABSPATH', sys_get_temp_dir() . '/wp/' ); }
        if ( ! defined( 'DAY_IN_SECONDS' ) ) { define( 'DAY_IN_SECONDS', 86400 ); }
        if ( ! defined( 'HOUR_IN_SECONDS' ) ) { define( 'HOUR_IN_SECONDS', 3600 ); }
        if ( ! function_exists( 'wp_schedule_single_event' ) ) {
            function wp_schedule_single_event( $ts, $hook, $args = [] ) {
                $GLOBALS['scheduled'][] = [ $ts, $hook, $args ];
            }
        }
        if ( ! function_exists( 'wp_next_scheduled' ) ) {
            function wp_next_scheduled( $hook, $args = [] ) {
                return false;
            }
        }
        $GLOBALS['wpdb'] = new class {
            public $prefix = '';
            public function get_var() { return 'ute_1'; }
            public function get_row() {
                return [
                    'id' => 1,
                    'name' => 'Test',
                    'date' => '2030-08-15',
                    'time' => '18:00|20:00',
                    'address' => '',
                    'page_id' => 0,
                    'type' => '',
                    'venuename' => '',
                    'venueurl' => '',
                    'baseeventcost' => 0,
                    'discountedmembercost' => 0,
                    'premiummembercost' => 0,
                    'host_notes' => '',
                ];
            }
            public function prepare( $query, ...$args ) { return $query; }
        };
        if ( ! function_exists( 'sanitize_text_field' ) ) {
            function sanitize_text_field( $v ) { return $v; }
        }
        if ( ! function_exists( 'sanitize_textarea_field' ) ) {
            function sanitize_textarea_field( $v ) { return $v; }
        }
        if ( ! function_exists( 'esc_url_raw' ) ) {
            function esc_url_raw( $v ) { return $v; }
        }
        if ( ! function_exists( 'get_permalink' ) ) {
            function get_permalink( $id ) { return ''; }
        }
    }

    protected function tearDown(): void {
        $GLOBALS['scheduled'] = [];
    }

    public function test_schedule_event_emails_creates_six_events() {
        require_once __DIR__ . '/../includes/email/class-email-reminders.php';
        TTA_Email_Reminders::schedule_event_emails( 1 );
        $this->assertCount( 6, $GLOBALS['scheduled'] );
        $hooks = array_column( $GLOBALS['scheduled'], 1 );
        $this->assertContains( 'tta_attendee_reminder_email', $hooks );
        $this->assertContains( 'tta_host_reminder_email', $hooks );
        $this->assertContains( 'tta_volunteer_reminder_email', $hooks );
    }
}
