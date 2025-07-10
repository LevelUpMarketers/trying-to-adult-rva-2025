<?php
use PHPUnit\Framework\TestCase;
if (!defined('ARRAY_A')) { define('ARRAY_A', 'ARRAY_A'); }

class EmailTokensTest extends TestCase {
    protected function setUp(): void {
        if (!function_exists('sanitize_text_field')) {
            function sanitize_text_field($v){ return is_string($v)?trim($v):$v; }
        }
        if (!function_exists('sanitize_email')) {
            function sanitize_email($v){ return trim($v); }
        }
        if (!function_exists('home_url')) {
            function home_url($p='', $t='relative'){ return '/'.ltrim($p,'/'); }
        }
        require_once __DIR__ . '/../includes/helpers.php';
        require_once __DIR__ . '/../includes/email/class-email-handler.php';
    }

    public function test_build_tokens_includes_refund_event_name() {
        $handler = TTA_Email_Handler::get_instance();
        $reflect = new \ReflectionClass($handler);
        $method = $reflect->getMethod('build_tokens');
        $method->setAccessible(true);

        $event = [ 'name' => 'Sample Event', 'date' => '2025-06-30', 'time' => '18:00|20:00' ];
        $member = [ 'first_name' => 'Bob', 'last_name' => 'Smith', 'user_email' => 'bob@example.com',
                    'member' => [ 'phone' => '', 'member_type' => '' ], 'membership_level' => '' ];
        $attendees = [];
        $refund = [ 'ticket_name' => 'General', 'amount' => '10.00' ];

        $tokens = $method->invoke($handler, $event, $member, $attendees, $refund);

        $this->assertArrayHasKey('{refund_event_name}', $tokens);
        $this->assertSame('Sample Event', $tokens['{refund_event_name}']);
    }
}
