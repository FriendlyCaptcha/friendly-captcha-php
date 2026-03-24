<?php

declare(strict_types=1);

namespace FriendlyCaptcha\SDK;

use FriendlyCaptcha\SDK\{VerifyResponse, RiskIntelligenceRetrieveResponse};
use Exception;

class VerifyResult
{
    /** @var bool Whether the request was made in strict mode. */
    private $strict;

    /** @var int The HTTP status code of the response. */
    public $status;

    /** @var VerifyResponse|null The response body. */
    public $response;

    /** 
     * `null` if the puzzle could be verified, in other words we got a 200 response.
     * 
     * Otherwise this will be set to one of the error codes in `ErrorCodes`:
     * * `ErrorCodes::$RequestFailed`
     * * `ErrorCodes::$FailedDueToClientError` (see $response->error for more details, your API key might be wrong).
     * * `ErrorCodes::$FailedToEncodeRequest`
     * * `ErrorCodes::$FailedToDecodeResponse`
     * 
     * @var string|null
     */
    public $errorCode = null;

    public function __construct($strict)
    {
        $this->strict = $strict;
    }

    /** Whether the request was made in strict mode. */
    public function isStrict(): bool
    {
        return $this->strict;
    }

    public function shouldAccept(): bool
    {
        if ($this->wasAbleToVerify()) {
            if ($this->isEncodeError()) {
                return false;
            }
            return $this->response->success;
        }
        if ($this->errorCode !== null) {
            if ($this->strict) {
                return false;
            }
            if ($this->errorCode === ErrorCodes::$RequestFailed || $this->errorCode === ErrorCodes::$FailedDueToClientError || $this->errorCode === ErrorCodes::$FailedToDecodeResponse) {
                return true;
            }
            return false;
        }

        // This case should never happen, if it does it means there is a bug in this SDK itself.
        throw new Exception("Implementation error in friendly-captcha-php-sdk shouldAccept: error should never be null if success is false. " . print_r($this, true));
    }

    public function shouldReject(): bool
    {
        return !$this->ShouldAccept();
    }

    /**
     * Was unable to encode the captcha response. This means the captcha response was invalid and should never be accepted.
     */
    public function isEncodeError(): bool
    {
        return $this->errorCode === ErrorCodes::$FailedToEncodeRequest;
    }

    /**
     * Something went wrong making the request to the Friendly Captcha API, perhaps there is a network connection issue?
     */
    public function isRequestError(): bool
    {
        return $this->errorCode === ErrorCodes::$RequestFailed;
    }

    /**
     * Something went wrong decoding the response from the Friendly Captcha API.
     */
    public function isDecodeError(): bool
    {
        return $this->errorCode === ErrorCodes::$FailedToDecodeResponse;
    }

    /**
     * Something went wrong on the client side, this generally means your configuration is wrong.
     * Check your secrets (API key) and sitekey.
     * 
     * See `$this->response->error` for more details.
     */
    public function isClientError(): bool
    {
        return $this->errorCode === ErrorCodes::$FailedDueToClientError;
    }

    /** 
     * Get the response as was sent from the server.
     * This can be null if the request to the API could not be made succesfully.
     */
    public function getResponse(): ?VerifyResponse
    {
        return $this->response;
    }

    /**
     * Get the error field from the response as was returned by the API, or null if the field is not present.
     */
    public function getResponseError(): ?APIResponseError
    {
        if ($this->response === null) {
            return null;
        }
        return $this->response->error;
    }

    /**
     * 
     */
    public function getErrorCode(): ?string
    {
        return $this->errorCode;
    }

    /**
     * Whether the request to verify the captcha was completed. In other words: the API responded with status 200.'
     * If this is false, you should notify yourself and check `$this->errorCode` to see what is wrong.
     * 
     */
    public function wasAbleToVerify(): bool
    {
        if ($this->isEncodeError()) {
            // Despite not being able to make the request, if we are not even able to encode the captcha response
            // we can be certain it's invalid and were thus able to verify it without even making a request.
            return true;
        }
        return $this->status == 200 && !$this->isRequestError() && !$this->isDecodeError();
    }
}

/**
 * The result of a risk intelligence retrieve request.
 */
class RiskIntelligenceRetrieveResult
{
    /** @var int The HTTP status code of the response. */
    public $status;

    /** @var RiskIntelligenceRetrieveResponse|null The response body. */
    public $response;

    /**
     * `null` if Risk Intelligence data could be retrieved; in other words we got a 200 response.
     * \
     * Otherwise this will be set to one of the error codes in `ErrorCodes`:
     * * `ErrorCodes::$RequestFailed`
     * * `ErrorCodes::$FailedDueToClientError` (see $response->error for more details, your API key might be wrong).
     * * `ErrorCodes::$FailedToEncodeRequest`
     * * `ErrorCodes::$FailedToDecodeResponse`
     *
     * @var string|null
     */
    public $errorCode = null;

    /**
     * Get the response that was sent from the server.
     * This can be null if the request to the API could not be made succesfully.
     */
    public function getResponse(): ?RiskIntelligenceRetrieveResponse
    {
        return $this->response;
    }

    /**
     * Get the error field from the response as was returned by the API, or null if the field is not present.
     */
    public function getResponseError(): ?APIResponseError
    {
        if ($this->response === null) {
            return null;
        }
        return $this->response->error;
    }

    /**
     * Something went wrong on the client side, this generally means your configuration is wrong.
     * Check your secrets (API key) and sitekey.
     *
     * See `$this->response->error` for more details.
     */
    public function isClientError(): bool
    {
        return $this->errorCode === ErrorCodes::$FailedDueToClientError;
    }

    /**
     * Failed to encode the Risk Intelligence token.
     */
    public function isEncodeError(): bool
    {
        return $this->errorCode === ErrorCodes::$FailedToEncodeRequest;
    }

    /**
     * Something went wrong making the request to the Friendly Captcha API, perhaps there is a network connection issue?
     */
    public function isRequestError(): bool
    {
        return $this->errorCode === ErrorCodes::$RequestFailed;
    }

    /**
     * Something went wrong decoding the response from the Friendly Captcha API.
     */
    public function isDecodeError(): bool
    {
        return $this->errorCode === ErrorCodes::$FailedToDecodeResponse;
    }

    /**
     * Whether the request to retrieve risk intelligence was completed. In other words: the API responded with status 200.
     * If this is false, you should notify yourself and use `getResponseError()` to see what is wrong.
     */
    public function wasAbleToRetrieve(): bool
    {
        // If we failed to encode, we actually consider `wasAbleToRetrieve` to be true. This is because we don't want to
        // alert on failed encoding: an attacker could send such malformed data that it fails to encode.
        if ($this->isEncodeError()) {
            return true;
        }

        return $this->status == 200 && !$this->isRequestError() && !$this->isDecodeError();
    }

    /**
     * Whether the Risk Intelligence data was successfully retrieved and is valid.
     */
    public function isValid(): bool {
        if ($this->wasAbleToRetrieve()) {
            if ($this->isEncodeError()) {
                return false;
            }
            if ($this->response != null) {
                return $this->response->success;
            }
        }
        return false;
    }
}
