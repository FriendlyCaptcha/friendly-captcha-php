<?php

declare(strict_types=1);

namespace FriendlyCaptcha\SDK;

class RiskScoresData
{
    /**
     * RiskScoresData summarizes the entire risk intelligence assessment into scores per category.
     *
     * Available when the Risk Scores module is enabled for your account.
     * Null when the Risk Scores module is not enabled for your account.
     */

    /** @var int Overall risk score combining all signals. */
    public $overall;
    /**
     * @var int Network-related risk score. Captures likelihood of automation/malicious activity based on
     * IP address, ASN, reputation, geolocation, past abuse from this network, and other network signals.
     */
    public $network;
    /**
     * @var int Browser-related risk score. Captures likelihood of automation, malicious activity or browser spoofing based on
     * user agent consistency, automation traces, past abuse, and browser characteristics.
     */
    public $browser;

    public static function fromStdClass($obj): RiskScoresData
    {
        $instance = new self();
        $instance->overall = $obj->overall;
        $instance->network = $obj->network;
        $instance->browser = $obj->browser;
        return $instance;
    }
}

class NetworkAutonomousSystemData
{
    /**
     * NetworkAutonomousSystemData contains information about the AS that owns the IP.
     *
     * Available when the IP Intelligence module is enabled for your account.
     * Null when the IP Intelligence module is not enabled for your account.
     */

    /**
     * @var int Autonomous System Number (ASN) identifier.
     * Example: 3209 for Vodafone GmbH
     */
    public $number;
    /**
     * @var string Name of the autonomous system. This is usually a short name or handle.
     * Example: "VODANET"
     */
    public $name;
    /**
     * @var string Company is the organization name that owns the ASN.
     * Example: "Vodafone GmbH"
     */
    public $company;
    /**
     * @var string Description of the company that owns the ASN.
     * Example: "Provides mobile and fixed broadband and telecommunication services to consumers and businesses."
     */
    public $description;
    /**
     * @var string Domain name associated with the ASN.
     * Example: "vodafone.de"
     */
    public $domain;
    /**
     * @var string Two-letter ISO 3166-1 alpha-2 country code where the ASN is registered.
     * Example: "DE"
     */
    public $country;
    /**
     * @var string Regional Internet Registry that allocated the ASN.
     * Example: "RIPE"
     */
    public $rir;
    /**
     * @var string IP route associated with the ASN in CIDR notation.
     * Example: "88.64.0.0/12"
     */
    public $route;
    /**
     * @var string Autonomous system type.
     * Example: "isp"
     */
    public $type;

    public static function fromStdClass($obj): NetworkAutonomousSystemData
    {
        $instance = new self();
        $instance->number = $obj->number;
        $instance->name = $obj->name;
        $instance->company = $obj->company;
        $instance->description = $obj->description;
        $instance->domain = $obj->domain;
        $instance->country = $obj->country;
        $instance->rir = $obj->rir;
        $instance->route = $obj->route;
        $instance->type = $obj->type;
        return $instance;
    }
}

class NetworkGeolocationCountryData
{
    /** NetworkGeolocationCountryData contains detailed country data. */

    /**
     * @var string Two-letter ISO 3166-1 alpha-2 country code.
     * Example: "DE"
     */
    public $iso2;
    /**
     * @var string Three-letter ISO 3166-1 alpha-3 country code.
     * Example: "DEU"
     */
    public $iso3;
    /**
     * @var string English name of the country.
     * Example: "Germany"
     */
    public $name;
    /**
     * @var string Native name of the country.
     * Example: "Deutschland"
     */
    public $name_native;
    /**
     * @var string Major world region.
     * Example: "Europe"
     */
    public $region;
    /**
     * @var string More specific world region.
     * Example: "Western Europe"
     */
    public $subregion;
    /**
     * @var string ISO 4217 currency code.
     * Example: "EUR"
     */
    public $currency;
    /**
     * @var string Full name of the currency.
     * Example: "Euro"
     */
    public $currency_name;
    /**
     * @var string International dialing code.
     * Example: "49"
     */
    public $phone_code;
    /**
     * @var string Name of the capital city.
     * Example: "Berlin"
     */
    public $capital;

