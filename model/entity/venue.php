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
            else if(is_array($value)) $value = isset($value[$key]) ? $value[$key] : null;
            else if(is_object($value)) $value = isset($value->$key) ? $value->$key : null;
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

    /**
     * Gets the formatted address
     *
     * @return array
     */
    public function getFormattedAddress()
    {
        return $this->getValue('location.formattedAddress');
    }

    /**
     * Gets the times the venue is open
     *
     * @return array
     */
    public function getHours()
    {
        $data = (array) $this->getValue('hours.timeframes');
        $hours = array();

        foreach($data AS $d){
            $open = isset($d['open']) ? $d['open'] : null;
            if(!$open || !is_array($open)) continue;

            $hours[] = array('days' => $d['days'], 'hours' => array_values($open));
        }

        return $hours;
    }
}