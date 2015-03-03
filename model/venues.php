<?php
/**
 * User: Oli Griffiths
 * Date: 14/09/2013
 * Time: 16:35
 */

namespace Oligriffiths\Component\Foursquare;

use Nooku\Library;

class ModelVenues extends Library\ModelAbstract
{
	protected $_factory;
    protected $_row;
    protected $_rowset;

	public function __construct(Library\ObjectConfig $config)
	{
		parent::__construct($config);

		$this->getState()
			->insert('id','string',null, true)
			->insert('latitude','string')
			->insert('longitude','string')
			->insert('ll','string')
			->insert('query','string')
			->insert('search','string')             //alias to query
			->insert('radius','int', 1000)
			->insert('intent','string', 'global')   //checkin, browse, global, match
			->insert('near','string')
			->insert('limit','int', 10)
			->insert('ne','float')
			->insert('sw','float')
			->insert('categoryId','int')
			->insert('url','string')
			->insert('linkedId','int')
			->insert('providerId','int');
	}

	protected function _initialize(Library\ObjectConfig $config)
	{
		$config->append(array(
			'client_id' => '',
			'client_secret' => '',
			'token' => null,
			'redirect_url' => null
		));

		parent::_initialize($config);
	}


	protected function getFactory()
	{
		if(!$this->_factory){
			$client = new \TheTwelve\Foursquare\HttpClient\CurlHttpClient();
			$client->setVerifyPeer(false);
			$redirector = new \TheTwelve\Foursquare\Redirector\HeaderRedirector();
			$this->_factory = new \TheTwelve\Foursquare\ApiGatewayFactory($client, $redirector);

			$this->_factory->setClientCredentials($this->getConfig()->client_id, $this->getConfig()->client_secret);
			$this->_factory->setToken($this->getConfig()->token);
		}

		return $this->_factory;
	}

	protected function getGateway()
	{
		return $this->getFactory()->getVenuesGateway();
	}


	protected function login()
	{
		$auth = $this->getFactory()->getAuthenticationGateway(
			'https://foursquare.com/oauth2/authorize',
			'https://foursquare.com/oauth2/access_token',
			$this->getConfig()->redirect_url
		);

		$auth->initiateLogin();
	}

    /**
     * Fetch a new entity from the data source
     *
     * @param ModelContext $context A model context object
     * @return ModelEntityInterface The entity
     */
    protected function _actionFetch(Library\ModelContext $context)
    {
        if($this->getState()->isUnique()){
            return $this->getRow($context);
        }else{
            return $this->getRowset($context);
        }
    }



    protected function getRow(Library\ModelContext $context)
	{
		if(!$this->_row){
			$state = $this->getState();

            $options = array(
                'identity_key' => $context->getIdentityKey()
            );

			if($state->isUnique())
			{
				try{
					$venue = $this->getGateway()->getVenue($state->id);
				}catch(\Exception $e){
					$venue = array();
				}

                $options['data'] = (array) $venue;
			}

            $this->_row = $this->getObject('com:foursquare.model.entity.venue', $options);
		}

		return $this->_row;
	}


    protected function getRowset(Library\ModelContext $context)
	{
		if(!$this->_rowset)
		{
			$state = $this->getState()->getValues();
			if(!isset($state['ll']) && isset($state['latitude']) && isset($state['longitude'])){
				$state['ll'] = $state['latitude'].','.$state['longitude'];
			}
			if(isset($state['search']) && !isset($state['query'])){
				$state['query'] = $state['search'];
				unset($state['search']);
			}

			try{
				if(extension_loaded('apc'))
				{
					ksort($state);
					$key = md5(serialize($state));
					if(!$venues = apc_fetch('foursquare-venues.'.$key)){
						$venues = $this->getGateway()->search($state);
						apc_store('foursquare-venues.'.$key, $venues, 3600);
					}
				}else{
					$venues = $this->getGateway()->search($state);
				}
			}catch(\Exception $e){
				$venues = array();
			}

            // Each venue must be an array!
            $venues = array_map(function($venue) { return (array) $venue; }, $venues);

            $options = array(
                'identity_key' => $context->getIdentityKey(),
                'data' => $venues
            );

			$this->_rowset = $this->getObject('com:foursquare.model.entity.venues', $options);
		}

		return $this->_rowset;
	}
}