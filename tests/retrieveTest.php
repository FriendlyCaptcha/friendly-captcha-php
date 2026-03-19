<?php

declare(strict_types=1);

namespace FriendlyCaptcha\SDK\Test;

use FriendlyCaptcha\SDK\{Client, ClientConfig, RiskIntelligenceRetrieveResponse};
use Exception;

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

const MOCK_SERVER_URL = "http://localhost:1090";

function loadRetrieveSDKTestsFromServer(string $serverURL)
{
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $serverURL . "/api/v1/riskIntelligence/retrieveTests");
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    $response = curl_exec($ch);
    if ($response === false) {
        throw new Exception("Failed to load tests from server " . curl_error($ch) . " " . curl_errno($ch));
    }
    curl_close($ch);
    $json = json_decode($response, true);
    return $json;
}


final class RiskIntelligenceRetrieveTest extends TestCase
{
    public function testNonEncodeableResponse(): void
    {
        $opts = new ClientConfig();
        $opts->setAPIKey("some-key");
        $client = new Client($opts);
        $result = $client->retrieveRiskIntelligence("\xB1\x31"); // This fails to encode to JSON in PHP.

        $this->assertTrue($result->isEncodeError());
        $this->assertFalse($result->isClientError());
        $this->assertFalse($result->isRequestError());
        $this->assertFalse($result->isDecodeError());
        $this->assertTrue($result->wasAbleToRetrieve());
        $this->assertFalse($result->isValid());
    }

    public function testNonReachableEndpoint(): void
    {
        $opts = new ClientConfig();
        $opts->setAPIKey("some-key")->setApiEndpoint("https://localhost:9999"); // Assuming there's nothing running on that port..
        $client = new Client($opts);
        $result = $client->retrieveRiskIntelligence("my-response");

        $this->assertTrue($result->isRequestError());
        $this->assertFalse($result->isClientError());
        $this->assertFalse($result->isEncodeError());
        $this->assertFalse($result->isDecodeError());
        $this->assertFalse($result->wasAbleToRetrieve());
        $this->assertFalse($result->isValid());
    }



    public static function sdkMockTestsProvider(): array
    {
        $cases = loadRetrieveSDKTestsFromServer(MOCK_SERVER_URL)["tests"];
        $testCases = array();
        foreach ($cases as $case) {
            $testCases[$case["name"]] = array($case);
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
        $opts->setAPIKey("some-key")->setApiEndpoint(MOCK_SERVER_URL);
        $client = new Client($opts);
        $result = $client->retrieveRiskIntelligence($test["token"]);

        $expectWasAbleToRetrieve = $test["expectation"]["was_able_to_retrieve"];
        $expectIsValid = $test["expectation"]["is_valid"];
        $expectIsClientError = $test["expectation"]["is_client_error"];
        $wasAbleToRetrieve = $result->wasAbleToRetrieve();
        $isValid = $result->isValid();
        $isClientError = $result->isClientError();

        $this->assertEquals($expectWasAbleToRetrieve, $wasAbleToRetrieve,
            "'was_able_to_retrieve' is not as expected: " . json_encode($wasAbleToRetrieve) . " result: " . print_r($result, true));

        $this->assertEquals($expectIsValid, $isValid,
            "'is_valid' is not as expected: " . json_encode($isValid) . " result: " . print_r($result, true));

        $this->assertEquals($expectIsClientError, $isClientError,
            "'is_client_error' is not as expected: " . json_encode($isClientError) . " result: " . print_r($result, true));

        $expectedResponse = RiskIntelligenceRetrieveResponse::fromJson(json_encode($test["retrieve_response"]));
        $actualResponse = $result->getResponse();
        $this->assertEquals($expectedResponse, $actualResponse);
    }
}
