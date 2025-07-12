  `memberlimit` int unsigned NOT NULL DEFAULT '2',
INSERT INTO `wp_j9bzlz98u3_tta_tickets` (`id`, `event_ute_id`, `event_name`, `ticket_name`, `waitlist_id`, `ticketlimit`, `memberlimit`, `baseeventcost`, `discountedmembercost`, `premiummembercost`) VALUES
  `memberlimit` int unsigned NOT NULL DEFAULT '2',
  `wp_user_id` bigint unsigned DEFAULT 0,
  `first_name` varchar(255) COLLATE utf8mb4_unicode_520_ci NOT NULL,
  `last_name` varchar(255) COLLATE utf8mb4_unicode_520_ci NOT NULL,
  `email` varchar(255) COLLATE utf8mb4_unicode_520_ci NOT NULL,
  `phone` varchar(50) COLLATE utf8mb4_unicode_520_ci DEFAULT '',
  `opt_in_email` tinyint(1) DEFAULT 1,
  `opt_in_sms` tinyint(1) DEFAULT 1,
  KEY `ticket_id_idx` (`ticket_id`),
  KEY `wp_user_id_idx` (`wp_user_id`)
/* sample waitlist rows intentionally omitted */
