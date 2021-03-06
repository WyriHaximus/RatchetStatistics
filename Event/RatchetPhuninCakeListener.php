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

App::uses('RatchetPhuninPubSub', 'RatchetStatistics.Lib/Phunin');
App::uses('RatchetMessageQueueGetPubSubCommand', 'RatchetStatistics.Lib/MessageQueue/Command');

App::uses('RatchetPhuninEvents', 'RatchetStatistics.Lib/Phunin');
App::uses('RatchetMessageQueueGetEventsCommand', 'RatchetStatistics.Lib/MessageQueue/Command');

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
		$this->__loop = $event->data['loop'];
		$this->__node = $event->data['node'];

		$this->__node->addPlugin(new RatchetPhuninConnections($this->__loop));
		$this->__node->addPlugin(new RatchetPhuninUptime($this->__loop));
		$this->__node->addPlugin(new RatchetPhuninMemoryUsage($this->__loop));
		$this->__node->addPlugin(new RatchetPhuninPubSub($this->__loop));
		$this->__node->addPlugin(new RatchetPhuninEvents($this->__loop));
	}
}
