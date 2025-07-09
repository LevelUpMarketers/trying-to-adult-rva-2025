## Ticket Attendees

The ticket editor displays an **Attendees** section for each ticket. This list
shows every attendee who purchased that ticket in a single table. The table
heading contains **Name**, **Email**, **Phone**, **Paid**, **Refund $** and
**Actions** columns. Each heading includes a tooltip icon explaining the field.
Each attendee occupies one row beneath the heading. Transactions are grouped by
their numeric ID with the gateway transaction ID and purchase date displayed in
the group heading.

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
