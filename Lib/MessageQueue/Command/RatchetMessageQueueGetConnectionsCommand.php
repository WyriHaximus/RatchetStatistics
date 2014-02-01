<?php

/**
 * This file is part of RatchetStatistics for CakePHP.
 *
 ** (c) 2013 Cees-Jan Kiewiet
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

App::uses('RatchetMessageQueueCommand', 'RatchetCommands.Lib/MessageQueue/Command');

class RatchetMessageQueueGetConnectionsCommand extends RatchetMessageQueueCommand {

	protected $_hash;

	public function serialize() {
		return serialize(array(
			'hash' => $this->_hash,
			'id' => $this->_id,
		));
	}

	public function unserialize($commandString) {
		$commandString = unserialize($commandString);
		$this->setHash($commandString['hash']);
		$this->_id = $commandString['hash'];
	}

	public function setHash($hash) {
		$this->_hash = $hash;
	}

	public function setDeferedResolver($resolver) {
		$this->resolver = $resolver;
	}

	public function execute($eventSubject) {
		$event = new CakeEvent('RachetStatistics.WebsocketServer.getConnectionCounts', $this, array());
		CakeEventManager::instance()->dispatch($event);

		return $event->result;
	}

	public function response($response) {
		$this->resolver->resolve($response);
	}

}
