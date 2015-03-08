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
     * Gets a value using dot syntax
     *
     * @param $key
     * @return null|mixed
     */
    public function getValue($key)
    {
        $keys = explode('.', $key);
        $key = array_shift($keys);

        $value = $this->$key;

        foreach($keys AS $key){
            if(is_null($value)) break;
            else if(is_scalar($value)) $value = null;
            else if(is_array($value) && isset($value[$key])) $value = $value[$key];
            else if(is_object($value) && isset($value->$key)) $value = $value->$key;
        }

        return $value;
    }

    /**
     * Gets the venues latitude if set
     *
     * @return float
     */
    public function getLatitude()
    {
        return (float) $this->getValue('location.lat');
    }

    /**
     * Gets the venues longitude if set
     *
     * @return float
     */
    public function getLongitude()
    {
        return (float) $this->getValue('location.lng');
    }

    /**
     * Gets the address
     *
     * @return null|string
     */
    public function getAddress()
    {
        return $this->getValue('location.address');
    }

    /**
     * Gets the city
     *
     * @return null|stringx
     */
    public function getCity()
    {
        return $this->getValue('location.city');
    }

    /**
     * Gets the state
     *
     * @return null|string
     */
    public function getState()
    {
        return $this->getValue('location.state');
    }

    /**
     * Gets the postal code
     *
     * @return null|string
     */
    public function getPostalcode()
    {
        return $this->getValue('location.postalCode');
    }

    /**
     * Gets the country
     *
     * @return null|string
     */
    public function getCountry()
    {
        return $this->getValue('location.cc');
    }
}