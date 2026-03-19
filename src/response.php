<?php

declare(strict_types=1);

namespace FriendlyCaptcha\SDK;

use DateTimeImmutable;

class APIResponseError
{
    /** @var string */
    public $error_code;
    /** @var string */
    public $detail;

    public static function fromJson($json): ?APIResponseError
    {
        $data = json_decode($json);
        if ($data == null || !is_object($data)) {
            return null;
        }
        return self::fromStdClass($data);
    }

    public static function fromStdClass($obj): APIResponseError
    {
        $instance = new self();
        $instance->error_code = $obj->error_code;
        $instance->detail = $obj->detail;
        return $instance;
    }
}

class VerifyResponseChallengeData
{
    /** @var DateTimeImmutable */
    public $timestamp;
    /** @var string */
    public $origin;

    public static function fromJson($json): ?VerifyResponseChallengeData
    {
        $data = json_decode($json);
        if ($data == null || !is_object($data)) {
            return null;
        }
        return self::fromStdClass($data);
    }

    public static function fromStdClass($obj): VerifyResponseChallengeData
    {
        $instance = new self();
        $instance->timestamp = DateTimeImmutable::createFromFormat("c", $obj->timestamp);
        $instance->origin = $obj->origin;
        return $instance;
    }
}

class VerifyResponseData
{
    /** @var string Unique identifier for this siteverify call. */
    public $event_id;
    /** @var VerifyResponseChallengeData Information about the challenge that was solved. */
    public $challenge;
    /**
     * @var RiskIntelligenceData|null Risk Intelligence data about the solver of the challenge.
     * If Risk Intelligence is not enabled for your Friendly Captcha account, this field will be null.
     */
    public $risk_intelligence;

    public static function fromJson($json): ?VerifyResponseData
    {
        $data = json_decode($json);
        if ($data == null || !is_object($data)) {
            return null;
        }
        return self::fromStdClass($data);
    }

    public static function fromStdClass($obj): VerifyResponseData
    {
        $instance = new self();
        $instance->event_id = $obj->event_id;
        $instance->challenge = VerifyResponseChallengeData::fromStdClass($obj->challenge);
        $instance->risk_intelligence = isset($obj->risk_intelligence) ? RiskIntelligenceData::fromStdClass($obj->risk_intelligence) : null;
        return $instance;
    }
}

class VerifyResponse
{
    /** @var bool */
    public $success;
    /** @var VerifyResponseData|null */
    public $data;
    /** @var APIResponseError|null */
    public $error;

    public static function fromJson($json): ?VerifyResponse
    {
        $d = json_decode($json);
        if ($d == null || !is_object($d)) {
            return null;
        }

        $instance = new self();
        $instance->success = false;
        if (isset($d->success)) {
            $instance->success = $d->success;
        }


        if (isset($d->data)) {
            $instance->data = VerifyResponseData::fromStdClass($d->data);
        }

        if (isset($d->error)) {
            $instance->error = APIResponseError::fromStdClass($d->error);
        }

        return $instance;
    }
}

class RiskIntelligenceRetrieveTokenData
{
    /** @var DateTimeImmutable Timestamp when the token was generated. */
    public $timestamp;
    /** @var DateTimeImmutable Timestamp when the token expires. */
    public $expires_at;
    /** @var int Number of times the token has been used. */
    public $num_users;
    /** @var string The origin of the site where the token was generated. */
    public $origin;

    public static function fromJson($json): ?RiskIntelligenceRetrieveTokenData
    {
        $data = json_decode($json);
        if ($data == null || !is_object($data)) {
            return null;
        }
        $instance = new self();
        $instance->timestamp = DateTimeImmutable::createFromFormat("c", $data->timestamp);
        $instance->expires_at = DateTimeImmutable::createFromFormat("c", $data->expires_at);
        $instance->num_uses = $data->num_uses;
        $instance->origin = $data->origin;
        return $instance;
    }

    public static function fromStdClass($obj): RiskIntelligenceRetrieveTokenData
    {
        $instance = new self();
        $instance->timestamp = DateTimeImmutable::createFromFormat("c", $obj->timestamp);
        $instance->expires_at = DateTimeImmutable::createFromFormat("c", $obj->expires_at);
        $instance->num_uses = $obj->num_uses;
        $instance->origin = $obj->origin;
        return $instance;
    }
}

class RiskIntelligenceRetrieveResponseData
{
    /** @var string Unique identifier for this Risk Intelligence retrieve call. */
    public $event_id;
    /** @var RiskIntelligenceRetrieveTokenData Metadata about the token used for retrieval. */
    public $token;
    /** @var RiskIntelligenceData Risk information retrieved with the provided token. */
    public $risk_intelligence;

    public static function fromJson($json): ?RiskIntelligenceRetrieveResponseData
    {
        $data = json_decode($json);
        if ($data == null || !is_object($data)) {
            return null;
        }
        return self::fromStdClass($data);
    }

    public static function fromStdClass($obj): RiskIntelligenceRetrieveResponseData
    {
        $instance = new self();
        $instance->event_id = $obj->event_id;
        $instance->token = RiskIntelligenceRetrieveTokenData::fromStdClass($obj->token);
        $instance->risk_intelligence = RiskIntelligenceData::fromStdClass($obj->risk_intelligence);
        return $instance;
    }
}

class RiskIntelligenceRetrieveResponse
{
    /** @var bool */
    public $success;
    /** @var RiskIntelligenceRetrieveResponseData|null */
    public $data;
    /** @var APIResponseError|null */
    public $error;

    public static function fromJson($json): ?RiskIntelligenceRetrieveResponse
    {
        $d = json_decode($json);
        if ($d == null || !is_object($d)) {
            return null;
        }

        $instance = new self();
        $instance->success = false;
        if (isset($d->success)) {
            $instance->success = $d->success;
        }


        if (isset($d->data)) {
            $instance->data = RiskIntelligenceRetrieveResponseData::fromStdClass($d->data);
        }

        if (isset($d->error)) {
            $instance->error = APIResponseError::fromStdClass($d->error);
        }

        return $instance;
    }
}
