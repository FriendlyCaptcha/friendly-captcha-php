<?php

declare(strict_types=1);

namespace FriendlyCaptcha\SDK;

use FriendlyCaptcha\SDK\{ClientConfig, VerifyResult, RiskIntelligenceRetrieveResult, ErrorCodes};

const VERSION = "0.2.0";
const EU_API_ENDPOINT = "https://eu.frcapi.com";
const GLOBAL_API_ENDPOINT = "https://global.frcapi.com";
const SITEVERIFY_PATH = "/api/v2/captcha/siteverify";
const RETRIEVE_PATH = "/api/v2/riskIntelligence/retrieve";

class Client
{
    /** @var ClientConfig */
    private $config;

    /**
     * @var string the resolved API endpoint, with any shorthands resolved to their full URL.
     */
    private $resolvedApiEndpoint;

    public function __construct(ClientConfig $config)
    {
        $this->config = $config;

        if ($this->config->apiKey == "") {
            throw new \Exception("API key is required");
        }

        $endpoint = $this->config->apiEndpoint ?: $this->config->siteverifyEndpoint ?: "global";

        if ($endpoint === "eu") {
            $endpoint = EU_API_ENDPOINT;
        } elseif ($endpoint === "global") {
            $endpoint = GLOBAL_API_ENDPOINT;
        }

        $this->resolvedApiEndpoint = $endpoint;
    }

    /**
     * Makes a POST request to the API and returns the HTTP status and response body.
     * Returns ['status' => int, 'body' => string] on success, or ['errorCode' => string] on failure.
     */
    private function makeRequest(string $path, array $fields): array
    {
        $frcSdk = 'friendly-captcha-php@' . VERSION;
        if ($this->config->sdkTrailer != "") {
            $frcSdk = $frcSdk . "; " . $this->config->sdkTrailer;
        }

        $payload = json_encode($fields);
        if ($payload === false) {
            // TODO: should we expose `json_last_error()`?
            return ['errorCode' => ErrorCodes::$FailedToEncodeRequest];
        }

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $this->resolvedApiEndpoint . $path);
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
                'Frc-Sdk: ' . $frcSdk,
            )
        );
        curl_setopt($ch, CURLOPT_POSTFIELDS, $payload);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $this->config->connectTimeout);
        curl_setopt($ch, CURLOPT_TIMEOUT, $this->config->timeout);

        $resp = curl_exec($ch);

        if ($resp === false) {
            // TODO: should we expose `curl_errno($ch)`?
            curl_close($ch);
            return ['errorCode' => ErrorCodes::$RequestFailed];
        }

        $status = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        return ['status' => $status, 'body' => $resp];
    }

    public function verifyCaptchaResponse(?string $response, string $sitekey = ""): VerifyResult
    {
        $verifyResult = new VerifyResult($this->config->strict);
        $verifyResult->status = -1; // So that it is always set, this will only be -1 if the request fails.

        $fields = array("response" => $response ?? "");
        if ($sitekey != "" || $this->config->sitekey != "") {
            $fields["sitekey"] = $sitekey ?: $this->config->sitekey;
        }

        $apiResult = $this->makeRequest(SITEVERIFY_PATH, $fields);
        if (isset($apiResult['errorCode'])) {
            $verifyResult->errorCode = $apiResult['errorCode'];
            return $verifyResult;
        }

        $verifyResult->status = $apiResult['status'];

        $parsedResponse = VerifyResponse::fromJson($apiResult['body']);
        if ($parsedResponse == null) {
            // TODO: should we expose `json_last_error()`?
            $verifyResult->errorCode = ErrorCodes::$FailedToDecodeResponse;
            return $verifyResult;
        }
        $verifyResult->response = $parsedResponse;

        if ($verifyResult->status >= 400 && $verifyResult->status < 500) {
            $verifyResult->errorCode = ErrorCodes::$FailedDueToClientError;
        }

        return $verifyResult;
    }

    public function retrieveRiskIntelligence(?string $token, string $sitekey = ""): RiskIntelligenceRetrieveResult
    {
        $result = new RiskIntelligenceRetrieveResult();
        $result->status = -1; // So that it is always set, this will only be -1 if the request fails.

        $fields = array("token" => $token ?? "");
        if ($sitekey != "" || $this->config->sitekey != "") {
            $fields["sitekey"] = $sitekey ?: $this->config->sitekey;
        }

        $apiResult = $this->makeRequest(RETRIEVE_PATH, $fields);
        if (isset($apiResult['errorCode'])) {
            $result->errorCode = $apiResult['errorCode'];
            return $result;
        }

        $result->status = $apiResult['status'];

        $parsedResponse = RiskIntelligenceRetrieveResponse::fromJson($apiResult['body']);
        if ($parsedResponse == null) {
            // TODO: should we expose `json_last_error()`?
            $result->errorCode = ErrorCodes::$FailedToDecodeResponse;
            return $result;
        }
        $result->response = $parsedResponse;

        if ($result->status >= 400 && $result->status < 500) {
            $result->errorCode = ErrorCodes::$FailedDueToClientError;
        }

        return $result;
    }
}
