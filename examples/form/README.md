# form

Example of using the Friendly Captcha SDK for PHP. It displays a form with a captcha and verifies the captcha response on the back-end. If the application has [**Risk Intelligence**](https://developer.friendlycaptcha.com/docs/v2/risk-intelligence/) enabled, it will also generate a token and use it to retrieve the Risk Intelligence data.

## Running the example

```shell
FRC_APIKEY=YOUR_API_KEY FRC_SITEKEY=YOUR_SITEKEY php -S localhost:8000
```

Alternatively, you can also specify custom endpoints:

```shell
FRC_SITEKEY=YOUR_API_KEY FRC_APIKEY=YOUR_SITEKEY FRC_API_ENDPOINT=https://eu-dev.frcapi.com FRC_WIDGET_ENDPOINT=https://eu-dev.frcapi.com php -S localhost:8000
```

Now open your browser and navigate to [http://localhost:8000](http://localhost:8000).
