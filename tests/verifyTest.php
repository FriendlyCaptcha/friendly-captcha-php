<?php

declare(strict_types=1);

namespace FriendlyCaptcha\SDK\Test;

use FriendlyCaptcha\SDK\{Client, ClientConfig};
use Exception;

use PHPUnit\Framework\TestCase;

const MOCK_SERVER_URL = "http://localhost:1090";

function loadSDKTestsFromServer(string $serverURL)
{
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $serverURL . "/api/v1/tests");
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    $response = curl_exec($ch);
    if ($response === false) {
        throw new Exception("Failed to load tests from server " . curl_error($ch) . " " . curl_errno($ch));
    }
    curl_close($ch);
    $json = json_decode($response, true);
    return $json;
}


final class VerifyTest extends TestCase
{
    public function testConfigWithoutAPIKeyThrows(): void
    {
        $this->expectException(Exception::class);
        $this->expectExceptionMessage("API key is required");
        $opts = new ClientConfig();
        $client = new Client($opts);
    }
    public function testConfigInvalidEndpointThrows(): void
    {
        $this->expectException(Exception::class);
        $opts = new ClientConfig();
        $opts->setSiteverifyEndpoint("something-invalid-that-is-not-a-url");
    }

    public function testNonEncodeableResponse(): void
    {
        $opts = new ClientConfig();
        $opts->setAPIKey("some-key");
        $client = new Client($opts);
        $result = $client->verifyCaptchaResponse("\xB1\x31"); // This fails to encode to JSON in PHP.

        $this->assertTrue($result->isEncodeError());
        $this->assertFalse($result->shouldAccept());
        $this->assertFalse($result->isClientError());
        $this->assertFalse($result->isRequestError());
        $this->assertFalse($result->isDecodeError());
        $this->assertTrue($result->wasAbleToVerify());
    }

    public function testNonReachableEndpoint(): void
    {
        $opts = new ClientConfig();
        $opts->setAPIKey("some-key")->setSiteverifyEndpoint("https://localhost:9999"); // Assuming there's nothing running on that port..
        $client = new Client($opts);
        $result = $client->verifyCaptchaResponse("my-response");

        $this->assertTrue($result->isRequestError());
        $this->assertTrue($result->shouldAccept());
        $this->assertFalse($result->isClientError());
        $this->assertFalse($result->isEncodeError());
        $this->assertFalse($result->isDecodeError());
    }



    public function sdkMockTestsProvider(): array
    {
        $cases = loadSDKTestsFromServer(MOCK_SERVER_URL)["tests"];
        $testCases = array();
        foreach ($cases as $case) {
            $testCases[] = array($case);
        }
        return $testCases;
    }

    /**
     * @dataProvider sdkMockTestsProvider
     */
    public function testSDKTestServerCase($test): void
    {
        $opts = new ClientConfig();
        $opts->setAPIKey("some-key")->setSiteverifyEndpoint(MOCK_SERVER_URL . "/api/v2/captcha/siteverify")->setStrict($test["strict"]); // Assuming there's nothing running on that port..
        $client = new Client($opts);
        $result = $client->verifyCaptchaResponse($test["response"]);

        $expectWasAbleToVerify = $test["expectation"]["was_able_to_verify"];
        $expectShouldAccept = $test["expectation"]["should_accept"];
        $expectIsClientError = $test["expectation"]["is_client_error"];
        $shouldAccept = $result->shouldAccept();
        $wasAbleToVerify = $result->wasAbleToVerify();
        $isClientError = $result->isClientError();

        $this->assertEquals($expectShouldAccept, $shouldAccept,
            "'should_accept' is not as expected, should accept: " . json_encode($shouldAccept) . " result: " . print_r($result, true));

        $this->assertEquals($expectWasAbleToVerify, $wasAbleToVerify,
            "'was_able_to_verify' is not as expected, was able to verify: " . json_encode($wasAbleToVerify) . " result: " . print_r($result, true));

        $this->assertEquals($expectIsClientError, $isClientError,
            "'is_client_error' is not as expected, is client error: " . json_encode($isClientError) . " result: " . print_r($result, true));


        if ($wasAbleToVerify) {
            $this->assertEquals($result->shouldAccept(), $result->getResponse()->success, "shouldAccept and response->success should be the same in case of succesful verification");
        } else {
            if ($result->isStrict()) {
                $this->assertTrue($result->shouldReject(), "strict mode should reject when not able to verify");
            } else {
                $this->assertTrue($result->shouldAccept(), "non-strict mode should accept when not able to verify");
            }
        }
    }
}
