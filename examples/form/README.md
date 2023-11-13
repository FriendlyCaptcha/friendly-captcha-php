# form

Example of using the Friendly Captcha SDK for PHP.

## Running the example

```shell
FRC_APIKEY=YOUR_API_KEY FRC_SITEKEY=YOUR_SITEKEY php -S localhost:8000
```

Alternatively, you can also specify custom endpoints:

```shell
FRC_SITEKEY=YOUR_API_KEY FRC_APIKEY=YOUR_SITEKEY FRC_SITEVERIFY_ENDPOINT=https://eu-dev.frcapi.com/api/v2/captcha/siteverify FRC_WIDGET_ENDPOINT=https://eu-dev.frcapi.com/api/v2/captcha php -S localhost:8000
```

Now open your browser and navigate to [http://localhost:8000](http://localhost:8000).
