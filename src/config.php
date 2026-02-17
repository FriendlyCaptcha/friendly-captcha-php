<?php

declare(strict_types=1);

namespace FriendlyCaptcha\SDK;

use Exception;

class ClientConfig
{
    public $apiKey = "";
    public $sitekey = "";
    public $sdkTrailer = "";
    
    /**
     * @deprecated Use apiEndpoint instead. This field will be removed in a future version.
     */
    public $siteverifyEndpoint = "global";
    
    /**
     * The API endpoint URL without path. Accepts shorthands "global" or "eu", or a base URL like "https://api.example.com".
     */
    public $apiEndpoint = "global";
    
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
     * An "Frc-Sdk" HTTP header is sent as part of the API request to identify the SDK being used to initiate the request.
     * A downstream SDK might depend on *this* SDK, so this function is provided to allow the downstream SDK to append an
     * identifier to uniquely identify it. For example, this library sends an "Frc-Sdk" header of
     * `friendly-captcha-php@1.2.3`. If `friendly-captcha-wordpress` depends on it, it can add a trailer so that requests
     * will use an "Frc-Sdk" header of `friendly-captcha-php@1.2.3; friendly-captcha-wordpress@4.5.6`.
     *
     * @param string $sdk an identifier that describes the component that depends on this SDK.
     */
    public function setSDKTrailer(string $sdkTrailer): self
    {
        $this->sdkTrailer = $sdkTrailer;
        return $this;
    }

    /**
     * @deprecated Use setApiEndpoint instead. This method will be removed in a future version.
     * @param string $siteverifyEndpoint a full URL, or the shorthands `"global"` or `"eu"`.
     */
    public function setSiteverifyEndpoint(string $siteverifyEndpoint): self
    {
        if ($siteverifyEndpoint != "global" && $siteverifyEndpoint != "eu" && substr($siteverifyEndpoint, 0, 4) != "http") {
            throw new Exception("Invalid argument '" . $siteverifyEndpoint . "' to setSiteverifyEndpoint, it must be a full URL or one of the shorthands 'global' or 'eu'.");
        }
        
        // Strip the path from the URL if it's a full URL
        if (substr($siteverifyEndpoint, 0, 4) == "http") {
            $parsed = parse_url($siteverifyEndpoint);
            if ($parsed === false || !isset($parsed['scheme']) || !isset($parsed['host'])) {
                throw new Exception("Invalid URL '" . $siteverifyEndpoint . "' provided to setSiteverifyEndpoint.");
            }
            $siteverifyEndpoint = $parsed['scheme'] . '://' . $parsed['host'];
            if (isset($parsed['port'])) {
                $siteverifyEndpoint .= ':' . $parsed['port'];
            }
        }
        
        $this->siteverifyEndpoint = $siteverifyEndpoint;
        $this->apiEndpoint = $siteverifyEndpoint;
        return $this;
    }
    
    /**
     * Set the API endpoint URL without path.
     * 
     * @param string $apiEndpoint Base URL without path (e.g., "https://api.example.com") or shorthands "global" or "eu".
     */
    public function setApiEndpoint(string $apiEndpoint): self
    {
        if ($apiEndpoint != "global" && $apiEndpoint != "eu" && substr($apiEndpoint, 0, 4) != "http") {
            throw new Exception("Invalid argument '" . $apiEndpoint . "' to setApiEndpoint, it must be a base URL (without path) or one of the shorthands 'global' or 'eu'.");
        }
        
        // Validate that it's a base URL without path
        if (substr($apiEndpoint, 0, 4) == "http") {
            $parsed = parse_url($apiEndpoint);
            if ($parsed === false || !isset($parsed['scheme']) || !isset($parsed['host'])) {
                throw new Exception("Invalid URL '" . $apiEndpoint . "' provided to setApiEndpoint.");
            }
            
            // Check if path is present and not just '/'
            if (isset($parsed['path']) && $parsed['path'] !== '' && $parsed['path'] !== '/') {
                throw new Exception("API endpoint should not include a path. Got: '" . $apiEndpoint . "'. Please use the base URL only (e.g., 'https://api.example.com').");
            }
            
            // Reconstruct URL without path
            $apiEndpoint = $parsed['scheme'] . '://' . $parsed['host'];
            if (isset($parsed['port'])) {
                $apiEndpoint .= ':' . $parsed['port'];
            }
        }
        
        $this->apiEndpoint = $apiEndpoint;
        $this->siteverifyEndpoint = $apiEndpoint;
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
