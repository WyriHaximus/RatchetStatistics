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

App::uses('RatchetPhuninConnections', 'RatchetStatistics.Lib/Phunin');
App::uses('RatchetMessageQueueGetConnectionsCommand', 'RatchetStatistics.Lib/MessageQueue/Command');

App::uses('RatchetPhuninUptime', 'RatchetStatistics.Lib/Phunin');
App::uses('RatchetMessageQueueGetUptimeCommand', 'RatchetStatistics.Lib/MessageQueue/Command');

App::uses('RatchetPhuninMemoryUsage', 'RatchetStatistics.Lib/Phunin');
App::uses('RatchetMessageQueueGetMemoryUsageCommand', 'RatchetStatistics.Lib/MessageQueue/Command');

App::uses('RatchetPhuninPubSubStatistics', 'RatchetStatistics.Lib/Phunin');
App::uses('RatchetMessageQueueGetPubSubStatisticsCommand', 'RatchetStatistics.Lib/MessageQueue/Command/Statistics');

class RatchetPhuninCakeListener implements CakeEventListener {

/**
 * The ReactPHP event
 *
 * @var \React\EventLoop\LoopInterface
 */
	private $__loop;

/**
 * The PhuninNode server
 *
 * @var \PhuninNode\Node
 */
	private $__node;

/**
 * Return an array with events this listener implements
 * @return array
 */
	public function implementedEvents() {
		return array(
			'PhuninCake.Node.start' => 'start',
		);
	}

/**
 * Attach all PhuninNode plugins for the Ratchet plugin
 *
 * @param CakeEvent $event
 */
	public function start(CakeEvent $event) {
		$this->_loop = $event->data['loop'];
		$this->__node = $event->data['node'];

		$this->__node->addPlugin(new RatchetPhuninConnections($this->_loop));
		$this->__node->addPlugin(new RatchetPhuninUptime($this->_loop));
		$this->__node->addPlugin(new RatchetPhuninMemoryUsage($this->_loop));
		$this->__node->addPlugin(new RatchetPhuninPubSubStatistics($this->_loop));
	}
}
