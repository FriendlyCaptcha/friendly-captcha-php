<?php

declare(strict_types=1);

namespace FriendlyCaptcha\SDK;

use DateTimeImmutable;
use FriendlyCaptcha\SDK\RiskIntelligence;

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
        $instance = new self();
        try {
            $instance->timestamp = new DateTimeImmutable($data->timestamp);
        } catch (\Exception $e) {
            // This should never happen - indicates malformed API response
            error_log("Failed to parse timestamp from API response: " . $e->getMessage() . ". Using Unix epoch as fallback.");
            $instance->timestamp = new DateTimeImmutable('@0');
        }
        $instance->origin = $data->origin;
        return $instance;
    }

    public static function fromStdClass($obj): VerifyResponseChallengeData
    {
        $instance = new self();
        try {
            $instance->timestamp = new DateTimeImmutable($obj->timestamp);
        } catch (\Exception $e) {
            // This should never happen - indicates malformed API response
            error_log("Failed to parse timestamp from API response: " . $e->getMessage() . ". Using Unix epoch as fallback.");
            $instance->timestamp = new DateTimeImmutable('@0');
        }
        $instance->origin = $obj->origin;
        return $instance;
    }
}

class VerifyResponseData
{
    /** @var string */
    public $event_id;
    /** @var VerifyResponseChallengeData */
    public $challenge;

    public static function fromJson($json): ?VerifyResponseData
    {
        $data = json_decode($json);
        if ($data == null || !is_object($data)) {
            return null;
        }
        $instance = new self();
        $instance->event_id = $data->event_id;
        $instance->challenge = VerifyResponseChallengeData::fromStdClass($data->challenge);
        return $instance;
    }

    public static function fromStdClass($obj): VerifyResponseData
    {
        $instance = new self();
        $instance->event_id = $obj->event_id;
        $instance->challenge = VerifyResponseChallengeData::fromStdClass($obj->challenge);
        return $instance;
    }
}

class VerifyResponseError
{
    /** @var string */
    public $error_code;
    /** @var string */
    public $detail;

    public static function fromJson($json): ?VerifyResponseError
    {
        $data = json_decode($json);
        if ($data == null || !is_object($data)) {
            return null;
        }
        $instance = new self();
        $instance->error_code = $data->error_code;
        $instance->detail = $data->detail;
        return $instance;
    }

    public static function fromStdClass($obj): VerifyResponseError
    {
        $instance = new self();
        $instance->error_code = $obj->error_code;
        $instance->detail = $obj->detail;
        return $instance;
    }
}

class VerifyResponse
{
    /** @var bool */
    public $success;
    /** @var VerifyResponseData|null */
    public $data;
    /** @var VerifyResponseError|null */
    public $error;
    /** @var RiskIntelligence|null */
    public $risk_intelligence;
    /** @var object|null Raw untyped risk intelligence data */
    private $risk_intelligence_raw;

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
            
            // risk_intelligence is part of the data object in the API response
            if (isset($d->data->risk_intelligence)) {
                $instance->risk_intelligence_raw = $d->data->risk_intelligence;
                $instance->risk_intelligence = RiskIntelligence::fromStdClass($d->data->risk_intelligence);
            }
        }

        if (isset($d->error)) {
            $instance->error = VerifyResponseError::fromStdClass($d->error);
        }

        return $instance;
    }

    /**
     * Get the raw risk intelligence data as an untyped object.
     * This can be useful when you need access to the data in its original form
     * or when new fields are added that aren't yet supported by the typed API.
     * 
     * @return object|null The raw risk intelligence data, or null if not present
     */
    public function getRawRiskIntelligence(): ?object
    {
        return $this->risk_intelligence_raw;
    }
}
