<?php

/**
 * This file is part of RatchetStatistics for CakePHP.
 *
 ** (c) 2013 Cees-Jan Kiewiet
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

App::uses('CakeEventListener', 'Event');

class RatchetUptimeListener implements CakeEventListener {

/**
 * Attribute used to store the servers start time
 *
 * @var int
 */
	private $__startTime = 0;

/**
 * Returns an array with the events this listener hooks into
 *
 * @return array
 */
	public function implementedEvents() {
		return array(
			'Rachet.WampServer.construct' => 'construct',
			'RachetStatistics.WebsocketServer.getUptime' => 'getUptime',
		);
	}

/**
 * Stores the time of method execution to be used as referer point for the uptime calculation
 *
 * @param CakeEvent $event
 */
	public function construct(CakeEvent $event) {
		$this->__startTime = time();
	}

/**
 * Sets the event result to the uptime in seconds
 *
 * @param CakeEvent $event
 * @throws Exception
 */
	public function getUptime(CakeEvent $event) {
		$event->result = array(
			'uptime' => (time() - $this->__startTime),
		);
	}

}
