# TTA BI Dashboard

The **TTA BI Dashboard** appears as its own menu item in the WordPress admin and surfaces business intelligence metrics for managers. Data is fetched via the `tta_bi_data` AJAX action and rendered with D3.js.

## Available Charts

The dashboard now displays multiple sections:

1. **Subscription Status** – bar chart of active, cancelled and payment-problem subscriptions.
2. **New Member Signups** – line chart showing signups this month.
3. **Monthly Revenue** – line chart of total revenue from all transactions over the last six months.
4. **Ticket Sales Per Year** – bar chart summarising yearly event revenue.
5. **Average Tickets per Event** – monthly average tickets sold per event for the current year.
6. **Membership Levels** – pie chart of members by current level.
7. **Predicted Revenue** – simple forecast for next month based on the recent average.

The AJAX response includes arrays for each dataset:

```json
{
  "subs": [{"label":"Active","count":10}],
  "signups": [{"label":"Jul","count":5}],
  "revenue": [{"label":"2025-01","amount":1200}],
  "ticket_sales": [{"label":"2025","amount":5000}],
  "avg_tickets": [{"label":"01","count":15}],
  "by_level": [{"label":"premium","count":50}],
  "prediction": {"label":"2025-08","amount":1500}
}
```

Charts are drawn asynchronously without page reloads. Additional metrics can be added by extending the AJAX handler and appending new chart containers in the view.
