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

class RatchetPubSubListener implements CakeEventListener {

/**
 * Returns an array with the events this listener hooks into
 *
 * @return array
 */
	public function implementedEvents() {
		return array(
			'Rachet.WampServer.construct' => 'construct',
			'RachetStatistics.WebsocketServer.getPubSub' => 'getPubSub',
		);
	}

/**
 * Stores the time of method execution to be used as referer point for the uptime calculation
 *
 * @param CakeEvent $event
 */
	public function construct(CakeEvent $event) {
		$this->wampServer = $event->subject();
	}

/**
 * Returns an array with the current memory usage and the peak memory usage
 *
 * @param CakeEvent $event
 */
	public function getPubSub(CakeEvent $event) {
		$topics = $this->wampServer->getTopics();
    $topicCount = count($topics);
		$connections = $this->wampServer->getConnections();
    $connectionCount = count($connections);

		$subscribesCount = 0;
    $subscribersCount = 0;
		foreach ($connections as $connection) {
        $count = count($connection['topics']);
        if ($count > 0) {
            $subscribesCount += $count;
            $subscribersCount++;
        }
		}

		$avg_topics_subscribers = 0;
		if ($subscribersCount > 0 && $subscribesCount > 0) {
			$avg_topics_subscribers = $subscribesCount / $subscribersCount;
		}

		$event->result = array(
			'active_topics' => $topicCount,
			'active_subscribers' => $subscribersCount,
			'active_connections' => $connectionCount,
			'avg_topics_subscribers' => $avg_topics_subscribers,
		);
	}
}
