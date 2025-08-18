# Trying To Adult Management Plugin

This plugin integrates with Authorize.Net for payment processing. The plugin's files live in `app/public/wp-content/plugins/tta-management-plugin`. Authorize.Net and SendGrid API keys are configured in the WordPress dashboard under **TTA Settings → API Settings**. The values are stored in the database using WordPress options and can alternatively be supplied via environment variables (`TTA_AUTHNET_LOGIN_ID`, `TTA_AUTHNET_TRANSACTION_KEY`, `TTA_SENDGRID_API_KEY`). The API Settings tab also lets administrators upload a two‑column CSV (`email`,`membership`) to list recent Authorize.Net transactions for each address. For every row the plugin searches the last three months of settled batches and outputs each matching transaction's ID, amount, date, status, invoice number, and description. Results are capped at the most recent twenty transactions to avoid timeouts. The lookup is read‑only and the optional **Dry run** checkbox simply performs the search without side effects.

SMS notifications use Twilio. Similar to the Authorize.Net credentials, you can create a `twilio-config.php` file or set environment variables:

```
define('TTA_TWILIO_SID', 'your_account_sid');
define('TTA_TWILIO_TOKEN', 'your_auth_token');
define('TTA_TWILIO_FROM', '+15555555555');
```
If any of these constants are missing the plugin will warn administrators and SMS
messages will not be sent.

## Development Quick Start

1. Install PHP and Composer.
2. Run `composer install` from the project root.
3. Run `composer install` inside `app/public/wp-content/plugins/tta-management-plugin`.
4. Execute `vendor/bin/phpunit` to ensure all tests pass.

## Documentation

- [Cart and Checkout Flow](docs/CartFlow.md)
- [Object Caching](docs/ObjectCaching.md)
  - Plugin caches can now be cleared reliably even on hosts with persistent object caching.
- [Alert Bar](docs/AlertBar.md)
- [Admin Bar Visibility](docs/AdminBarVisibility.md)
- [Input Sanitization Helpers](docs/InputSanitization.md)
- [Authorize.Net Error Codes](docs/AuthorizeNetErrors.md)
- [Address Helper Functions](docs/AddressHelpers.md)
- [Event Page Context](docs/EventPage.md)
- [Homepage Shortcode](docs/HomepageShortcode.md)
- [Event Hosts & Volunteers](docs/EventPage.md#event-hosts-and-volunteers)
- [Event Creation Admin](docs/EventCreationAdmin.md)
- [Event Type Options](docs/EventTypes.md)
- [Testing Information](docs/TestingInformation.md) (includes sandbox credit card numbers)
- [Member Privacy Options](docs/MemberPrivacy.md)
- [Membership Benefits](docs/MembershipBenefits.md)
- [Member Dashboard](docs/MemberDashboard.md)
- [Member History Admin](docs/MemberHistoryAdmin.md)
- [Admin List Sorting Options](docs/AdminListSorting.md)
- [Admin Menu Ordering](docs/AdminMenu.md)
- [Event Metrics Export](docs/EventMetricsExport.md)
- [Member Metrics Export](docs/MemberMetricsExport.md)
- [TTA Refund Requests Admin](docs/RefundRequestsAdmin.md)
- [TTA Ads Admin](docs/AdsAdmin.md)
- [TTA Discount Codes Admin](docs/DiscountCodesAdmin.md)
- [Event Check-In Page](docs/EventCheckInAdmin.md)
- [Venue Administration](docs/VenuesAdmin.md)
- [Event Creation Requirements](docs/EventCreationAdmin.md)
- [Ticket Attendees](docs/TicketAttendees.md)
- [Events List Page](docs/EventsListPage.md)
- [Profile Image Popup](docs/ProfilePopup.md)
- [Event Sharing](docs/EventShare.md)
- [Tooltip Text Management](docs/TooltipText.md)
- [Events List Page CSS](assets/css/frontend/events-list.css)
- [Become a Member Page](docs/BecomeMemberPage.md)
- [Email and SMS Templates](docs/EmailSMS.md) – manage message text with live previews and token insertion
- [Recurring Billing](docs/RecurringBilling.md)
- [Debugging Tools](docs/Debugging.md)
- [Database Upgrades](docs/DevelopmentSQL.md#automatic-upgrades)
- [Development SQL Assets](docs/DevelopmentSQL.md)
- [Sample Database Data](docs/SampleData.md)
- [Operator Testing Guide](docs/OperatorTestingGuide.md)
- [Project TODOs](TODO.md)
- [Events List Page](docs/EventsListPage.md) explains waitlist behavior when events sell out.
- [Event Page](docs/EventPage.md#waitlist-popup) covers the join waitlist modal.

Old events are automatically moved to an `tta_events_archive` table by a daily cron. The process is transparent to admins and members.
Whenever the structure of `tta_events` changes, mirror those updates to `tta_events_archive` as well.

## Running Tests

After installing PHP and Composer, execute `composer install` followed by
`vendor/bin/phpunit` to run the plugin's unit tests.
The plugin itself includes its own `composer.json` under
`app/public/wp-content/plugins/tta-management-plugin`. Run `composer install`
inside that directory as well to install libraries like PhpSpreadsheet used for
the Event Metrics export feature.
