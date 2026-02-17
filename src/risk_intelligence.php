<?php

declare(strict_types=1);

namespace FriendlyCaptcha\SDK;

/**
 * Risk score constants.
 * 
 * Risk scores are integer values between 0 and 5, where:
 * - 0 indicates unknown or missing
 * - 1 indicates very low risk
 * - 2 indicates low risk
 * - 3 indicates medium risk
 * - 4 indicates high risk
 * - 5 indicates very high risk
 */
class RiskScore
{
    /** Unknown or missing risk score */
    public const UNKNOWN = 0;
    
    /** Very low risk score value */
    public const VERY_LOW = 1;
    
    /** Low risk score value */
    public const LOW = 2;
    
    /** Medium risk score value */
    public const MEDIUM = 3;
    
    /** High risk score value */
    public const HIGH = 4;
    
    /** Very high risk score value */
    public const VERY_HIGH = 5;
}

/**
 * Risk assessment scores for different categories.
 * Each score is an integer value between 1 and 5.
 */
class RiskScores
{
    /**
     * Overall risk score that combines all available signals into a single score.
     * @var int
     */
    public $overall;
    
    /**
     * Risk score based on network-related signals such as IP reputation, ASN information, and geolocation.
     * Indicates how likely the request is to be automated, fraudulent or malicious based on network characteristics.
     * @var int
     */
    public $network;
    
    /**
     * Risk score based on browser-related signals such as user agent, browser identification, bot identification
     * and other client-side characteristics. Assesses the likelihood of the request being automated or coming
     * from a suspicious client.
     * @var int
     */
    public $browser;

    public static function fromStdClass($obj): RiskScores
    {
        $instance = new self();
        $instance->overall = $obj->overall ?? 0;
        $instance->network = $obj->network ?? 0;
        $instance->browser = $obj->browser ?? 0;
        return $instance;
    }
}

/**
 * Information about the Autonomous System associated with an IP address.
 */
class NetworkAS
{
    /** @var int Autonomous System Number */
    public $number;
    
    /** @var string AS name */
    public $name;
    
    /** @var string Company name */
    public $company;
    
    /** @var string Description */
    public $description;
    
    /** @var string Domain */
    public $domain;
    
    /** @var string Country code */
    public $country;
    
    /** @var string Regional Internet Registry */
    public $rir;
    
    /** @var string Route */
    public $route;
    
    /** @var string Type */
    public $type;

    public static function fromStdClass($obj): NetworkAS
    {
        $instance = new self();
        $instance->number = $obj->number ?? 0;
        $instance->name = $obj->name ?? '';
        $instance->company = $obj->company ?? '';
        $instance->description = $obj->description ?? '';
        $instance->domain = $obj->domain ?? '';
        $instance->country = $obj->country ?? '';
        $instance->rir = $obj->rir ?? '';
        $instance->route = $obj->route ?? '';
        $instance->type = $obj->type ?? '';
        return $instance;
    }
}

/**
 * Country information.
 */
class NetworkGeolocationCountry
{
    /** @var string Two-letter ISO 3166-1 alpha-2 country code */
    public $iso2;
    
    /** @var string Three-letter ISO 3166-1 alpha-3 country code */
    public $iso3;
    
    /** @var string English name of the country */
    public $name;
    
    /** @var string Native name of the country */
    public $name_native;
    
    /** @var string Geographic region */
    public $region;
    
    /** @var string Geographic subregion */
    public $subregion;
    
    /** @var string Currency code */
    public $currency;
    
    /** @var string Currency name */
    public $currency_name;
    
    /** @var string International dialing code */
    public $phone_code;
    
    /** @var string Capital city */
    public $capital;

    public static function fromStdClass($obj): NetworkGeolocationCountry
    {
        $instance = new self();
        $instance->iso2 = $obj->iso2 ?? '';
        $instance->iso3 = $obj->iso3 ?? '';
        $instance->name = $obj->name ?? '';
        $instance->name_native = $obj->name_native ?? '';
        $instance->region = $obj->region ?? '';
        $instance->subregion = $obj->subregion ?? '';
        $instance->currency = $obj->currency ?? '';
        $instance->currency_name = $obj->currency_name ?? '';
        $instance->phone_code = $obj->phone_code ?? '';
        $instance->capital = $obj->capital ?? '';
        return $instance;
    }
}

