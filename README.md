# Trying To Adult Management Plugin

This plugin integrates with Authorize.Net for payment processing. The plugin's files live in `app/public/wp-content/plugins/tta-management-plugin` within this repository. For development you may place the API credentials in an `authnet-config.php` file at the plugin root or set them as environment variables before WordPress loads:

```
define('TTA_AUTHNET_LOGIN_ID', 'your_login_id');
define('TTA_AUTHNET_TRANSACTION_KEY', 'your_transaction_key');
# Optional: set to 'false' for production
define('TTA_AUTHNET_SANDBOX', true);
```
If these constants are not defined, checkout will fail and an admin notice will be displayed. When deploying to production, move the credentials out of the plugin directory.

SMS notifications use Twilio. Similar to the Authorize.Net credentials, you can
create a `twilio-config.php` file or set environment variables:

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
- [Input Sanitization Helpers](docs/InputSanitization.md)
- [Authorize.Net Error Codes](docs/AuthorizeNetErrors.md)
- [Address Helper Functions](docs/AddressHelpers.md)
- [Event Page Context](docs/EventPage.md)
- [Event Hosts & Volunteers](docs/EventPage.md#event-hosts-and-volunteers)
- [Event Creation Admin](docs/EventCreationAdmin.md)
- [Event Type Options](docs/EventTypes.md)
- [Testing Information](docs/TestingInformation.md) (includes sandbox credit card numbers)
- [Member Privacy Options](docs/MemberPrivacy.md)
- [Membership Benefits](docs/MembershipBenefits.md)
- [Member Dashboard](docs/MemberDashboard.md)
- [Member History Admin](docs/MemberHistoryAdmin.md)
- [Admin List Sorting Options](docs/AdminListSorting.md)
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
