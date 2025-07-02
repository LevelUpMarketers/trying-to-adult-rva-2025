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
(No Refund)** button simply frees the ticket and removes the attendee. If the
transaction has not yet settled, refund requests automatically void the original
charge. Leaving the **Refund $** field blank refunds the full amount paid for
that attendee only, not the entire transaction.
