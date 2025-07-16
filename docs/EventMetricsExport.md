# Event Metrics Export

Administrators can export a spreadsheet of event metrics from either the **Manage Events** or **Archived Events** tabs. A short form above each table lets you optionally specify a start and end date. Leaving both fields blank exports all events.

This feature relies on the PhpSpreadsheet library installed via Composer inside the plugin directory. Run `composer install` within `app/public/wp-content/plugins/tta-management-plugin` after cloning the repository. If the library has not been installed, the export form displays an admin notice explaining how to install it.

The spreadsheet includes every column from the events table along with additional metrics:

- `expected_attendees` – number of purchased tickets (after refunds)
- `checked_in` – count of attendees marked as checked in
- `no_show` – count of attendees marked as no show
- `refund_requests` – number of pending refund requests
- `refunded_amount` – total amount refunded
- `revenue` – gross revenue from ticket sales
- `waitlist_count` – current waitlist entries

Press **Export Metrics** and an `.xlsx` file downloads immediately.
