<?php

declare(strict_types=1);

namespace FriendlyCaptcha\SDK;

use DateTimeImmutable;

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
        $instance->timestamp = DateTimeImmutable::createFromFormat("c", $data->timestamp);
        $instance->origin = $data->origin;
        return $instance;
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
    /** @var VerifyResponseChallengeData */
    public $challenge;

    public static function fromJson($json): ?VerifyResponseData
    {
        $data = json_decode($json);
        if ($data == null || !is_object($data)) {
            return null;
        }
        $instance = new self();
        $instance->challenge = VerifyResponseChallengeData::fromStdClass($data->challenge);
        return $instance;
    }

    public static function fromStdClass($obj): VerifyResponseData
    {
        $instance = new self();
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
            $instance->error = VerifyResponseError::fromStdClass($d->error);
        }

        return $instance;
    }
}
