<?php
/**
 * User: Oli Griffiths
 * Date: 14/09/2013
 * Time: 16:55
 */

namespace Oligriffiths\Component\Foursquare;

use Nooku\Library;

class ModelEntityVenue extends Library\ModelEntityAbstract
{
    /**
     * Gets the venues latitude if set
     *
     * @return float
     */
    public function getLatitude()
    {
        return (float) ($this->location && isset($this->location['lat']) ? $this->location['lat'] : 0);
    }

    /**
     * Gets the venues longitude if set
     *
     * @return float
     */
    public function getLongitude()
    {
        return (float) ($this->location && isset($this->location['lng']) ? $this->location['lng'] : 0);
    }

    /**
     * Gets the address
     *
     * @return null|string
     */
    public function getAddress()
    {
        return $this->location && isset($this->location['address']) ? $this->location['address'] : null;
    }

    /**
     * Gets the city
     *
     * @return null|string
     */
    public function getCity()
    {
        return $this->location && isset($this->location['city']) ? $this->location['city'] : null;
    }

    /**
     * Gets the state
     *
     * @return null|string
     */
    public function getState()
    {
        return $this->location && isset($this->location['state']) ? $this->location['state'] : null;
    }

    /**
     * Gets the postal code
     *
     * @return null|string
     */
    public function getPostalcode()
    {
        return $this->location && isset($this->location['postal_code']) ? $this->location['postal_code'] : null;
    }

    /**
     * Gets the country
     *
     * @return null|string
     */
    public function getCountry()
    {
        return $this->location && isset($this->location['cc']) ? $this->location['cc'] : null;
    }
}