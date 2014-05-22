Money Transfer Emulator
===============

Service gateway emulates the international money transfer system.
Simple and debug web interface inside.
Install and use path /mt/emulator/demo in your web project.

- PHP SDK: https://github.com/fintech-fab/money-transfer-emulator-sdk
- Full debug web-form
- Public demo: Coming soon



# Requirements

- php >=5.3.0
- Laravel Framework 4.1.*
- MySQL Database
- User auth identifier in your web project

# Uses

- bootstrap cdn
- jquery cdn

# Installation

## Composer

Package only:

    {
        "require": {
            "fintech-fab/money-transfer-emulator": "dev-master"
        },
    }

Package dependency:

    {
        "require": {
	        "php": ">=5.3.0",
	        "laravel/framework": "4.1.*",
            "fintech-fab/money-transfer-emulator": "dev-master"
        },
	    "require-dev": {
		    "phpunit/phpunit": "3.7.*",
		    "mockery/mockery": "dev-master"
	    },
    }

Run it:

	composer update
	php artisan dump-autoload

## Local configuration

Add service provider to `config/app.php`:

	'providers' => array(
		'FintechFab\MoneyTransferEmulator\MoneyTransferEmulatorServiceProvider'
	)

### Database connection named 'ff-mt-em'

Add to `config/#env#/database.php`:

	'connections' => array(
		'ff-mt-em'  => array(
			'driver'    => 'mysql',
			'host'      => 'your-mysql-host',
			'database'  => 'your-mysql-database',
			'username'  => 'root',
			'password'  => 'your-mysql-password',
			'charset'   => 'utf8',
			'collation' => 'utf8_unicode_ci',
			'prefix'    => 'your-table-prefix',
		),
	),

## Migrations

	php artisan migrate --package="fintech-fab/money-transfer-emulator" --database="ff-mt-em"

### Custom user auth identifier:

Default, user auth id detect by `Auth::user()->getAuthIdentifier()`.
Your can set integer value (e.g. `'user_id' => 1`), or use some your function with identifier return;

For this, publish configuration from package:

	php artisan config:publish fintech-fab/money-transfer-emulator

And change user auth identifier for your web project `app/config/packages/fintech-fab/money-transfer-emulator/config.php`:

	'user_id' => 'user-auth-identifier',

### Optionally, external logs by loggly.com:

Add to `config/#env#/app.php`:

	'logglykey' => 'your-loggly-key',
	'logglytag' => 'your-loggly-tag',

Change `start/global.php` (`Application Error Logger` section):

	Log::useFiles(storage_path() . '/logs/laravel.log');

	if (Config::get('app.logglykey') && Config::get('app.logglytag')) {
		$handler = new \Monolog\Handler\LogglyHandler(Config::get('app.logglykey'), \Monolog\Logger::DEBUG);
		$handler->setTag(Config::get('app.logglytag'));
		$logger = Log::getMonolog();
		$logger->pushHandler($handler);
	}


## Development How to

### Workbench migrations

	php artisan migrate:reset --database="ff-mt-em"
	php artisan migrate --bench="fintech-fab/money-transfer-emulator" --database="ff-mt-em"

	php artisan migrate:reset --database="ff-mt-em" --env="testing"
	php artisan migrate --bench="fintech-fab/money-transfer-emulator" --database="ff-mt-em" --env="testing"

### Package migrations

	php artisan migrate:reset --database="ff-mt-em"
	php artisan migrate --package="fintech-fab/money-transfer-emulator" --database="ff-mt-em"

	php artisan migrate:reset --database="ff-mt-em" --env="testing"
	php artisan migrate --package="fintech-fab/money-transfer-emulator" --database="ff-mt-em" --env="testing"

### Workbench publish

	php artisan config:publish --path=workbench/fintech-fab/money-transfer-emulator/src/config fintech-fab/money-transfer-emulator

