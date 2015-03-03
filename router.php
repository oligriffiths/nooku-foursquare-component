<?php
/**
 * @package        Nooku_Server
 * @subpackage     Articles
 * @copyright      Copyright (C) 2009 - 2012 Timble CVBA and Contributors. (http://www.timble.net)
 * @license        GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link           http://www.nooku.org
 */

namespace Oligriffiths\Component\Foursquare;

use Nooku\Library;

/**
 * Articles router class.
 *
 * @author     Arunas Mazeika <http://nooku.assembla.com/profile/arunasmazeika>
 * @package    Nooku_Server
 * @subpackage Articles
 */

class Router extends Library\DispatcherRouter
{
    public function build(Library\HttpUrl $url)
    {
	    $segments = array();
        return $segments;
    }
}