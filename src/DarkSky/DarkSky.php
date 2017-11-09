<?php
/**
 * Main class to be instantiated by the DarkSky Factory
 * User: erwinkloosterboer
 * Date: 09/11/2017
 * Time: 09:32
 */

namespace NF\DarkSky;


use GuzzleHttp\Exception\TransferException;
use NF\DarkSky\Request\DarkSkyRequest;
use GuzzleHttp\Client;

class DarkSky {

    const UNIT_AUTO = 'auto'; //automatically select units based on geographic location
    const UNIT_CATALAN = 'ca'; // same as si, except that windSpeed is in kilometers per hour
    const UNIT_UK = 'uk2'; //same as si, except that nearestStormDistance and visibility are in miles and windSpeed is in miles per hour
    const UNIT_IMPERIAL = 'us'; //Imperial units (the default)
    const UNIT_SMITHSONIAN = 'si'; // SI units

    const SUPPORTED_LANGUAGES = [
        'ar',
        'az',
        'be',
        'bg',
        'bs',
        'ca',
        'cs',
        'de',
        'el',
        'en',
        'es',
        'et',
        'fr',
        'hr',
        'hu',
        'id',
        'it',
        'is',
        'kw',
        'nb',
        'nl',
        'pl',
        'pt',
        'ru',
        'sk',
        'sl',
        'sr',
        'sv',
        'tet',
        'tr',
        'uk',
        'x-pig-latin',
        'zh',
        'zh-tw'
    ];

    /*
Available language (https://darksky.net/dev/docs @ 2017-11-09)
ar: Arabic
az: Azerbaijani
be: Belarusian
bg: Bulgarian
bs: Bosnian
ca: Catalan
cs: Czech
de: German
el: Greek
en: English (which is the default)
es: Spanish
et: Estonian
fr: French
hr: Croatian
hu: Hungarian
id: Indonesian
it: Italian
is: Icelandic
kw: Cornish
nb: Norwegian Bokmål
nl: Dutch
pl: Polish
pt: Portuguese
ru: Russian
sk: Slovak
sl: Slovenian
sr: Serbian
sv: Swedish
tet: Tetum
tr: Turkish
uk: Ukrainian
x-pig-latin: Igpay Atinlay
zh: simplified Chinese
zh-tw: traditional Chinese
     */

    /**
     * @var string
     */
    protected $darkSkyApiKey;

    /**
     * @var string
     */
    protected $defaultUnit;

    /**
     * @var string
     */
    protected $defaultLanguage;

    /**
     * @var array
     */
    protected $guzzleOptions;

    /**
     * @var Client
     */
    protected $guzzleClient;

    public function __construct($darkSkyApiKey, $defaultUnit = self::UNIT_SMITHSONIAN, $defaultLanguage = 'en', array $guzzleOptions = []) {
        $this->setDarkSkyApiKey($darkSkyApiKey);
        $this->setDefaultUnit($defaultUnit);
        if (!$this->isLanguageValid($defaultLanguage)) {
            throw new \InvalidArgumentException('The specified default language of the DarkSky API is not a language DarkSky supports. Change the default language in the DarkSky options to another value');
        }
        $this->setDefaultLanguage($defaultLanguage);
        $this->setGuzzleOptions($guzzleOptions);
    }

    public function createRequest() {
        return new DarkSkyRequest($this);
    }

    public function request(DarkSkyRequest $requestOptions) {

        $uri = $this->requestToUri($requestOptions);
        $queryParams = $this->requestToQueryParameters($requestOptions);
        $apiResponse = $this->getGuzzleClient()->get($uri, [
            'query' => $queryParams
        ])->getBody()->getContents();

        return \GuzzleHttp\json_decode($apiResponse);
    }

    /**
     * @return string
     */
    public function getDefaultLanguage() {
        return $this->defaultLanguage;
    }

    /**
     * @param string $defaultLanguage
     */
    public function setDefaultLanguage($defaultLanguage) {
        $this->defaultLanguage = strtolower($defaultLanguage);
    }

    /**
     * @return string
     */
    public function getDefaultUnit() {
        return $this->defaultUnit;
    }

    /**
     * @param string $defaultUnit
     */
    public function setDefaultUnit($defaultUnit) {
        $this->defaultUnit = $defaultUnit;
    }

    /**
     * Check if the provided language is one DarkSky supports
     * @param string $lang
     * @return bool
     */
    protected function isLanguageValid($lang) {
        $lang = strtolower($lang);

        return in_array($lang, self::SUPPORTED_LANGUAGES, true);
    }

    protected function requestToQueryParameters(DarkSkyRequest $request) {
        $queryParams = [
            'lang' => $this->getDefaultLanguage(),
            'units' => $this->getDefaultUnit()
        ];

        if (!empty($request->getDataBlocksToExclude())) {
            $queryParams['exclude'] = implode(',', $request->getDataBlocksToExclude());
        }

        if ($request->isHourlyDataEnabled() === true) {
            $queryParams['extend'] = 'hourly';
        }

        if ($request->getLanguage() !== null) {
            if($this->isLanguageValid($request->getLanguage())){
                $queryParams['lang'] = strtolower($request->getLanguage());
            }
        }

        if ($request->getUnits() !== null) {
            $queryParams['units'] = $request->getUnits();
        }

        return $queryParams;
    }

    protected function requestToUri(DarkSkyRequest $request) {
        $uriParams = [
            'lat' => $request->getLat(),
            'lon' => $request->getLon()
        ];
        if ($request->getTime() !== null) {
            $uriParams['time'] = $request->getTime()->getTimestamp();
        }

        return sprintf('forecast/%s/%s', $this->getDarkSkyApiKey(), implode(',', $uriParams));
    }

    /**
     * @param Client $guzzleClient
     */
    protected function setGuzzleClient($guzzleClient) {
        $this->guzzleClient = $guzzleClient;
    }

    /**
     * @return array
     */
    protected function getGuzzleOptions() {
        return $this->guzzleOptions;
    }

    /**
     * @param array $guzzleOptions
     */
    protected function setGuzzleOptions($guzzleOptions) {
        $this->guzzleOptions = $guzzleOptions;
    }

    /**
     * @return Client
     */
    protected function getGuzzleClient() {
        if (!isset($this->guzzleClient)) {
            $guzzleOptions = array_merge(
                [
                    'base_uri' => 'https://api.darksky.net',
                    'timeout' => 8.0,
                    'allow_redirects' => true,
                    'headers' => [
                        'Accept' => 'application/json',
                        'User-Agent' => 'nf-darksky/1.0.0',
                    ],
                ], $this->getGuzzleOptions()
            );
            $client = new Client($guzzleOptions);
            $this->setGuzzleClient($client);
        }
        return $this->guzzleClient;
    }

    /**
     * @return string
     */
    protected function getDarkSkyApiKey() {
        return $this->darkSkyApiKey;
    }

    /**
     * @param string $darkSkyApiKey
     */
    protected function setDarkSkyApiKey($darkSkyApiKey) {
        $this->darkSkyApiKey = $darkSkyApiKey;
    }

}