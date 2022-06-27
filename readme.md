## Meta Five

This is Laravel 8.x package wrapper library for Metatrader 5 Web API

- [Official MT5 Web Api Documentation](https://support.metaquotes.net/en/docs/mt5/api/webapi).

## Documentation

### Packagist

[https://packagist.org/packages/aleedhillon/meta-five](https://packagist.org/packages/aleedhillon/meta-five).

### Installing

To install the package, in terminal:

```
composer require aleedhillon/meta-five
```

### Configure

If you don't use auto-discovery, add the ServiceProvider to the providers array in config/app.php

```
AleeDhillon\MetaFive\MetaFiveProvider::class,
```

#### Copy the package config to your local config with the publish command:

```bash
php artisan vendor:publish --tag=meta-five-config
```

and then you can configure connection information to MT5 with this `.env` value

```dotenv
MT5_SERVER_IP=
MT5_SERVER_PORT=
MT5_SERVER_WEB_LOGIN=
MT5_SERVER_WEB_PASSWORD=
```

## Usage

### Create Deposit

You can withdraw money by giving negetive number to the same `trade` method.

```php
use AleeDhillon\MetaFive\Entities\Trade;
use AleeDhillon\MetaFive\Facades\Client;

$trade = new Trade();
$trade->setLogin(6000189);
$trade->setAmount(100);
$trade->setComment("Deposit");
$trade->setType(Trade::DEAL_BALANCE);
$result = Client::trade($trade);
```

The result variable will return Trade class with ticket information, you can grab ticket number by calling `$result->getTicket()`

### Create User

```php
use AleeDhillon\MetaFive\Entities\User;
use AleeDhillon\MetaFive\Facades\Client;

$user = new User();
$user->setName("John Doe");
$user->setEmail("johndoe@example.com");
$user->setGroup("demo\demoforex");
$user->setLeverage("50");
$user->setPhone("0123456789");
$user->setAddress("Lahore");
$user->setCity("Lahore");
$user->setState("Punjab");
$user->setCountry("Pakistan");
$user->setZipCode(1470);
$user->setMainPassword("secret");
$user->setInvestorPassword("secret");
$user->setPhonePassword("secret");

$result = Client::createUser($user);
```

### Get Trading Account Information

```php
use AleeDhillon\MetaFive\Facades\Client;

$user = Client::getTradingAccounts($login);

$balance = $user->Balance;
$equity = $user->Equity;
$freeMargin = $user->MarginFree;
```

### Get Trading History By Login Number

```php
use AleeDhillon\MetaFive\Facades\Client;

// Get Closed Order Total and pagination
$total = Client::getOrderHistoryTotal($exampleLogin, $timestampfrom, $timestampto);
$trades = Client::getOrderHistoryPagination($exampleLogin, $timestampfrom, $timestampto, 0, $total);
foreach ($trades as $trade) {
    // see class MTOrder
    echo "LOGIN : ".$trade->Login.PHP_EOL;
    echo "TICKET : ".$trade->Order.PHP_EOL;
}
```

### Open Order

```php
use AleeDhillon\MetaFive\Facades\Client;
Client::dealerSend([
    'Login' => 8113,
    'Symbol' => 'XAUUSD',
    'Volume' => 100,
    'Type' => 0
});
```

The result variable will return User class with login information, you can grab login number by calling `$result->getLogin()`

### Todo

- [x] Deposit or Withdrawal
- [x] Create Account
- [x] Open Order
- [x] Get Trading Account Information
- [ ] Change Password
- [ ] Create Group
- [ ] Delete Group
- [ ] Get Accounts
- [ ] Remove Account
- [ ] Get Trades
- [ ] Get Group

This is work in progress, I will may be improve this package or rewrite the entire one with Laravel 9 and PHP 8 Support.
In this revisioin I haven't touched much to the core in next I intend to rewrite the core.

## Credits

Thanx to [Tarikh Agustia](https://github.com/tarikhagustia) who wrote the following two packges from which I have rewritten this current package with improvements like the Laravel singleton pattern for speed and reducing API calls to MT5.

- [https://github.com/tarikhagustia/php-mt5](https://github.com/tarikhagustia/php-mt5)
- [https://github.com/tarikhagustia/laravel-mt5](https://github.com/tarikhagustia/laravel-mt5)

## Contributing

Thank you for considering contributing to the MetaFive! you can fork this repository and make pull request.

## Security Vulnerabilities

If you discover a security vulnerability within MetaFive, please send an e-mail to Ali A. Dhillon via [aleedhx@gmail.com](aleedhx@gmail.com). All security vulnerabilities will be promptly addressed.

## License

The MetaFive packge is open-source software licensed under the [MIT license](https://opensource.org/licenses/MIT).
