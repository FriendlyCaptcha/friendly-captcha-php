<?php

declare(strict_types=1);

require_once '../../vendor/autoload.php';

use FriendlyCaptcha\SDK\{Client, ClientConfig};

$sitekey = getenv('FRC_SITEKEY');
$apikey = getenv('FRC_APIKEY');

// Optionally we can pass in custom endpoints to be used, such as "eu".
$siteverifyEndpoint = getenv('FRC_SITEVERIFY_ENDPOINT');
$widgetEndpoint = getenv('FRC_WIDGET_ENDPOINT');

// TODO: to be replaced with the actual SDK URL once it's published.
const MODULE_SCRIPT_URL = "https://cdn.jsdelivr.net/npm/@friendlycaptcha/sdk@0.1.0/TODO.js";
const NOMODULE_SCRIPT_URL = "https://cdn.jsdelivr.net/npm/@friendlycaptcha/sdk@0.1.0/TODO.js";; // Compatibility fallback for old browsers.

if (empty($sitekey) || empty($apikey)) {
    die("Please set the FRC_SITEKEY and FRC_APIKEY environment values before running this example.");
}

function generateForm(bool $didSubmit, bool $captchaOK, string $sitekey)
{
    global $widgetEndpoint;
    $html = '';

    if ($didSubmit) {
        if ($captchaOK) {
            $html .= '<p>✅ Your message has been submitted successfully.</p>';
        } else {
            $html .= '<p style="color:#ba1f1f">❌ Anti-robot check failed, please try again.<br>See console output for details.</p>';
        }
        $html .= '<a href=".">Back to form</a>';
    }

    if (!$didSubmit) {
        $html .= '
        <form method="POST">
            <div class="form-group">
                <label>Your Name:</label><br />
                <input type="text" name="name" value="Jane Doe"><br />
                <label>Message:</label><br />
                <textarea name="message"></textarea><br />
                <div class="frc-captcha"
                  data-sitekey="' . $sitekey . '"' .
            (isset($widgetEndpoint) ? (' data-api-endpoint="' . $widgetEndpoint . '"') : '') . '></div> 
                <input style="margin-top: 1em" type="submit" value="Submit">
            </div>
        </form>';
    }
    return $html;
}

$config = new \FriendlyCaptcha\SDK\ClientConfig();
$config->setAPIKey($apikey);
$config->setSitekey($sitekey);
if (!empty($siteverifyEndpoint)) {
    $config->setSiteverifyEndpoint($siteverifyEndpoint); // Optional, it defaults to "global".
}

$frcClient = new \FriendlyCaptcha\SDK\Client($config);

$didSubmit = $_SERVER['REQUEST_METHOD'] === 'POST';
$captchaOK = false;

if ($didSubmit) {
    $captchaResponse = isset($_POST["frc-captcha-response"]) ? $_POST["frc-captcha-response"] : null;
    $captchaResult = $frcClient->verifyCaptchaResponse($captchaResponse);

    if (!$captchaResult->wasAbleToVerify()) {
        // In this case we were not actually able to verify the response embedded in the form, but we may still want to accept it.
        // It could mean there is a network issue or that the service is down. In those cases you generally want to accept submissions anyhow
        // That's why we use `shouldAccept()` below to actually accept or reject the form submission. It will return true in these cases.
        error_log("Failed to verify captcha response: " . $captchaResult->getErrorCode() . " " . print_r($captchaResult->getResponseError(), true));

        if ($captchaResult->isClientError()) {
            // Something is wrong with our configuration, check your API key!
            // Send yourself an alert to fix this! Your site is unprotected until you fix this.
            error_log("CAPTCHA CONFIG ERROR:" . $captchaResult->getErrorCode() . " " . print_r($captchaResult->getResponseError(), true));
        }
    }

    if ($captchaResult->shouldAccept()) {
        $captchaOK = true;
        // The captcha was OK, process the form.
        $name = $_POST['name'];
        $message = $_POST['message'];

        // Process the request here, put it in the DB: the captcha was accepted.
        // In this example we will simply print the message to the console using `error_log`.
        error_log("Message submitted by \"" . $name . "\": \"" . $message . "\"");
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Friendly Captcha PHP SDK Form Example</title>

    <script type="module" src="<?php echo MODULE_SCRIPT_URL ?>" async defer></script>
    <script nomodule src="<?php echo NOMODULE_SCRIPT_URL ?>" async defer></script>

    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/holiday.css@0.11.2" />
</head>

<body>
    <main>
        <h1>PHP Form Example</h1>
        <?php echo generateForm($didSubmit, $captchaOK, $sitekey); ?>
    </main>

    <script>
        // Prevent re-submission when the user reloads the page.
        if (window.history.replaceState) {
            window.history.replaceState(null, null, window.location.href);
        }
    </script>
</body>

</html>