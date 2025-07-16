# Event Creation and Editing

Administrators manage events from the **TTA Events** pages. The editor collects standard details like the name, date and location along with optional images and links.

## Hosts and Volunteers

Host and volunteer fields use an autocomplete list populated from members whose type is Volunteer, Admin or Super Admin. When the form is saved each name is converted to the member's WordPress user ID and stored in the `hosts` and `volunteers` columns. Older records that still contain names are handled automatically.

## Inline Editing

The Manage Events table supports inline editing via AJAX. Fields match the create form and also save host and volunteer IDs.
