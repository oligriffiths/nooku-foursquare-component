<?php
/**
 * User: Oli Griffiths
 * Date: 14/09/2013
 * Time: 16:55
 */

namespace Oligriffiths\Component\Foursquare;

use Nooku\Library;

class ModelEntityVenue extends Library\ModelEntityRow
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
}