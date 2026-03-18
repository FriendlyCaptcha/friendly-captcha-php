<?php

declare(strict_types=1);

namespace FriendlyCaptcha\SDK\Test;

use FriendlyCaptcha\SDK\{Client, ClientConfig};
use Exception;

use PHPUnit\Framework\TestCase;

final class ConfigTest extends TestCase
{
    public function testConfigWithoutAPIKeyThrows(): void
    {
        $this->expectException(Exception::class);
        $this->expectExceptionMessage("API key is required");
        $opts = new ClientConfig();
        $client = new Client($opts);
    }

    public function testConfigInvalidSiteverifyEndpointThrows(): void
    {
        $this->expectException(Exception::class);
        $opts = new ClientConfig();
        $opts->setSiteverifyEndpoint("something-invalid-that-is-not-a-url");
    }

    public function testConfigInvalidApiEndpointThrows(): void
    {
        $this->expectException(Exception::class);
        $opts = new ClientConfig();
        $opts->setApiEndpoint("something-invalid-that-is-not-a-url");
    }

    public function testConfigApiEndpointStripsPaths(): void
    {
        $opts = new ClientConfig();
        $opts->setApiEndpoint("https://exmple.com/a/b/c");
        $this->assertEquals("https://exmple.com", $opts->apiEndpoint);
    }

    public function testConfigSiteverifyEndpointStripsPaths(): void
    {
        $opts = new ClientConfig();
        $opts->setSiteverifyEndpoint("https://exmple.com/a/b/c");
        $this->assertEquals("https://exmple.com", $opts->siteverifyEndpoint);
    }
}
