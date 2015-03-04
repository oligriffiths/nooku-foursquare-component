<?php
/**
 * User: Oli Griffiths
 * Date: 14/09/2013
 * Time: 16:35
 */

namespace Oligriffiths\Component\Foursquare;

use Nooku\Library;
use Jcroll\FoursquareApiClient\Client\FoursquareClient;


class ModelVenues extends Library\ModelAbstract
{
    /**
     * @var FoursquareClient
     */
    protected $_client;

    /**
     * @var
     */
    protected $_data;

    public function __construct(Library\ObjectConfig $config)
    {
        parent::__construct($config);

        $this->getState()
            ->insert('id','string',null,true)
            ->insert('latitude','string')
            ->insert('longitude','string')
            ->insert('ll','string')
            ->insert('query','string')
            ->insert('radius','int')
            ->insert('intent','string')   //checkin, browse, global, match
            ->insert('near','string')
            ->insert('limit','int', 10)
            ->insert('ne','float')
            ->insert('sw','float')
            ->insert('categoryId','alnum')
            ->insert('url','string')
            ->insert('linkedId','int')
            ->insert('providerId','int');

        $this->_client = $config->client;
    }


    protected function _initialize(Library\ObjectConfig $config)
    {
        $config->append(array(
            'client_id' => '',
            'client_secret' => '',
            'token' => null,
            'redirect_url' => null,
            'client' => null
        ));

        parent::_initialize($config);
    }

    /**
     * Gets the foursquare client
     *
     * @return FoursquareClient
     */
    protected function getClient()
    {
        if(!$this->_client){

            $this->_client = $this->getObject('com://oligriffiths/foursquare.helper.client', array(
                'client_id' => $this->getConfig()->client_id,
                'client_secret' => $this->getConfig()->client_secret,
                'token' => $this->getConfig()->token
            ));
        }

        return $this->_client;
    }

    /**
     * Resets the model & clears cached data
     *
     * @param ModelContext $context
     */
    protected function _actionReset(Library\ModelContext $context)
    {
        parent::_actionReset($context);

        $this->_data = null;
    }

    /**
     * Fetch a new entity from the data source
     *
     * @param ModelContext $context A model context object
     * @return ModelEntityInterface The entity
     */
    protected function _actionFetch(Library\ModelContext $context)
    {
        if(!$this->_data){
            if($this->getState()->isUnique()){
                $this->_data = $this->getVenue($context);
            }else{
                $this->_data =  $this->getVenues($context);
            }
        }

        return $this->_data;
    }

    /**
     * Gets a single venue by ID
     *
     * @param Library\ModelContext $context
     * @return Callable|Library\ObjectInterface
     */
    protected function getVenue(Library\ModelContext $context)
    {
        try{
            $response = $this->getClient()->getCommand('venues', array('venue_id' => $this->getState()->id))->execute();
            $venue = isset($response['response']) && isset($response['response']['venue']) ? $response['response']['venue'] : array();
        }catch(\Exception $e){
            $venue = array();
        }

        $options = array(
            'identity_key' => $context->getIdentityKey(),
            'data' => $venue,
            'status' => Library\Database::STATUS_CREATED
        );

        return $this->getObject('com:foursquare.model.entity.venue', $options);
    }

    /**
     * Gets multiple venues, filtered by state params
     *
     * @param Library\ModelContext $context
     * @return Callable|Library\ObjectInterface
     */
    protected function getVenues(Library\ModelContext $context)
    {
        $state = $this->getState()->getValues();
        if(!isset($state['ll']) && isset($state['latitude']) && isset($state['longitude'])){
            $state['ll'] = $state['latitude'].','.$state['longitude'];
        }

        try{
            $response = $this->getClient()->getCommand('venues/search', $state)->execute();
            $venues = isset($response['response']) && isset($response['response']['venues']) ? $response['response']['venues'] : array();
        }catch(\Exception $e){
            $venues = array();
        }

        $options = array(
            'identity_key' => $context->getIdentityKey(),
            'data' => $venues,
            'status' => Library\Database::STATUS_CREATED
        );

        return $this->getObject('com:foursquare.model.entity.venues', $options);
    }
}