/**
 * Geographic information about an IP address.
 */
class NetworkGeolocation
{
    /** @var NetworkGeolocationCountry Country information */
    public $country;
    
    /** @var string City name (empty string if unknown) */
    public $city;
    
    /** @var string State/region/province (empty string if unknown) */
    public $state;

    public static function fromStdClass($obj): NetworkGeolocation
    {
        $instance = new self();
        $instance->country = NetworkGeolocationCountry::fromStdClass($obj->country);
        $instance->city = $obj->city ?? '';
        $instance->state = $obj->state ?? '';
        return $instance;
    }
}

/**
 * Abuse contact information for reporting network abuse.
 */
class NetworkAbuseContact
{
    /** @var string Postal address of the abuse contact */
    public $address;
    
    /** @var string Name of the abuse contact person or team */
    public $name;
    
    /** @var string Abuse contact email address */
    public $email;
    
    /** @var string Abuse contact phone number */
    public $phone;

    public static function fromStdClass($obj): NetworkAbuseContact
    {
        $instance = new self();
        $instance->address = $obj->address ?? '';
        $instance->name = $obj->name ?? '';
        $instance->email = $obj->email ?? '';
        $instance->phone = $obj->phone ?? '';
        return $instance;
    }
}

/**
 * IP anonymization and privacy information.
 */
class NetworkAnonymization
{
    /** @var int Likelihood that the IP is from a VPN service (0-5) */
    public $vpn_score;
    
    /** @var int Likelihood that the IP is from a proxy service (0-5) */
    public $proxy_score;
    
    /** @var bool Whether the IP is a Tor exit node */
    public $tor;
    
    /** @var bool Whether the IP is from iCloud Private Relay */
    public $icloud_private_relay;

    public static function fromStdClass($obj): NetworkAnonymization
    {
        $instance = new self();
        $instance->vpn_score = $obj->vpn_score ?? 0;
        $instance->proxy_score = $obj->proxy_score ?? 0;
        $instance->tor = $obj->tor ?? false;
        $instance->icloud_private_relay = $obj->icloud_private_relay ?? false;
        return $instance;
    }
}

/**
 * Network and IP information.
 */
class Network
{
    /** 
     * The IP address of the user when the risk intelligence data was gathered.
     * Note: The IP address is never stored on Friendly Captcha servers in an unhashed format.
     * @var string 
     */
    public $ip;
    
    /** @var NetworkAS|null Autonomous System information (null when IP Intelligence module not enabled) */
    public $as;
    
    /** @var NetworkGeolocation|null Geographic information (null when IP Intelligence module not enabled) */
    public $geolocation;
    
    /** @var NetworkAbuseContact|null Abuse contact information (null when IP Intelligence module not enabled) */
    public $abuse_contact;
    
    /** @var NetworkAnonymization|null Anonymization service detection (null when Anonymization Detection module not enabled) */
    public $anonymization;

    public static function fromStdClass($obj): Network
    {
        $instance = new self();
        $instance->ip = $obj->ip ?? '';
        $instance->as = isset($obj->as) ? NetworkAS::fromStdClass($obj->as) : null;
        $instance->geolocation = isset($obj->geolocation) ? NetworkGeolocation::fromStdClass($obj->geolocation) : null;
        $instance->abuse_contact = isset($obj->abuse_contact) ? NetworkAbuseContact::fromStdClass($obj->abuse_contact) : null;
        $instance->anonymization = isset($obj->anonymization) ? NetworkAnonymization::fromStdClass($obj->anonymization) : null;
        return $instance;
    }
}

/**
 * Time zone information from the browser.
 */
class ClientTimeZone
{
    /** @var string IANA time zone name */
    public $name;
    
    /** @var string Two-letter ISO 3166-1 alpha-2 country code derived from the time zone */
    public $country_iso2;

