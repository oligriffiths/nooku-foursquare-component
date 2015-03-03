<?php
/**
 * User: Oli Griffiths
 * Date: 14/09/2013
 * Time: 16:57
 */

namespace Oligriffiths\Component\Foursquare;

use Nooku\Library;

class DatabaseRowsetVenues extends Library\DatabaseRowsetAbstract
{
	protected function _initialize(Library\ObjectConfig $config)
	{
		$config->append(array(
			'identity_column' => 'id',
			'status' => Library\Database::STATUS_FETCHED
		));
		parent::_initialize($config);
	}
}