    public static function fromStdClass($obj): NetworkGeolocationCountryData
    {
        $instance = new self();
        $instance->iso2 = $obj->iso2;
        $instance->iso3 = $obj->iso3;
        $instance->name = $obj->name;
        $instance->name_native = $obj->name_native;
        $instance->region = $obj->region;
        $instance->subregion = $obj->subregion;
        $instance->currency = $obj->currency;
        $instance->currency_name = $obj->currency_name;
        $instance->phone_code = $obj->phone_code;
        $instance->capital = $obj->capital;
        return $instance;
    }
}

class NetworkGeolocationData
{
    /**
     * NetworkGeolocationData contains geographic location of the IP address.
     *
     * Available when the IP Intelligence module is enabled.
     * Null when the IP Intelligence module is not enabled.
     */

    /** @var NetworkGeolocationCountryData Country information. */
    public $country;
    /**
     * @var string City name. Empty string if unknown.
     * Example: "Eschborn"
     */
    public $city;
    /**
     * @var string State, region, or province. Empty string if unknown.
     * Example: "Hessen"
     */
    public $state;

    public static function fromStdClass($obj): NetworkGeolocationData
    {
        $instance = new self();
        $instance->country = NetworkGeolocationCountryData::fromStdClass($obj->country);
        $instance->city = $obj->city;
        $instance->state = $obj->state;
        return $instance;
    }
}

class NetworkAbuseContactData
{
    /**
     * NetworkAbuseContactData contains contact details for reporting abuse.
     *
     * Available when the IP Intelligence module is enabled.
     * Null when the IP Intelligence module is not enabled.
     */

    /**
     * @var string Postal address of the abuse contact.
     * Example: "Vodafone GmbH, Campus Eschborn, Duesseldorfer Strasse 15, D-65760 Eschborn, Germany"
     */
    public $address;
    /**
     * @var string Name of the abuse contact person or team.
     * Example: "Vodafone Germany IP Core Backbone"
     */
    public $name;
    /**
     * @var string Abuse contact email address.
     * Example: "abuse.de@vodafone.com"
     */
    public $email;
    /**
     * @var string Abuse contact phone number.
     * Example: "+49 6196 52352105"
     */
    public $phone;

    public static function fromStdClass($obj): NetworkAbuseContactData
    {
        $instance = new self();
        $instance->address = $obj->address;
        $instance->name = $obj->name;
        $instance->email = $obj->email;
        $instance->phone = $obj->phone;
        return $instance;
    }
}

class NetworkAnonymizationData
{
    /**
     * NetworkAnonymizationData contains detection of VPNs, proxies, and anonymization services.
     *
     * Available when the Anonymization Detection module is enabled.
     * Null when the Anonymization Detection module is not enabled.
     */

    /** @var int Likelihood that the IP is from a VPN service. */
    public $vpn_score;
    /** @var int Likelihood that the IP is from a proxy service. */
    public $proxy_score;
    /** @var bool Whether the IP is a Tor exit node. */
    public $tor;
    /** @var bool Whether the IP is from iCloud Private Relay. */
    public $icloud_private_relay;

    public static function fromStdClass($obj): NetworkAnonymizationData
    {
        $instance = new self();
        $instance->vpn_score = $obj->vpn_score;
        $instance->proxy_score = $obj->proxy_score;
        $instance->tor = $obj->tor;
        $instance->icloud_private_relay = $obj->icloud_private_relay;
        return $instance;
    }
}

class NetworkData
{
    /** NetworkData contains information about the network. */

    /**
     * @var string IP address used when requesting the challenge.
     * Example: "88.64.4.22"
     */
    public $ip;
    /**
     * @var NetworkAutonomousSystemData|null Autonomous System information.
     *
     * Available when the IP Intelligence module is enabled.
     * Null when the IP Intelligence module is not enabled.
     */
    public $as;
    /**
     * @var NetworkGeolocationData|null Geolocation information.
     *
     * Available when the IP Intelligence module is enabled.
     * Null when the IP Intelligence module is not enabled.
     */
    public $geolocation;
    /**
     * @var NetworkAbuseContactData|null Abuse contact information.
     *
     * Available when the IP Intelligence module is enabled.
     * Null when the IP Intelligence module is not enabled.
     */
    public $abuse_contact;
    /**
     * @var NetworkAnonymizationData|null IP masking/anonymization information.
     *
     * Available when the Anonymization Detection module is enabled.
     * Null when the Anonymization Detection module is not enabled.
     */
    public $anonymization;

