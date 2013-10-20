<?php

/*
* This file is part of Ratchet for CakePHP.
*
** (c) 2012 - 2013 Cees-Jan Kiewiet
*
* For the full copyright and license information, please view the LICENSE
* file that was distributed with this source code.
*/

App::uses('CakeEventListener', 'Event');

class RatchetPubSubStatisticsListener implements CakeEventListener {

/**
 * Returns an array with the events this listener hooks into
 *
 * @return array
 */
	public function implementedEvents() {
		return array(
			'Rachet.WampServer.construct' => 'construct',
			'Rachet.WebsocketServer.getPubSubStatistics' => 'getPubSubStatistics',
		);
	}

/**
 * Stores the time of method execution to be used as referer point for the uptime calculation
 *
 * @param CakeEvent $event
 */
	public function construct(CakeEvent $event) {
		$this->startTime = time();
		$this->wampServer = $event->subject();
	}

/**
 * Returns an array with the current memory usage and the peak memory usage
 *
 * @param CakeEvent $event
 */
	public function getPubSubStatistics(CakeEvent $event) {
		$topics = $this->wampServer->getTopics();
		$topicCount = count($topics);

		$subscribers = array();
		foreach ($topics as $topic) {
			foreach ($topic as $subscriber) {
				$subscribers[$subscriber] = true;
			}
		}
		$subscribersCount = count($subscribers);

		$avgTopicSubscribers = 0;
		if ($subscribersCount > 0 && $topicCount > 0) {
			$avgTopicSubscribers = $topicCount / $subscribersCount;
		}
		$event->result = array(
			'active_topics' => $topicCount,
			'active_subscribers' => $subscribersCount,
			'broadcasts' => 0,
			'broadcasts_sec' => 0,
			'avg_topics_subscribers' => $avgTopicSubscribers,
		);
	}

}
