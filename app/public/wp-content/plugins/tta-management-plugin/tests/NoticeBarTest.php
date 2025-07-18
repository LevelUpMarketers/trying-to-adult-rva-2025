<?php
use PHPUnit\Framework\TestCase;

class NoticeBarTest extends TestCase {
    public function test_determine_message_picks_first() {
        require_once __DIR__ . '/../includes/frontend/class-tta-notice-bar.php';
        $msgs = [ [], [ 'html' => 'Hi', 'expires' => 5 ] ];
        $res  = TTA_Notice_Bar::determine_message( $msgs );
        $this->assertSame( 'Hi', $res['html'] );
        $this->assertSame( 5, $res['expires'] );
    }

    public function test_determine_message_empty() {
        require_once __DIR__ . '/../includes/frontend/class-tta-notice-bar.php';
        $res = TTA_Notice_Bar::determine_message( [] );
        $this->assertSame( '', $res['html'] );
        $this->assertSame( 0, $res['expires'] );
    }
}