    public static function fromStdClass($obj): ClientTimeZone
    {
        $instance = new self();
        $instance->name = $obj->name ?? '';
        $instance->country_iso2 = $obj->country_iso2 ?? '';
        return $instance;
    }
}

/**
 * Detected browser information.
 */
class ClientBrowser
{
    /** @var string Browser ID (empty string if unknown) */
    public $id;
    
    /** @var string Browser name (empty string if unknown) */
    public $name;
    
    /** @var string Browser version (empty string if unknown) */
    public $version;
    
    /** @var string Release date in YYYY-MM-DD format (empty string if unknown) */
    public $release_date;

    public static function fromStdClass($obj): ClientBrowser
    {
        $instance = new self();
        $instance->id = $obj->id ?? '';
        $instance->name = $obj->name ?? '';
        $instance->version = $obj->version ?? '';
        $instance->release_date = $obj->release_date ?? '';
        return $instance;
    }
}

/**
 * Browser engine (rendering engine) information.
 */
class ClientBrowserEngine
{
    /** @var string Engine ID (empty string if unknown) */
    public $id;
    
    /** @var string Engine name (empty string if unknown) */
    public $name;
    
    /** @var string Engine version (empty string if unknown) */
    public $version;

    public static function fromStdClass($obj): ClientBrowserEngine
    {
        $instance = new self();
        $instance->id = $obj->id ?? '';
        $instance->name = $obj->name ?? '';
        $instance->version = $obj->version ?? '';
        return $instance;
    }
}

/**
 * Device type and screen information.
 */
class ClientDevice
{
    /** @var string Device type */
    public $type;
    
    /** @var string Device brand */
    public $brand;
    
    /** @var string Device model */
    public $model;

    public static function fromStdClass($obj): ClientDevice
    {
        $instance = new self();
        $instance->type = $obj->type ?? '';
        $instance->brand = $obj->brand ?? '';
        $instance->model = $obj->model ?? '';
        return $instance;
    }
}

/**
 * Operating system information.
 */
class ClientOS
{
    /** @var string OS ID (empty string if unknown) */
    public $id;
    
    /** @var string OS name (empty string if unknown) */
    public $name;
    
    /** @var string OS version */
    public $version;

    public static function fromStdClass($obj): ClientOS
    {
        $instance = new self();
        $instance->id = $obj->id ?? '';
        $instance->name = $obj->name ?? '';
        $instance->version = $obj->version ?? '';
        return $instance;
    }
}

/**
 * TLS/SSL signature information for client fingerprinting.
 */
class ClientTLSSignature
{
    /** @var string JA3 hash */
    public $ja3;
    
    /** @var string JA3N hash */
    public $ja3n;
    
    /** @var string JA4 signature */
    public $ja4;

    public static function fromStdClass($obj): ClientTLSSignature
    {
        $instance = new self();
        $instance->ja3 = $obj->ja3 ?? '';
        $instance->ja3n = $obj->ja3n ?? '';
        $instance->ja4 = $obj->ja4 ?? '';
        return $instance;
    }
}

/**
 * Known bot information.
 */
class ClientAutomationKnownBot
{
    /** @var bool Whether a known bot was detected */
    public $detected;
    
    /** @var string Bot identifier (empty if not detected) */
    public $id;
    
    /** @var string Bot name (empty if not detected) */
    public $name;
    
    /** @var string Bot type classification (empty if not detected) */
    public $type;
    
    /** @var string Link to bot documentation (empty if not detected) */
    public $url;

    public static function fromStdClass($obj): ClientAutomationKnownBot
    {
        $instance = new self();
        $instance->detected = $obj->detected ?? false;
        $instance->id = $obj->id ?? '';
        $instance->name = $obj->name ?? '';
        $instance->type = $obj->type ?? '';
        $instance->url = $obj->url ?? '';
        return $instance;
    }
}

/**
 * Automation tool information.
 */
class ClientAutomationTool
{
    /** @var bool Whether an automation tool was detected */
    public $detected;
    
    /** @var string Tool identifier (empty if not detected) */
    public $id;
    
    /** @var string Tool name (empty if not detected) */
    public $name;
    
