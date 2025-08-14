<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Import ARB subscriptions from a CSV.
 */
class TTA_Subscription_Importer {
    /**
     * Process a CSV file of email and membership level rows.
     *
     * @param string               $file    Uploaded CSV path.
     * @param bool                 $dry_run Whether to skip creating subscriptions.
     * @param TTA_AuthorizeNet_API $api     Authorize.Net API instance.
     * @return array[] Results per row.
     */
    public static function process_csv( $file, $dry_run, TTA_AuthorizeNet_API $api ) {
        $results = [];
        if ( ! file_exists( $file ) ) {
            return $results;
        }

        $handle = fopen( $file, 'r' );
        if ( false === $handle ) {
            return $results;
        }

        // Skip header row.
        fgetcsv( $handle );
        while ( ( $data = fgetcsv( $handle ) ) !== false ) {
            $email  = sanitize_email( $data[0] ?? '' );
            $member = sanitize_text_field( $data[1] ?? '' );
            if ( empty( $email ) ) {
                continue;
            }

            $row = [
                'email'      => $email,
                'membership' => $member,
            ];

            $txn = $api->find_transaction_by_email_and_description( $email, TTA_SUBSCRIPTION_RENEWAL_DESCRIPTION );
            if ( $txn ) {
                $row['transaction_id'] = $txn['id'];
                $row['amount']         = $txn['amount'];
                $row['date']           = $txn['date'];
                $row['details']        = $txn['details'];
                if ( $dry_run ) {
                    $row['status'] = 'found';
                } else {
                    $start = ( new DateTime( $txn['date'] ) )->modify( '+1 month' )->format( 'Y-m-d' );
                    $res   = $api->create_subscription_from_transaction( $txn['id'], $txn['amount'], $member, 'Initial 2025 Website Launch', $start );
                    if ( $res['success'] ) {
                        $row['status']         = 'created';
                        $row['subscription_id'] = $res['subscription_id'];
                    } else {
                        $row['status'] = 'error';
                        $row['error']  = $res['error'] ?? '';
                    }
                }
            } else {
                $row['status'] = 'not_found';
            }
            $results[] = $row;
        }
        fclose( $handle );
        return $results;
    }
}
