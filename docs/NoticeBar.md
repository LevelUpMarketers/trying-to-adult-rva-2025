# Notice Bar

The Notice Bar is displayed at the very top of every page on the site. It shows
important alerts and includes quick links to social accounts and the cart.

## Features

- Always visible on desktop and mobile via the `wp_body_open` hook.
- Social icons and links mirror the original top bar markup.
- A persistent **Cart** link appears on the far right.
- The message area in the center is populated via the `tta_notice_bar_messages`
  filter so other components can display warnings or timers.
- Countdown timers work automatically when a message provides an `expires`
  timestamp.

## Extending

Plugins or themes can add messages using:

```php
add_filter( 'tta_notice_bar_messages', function( $messages ) {
    $messages[] = [
        'html'    => 'Items in your cart are expiring soon!',
        'expires' => time() + 300,
    ];
    return $messages;
} );
```
