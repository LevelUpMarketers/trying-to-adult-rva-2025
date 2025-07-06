# TTA BI Dashboard

The **TTA BI Dashboard** is a new admin page found under its own menu item in the WordPress dashboard. It surfaces business intelligence metrics about subscriptions and member activity.

The page loads D3.js charts via AJAX. The first version shows:

- Counts of active, cancelled and payment-problem subscriptions
- Number of new member signups in the current month

These counts are fetched from the database through the `tta_bi_data` AJAX action. The response format is:

```json
{
  "subs": [ {"label":"Active","count":10}, ... ],
  "signups": [ {"label":"Jul","count":5} ]
}
```

The page plots a bar chart of subscription counts and a simple line chart of monthly signups. Additional metrics can be added by extending the AJAX handler.

Charts update asynchronously without a page reload so new data appears immediately when the dashboard is visited.
