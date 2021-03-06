<?php

/**
 * This file is part of RatchetStatistics for CakePHP.
 *
 ** (c) 2013 Cees-Jan Kiewiet
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

App::uses('CakeEventManager', 'Event');

/**
 * Statistical listeners
 */

App::uses('RatchetConnectionsListener', 'RatchetStatistics.Event/Statistics');
CakeEventManager::instance()->attach(new RatchetConnectionsListener());

App::uses('RatchetUptimeListener', 'RatchetStatistics.Event/Statistics');
CakeEventManager::instance()->attach(new RatchetUptimeListener());

App::uses('RatchetMemoryUsageListener', 'RatchetStatistics.Event/Statistics');
CakeEventManager::instance()->attach(new RatchetMemoryUsageListener());

App::uses('RatchetPubSubListener', 'RatchetStatistics.Event/Statistics');
CakeEventManager::instance()->attach(new RatchetPubSubListener());

App::uses('RatchetEventsListener', 'RatchetStatistics.Event/Statistics');
CakeEventManager::instance()->attach(new RatchetEventsListener());

/**
 * PhuninCake listener
 *
 * (Make sure PhuninCake is loaded before Ratchet is!)
 */

if (CakePlugin::loaded('PhuninCake')) {
	App::uses('RatchetPhuninCakeListener', 'RatchetStatistics.Event');
	CakeEventManager::instance()->attach(new RatchetPhuninCakeListener());
}
