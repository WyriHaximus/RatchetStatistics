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

class RatchetConnectionsListener implements CakeEventListener {

/**
 * Open connection count for authenticated users
 *
 * @var int
 */
	private $__userConnectionCount = 0;

/**
 * Open connection count for non-authenticated users
 *
 * @var int
 */
	private $__guestConnectionCount = 0;

/**
 * Holds the broadcast topic for
 *
 * @var \Ratchet\Wamp\Topic|boolean
 */
	private $__topic = false;

/**
 * Returns an array with the events this listener hooks into
 *
 * @return array
 */
	public function implementedEvents() {
		return array(
			'Rachet.WampServer.onOpen' => 'onOpen',
			'Rachet.WampServer.onClose' => 'onClose',
			'Rachet.WampServer.onSubscribeNewTopic.connectionCount' => 'onSubscribeNewTopic',
			'Rachet.WampServer.onSubscribe.connectionCount' => 'onSubscribe',
			'Rachet.WampServer.onUnSubscribeStaleTopic.connectionCount' => 'onUnSubscribeStaleTopic',
			'Rachet.WebsocketServer.getConnectionCounts' => 'getConnectionCounts',
		);
	}

/**
 * Event that triggers when a new client connects,
 * determens if the connection is authenticated and
 * broadcasts a message to all clients listening on connectionCount
 *
 * @param CakeEvent $event
 */
	public function onOpen(CakeEvent $event) {
		if (isset($event->data['connectionData']['session']['Auth']['User']['id'])) {
			$this->__userConnectionCount++;
		} else {
			$this->__guestConnectionCount++;
		}

		if ($this->__topic instanceof \Ratchet\Wamp\Topic) {
			$this->__topic->broadcast(array(
				'guests' => $this->__guestConnectionCount,
				'users' => $this->__userConnectionCount,
			));
		}
	}

/**
 * Event that triggers when a new client connects,
 * determens if the connection is authenticated and
 * broadcasts a message to all clients listening on connectionCount
 *
 * @param CakeEvent $event
 */
	public function onClose(CakeEvent $event) {
		if (isset($event->data['connectionData']['session']['Auth']['User']['id'])) {
			$this->__userConnectionCount--;
		} else {
			$this->__guestConnectionCount--;
		}

		if ($this->__topic instanceof \Ratchet\Wamp\Topic) {
			$this->__topic->broadcast(array(
				'guests' => $this->__guestConnectionCount,
				'users' => $this->__userConnectionCount,
			));
		}
	}

/**
 * Stores the \Ratchet\Wamp\Topic instance for connectionCount
 *
 * @param CakeEvent $event
 */
	public function onSubscribeNewTopic(CakeEvent $event) {
		$this->__topic = $event->data['topic'];
	}

/**
 * Sends newly listening clients the current situation
 *
 * @param CakeEvent $event
 */
	public function onSubscribe(CakeEvent $event) {
		$event->data['connection']->event($this->__topic->getId(), array(
			'guests' => $this->__guestConnectionCount,
			'users' => $this->__userConnectionCount,
		));
	}

/**
 * Get rid of the \Ratchet\Wamp\Topic instance when no one is listening
 *
 * @param CakeEvent $event
 */
	public function onUnSubscribeStaleTopic(CakeEvent $event) {
		$this->__topic = false;
	}

/**
 * Event that returns the current connection counts
 *
 * @param CakeEvent $event
 */
	public function getConnectionCounts(CakeEvent $event) {
		$event->result = array(
			'guests' => $this->__guestConnectionCount,
			'users' => $this->__userConnectionCount,
		);
	}
}
