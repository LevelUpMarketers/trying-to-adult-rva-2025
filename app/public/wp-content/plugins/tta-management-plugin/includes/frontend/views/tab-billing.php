<!-- BILLING & MEMBERSHIP INFO -->
<div id="tab-billing" class="tta-dashboard-section">
  <h3><?php esc_html_e( 'Billing & Membership Info', 'tta' ); ?></h3>
  <?php
  $level  = strtolower( $member['membership_level'] ?? 'free' );
  $status = strtolower( $member['subscription_status'] ?? 'active' );
  $sub_id = $member['subscription_id'] ?? '';
  $last4  = $sub_id ? tta_get_subscription_card_last4( $sub_id ) : '';
  $cancel = ( 'cancelled' === $status ) ? tta_get_last_membership_cancellation( get_current_user_id() ) : null;
  if ( 'free' === $level && ! in_array( $status, array( 'cancelled', 'paymentproblem' ), true ) ) :
  ?>
    <p><?php esc_html_e( 'You do not currently have a paid membership.', 'tta' ); ?></p>
  <?php elseif ( 'cancelled' === $status ) :
    $prev_level = $cancel['level'] ?? 'basic';
    ?>
    <p>
      <?php
      printf(
        esc_html__( 'Your %1$s membership was cancelled on %2$s by %3$s.', 'tta' ),
        esc_html( tta_get_membership_label( $prev_level ) ),
        esc_html( date_i18n( 'F j, Y', strtotime( $cancel['date'] ?? current_time( 'mysql' ) ) ) ),
        ( ( $cancel['by'] ?? 'member' ) === 'admin' ) ? esc_html__( 'an administrator', 'tta' ) : esc_html__( 'you', 'tta' )
      );
      ?>
    </p>
    <?php if ( ! empty( $cancel['card_last4'] ) ) : ?>
      <p><?php esc_html_e( 'Last Card Used:', 'tta' ); ?> **** <?php echo esc_html( $cancel['card_last4'] ); ?></p>
    <?php endif; ?>
    <p>
      <a class="button" href="<?php echo esc_url( home_url( '/become-a-member/?level=' . urlencode( $prev_level ) ) ); ?>">
        <?php esc_html_e( 'Reactivate Membership', 'tta' ); ?>
      </a>
    </p>
  <?php elseif ( 'paymentproblem' === $status ) :
    $info = $sub_id ? tta_get_subscription_status_info( $sub_id ) : [];
    ?>
    <p><?php esc_html_e( 'There was a problem processing your membership payment.', 'tta' ); ?></p>
    <?php if ( ! empty( $info['status'] ) ) : ?>
      <p><?php printf( esc_html__( 'Gateway status: %s', 'tta' ), esc_html( ucfirst( $info['status'] ) ) ); ?></p>
    <?php endif; ?>
    <?php if ( ! empty( $info['last4'] ) ) : ?>
      <p><?php esc_html_e( 'Card on File:', 'tta' ); ?> **** <?php echo esc_html( $info['last4'] ); ?></p>
    <?php endif; ?>
    <p><?php esc_html_e( 'Please update your payment method below to restore your membership.', 'tta' ); ?></p>
    <?php $price = tta_get_membership_price( get_user_meta( get_current_user_id(), 'tta_prev_level', true ) ?: 'basic' ); ?>
    <p><?php esc_html_e( 'Status:', 'tta' ); ?> <span id="tta-membership-status"><?php echo esc_html( ucfirst( $status ) ); ?></span></p>
    <?php if ( 'cancelled' !== $status ) : ?>
      <form id="tta-update-card-form" method="post" action="<?php echo esc_url( admin_url( 'admin-ajax.php' ) ); ?>" class="tta-update-card-form">
        <?php wp_nonce_field( 'tta_member_front_update', 'nonce' ); ?>
        <input type="hidden" name="action" value="tta_update_payment" />
        <p>
          <label>
            <?php esc_html_e( 'Card Number', 'tta' ); ?><br />
            <input type="text" name="card_number" placeholder="&#8226;&#8226;&#8226;&#8226; &#8226;&#8226;&#8226;&#8226; &#8226;&#8226;&#8226;&#8226; &#8226;&#8226;&#8226;&#8226;" required />
          </label>
        </p>
        <p>
          <label>
            <?php esc_html_e( 'Expiration', 'tta' ); ?><br />
            <input type="text" class="tta-card-exp" name="exp_date" placeholder="MM/YY" required maxlength="5" pattern="\d{2}/\d{2}" inputmode="numeric" />
          </label>
        </p>
        <p>
          <label>
            <?php esc_html_e( 'CVC', 'tta' ); ?><br />
            <input type="text" name="card_cvc" placeholder="123" required />
          </label>
        </p>
        <p class="tta-submit-wrap">
          <button type="submit" class="button"><?php esc_html_e( 'Update Card', 'tta' ); ?></button>
          <span class="tta-progress-spinner">
            <img class="tta-admin-progress-spinner-svg" src="<?php echo esc_url( TTA_PLUGIN_URL . 'assets/images/admin/loading.svg' ); ?>" alt="<?php esc_attr_e( 'Loading…', 'tta' ); ?>" />
          </span>
          <span class="tta-admin-progress-response"><p class="tta-admin-progress-response-p"></p></span>
        </p>
      </form>
    <?php endif; ?>
  <?php else :
    $price = tta_get_membership_price( $level );
    ?>
    <p>
      <strong id="tta-membership-level"><?php echo esc_html( tta_get_membership_label( $level ) ); ?></strong>
      – $<?php echo number_format( $price, 2 ); ?> <?php esc_html_e( 'Per Month', 'tta' ); ?>
    </p>
    <p><?php esc_html_e( 'Status:', 'tta' ); ?> <span id="tta-membership-status"><?php echo esc_html( ucfirst( $status ) ); ?></span></p>
    <?php if ( $last4 ) : ?>
      <p><?php esc_html_e( 'Current Card:', 'tta' ); ?> <span id="tta-card-last4">**** <?php echo esc_html( $last4 ); ?></span></p>
    <?php endif; ?>
    <?php if ( 'cancelled' !== $status ) : ?>
      <form id="tta-cancel-membership-form" method="post" action="<?php echo esc_url( admin_url( 'admin-ajax.php' ) ); ?>">
        <?php wp_nonce_field( 'tta_member_front_update', 'nonce' ); ?>
        <input type="hidden" name="action" value="tta_cancel_membership" />
        <p class="tta-submit-wrap">
          <button type="submit" class="button">
            <?php esc_html_e( 'Cancel Membership', 'tta' ); ?>
          </button>
          <span class="tta-progress-spinner">
            <img class="tta-admin-progress-spinner-svg" src="<?php echo esc_url( TTA_PLUGIN_URL . 'assets/images/admin/loading.svg' ); ?>" alt="<?php esc_attr_e( 'Loading…', 'tta' ); ?>" />
          </span>
          <span class="tta-admin-progress-response"><p class="tta-admin-progress-response-p"></p></span>
        </p>
      </form>

      <form id="tta-update-card-form" method="post" action="<?php echo esc_url( admin_url( 'admin-ajax.php' ) ); ?>" class="tta-update-card-form">
        <?php wp_nonce_field( 'tta_member_front_update', 'nonce' ); ?>
        <input type="hidden" name="action" value="tta_update_payment" />
        <p>
          <label>
            <?php esc_html_e( 'Card Number', 'tta' ); ?><br />
            <input type="text" name="card_number" placeholder="&#8226;&#8226;&#8226;&#8226; &#8226;&#8226;&#8226;&#8226; &#8226;&#8226;&#8226;&#8226; &#8226;&#8226;&#8226;&#8226;" required />
          </label>
        </p>
        <p>
          <label>
            <?php esc_html_e( 'Expiration', 'tta' ); ?><br />
            <input type="text" class="tta-card-exp" name="exp_date" placeholder="MM/YY" required maxlength="5" pattern="\d{2}/\d{2}" inputmode="numeric" />
          </label>
        </p>
        <p>
          <label>
            <?php esc_html_e( 'CVC', 'tta' ); ?><br />
            <input type="text" name="card_cvc" placeholder="123" required />
          </label>
        </p>
        <p>
          <label>
            <?php esc_html_e( 'Billing First Name', 'tta' ); ?><br />
            <input type="text" name="bill_first" value="<?php echo esc_attr( $member['first_name'] ); ?>" required />
          </label>
        </p>
        <p>
          <label>
            <?php esc_html_e( 'Billing Last Name', 'tta' ); ?><br />
            <input type="text" name="bill_last" value="<?php echo esc_attr( $member['last_name'] ); ?>" required />
          </label>
        </p>
        <p>
          <label>
            <?php esc_html_e( 'Street Address', 'tta' ); ?><br />
            <input type="text" name="bill_address" value="<?php echo esc_attr( $street_address ); ?>" required />
          </label>
        </p>
        <p>
          <label>
            <?php esc_html_e( 'City', 'tta' ); ?><br />
            <input type="text" name="bill_city" value="<?php echo esc_attr( $city ); ?>" required />
          </label>
        </p>
        <p>
          <label>
            <?php esc_html_e( 'State', 'tta' ); ?><br />
            <select name="bill_state">
              <?php foreach ( tta_get_us_states() as $abbr => $name ) : ?>
                <option value="<?php echo esc_attr( $abbr ); ?>" <?php selected( $state, $abbr ); ?>><?php echo esc_html( $name ); ?></option>
              <?php endforeach; ?>
            </select>
          </label>
        </p>
        <p>
          <label>
            <?php esc_html_e( 'ZIP', 'tta' ); ?><br />
            <input type="text" name="bill_zip" value="<?php echo esc_attr( $zip ); ?>" required />
          </label>
        </p>
        <p class="tta-submit-wrap">
          <button type="submit" class="button"><?php esc_html_e( 'Update Card', 'tta' ); ?></button>
          <span class="tta-progress-spinner">
            <img class="tta-admin-progress-spinner-svg" src="<?php echo esc_url( TTA_PLUGIN_URL . 'assets/images/admin/loading.svg' ); ?>" alt="<?php esc_attr_e( 'Loading…', 'tta' ); ?>" />
          </span>
          <span class="tta-admin-progress-response"><p class="tta-admin-progress-response-p"></p></span>
        </p>
      </form>
    <?php endif; ?>
  <?php endif; ?>

  <?php
  $history = tta_get_member_billing_history( get_current_user_id() );
  if ( $history ) : ?>
    <h4><?php esc_html_e( 'Payment History', 'tta' ); ?></h4>
    <table class="widefat striped tta-billing-history">
      <thead>
        <tr>
          <th><?php esc_html_e( 'Date', 'tta' ); ?></th>
          <th><?php esc_html_e( 'Item', 'tta' ); ?></th>
          <th><?php esc_html_e( 'Amount', 'tta' ); ?></th>
          <th><?php esc_html_e( 'Type', 'tta' ); ?></th>
          <th><?php esc_html_e( 'Payment Method', 'tta' ); ?></th>
        </tr>
      </thead>
      <tbody>
        <?php foreach ( $history as $row ) : ?>
          <tr>
            <td><?php echo esc_html( date_i18n( 'F j, Y', strtotime( $row['date'] ) ) ); ?></td>
            <td>
              <?php if ( ! empty( $row['url'] ) ) : ?>
                <a href="<?php echo esc_url( $row['url'] ); ?>">
                  <?php echo esc_html( $row['description'] ); ?>
                </a>
              <?php else : ?>
                <?php echo esc_html( $row['description'] ); ?>
              <?php endif; ?>
            </td>
            <td>$<?php echo esc_html( number_format( $row['amount'], 2 ) ); ?></td>
            <td><?php echo esc_html( ucwords( $row['type'] ) ); ?></td>
            <td><?php echo esc_html( $row['method'] ); ?></td>
          </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  <?php else : ?>
    <p><?php esc_html_e( 'No transactions found.', 'tta' ); ?></p>
  <?php endif; ?>
</div>
