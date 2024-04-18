# friendly-captcha-php

A PHP client for the [Friendly Captcha](https://friendlycaptcha.com) service. This client allows for easy integration and verification of captcha responses with the Friendly Captcha API.

> Note, this is for [Friendly Captcha v2](https://developer.friendlycaptcha.com) only. This version is in preview right now, for the current version see the [documentation here](https://docs.friendlycaptcha.com).

## Installation

Requires **PHP 7.1** or later.

**Install using [Composer](https://getcomposer.org/)**

```shell
composer require friendlycaptcha/sdk
```

## Usage

First configure and create a SDK client

```php
use FriendlyCaptcha\SDK\{Client, ClientConfig}

$config = new ClientConfig();
$config->setAPIKey("<YOUR API KEY>")->setSitekey("<YOUR SITEKEY (optional)>");

// You can also specify which endpoint to use, for example `"global"` or `"eu"`.
// $config->setEndpoint("eu")

$captchaClient = new Client($config)
```

Then use it in the endpoint you want to protect

```php
function handleLoginRequest() {
    global $captchaClient;

    $captchaResponse = isset($_POST["frc-captcha-response"]) ? $_POST["frc-captcha-response"] : null;
    $result = $captchaClient->verifyCaptchaResponse($captchaResponse);

    if (!$result->wasAbleToVerify()) {
        if ($result->isClientError()) {
            // ALERT: your website is NOT PROTECTED because of a configuration error.
            // Send an alert to yourself, check your API key (and sitekey).
            error_log("Failed to verify captcha response because of configuration problem: " . print_r($result->getResponseError()));
        } else {
            // Something else went wrong, maybe there is a connection problem or the API is down.
            error_log("Failed to verify captcha response: " . print_r($result->getErrorCode()));
        }
    }

    if ($result->shouldReject()) {
        // The captcha was not OK, show an error message to the user
        echo "Captcha anti-robot check failed, please try again.";
        return;
    }

    // The captcha is accepted, handle the request:
    loginUser($_POST["username"], $_POST["password"]);
}
```

## Development

Make sure you have PHP installed (e.g. with `brew install php` on a Macbook).

### Install Composer

```shell
mkdir -p bin
php -r "copy('https://getcomposer.org/installer', './bin/composer-setup.php');"
# You can omit `--2.2 LTS` if you are using a more recent PHP version than 7.2
php bin/composer-setup.php --install-dir=bin --2.2 LTS
```

### Install dependencies

```shell
bin/composer.phar install
```

### Run the tests

First download the [friendly-captcha-sdk-testserver](https://github.com/FriendlyCaptcha/friendly-captcha-sdk-tooling/releases) for your operating system.

```shell
# Run the friendly-captcha-sdk-testserver
./friendly-captcha-sdk-testserver serve
```

Then open a new terminal, and run the following

```shell
# Generate the autoload files
./bin/composer.phar dump
./vendor/bin/phpunit
```

You should then see output like the following

```
PHPUnit 7.0.0 by Sebastian Bergmann and contributors.

............................                                      28 / 28 (100%)

Time: 36 ms, Memory: 4.00 MB

OK (28 tests, 110 assertions)
```

### Optional

Install an old version of PHP (to be sure it works in that version). The oldest PHP version this SDK supports is 7.1.

```php
brew install shivammathur/php/php@7.1
echo 'export PATH="/opt/homebrew/opt/php@7.1/bin:$PATH"' >> ~/.zshrc
echo 'export PATH="/opt/homebrew/opt/php@7.1/sbin:$PATH"' >> ~/.zshrc

# open a new terminal and check the new version
php --version
```

### Some features you can't use to be compatible with PHP 7.1

- Typings of class member variables.
- Union types (outside of comments).

## License

Open source under [MIT](./LICENSE). Contributions are welcome!
