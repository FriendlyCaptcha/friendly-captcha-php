<?php

declare(strict_types=1);

namespace FriendlyCaptcha\SDK;

use FriendlyCaptcha\SDK\{ClientConfig, VerifyResult, ErrorCodes};

const VERSION = "0.1.0";
const EU_API_ENDPOINT = "https://eu.frcapi.com/api/v2/captcha/siteverify";
const GLOBAL_API_ENDPOINT = "https://global.frcapi.com/api/v2/captcha/siteverify";

class Client
{
    /** @var ClientConfig */
    private $config;

    /**
     * @var string the resolved siteverify endpoint, with any shorthands resolved to their full URL.
     */
    private $resolvedSiteverifyEndpoint;

    public function __construct(ClientConfig $config)
    {
        $this->config = $config;

        if ($this->config->apiKey == "") {
            throw new \Exception("API key is required");
        }

        $endpoint = $this->config->siteverifyEndpoint;

        if ($endpoint === "eu") {
            $endpoint = EU_API_ENDPOINT;
        } elseif ($endpoint === "global") {
            $endpoint = GLOBAL_API_ENDPOINT;
        }

        $this->resolvedSiteverifyEndpoint = $endpoint;
    }

    public function verifyCaptchaResponse(?string $response): VerifyResult
    {
        $verifyResult = new VerifyResult($this->config->strict);
        $verifyResult->status = -1; // So that it is always set, this will only be -1 if the request fails.

        
        if ($response === null) {
            $response = "";
        }
        $requestFields = array("response" => $response);
    
        if ($this->config->sitekey != "") {
            $requestFields["sitekey"] = $this->config->sitekey;
        }

        $payload = json_encode($requestFields);
        if ($payload === false) {
            // TODO: should we put `json_last_error()` somewhere on the object?
            $verifyResult->errorCode = ErrorCodes::$FailedToEncodeRequest;
            return $verifyResult;
        }

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $this->resolvedSiteverifyEndpoint);
        curl_setopt($ch, CURLOPT_POST, true);

        // Return response instead of outputting
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        curl_setopt(
            $ch,
            CURLOPT_HTTPHEADER,
            array(
                'Accept: application/json',
                'Content-Type: application/json',
                'Content-Length: ' . strlen($payload),
                'X-Api-Key: ' . $this->config->apiKey,
                'X-Frc-Sdk: ' . 'friendly-captcha-php-sdk@' . VERSION
            )
        );
        curl_setopt($ch, CURLOPT_POSTFIELDS, $payload);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $this->config->connectTimeout);
        curl_setopt($ch, CURLOPT_TIMEOUT, $this->config->timeout);

        $resp = curl_exec($ch);


        if ($resp === false) {
            // TODO: should we put `curl_errno($ch)` somewhere on the object?           
            $verifyResult->errorCode = ErrorCodes::$RequestFailed;
            curl_close($ch);
            return $verifyResult;
        }

        // Get HTTP status code
        $verifyResult->status = curl_getinfo($ch, CURLINFO_HTTP_CODE);

        curl_close($ch);

        $response = VerifyResponse::fromJson($resp);
        if ($response == null) {
            // TODO: should we put `json_last_error()` somewhere on the object?
            $verifyResult->errorCode = ErrorCodes::$FailedToDecodeResponse;
            return $verifyResult;
        }
        $verifyResult->response = $response;

        if ($verifyResult->status >= 400 && $verifyResult->status < 500) {
            $verifyResult->errorCode = ErrorCodes::$FailedDueToClientError;
            return $verifyResult;
        }

        return $verifyResult;
    }
}
