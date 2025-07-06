# Member Dashboard

The Member Dashboard is accessible at `/member-dashboard/` via the shortcode `[tta_member_dashboard]`.
It presents four tabs: **Profile Info**, **Your Upcoming Events**, **Your Past Events**, and **Billing & Membership Info**. Tooltip icons now appear before each field label for quicker context. The tooltip text is displayed instantly on hover using visibility toggles instead of opacity fades. The dashboard's JavaScript and CSS are enqueued whenever the page is viewed so tab switching works even when not logged in.

If a member is banned the dashboard displays a prominent notice at the top explaining the ban duration and purchases are blocked until it expires.
Non-admin users never see the WordPress dashboard. On login the page simply reloads, and any attempt to access `/wp-admin/` redirects back to the front end.


## Upcoming Events Tab

The **Your Upcoming Events** tab lists future events you have tickets for. Each event
shows:

- The event thumbnail image
- Event name linking to the event page
- Event date and time
- Event location
- The total amount paid for the transaction
- Each ticket purchased with the attendee names and emails
- A link to request a refund (paid events) or cancel attendance (free events) which reveals a small form

Events are loaded chronologically and the layout supports any number of events.
Attendee details are pulled from the transaction history and stored in the
`tta_attendees` table.
When a single checkout includes tickets for multiple events each event now
receives its own history record so it appears individually in this list.
Event thumbnails use the medium image size and are scaled to a consistent width so nothing is cropped.
Attendee lists now reflect the database in real time. Individual refunds or cancellations remove the person from the list, and an event disappears entirely once all of its attendees are gone.

## Past Events Tab

Past events show the same details as upcoming events. To keep the database small, events more than three days past are moved to an `tta_events_archive` table by a daily cron job. The dashboard transparently queries both the current events table and this archive so members can always view their history.

## Billing & Membership Info

The billing tab now displays the member's current plan and subscription status. When a Basic or Premium plan is active, a **Cancel Membership** button appears. Submitting the form calls an AJAX endpoint that shows a loading spinner and returns a success or error message. On success the membership level reverts to **Free**, the status changes to *Cancelled*, and the button disappears.
If the subscription remains active, the last four digits of the stored payment method are retrieved directly from Authorize.Net and displayed. Members can update the card by submitting a second form which calls `TTA_AuthorizeNet_API::update_subscription_payment()` via AJAX. The update form now requires the cardholder's billing address (first name, last name, street, city, state and ZIP) in addition to the card details and those fields are pre‑filled with the address from the member profile. The plugin never stores any full payment data.
Subscription metadata is stored in two columns on `tta_members`:

- `subscription_id` – Authorize.Net identifier for the recurring payment
- `subscription_status` – `active`, `cancelled`, or `paymentproblem`

When a membership is cancelled the action is recorded in `tta_memberhistory`.
If the dashboard detects a cancelled status it shows the date of cancellation,
who initiated it and the last four digits of the card used along with a button
to reactivate the previous level. If the subscription status is *paymentproblem*
the tab displays the gateway's reported status and prompts the member to update
their card information directly on the dashboard.
If no membership is active and there is no payment issue, a simple message
"You do not currently have a paid membership." appears instead of the controls.

Below the membership controls is a **Payment History** table. It lists all
transactions in chronological order including event purchases logged in the
`tta_transactions` table, any refunds processed, and monthly membership charges
retrieved from the Authorize.Net API. Refund transactions now appear alongside
all other charges so members can see every adjustment to their account.
Charges related to a membership, including the first month billed at checkout
and all recurring payments, use the transaction type **Membership Subscription**
in the history table.
Event names link to their event pages even after the events move into the
archive, and each row displays the date, item name, amount charged, transaction
type, and the payment method used. Refunds appear as negative amounts in the
table.
