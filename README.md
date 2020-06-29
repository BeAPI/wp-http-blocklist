<a href="https://beapi.fr">![Be API Github Banner](.github/banner-github.png)</a>

# WP HTTP Blocklist

Block unwanted HTTP requests with a blocklist. 

When you manage platforms with WordPress with composer, it is sometimes penalizing to have all HTTP requests for update control.

For WordPress updates, you can use the very good [Disable All WordPress Updates](https://wordpress.org/plugins/disable-wordpress-updates/) plugin, but for other plugins, blocklist management seems more flexible :)

## Requirements

* WordPress > 4.4
* PHP > 5.6

## Customization and hooks

By default, you have a blocklist integrated into the plugin which will be gradually enriched.

You can add or modify the domains to block via the following hook / filter: "wp_http_blocklist" which transmits an array.

Example


```
<?php 
add_filter( 'wp_http_blocklist', function( $hosts ) {
	$hosts[] = 'blockthisdomain.com';

	return $hosts;
}, 10 );
```

## How to build your own blocklist?

Install the famous plugin [Log HTTP Requests](https://wordpress.org/plugins/log-http-requests/), and you will see all the external requests made by your platform.

You can also find these requests via plugins like [Query Monitor](https://fr.wordpress.org/plugins/query-monitor/) or APM services (ex: NewRelic)

# Who ?

Created by [Be API](https://beapi.fr), the French WordPress leader agency since 2009. Based in Paris, we are more than 30 people and always [hiring](https://beapi.workable.com) some fun and talented guys. So we will be pleased to work with you.

This plugin is only maintained, which means we do not guarantee some free support. Consider reporting an [issue](#issues--features-request--proposal) and be patient.

If you really like what we do or want to thank us for our quick work, feel free to [donate](https://www.paypal.me/BeAPI) as much as you want / can, even 1€ is a great gift for buying coffee :)

## License

This plugin is licensed under the [GPLv2 or later](LICENSE.md).
