<?php

declare(strict_types=1);

namespace FriendlyCaptcha\SDK;

class ErrorCodes
{

    /** (-1, internal) Failed to encode request */
    public static $FailedToEncodeRequest = "failed_to_encode_request";

    /** (-1, internal) Failed to talk to the Friendly Captcha API */
    public static $RequestFailed = "request_failed";

    /** (-1, internal) Verification failed due to a client error (check your credentials) */
    public static $FailedDueToClientError = "request_failed_due_to_client_error";

    /** (-1, internal) Verification failed because we got an unexpected value from the server. */
    public static $FailedToDecodeResponse = "verification_response_could_not_be_decoded";

    /** (401) You forgot to set the X-API-Key header. */
    public static $AuthRequired = "auth_required";

    /** (401) The API key you provided is invalid. */
    public static $AuthInvalid = "auth_invalid";

    /** (400) The sitekey in your request is invalid. */
    public static $SitekeyInvalid = "sitekey_invalid";

    /** (400) The response field is missing in your request. */
    public static $ResponseMissing = "response_missing";

    /** (200) The response field is invalid. */
    public static $ResponseInvalid = "response_invalid";

    /** (200) The response has expired. */
    public static $ResponseTimeout = "response_timeout";
    /** (200) The response has already been used. */
    public static $ResponseDuplicate = "response_duplicate";

    /** (400) Something else is wrong with your request, e.g. the request body was empty. */
    public static $BadRequest = "bad_request";
}