    /** @var string Tool type (empty if not detected) */
    public $type;

    public static function fromStdClass($obj): ClientAutomationTool
    {
        $instance = new self();
        $instance->detected = $obj->detected ?? false;
        $instance->id = $obj->id ?? '';
        $instance->name = $obj->name ?? '';
        $instance->type = $obj->type ?? '';
        return $instance;
    }
}

/**
 * Automation and bot detection data.
 */
class ClientAutomation
{
    /** @var ClientAutomationTool Detected automation tool information */
    public $automation_tool;
    
    /** @var ClientAutomationKnownBot Detected known bot information */
    public $known_bot;

    public static function fromStdClass($obj): ClientAutomation
    {
        $instance = new self();
        $instance->automation_tool = ClientAutomationTool::fromStdClass($obj->automation_tool);
        $instance->known_bot = ClientAutomationKnownBot::fromStdClass($obj->known_bot);
        return $instance;
    }
}

/**
 * Client/device risk intelligence.
 */
class RiskIntelligenceClient
{
    /** @var string User-Agent HTTP header value */
    public $header_user_agent;
    
    /** @var ClientTimeZone|null Time zone information (null when Browser Identification module not enabled) */
    public $time_zone;
    
    /** @var ClientBrowser|null Browser information (null when Browser Identification module not enabled) */
    public $browser;
    
    /** @var ClientBrowserEngine|null Browser engine information (null when Browser Identification module not enabled) */
    public $browser_engine;
    
    /** @var ClientDevice|null Device information (null when Browser Identification module not enabled) */
    public $device;
    
    /** @var ClientOS|null Operating system information (null when Browser Identification module not enabled) */
    public $os;
    
    /** @var ClientTLSSignature|null TLS signature information (null when Bot Detection module not enabled) */
    public $tls_signature;
    
    /** @var ClientAutomation|null Automation detection data (null when Bot Detection module not enabled) */
    public $automation;

    public static function fromStdClass($obj): RiskIntelligenceClient
    {
        $instance = new self();
        $instance->header_user_agent = $obj->header_user_agent ?? '';
        $instance->time_zone = isset($obj->time_zone) ? ClientTimeZone::fromStdClass($obj->time_zone) : null;
        $instance->browser = isset($obj->browser) ? ClientBrowser::fromStdClass($obj->browser) : null;
        $instance->browser_engine = isset($obj->browser_engine) ? ClientBrowserEngine::fromStdClass($obj->browser_engine) : null;
        $instance->device = isset($obj->device) ? ClientDevice::fromStdClass($obj->device) : null;
        $instance->os = isset($obj->os) ? ClientOS::fromStdClass($obj->os) : null;
        $instance->tls_signature = isset($obj->tls_signature) ? ClientTLSSignature::fromStdClass($obj->tls_signature) : null;
        $instance->automation = isset($obj->automation) ? ClientAutomation::fromStdClass($obj->automation) : null;
        return $instance;
    }
}

/**
 * Risk Intelligence data providing comprehensive risk assessment and signals.
 * 
 * The Risk Intelligence data is organized into three high-level sections:
 * - risk_scores: Overall risk scores summarizing the assessment (0-5 scale, null when module not enabled)
 * - network: Information about the user's network and IP address
 * - client: Detected browser or bot information
 * 
 * Note: The structure may vary depending on which modules are enabled on your account.
 * The format is subject to change with new fields potentially being added in the future.
 */
class RiskIntelligence
{
    /** @var RiskScores|null Risk assessment scores (null when Risk Scores module not enabled) */
    public $risk_scores;
    
    /** @var Network Network and IP information */
    public $network;
    
    /** @var RiskIntelligenceClient User agent and device information */
    public $client;

    public static function fromStdClass($obj): RiskIntelligence
    {
        $instance = new self();
        $instance->risk_scores = isset($obj->risk_scores) ? RiskScores::fromStdClass($obj->risk_scores) : null;
        $instance->network = Network::fromStdClass($obj->network);
        $instance->client = RiskIntelligenceClient::fromStdClass($obj->client);
        return $instance;
    }
}
