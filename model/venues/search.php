<?php
/**
 * User: Oli Griffiths
 * Date: 14/09/2013
 * Time: 16:35
 */

namespace Oligriffiths\Component\Foursquare;

use Nooku\Library;

class ModelVenuesSearch extends ModelVenuesAbstract
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
            ->insert('intent','string')   //checkin, browse, global, match
            ->insert('ne','float')
            ->insert('sw','float')
            ->insert('categoryId','alnum')
            ->insert('url','string')
            ->insert('providerId','int')
            ->insert('linkedId','int');
    }
}