# Event Check-In Page for Admins

The plugin provides a dedicated check-in screen at `/event-check-in-for-admins/`.
Administrators and volunteers can quickly mark attendees as checked in or as
no-shows. The page loads event details, a list of ticket holders and buttons to
update each record via AJAX. Attendance status writes back to the
`tta_attendees` table and the interface updates instantly without a full refresh.

### Table details

- Attendees who cancelled or requested a refund no longer appear in the list so
  hosts don't accidentally mark them as no-shows.
- Two new columns display how many events each attendee has previously checked
  in for and any **Needs Assistance** note they left for the host. When no note
  exists a simple `-` is shown.
- A new **# of Expected Attendees** column shows how many approved attendees are
  expected for each event.
- Clicking the **Check In** or **No-Show** buttons now updates the status label
  with proper capitalization.
- Event headers display the date and time in a friendly format like `Saturday
  July 19, 2025 - 6:00 PM to 8:00 PM` and the venue address links directly to
  Google Maps for quick directions.
