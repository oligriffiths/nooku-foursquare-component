<?php
/**
 * User: Oli Griffiths
 * Date: 14/09/2013
 * Time: 16:35
 */

namespace Oligriffiths\Component\Foursquare;

use Nooku\Library;
use Guzzle\Service\Description\Parameter;
use Jcroll\FoursquareApiClient\Client\FoursquareClient;


abstract class ModelVenuesAbstract extends Library\ModelAbstract
{
    /**
     * @var FoursquareClient
     */
    protected $_client;

    /**
     * @var
     */
    protected $_data;

    /**
     * Constructor
     *
     * @param  ObjectConfig $config    An optional ObjectConfig object with configuration options
     */

    public function __construct(Library\ObjectConfig $config)
    {
        parent::__construct($config);

        $this->getState()
            ->insert('id','string',null,true)
            ->insert('latitude','float')
            ->insert('longitude','float')
            ->insert('ll','string')
            ->insert('near','string')
            ->insert('llAcc','int')
            ->insert('alt','int')
            ->insert('altAcc','int')
            ->insert('query','string')
            ->insert('limit','int', 10)
            ->insert('radius','int');

        $this->_client = $config->client;
    }

    /**
     * Initializes the options for the object
     *
     * Called from {@link __construct()} as a first step of object instantiation.
     *
     * @param   ObjectConfig $config An optional ObjectConfig object with configuration options
     * @return  void
     */
    protected function _initialize(Library\ObjectConfig $config)
    {
        $config->append(array(
            'behaviors' => array(
//                'cacheable' => array('cache_ttl' => 3600)
            ),
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
     * Create a new entity for the data source
     * Overridden to ensure identifier is set correctly
     *
     * @param Library\ModelContext $context A model context object
     * @return  Library\ModelEntityInterface The entity
     */
    protected function _actionCreate(Library\ModelContext $context)
    {
        //Get the data
        $data = Library\ModelContext::unbox($context->entity);

        //Create the entity identifier
        $identifier = $this->getIdentifier()->toArray();
        $identifier['path'] = array('model', 'entity');
        $identifier['name'] = 'venues';

        if(!is_numeric(key($data))) {
            $identifier['name'] = Library\StringInflector::singularize($identifier['name']);
        } else {
            $identifier['name'] = Library\StringInflector::pluralize($identifier['name']);
        }

        $options = array(
            'data'         => $data,
            'identity_key' => $context->getIdentityKey()
        );

        return $this->getObject($identifier, $options);
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
            'identity_key' => 'id',
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
        }else{
            unset($state['latitude']);
            unset($state['longitude']);
        }

        $command = $this->getClient()->getCommand('venues/'.$this->getIdentifier()->name, $state);

        //The JCroll library isn't in sync with the foursquare API, this is a patch
        //@TODO - Swap out JCroll for another foursquare lib
        $operation = $command->getOperation();
        $missing_params = array_diff_key($state, $operation->getParams());
        foreach($missing_params AS $param => $value){
            $operation->addParam(new Parameter(array('name' => $param, 'location' => 'query')));
        }

        //Execute the request
        $response = $command->execute();

        if(method_exists($this, '_getVenuesFromResponse')) $venues = $this->_getVenuesFromResponse($response);
        else $venues = isset($response['response']) && isset($response['response']['venues']) ? $response['response']['venues'] : array();

        $options = array(
            'identity_key' => 'id',
            'data' => $venues,
            'status' => Library\Database::STATUS_CREATED
        );

        return $this->getObject('com:foursquare.model.entity.venues', $options);
    }
}