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

    public function testConfigInvalidEndpointThrows(): void
    {
        $this->expectException(Exception::class);
        $opts = new ClientConfig();
        $opts->setSiteverifyEndpoint("something-invalid-that-is-not-a-url");
    }

    public function testApiEndpointWithPathThrows(): void
    {
        $this->expectException(Exception::class);
        $this->expectExceptionMessage("API endpoint should not include a path");
        $opts = new ClientConfig();
        $opts->setApiEndpoint("https://api.example.com/api/v2/captcha/siteverify");
    }

    public function testApiEndpointAcceptsBaseUrl(): void
    {
        $opts = new ClientConfig();
        $opts->setAPIKey("test-key");
        $opts->setApiEndpoint("https://api.example.com");
        $this->assertEquals("https://api.example.com", $opts->apiEndpoint);
    }

    public function testApiEndpointAcceptsBaseUrlWithPort(): void
    {
        $opts = new ClientConfig();
        $opts->setAPIKey("test-key");
        $opts->setApiEndpoint("https://api.example.com:8080");
        $this->assertEquals("https://api.example.com:8080", $opts->apiEndpoint);
    }

    public function testApiEndpointStripsTrailingSlash(): void
    {
        $opts = new ClientConfig();
        $opts->setAPIKey("test-key");
        $opts->setApiEndpoint("https://api.example.com/");
        $this->assertEquals("https://api.example.com", $opts->apiEndpoint);
    }

    public function testApiEndpointAcceptsShorthands(): void
    {
        $opts = new ClientConfig();
        $opts->setAPIKey("test-key");
        
        $opts->setApiEndpoint("eu");
        $this->assertEquals("eu", $opts->apiEndpoint);
        
        $opts->setApiEndpoint("global");
        $this->assertEquals("global", $opts->apiEndpoint);
    }

    public function testSiteverifyEndpointStripsPath(): void
    {
        $opts = new ClientConfig();
        $opts->setAPIKey("test-key");
        $opts->setSiteverifyEndpoint("https://api.example.com/api/v2/captcha/siteverify");
        $this->assertEquals("https://api.example.com", $opts->siteverifyEndpoint);
        $this->assertEquals("https://api.example.com", $opts->apiEndpoint);
    }

    public function testSiteverifyEndpointStripsPathWithPort(): void
    {
        $opts = new ClientConfig();
        $opts->setAPIKey("test-key");
        $opts->setSiteverifyEndpoint("http://localhost:9999/some/path/here");
        $this->assertEquals("http://localhost:9999", $opts->siteverifyEndpoint);
        $this->assertEquals("http://localhost:9999", $opts->apiEndpoint);
    }

    public function testClientUsesApiEndpoint(): void
    {
        $opts = new ClientConfig();
        $opts->setAPIKey("test-key");
        $opts->setApiEndpoint("http://localhost:1090");
        $client = new Client($opts);
        
        // The client should construct the full URL with the path
        $reflection = new \ReflectionClass($client);
        $property = $reflection->getProperty('resolvedSiteverifyEndpoint');
        $property->setAccessible(true);
        $resolved = $property->getValue($client);
        
        $this->assertEquals("http://localhost:1090/api/v2/captcha/siteverify", $resolved);
    }

    public function testBackwardsCompatibilityWithSiteverifyEndpointFullPath(): void
    {
        $opts = new ClientConfig();
        $opts->setAPIKey("test-key");
        // Old usage: passing full URL with path
        $opts->setSiteverifyEndpoint("http://localhost:1090/api/v2/captcha/siteverify");
        $client = new Client($opts);
        
        // Should strip path and reconstruct correctly
        $reflection = new \ReflectionClass($client);
        $property = $reflection->getProperty('resolvedSiteverifyEndpoint');
        $property->setAccessible(true);
        $resolved = $property->getValue($client);
        
        $this->assertEquals("http://localhost:1090/api/v2/captcha/siteverify", $resolved);
    }
}