    public static function fromStdClass($obj): NetworkData
    {
        $instance = new self();
        $instance->ip = $obj->ip;
        $instance->as = isset($obj->as) ? NetworkAutonomousSystemData::fromStdClass($obj->as) : null;
        $instance->geolocation = isset($obj->geolocation) ? NetworkGeolocationData::fromStdClass($obj->geolocation) : null;
        $instance->abuse_contact = isset($obj->abuse_contact) ? NetworkAbuseContactData::fromStdClass($obj->abuse_contact) : null;
        $instance->anonymization = isset($obj->anonymization) ? NetworkAnonymizationData::fromStdClass($obj->anonymization) : null;
        return $instance;
    }
}

class ClientTimeZoneData
{
    /**
     * ClientTimeZoneData contains IANA time zone data.
     *
     * Available when the Browser Identification module is enabled.
     * Null when the Browser Identification module is not enabled.
     */

    /**
     * @var string IANA time zone name reported by the browser.
     * Example: "America/New_York" or "Europe/Berlin"
     */
    public $name;
    /**
     * @var string Two-letter ISO 3166-1 alpha-2 country code derived from the time zone.
     * "XU" if timezone is missing or cannot be mapped to a country (e.g., "Etc/UTC").
     * Example: "US" or "DE"
     */
    public $country_iso2;

    public static function fromStdClass($obj): ClientTimeZoneData
    {
        $instance = new self();
        $instance->name = $obj->name;
        $instance->country_iso2 = $obj->country_iso2;
        return $instance;
    }
}

class ClientBrowserData
{
    /**
     * ClientBrowserData contains detected browser details.
     *
     * Available when the Browser Identification module is enabled.
     * Null when the Browser Identification module is not enabled.
     */

    /**
     * @var string Unique browser identifier. Empty string if browser could not be identified.
     * Example: "firefox", "chrome", "chrome_android", "edge", "safari", "safari_ios", "webview_ios"
     */
    public $id;
    /**
     * @var string Human-readable browser name. Empty string if browser could not be identified.
     * Example: "Firefox", "Chrome", "Edge", "Safari", "Safari on iOS", "WebView on iOS"
     */
    public $name;
    /**
     * @var string Browser version name. Assumed to be the most recent release matching the signature if exact version unknown. Empty if unknown.
     * Example: "146.0" or "16.5"
     */
    public $version;
    /**
     * @var string Release date of the browser version in "YYYY-MM-DD" format. Empty string if unknown.
     * Example: "2026-01-28"
     */
    public $release_date;

    public static function fromStdClass($obj): ClientBrowserData
    {
        $instance = new self();
        $instance->id = $obj->id;
        $instance->name = $obj->name;
        $instance->version = $obj->version;
        $instance->release_date = $obj->release_date;
        return $instance;
    }
}

class ClientBrowserEngineData
{
    /**
     * ClientBrowserEngineData contains detected rendering engine details.
     *
     * Available when the Browser Identification module is enabled.
     * Null when the Browser Identification module is not enabled.
     */

    /**
     * @var string Unique rendering engine identifier. Empty string if engine could not be identified.
     * Example: "gecko", "blink", "webkit"
     */
    public $id;
    /**
     * @var string Human-readable engine name. Empty string if engine could not be identified.
     * Example: "Gecko", "Blink", "WebKit"
     */
    public $name;
    /**
     * @var string Rendering engine version. Assumed to be the most recent release matching the signature if exact version unknown. Empty if unknown.
     * Example: "146.0" or "16.5"
     */
    public $version;

    public static function fromStdClass($obj): ClientBrowserEngineData
    {
        $instance = new self();
        $instance->id = $obj->id;
        $instance->name = $obj->name;
        $instance->version = $obj->version;
        return $instance;
    }
}

class ClientDeviceData
{
    /**
     * ClientDeviceData contains detected device details.
     *
     * Available when the Browser Identification module is enabled.
     * Null when the Browser Identification module is not enabled.
     */

