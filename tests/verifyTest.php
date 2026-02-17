<?php

declare(strict_types=1);

namespace FriendlyCaptcha\SDK\Test;

use FriendlyCaptcha\SDK\{Client, ClientConfig};
use Exception;

use PHPUnit\Framework\Attributes\DataProvider;
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



    public static function sdkMockTestsProvider(): array
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
    #[DataProvider('sdkMockTestsProvider')]
    public function testSDKTestServerCase($test): void
    {
        $opts = new ClientConfig();
        $opts->setAPIKey("some-key")->setApiEndpoint(MOCK_SERVER_URL)->setStrict($test["strict"]);
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

        // Additional checks for successful responses
        $response = $result->getResponse();
        if ($response !== null && $response->success && isset($test['siteverify_response'])) {
            // The test data might already be an array or it might be a JSON string
            if (is_string($test['siteverify_response'])) {
                $expectedResponse = json_decode($test['siteverify_response']);
                $this->assertNotNull($expectedResponse, "Failed to decode expected siteverify response");
            } else {
                // Already decoded, convert array to object for consistent access
                $expectedResponse = json_decode(json_encode($test['siteverify_response']));
            }

            // Check event_id if present
            if (isset($expectedResponse->data->event_id)) {
                $this->assertEquals(
                    $expectedResponse->data->event_id,
                    $response->data->event_id ?? null,
                    "Event ID does not match expected value"
                );
            }

            // Check challenge data
            if (isset($expectedResponse->data->challenge)) {
                // Verify timestamp is properly parsed as DateTimeImmutable
                $this->assertInstanceOf(
                    \DateTimeImmutable::class,
                    $response->data->challenge->timestamp,
                    "Challenge timestamp should be a DateTimeImmutable object"
                );
                
                // Compare timestamps by converting both to Unix timestamps
                $expectedTimestamp = new \DateTimeImmutable($expectedResponse->data->challenge->timestamp);
                $this->assertEquals(
                    $expectedTimestamp->getTimestamp(),
                    $response->data->challenge->timestamp->getTimestamp(),
                    "Challenge timestamp does not match expected value"
                );
                
                $this->assertEquals(
                    $expectedResponse->data->challenge->origin,
                    $response->data->challenge->origin,
                    "Challenge origin does not match expected value"
                );
            }

            // Check risk intelligence data if present
            if (isset($expectedResponse->data->risk_intelligence)) {
                $this->assertNotNull(
                    $response->risk_intelligence,
                    "Risk Intelligence data should be present"
                );

                // Check specific fields: header_user_agent
                if (isset($expectedResponse->data->risk_intelligence->client->header_user_agent)) {
                    $this->assertEquals(
                        $expectedResponse->data->risk_intelligence->client->header_user_agent,
                        $response->risk_intelligence->client->header_user_agent,
                        "Risk Intelligence header_user_agent does not match expected value"
                    );
                }

                // Check specific fields: browser ID
                if (isset($expectedResponse->data->risk_intelligence->client->browser->id)) {
                    $this->assertEquals(
                        $expectedResponse->data->risk_intelligence->client->browser->id,
                        $response->risk_intelligence->client->browser->id,
                        "Risk Intelligence browser ID does not match expected value"
                    );
                }

                // Check that raw risk intelligence contains header_user_agent
                $rawRiskIntelligence = $response->getRawRiskIntelligence();
                $this->assertNotNull($rawRiskIntelligence, "Raw risk intelligence should be available");
                $rawJson = json_encode($rawRiskIntelligence);
                $this->assertTrue(
                    strpos($rawJson, 'header_user_agent') !== false,
                    "Raw risk intelligence JSON should contain 'header_user_agent'"
                );
            }
        }
    }
}
