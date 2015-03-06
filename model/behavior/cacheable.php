<?php
/**
 * User: Oli Griffiths
 * Date: 06/03/15
 * Time: 09:53
 */

namespace Oligriffiths\Component\Foursquare;

use Nooku\Library;

class ModelBehaviorCacheable extends Library\ModelBehaviorAbstract
{
    /**
     * Initializes the options for the object
     *
     * Called from {@link __construct()} as a first step of object instantiation.
     *
     * @param  ObjectConfig $config A ObjectConfig object with configuration options
     * @return void
     */
    protected function _initialize(Library\ObjectConfig $config)
    {
        $config->append(array(
            'cache_prefix' => 'model-cache',
            'cache_ttl' => 0,
        ));
        parent::_initialize($config);
    }

    /**
     * If APC is not loaded, behavior is disabled
     *
     * @return bool
     */
    public function isSupported()
    {
        return extension_loaded('apc');
    }

    /**
     * Pre-fetch, attempts to load data from cache
     *
     * @param Library\ModelContextInterface $context
     * @return bool
     */
    protected function _beforeFetch(Library\ModelContextInterface $context)
    {
        $key = $this->_buildCacheKey($context->getState());
        $data = apc_fetch($key);

        //Ensure data was found and all the correct parameters are set
        if(false === $data ||
            !is_array($data) ||
            !isset($data['identifier']) ||
            !isset($data['data']) ||
            !isset($data['status']) ||
            !isset($data['identity_key'])) return;

        //Create entity with cached data
        $context->entity = $this->getObject($data['identifier'], array(
            'identity_key'  => $data['identity_key'],
            'data'          => $data['data'],
            'status'        => $data['status']
        ));

        return false;
    }

    /**
     * Post fetch stores data in cache
     *
     * @param Library\ModelContextInterface $context
     */
    protected function _afterFetch(Library\ModelContextInterface $context)
    {
        $key = $this->_buildCacheKey($context->getState());

        $entity = $context->getEntity();

        $data = array(
            'identifier'    => (string) $entity->getIdentifier(),
            'identity_key'  => $entity->getIdentityKey(),
            'data'          => $entity->toArray(),
            'status'        => $entity->getStatus()
        );

        apc_store($key, $data, $this->getConfig()->cache_ttl);
    }

    /**
     * Builds a cache identifier using state data
     *
     * @param Library\ModelState $state
     * @return string
     */
    protected function _buildCacheKey(Library\ModelState $state)
    {
        $values = $state->getValues();
        ksort($values);

        $query = http_build_query($values);
        $identifier = $this->getMixer()->getIdentifier();

        return $this->getConfig()->cache_prefix.':'.$identifier.'?'.$query;
    }
}