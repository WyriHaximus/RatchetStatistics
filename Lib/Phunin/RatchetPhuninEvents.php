<?php

/**
 * This file is part of RatchetStatistics for CakePHP.
 *
 ** (c) 2013 Cees-Jan Kiewiet
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

App::uses('TransportProxy', 'RatchetCommands.Lib/MessageQueue/Transports');
App::uses('RatchetMessageQueueGetEventsCommand', 'RatchetStatistics.Lib/MessageQueue/Command');
App::uses('AbstractRatchetPhuninPlugin', 'RatchetStatistics.Lib/Phunin');

class RatchetPhuninEvents extends AbstractRatchetPhuninPlugin implements \WyriHaximus\PhuninNode\PluginInterface {

/**
 * PhuninNode server
 *
 * @var \WyriHaximus\PhuninNode\Node
 */
	private $__node;

/**
 * Configuration object for this plugin,
 *
 * @var \WyriHaximus\PhuninNode\PluginConfiguration
 */
	private $__configuration;

/**
 * ReactPHP Eventloop
 *
 * @var \React\EventLoop\LoopInterface
 */
	private $__loop;

/**
 *
 * @param \React\EventLoop\LoopInterface $loop
 */
	public function __construct(\React\EventLoop\LoopInterface $loop) {
		$this->__loop = $loop;
	}

/**
 * Sets the PhuninNode server instance for later reference, this plugin doesn't need it but gets it pass anyway due to the interface contract
 *
 * @param \WyriHaximus\PhuninNode\Node $node
 */
	public function setNode(\WyriHaximus\PhuninNode\Node $node) {
		$this->__node = $node;
	}

/**
 * Returns the unique slug for this plugin
 *
 * @return string
 */
	public function getSlug() {
		return 'ratchet_events';
	}

/**
 * Populate the configuration object, store it in an attribute and pass it into the resolver
 *
 * @param \React\Promise\DeferredResolver $deferredResolver
 */
	public function getConfiguration(\React\Promise\DeferredResolver $deferredResolver) {
		if ($this->__configuration instanceof \WyriHaximus\PhuninNode\PluginConfiguration) {
			$deferredResolver->resolve($this->__configuration);
			return;
		}

		$this->__configuration = new \WyriHaximus\PhuninNode\PluginConfiguration();
		$this->__configuration->setPair('graph_category', 'ratchet');
		$this->__configuration->setPair('graph_title', 'Events Statistics');

		$this->__configuration->setPair('broadcasts_incoming.label', 'Incoming Broadcasts');
		$this->__configuration->setPair('broadcasts_outgoing.label', 'Outgoing Broadcasts');
		$this->__configuration->setPair('rpc_success.label', 'Successful RPC calls');
		$this->__configuration->setPair('rpc_failed.label', 'Failed RPC calls');
		$this->__configuration->setPair('rpc_blocked.label', 'Blocked RPC calls');

		$deferredResolver->resolve($this->__configuration);
	}

/**
 * Retrive the current connection count values from the server
 *
 * @param \React\Promise\DeferredResolver $deferredResolver
 */
	public function getValues(\React\Promise\DeferredResolver $deferredResolver) {
		$command = new RatchetMessageQueueGetEventsCommand();
		$command->setDeferedResolver($this->_resultDecorator($deferredResolver));
		$command->setHash(Security::hash(serialize(Configure::read('PhuninCake.Node')), 'sha256', true));
		TransportProxy::instance()->queueMessage($command);
	}

}
