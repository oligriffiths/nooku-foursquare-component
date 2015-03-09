<?php
/**
 * User: Oli Griffiths
 * Date: 14/09/2013
 * Time: 16:35
 */

namespace Oligriffiths\Component\Foursquare;

use Nooku\Library;

class ModelVenuesExplore extends ModelVenuesAbstract
{
    /**
     * Constructor
     *
     * @param  ObjectConfig $config    An optional ObjectConfig object with configuration options
     */

    public function __construct(Library\ObjectConfig $config)
    {
        parent::__construct($config);

        $this->getState()
            ->insert('section','string')   //food, drinks, coffee, shops, arts, outdoors, sights, trending or specials, nextVenues, topPicks
            ->insert('offset','int')
            ->insert('novelty','string') //new or old
            ->insert('friendVisits','string') //visited or notvisited
            ->insert('time','string')   //null or any
            ->insert('day','string')    //null or any
            ->insert('venuePhotos','boolean')
            ->insert('lastVenue','alnum')
            ->insert('openNow','boolean')
            ->insert('sortByDistance','boolean', true)
            ->insert('price','string')
            ->insert('saved','boolean')
            ->insert('specials','boolean');
    }

    /**
     * Re-structures the data to find venues
     *
     * @param array $response
     * @return array
     */
    protected function _getVenuesFromResponse(array $response)
    {
        if(!isset($response['response']) || !isset($response['response']['groups'])) return array();

        $group = array_shift($response['response']['groups']);
        if(!isset($group['items'])) return array();

        $items = $group['items'];
        $venues = array_map(function($item){
            return isset($item['venue']) ? $item['venue'] : false;
        }, $items);

        return array_filter($venues);
    }
}