    /**
     * @var string Device type.
     * Example: "desktop", "mobile", "tablet"
     */
    public $type;
    /**
     * @var string Device brand.
     * Example: "Apple", "Samsung", "Google"
     */
    public $brand;
    /**
     * @var string Device model name.
     * Example: "iPhone 17", "Galaxy S21 (SM-G991B)", "Pixel 10"
     */
    public $model;

    public static function fromStdClass($obj): ClientDeviceData
    {
        $instance = new self();
        $instance->type = $obj->type;
        $instance->brand = $obj->brand;
        $instance->model = $obj->model;
        return $instance;
    }
}

class ClientOSData
{
    /**
     * ClientOSData contains detected OS details.
     *
     * Available when the Browser Identification module is enabled.
     * Null when the Browser Identification module is not enabled.
     */

    /**
     * @var string Unique operating system identifier. Empty string if OS could not be identified.
     * Example: "windows", "macos", "ios", "android", "linux"
     */
    public $id;
    /**
     * @var string Human-readable operating system name. Empty string if OS could not be identified.
     * Example: "Windows", "macOS", "iOS", "Android", "Linux"
     */
    public $name;
    /**
     * @var string Operating system version.
     * Example: "10", "11.2.3", "14.4"
     */
    public $version;

    public static function fromStdClass($obj): ClientOSData
    {
        $instance = new self();
        $instance->id = $obj->id;
        $instance->name = $obj->name;
        $instance->version = $obj->version;
        return $instance;
    }
}

class TLSSignatureData
{
    /**
     * TLSSignatureData contains TLS client hello signatures.
     *
     * Available when the Bot Detection module is enabled.
     * Null when the Bot Detection module is not enabled.
     */

    /**
     * @var string JA3 hash.
     * Example: "d87a30a5782a73a83c1544bb06332780"
     */
    public $ja3;
    /**
     * @var string JA3N hash.
     * Example: "28ecc2d2875b345cecbb632b12d8c1e0"
     */
    public $ja3n;
    /**
     * @var string JA4 signature.
     * Example: "t13d1516h2_8daaf6152771_02713d6af862"
     */
    public $ja4;

    public static function fromStdClass($obj): TLSSignatureData
    {
        $instance = new self();
        $instance->ja3 = $obj->ja3;
        $instance->ja3n = $obj->ja3n;
        $instance->ja4 = $obj->ja4;
        return $instance;
    }
}

class ClientAutomationKnownBotData
{
    /** ClientAutomationKnownBotData contains detected known bot details. */

    /** @var bool Whether a known bot was detected. */
    public $detected;
    /**
     * @var string Bot identifier. Empty if no bot detected.
     * Example: "googlebot", "bingbot", "chatgpt"
     */
    public $id;
    /**
     * @var string Human-readable bot name. Empty if no bot detected.
     * Example: "Googlebot", "Bingbot", "ChatGPT"
     */
    public $name;
    /** @var string Bot type classification. Empty if no bot detected. */
    public $type;
    /**
     * @var string Link to bot documentation. Empty if no bot detected.
     * Example: "https://developers.google.com/search/docs/crawling-indexing/googlebot"
     */
    public $url;

    public static function fromStdClass($obj): ClientAutomationKnownBotData
    {
        $instance = new self();
        $instance->detected = $obj->detected;
        $instance->id = $obj->id;
        $instance->name = $obj->name;
        $instance->type = $obj->type;
        $instance->url = $obj->url;
        return $instance;
    }
}

class ClientAutomationToolData
{
    /** ClientAutomationToolData contains detected automation tool details. */

    /** @var bool Whether an automation tool was detected. */
    public $detected;
    /**
     * @var string Automation tool identifier. Empty if no tool detected.
     * Example: "puppeteer", "selenium", "playwright"
     */
    public $id;
    /**
     * @var string Human-readable tool name. Empty if no tool detected.
     * Example: "Puppeteer", "Selenium WebDriver", "Playwright"
     */
    public $name;
    /** @var string Automation tool type. Empty if no tool detected. */
    public $type;

    public static function fromStdClass($obj): ClientAutomationToolData
    {
        $instance = new self();
        $instance->detected = $obj->detected;
        $instance->id = $obj->id;
        $instance->name = $obj->name;
        $instance->type = $obj->type;
        return $instance;
    }
}

