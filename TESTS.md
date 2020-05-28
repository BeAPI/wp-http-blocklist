# Installation

For this you need :
- A local mysql server
- A PHP interpreter
- Composer installed

You need to do : 

```
composer install
composer install-tests
```

Assuming you have the user root with no password.
This will download the latest version of WordPress available.
Other arguments allows you to download other versions of WordPress, read the `bin/install-wp-tests.sh` for more informations :)

# Launching tests

Since we moved the WordPress tests library to another location, you will need specify the folder.
There is a composer script for this :

```
composer test
```

# Known caveats

You cannot read tests from PHPStorm method by method because of the way they are handled.
You need to specify the WP_TESTS_DIR env variable by hand for each method after fail.
I recommend to launch all the tests every time.