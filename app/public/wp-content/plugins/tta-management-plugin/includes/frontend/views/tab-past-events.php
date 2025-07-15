<!-- PAST EVENTS -->
<div id="tab-past" class="tta-dashboard-section">
  <h3><?php esc_html_e( 'Your Past Events', 'tta' ); ?></h3>
  <?php
  $user_id = get_current_user_id();
  $summary = tta_get_member_attendance_summary( $user_id );
  echo '<p class="tta-attendance-summary">' . esc_html( sprintf(
      /* translators: 1: attended count, 2: no-show count, 3: amount saved */
      __( 'Attended: %1$d | No-Shows: %2$d | Total Saved: $%3$s', 'tta' ),
      $summary['attended'],
      $summary['no_show'],
      number_format( $summary['savings'], 2 )
  ) ) . '</p>';

  $events = tta_get_member_past_events( $user_id );
  if ( $events ) :
      foreach ( $events as $ev ) :
          $thumb = '';
          if ( ! empty( $ev['image_id'] ) ) {
              $thumb = wp_get_attachment_image( $ev['image_id'], 'medium' );
          } elseif ( ! empty( $ev['page_id'] ) ) {
              $thumb = get_the_post_thumbnail( $ev['page_id'], 'medium' );
          }

          $date_str = date_i18n( get_option( 'date_format' ), strtotime( $ev['date'] ) );
          $time_str = '';
          if ( ! empty( $ev['time'] ) ) {
              $parts     = array_pad( explode( '|', $ev['time'] ), 2, '' );
              $start_fmt = $parts[0] ? date_i18n( get_option( 'time_format' ), strtotime( $parts[0] ) ) : '';
              $end_fmt   = $parts[1] ? date_i18n( get_option( 'time_format' ), strtotime( $parts[1] ) ) : '';
              $time_str  = trim( $start_fmt . ( $end_fmt ? ' – ' . $end_fmt : '' ) );
          }
          ?>
          <div class="tta-upcoming-event">
            <?php if ( $thumb ) : ?>
              <div class="tta-upcoming-thumb"><?php echo $thumb; ?></div>
            <?php endif; ?>
            <div class="tta-upcoming-info">
              <h4><a href="<?php echo esc_url( get_permalink( $ev['page_id'] ) ); ?>"><?php echo esc_html( $ev['name'] ); ?></a></h4>
              <p class="tta-upcoming-date"><?php echo esc_html( $date_str . ( $time_str ? ' – ' . $time_str : '' ) ); ?></p>
              <p class="tta-upcoming-address"><?php echo esc_html( tta_format_address( $ev['address'] ) ); ?></p>
              <p class="tta-upcoming-total"><?php printf( esc_html__( 'Total Paid: $%s', 'tta' ), number_format( $ev['amount'], 2 ) ); ?></p>
            </div>
          </div>
          <?php foreach ( $ev['items'] as $it ) : ?>
            <div class="tta-ticket-details">
              <strong><?php echo esc_html( $it['ticket_name'] ); ?> (<?php echo intval( $it['quantity'] ); ?>)</strong>
              <ul class="tta-attendees">
              <?php foreach ( (array) ( $it['attendees'] ?? [] ) as $att ) : ?>
                <li>
                  <?php echo esc_html( trim( $att['first_name'] . ' ' . $att['last_name'] ) . ' (' . $att['email'] . ')' ); ?>
                  <span class="tta-attendance-status">
                    <?php
                    $st = $att['status'] ?? 'pending';
                    $map = [
                      'checked_in' => __( 'Attended', 'tta' ),
                      'no_show'    => __( 'No-Show', 'tta' ),
                      'pending'    => __( 'Pending', 'tta' ),
                    ];
                    echo esc_html( $map[ $st ] ?? ucwords( str_replace( '_', ' ', $st ) ) );
                    ?>
                  </span>
                </li>
              <?php endforeach; ?>
              </ul>
            </div>
          <?php endforeach; ?>
      <?php endforeach; ?>
  <?php else : ?>
      <p><?php esc_html_e( 'No past events found.', 'tta' ); ?></p>
  <?php endif; ?>
</div>