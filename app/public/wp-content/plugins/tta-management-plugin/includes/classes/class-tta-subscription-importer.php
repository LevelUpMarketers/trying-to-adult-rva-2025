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

            $transactions = $api->find_transactions_by_email( $email );
            if ( $transactions ) {
                foreach ( $transactions as $txn ) {
                    $results[] = [
                        'email'             => $email,
                        'membership'        => $member,
                        'status'            => 'found',
                        'transaction_id'    => $txn['id'],
                        'amount'            => $txn['amount'],
                        'date'              => $txn['date'],
                        'transaction_status'=> $txn['transaction_status'],
                        'invoice'           => $txn['invoice'],
                        'details'           => $txn['details'],
                    ];
                }
            } else {
                $results[] = [
                    'email'      => $email,
                    'membership' => $member,
                    'status'     => 'not_found',
                ];
            }
        }
        fclose( $handle );
        return $results;
    }
}
