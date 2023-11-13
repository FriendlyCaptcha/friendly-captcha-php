<?php

declare(strict_types=1);

namespace FriendlyCaptcha\SDK;

use Exception;

class ClientConfig
{
    public $apiKey = "";
    public $sitekey = "";
    public $siteverifyEndpoint = "global";
    public $strict = false;
    public $timeout = 30;
    public $connectTimeout = 20;

    public function __construct()
    {
    }

    public function setAPIKey(string $apiKey): self
    {
        $this->apiKey = $apiKey;
        return $this;
    }

    /**
     * For the API you can pass a sitekey, which will allow you to specify the exact sitekey you expect to accept captcha responses for.
     * If you do not pass a sitekey, the API will approve captcha solutions from any sitekey in your account.
     * 
     * @param string $sitekey the sitekey you received from friendlycaptcha.com, starts with `FC`
     */
    public function setSitekey(string $sitekey): self
    {
        $this->sitekey = $sitekey;
        return $this;
    }

    /**
     * @param string $siteverifyEndpoint a full URL, or the shorthands `"global"` or `"eu"`.
     */
    public function setSiteverifyEndpoint(string $siteverifyEndpoint): self
    {
        if ($siteverifyEndpoint != "global" && $siteverifyEndpoint != "eu" && substr($siteverifyEndpoint, 0, 4) != "http") {
            throw new Exception("Invalid argument '" . $siteverifyEndpoint . "' to setSiteverifyEndpoint, it must be a full URL or one of the shorthands 'global' or 'eu'.");
        }
        $this->siteverifyEndpoint = $siteverifyEndpoint;
        return $this;
    }

    /**
     * In strict mode only strictly verified captcha response are allowed.
     * With strict mode enabled: if your API key is invalid or your server can not reach the API endpoint all requests will be rejected.
     * 
     * This defaults to `false`.
     * @param bool $strict
     */
    public function setStrict(bool $strict): self
    {
        $this->strict = $strict;
        return $this;
    }

    /** Timeout for requests to establish connection in seconds, defaults to 15. */
    public function setConnectTimeout(int $timeout): self
    {
        $this->connectTimeout = $timeout;
        return $this;
    }

    /** Timeout for requests to complete in seconds, defaults to 20. */
    public function setTimeout(int $timeout): self
    {
        $this->timeout = $timeout;
        return $this;
    }
}
