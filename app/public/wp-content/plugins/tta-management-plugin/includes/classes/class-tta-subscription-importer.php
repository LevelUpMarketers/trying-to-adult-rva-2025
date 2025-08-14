<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Import ARB subscriptions from a CSV.
 */
class TTA_Subscription_Importer {
    /**
     * Process a CSV file of first name, last name, email, and membership level rows.
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
            $first  = sanitize_text_field( $data[0] ?? '' );
            $last   = sanitize_text_field( $data[1] ?? '' );
            $email  = sanitize_email( $data[2] ?? '' );
            $member = sanitize_text_field( $data[3] ?? '' );
            if ( empty( $first ) || empty( $last ) ) {
                continue;
            }

            $row         = [
                'first'      => $first,
                'last'       => $last,
                'email'      => $email,
                'membership' => $member,
            ];
            $descriptions = [
                TTA_PREMIUM_SUBSCRIPTION_DESCRIPTION,
                TTA_BASIC_SUBSCRIPTION_DESCRIPTION,
            ];
            $txn         = $api->find_transaction_by_name_and_invoice_description( $first, $last, $descriptions );
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
