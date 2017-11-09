<?php
/**
 * Created by PhpStorm.
 * User: erwinkloosterboer
 * Date: 09/11/2017
 * Time: 10:06
 */

namespace NF\DarkSky\Request;


use NF\DarkSky\DarkSky;

class DarkSkyRequest {

    /**
     * Latitude of the location to get weather information from
     * @var string
     */
    protected $lat;

    /**
     * longitude of the location to get weather information from
     * @var string
     */
    protected $lon;

    /**
     * List of data blocks to exclude from the API response. Must be zero or more of the following list:
     * currently
     * minutely
     * hourly
     * daily
     * alerts
     * flags
     *
     * @var string[]
     */
    protected $dataBlocksToExclude = [];

    /**
     * Return hour-by-hour data for the next 168 hours, instead of the next 48.
     *
     * @var bool
     */
    protected $hourlyDataEnabled = false;


    /**
     * When set, override the default language as set in the options and make the API return summary properties in the desired language
     * @var string|null
     */
    protected $language = null;

    /**
     * When set with one of the unit as described in the DarkSky class, the default unit as set int he options will be overridden.
     * @see \NF\DarkSky\DarkSky units
     * @var string|null
     */
    protected $units = null;

    /**
     * When set, a Time Machine request will be performed to get historical/forecasted weather for the location
     * @var \DateTime|null
     */
    protected $time;

    protected $darkSky;

    public function __construct(DarkSky $darkSky) {
        $this->darkSky = $darkSky;
    }

    /**
     * @return string
     */
    public function getLat() {
        return $this->lat;
    }

    /**
     * @param string $lat
     * @return $this
     */
    public function setLat($lat) {
        $this->lat = $lat;
        return $this;
    }

    /**
     * @return string
     */
    public function getLon() {
        return $this->lon;
    }

    /**
     * @param string $lon
     * @return $this
     */
    public function setLon($lon) {
        $this->lon = $lon;
        return $this;
    }

    /**
     * @return string[]
     */
    public function getDataBlocksToExclude() {
        return $this->dataBlocksToExclude;
    }

    /**
     * @param string[] $dataBlocksToExclude
     * @return $this
     */
    public function setDataBlocksToExclude($dataBlocksToExclude) {
        $this->dataBlocksToExclude = $dataBlocksToExclude;
        return $this;
    }

    /**
     * @return bool
     */
    public function isHourlyDataEnabled() {
        return $this->hourlyDataEnabled;
    }

    /**
     * @param bool $hourlyDataEnabled
     * @return $this
     */
    public function setHourlyDataEnabled($hourlyDataEnabled) {
        $this->hourlyDataEnabled = $hourlyDataEnabled;
        return $this;
    }

    /**
     * @return null|string
     */
    public function getLanguage() {
        return $this->language;
    }

    /**
     * @param null|string $language
     * @return $this
     */
    public function setLanguage($language) {
        $this->language = $language;
        return $this;
    }

    /**
     * @return null|string
     */
    public function getUnits() {
        return $this->units;
    }

    /**
     * @param null|string $units
     * @return $this
     */
    public function setUnits($units) {
        $this->units = $units;
        return $this;
    }

    public function execute() {
        return $this->darkSky->request($this);
    }

    /**
     * @return \DateTime|null
     */
    public function getTime() {
        return $this->time;
    }

    /**
     * @param \DateTime|null $time
     * @return $this
     */
    public function setTime($time) {
        $this->time = $time;
        return $this;
    }

}