class ClientAutomationData
{
    /**
     * ClientAutomationData contains information about detected automation.
     *
     * Available when the Bot Detection module is enabled.
     * Null when the Bot Detection module is not enabled.
     */

    /** @var ClientAutomationToolData Detected automation tool information. */
    public $automation_tool;
    /** @var ClientAutomationKnownBotData Detected known bot information. */
    public $known_bot;

    public static function fromStdClass($obj): ClientAutomationData
    {
        $instance = new self();
        $instance->automation_tool = ClientAutomationToolData::fromStdClass($obj->automation_tool);
        $instance->known_bot = ClientAutomationKnownBotData::fromStdClass($obj->known_bot);
        return $instance;
    }
}

class ClientData
{
    /** ClientData contains information about the user agent and device. */

    /**
     * @var string User-Agent HTTP header value.
     * Example: "Mozilla/5.0 (Macintosh; Intel Mac OS X 10.15; rv:146.0) Gecko/20100101 Firefox/146.0"
     */
    public $header_user_agent;
    /**
     * @var ClientTimeZoneData|null Time zone information.
     *
     * Available when the Browser Identification module is enabled.
     * Null when the Browser Identification module is not enabled.
     */
    public $time_zone;
    /**
     * @var ClientBrowserData|null Browser information.
     *
     * Available when the Browser Identification module is enabled.
     * Null when the Browser Identification module is not enabled.
     */
    public $browser;
    /**
     * @var ClientBrowserEngineData|null Browser engine information.
     *
     * Available when the Browser Identification module is enabled.
     * Null when the Browser Identification module is not enabled.
     */
    public $browser_engine;
    /**
     * @var ClientDeviceData|null Device information.
     *
     * Available when the Browser Identification module is enabled.
     * Null when the Browser Identification module is not enabled.
     */
    public $device;
    /**
     * @var ClientOSData|null OS information.
     *
     * Available when the Browser Identification module is enabled.
     * Null when the Browser Identification module is not enabled.
     */
    public $os;
    /**
     * @var TLSSignatureData|null TLS signatures.
     *
     * Available when the Bot Detection module is enabled.
     * Null when the Bot Detection module is not enabled.
     */
    public $tls_signature;
    /**
     * @var ClientAutomationData|null Automation detection data.
     *
     * Available when the Bot Detection module is enabled.
     * Null when the Bot Detection module is not enabled.
     */
    public $automation;

    public static function fromStdClass($obj): ClientData
    {
        $instance = new self();
        $instance->header_user_agent = $obj->header_user_agent;
        $instance->time_zone = isset($obj->time_zone) ? ClientTimeZoneData::fromStdClass($obj->time_zone) : null;
        $instance->browser = isset($obj->browser) ? ClientBrowserData::fromStdClass($obj->browser) : null;
        $instance->browser_engine = isset($obj->browser_engine) ? ClientBrowserEngineData::fromStdClass($obj->browser_engine) : null;
        $instance->device = isset($obj->device) ? ClientDeviceData::fromStdClass($obj->device) : null;
        $instance->os = isset($obj->os) ? ClientOSData::fromStdClass($obj->os) : null;
        $instance->tls_signature = isset($obj->tls_signature) ? TLSSignatureData::fromStdClass($obj->tls_signature) : null;
        $instance->automation = isset($obj->automation) ? ClientAutomationData::fromStdClass($obj->automation) : null;
        return $instance;
    }
}

class RiskIntelligenceData
{
    /**
     * RiskIntelligenceData contains all risk intelligence information.
     *
     * Field availability depends on enabled modules.
     */

    /**
     * @var RiskScoresData|null Risk scores from various signals, these summarize the risk intelligence assessment.
     *
     * Available when the Risk Scores module is enabled.
     * Null when the Risk Scores module is not enabled.
     */
    public $risk_scores;
    /** @var NetworkData Network-related risk intelligence. */
    public $network;
    /** @var ClientData Client/device risk intelligence. */
    public $client;

    public static function fromStdClass($obj): RiskIntelligenceData
    {
        $instance = new self();
        $instance->risk_scores = isset($obj->risk_scores) ? RiskScoresData::fromStdClass($obj->risk_scores) : null;
        $instance->network = NetworkData::fromStdClass($obj->network);
        $instance->client = ClientData::fromStdClass($obj->client);
        return $instance;
    }
}
