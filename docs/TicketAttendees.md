## Ticket Attendees

The ticket editor now shows three attendee tables for each ticket. **Verified Attendees** lists everyone who has successfully purchased the ticket. **Attendees With Pending Refund Requests** appears below it and shows members who cancelled and are waiting for another purchase before their refund is issued. The new **Refunded Attendees** table lists those whose refunds have already been processed. Admins can process or review these entries using the action buttons provided. All tables display the same columns: **Name**, **Email**, **Phone**, **Paid**, **Refund $** and **Actions**. Transactions are grouped by their numeric ID with the gateway transaction ID and purchase date displayed in the group heading.

The **Paid** column shows the amount charged for that attendee's ticket. The
**Refund $** field lets admins specify a partial refund before clicking either
**Refund & Cancel Attendance** or **Refund & Keep Attendance**. The first option
both issues the refund and removes the attendee from the event while increasing
the available ticket count. The second option refunds the amount but leaves the
attendee registered. For cases where no refund is needed, a **Cancel Attendance
(No Refund)** button simply frees the ticket and removes the attendee. Any refund or cancellation also reduces the member's purchase tally so they can buy additional tickets up to the limit. If the
transaction has not yet settled, the plugin now checks the transaction status
and automatically voids the original charge. Leaving the **Refund $** field blank refunds the full amount paid for
that attendee only, not the entire transaction.

## Waitlist Entries

Each ticket also shows a **Waitlist Entries** table when people have joined the
waitlist. Every heading now includes a tooltip icon like the Attendees table. The
columns show **Name**, **Email**, **Phone**, **Membership Level**, and **Date & Time
Joined**, followed by an **Actions** column with a Remove button. Entries are
ordered from oldest to newest so admins can quickly see who has been waiting the
longest. Removing an entry deletes it immediately via AJAX so another person can
take the open spot.

When a refund or cancellation increases a sold‑out ticket's remaining count from
zero to one, the waitlist email sequence is triggered automatically. Premium
members are notified immediately, Basic members after ten minutes and Free
members after fifteen. If a higher tier has no entries, lower tiers move up so
available tickets are offered fairly.
