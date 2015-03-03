<?php
/**
 * Created by PhpStorm.
 * User: oli
 * Date: 03/03/15
 * Time: 13:31
 */

namespace Oligriffiths\Component\Foursquare;

use Nooku\Library;
use Jcroll\FoursquareApiClient\Client\FoursquareClient;

class HelperClient extends Library\ObjectDecorator
{
    /**
     * Constructor
     *
     * @param ObjectConfig  $config  A ObjectConfig object with optional configuration options
     * @return ObjectDecorator
     */
    public function __construct(Library\ObjectConfig $config)
    {
        parent::__construct($config);

        //Set the token
        if($config->token) $this->getDelegate()->addToken($config->token);
    }

    /**
     * Initializes the options for the object
     *
     * Called from {@link __construct()} as a first step of object instantiation.
     *
     * @param   ObjectConfig $object An optional ObjectConfig object with configuration options
     * @return  void
     */
    protected function _initialize(Library\ObjectConfig $config)
    {
        $config->append(array(
            'client_id' => '',
            'client_secret' => '',
            'token' => null,
            'redirect_url' => null,
            'token' => null,
            'v' => '20150301'
        ))->append(array(
            'delegate' => FoursquareClient::factory($config->toArray())
                            ->setDefaultOption('query', array(
                                'client_id' => $config->client_id,
                                'client_secret' => $config->client_secret,
                                'v' => $config->v   //Reset the v(version) parameter as the factory method sets a default
                            ))
        ));

        parent::_initialize($config);
    }

    /**
     * @return FoursquareClient
     */
    public function getDelegate()
    {
        return parent::getDelegate();
    }

    /**
     * Sets the delegate and verifies its an instance of FoursquareClient
     *
     * @param Library\ObjectInterface $delegate
     * @return Library\ObjectDecorator|void
     * @throws \InvalidArgumentException
     */
    public function setDelegate($delegate)
    {
        if(!$delegate instanceof FoursquareClient){
            throw new \InvalidArgumentException(__CLASS__.'::'.__FUNCTION__.' - delegate must be an instance of FoursquareClient');
        }

        Library\ObjectDecoratorAbstract::setDelegate($delegate);
    }

    /**
     * Gets a guzzle command
     *
     * @param $command
     * @param $params
     * @return \Guzzle\Service\Command\CommandInterface|null
     */
    public function getCommand($command, $params)
    {
        return $this->getDelegate()->getCommand($command, $params);
    }
}