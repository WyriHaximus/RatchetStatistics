<?php

/*
 * This file is part of Ratchet for CakePHP.
 *
 ** (c) 2012 - 2013 Cees-Jan Kiewiet
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

App::uses('TransportProxy', 'RatchetCommands.Lib/MessageQueue/Transports');
App::uses('RatchetMessageQueueGetPubSubStatisticsCommand', 'Ratchet.Lib/MessageQueue/Command/Statistics');
App::uses('AbstractRatchetPhuninPlugin', 'RatchetStatistics.Lib/Phunin');

class RatchetPhuninPubSubStatistics extends AbstractRatchetPhuninPlugin implements \PhuninNode\Interfaces\Plugin {
    
    /**
     * PhuninNode server
     * 
     * @var \PhuninNode\Node
     */
    private $node;
    
    /**
     * Configuration object for this plugin, 
     * 
     * @var \PhuninNode\PluginConfiguration
     */
    private $configuration;
    
    /**
     * ReactPHP Eventloop
     * 
     * @var \React\EventLoop\LoopInterface
     */
    private $loop;
    
    /**
     * 
     * @param \React\EventLoop\LoopInterface $loop
     */
    public function __construct(\React\EventLoop\LoopInterface $loop) {
        $this->loop = $loop;
    }
    
    /**
     * Sets the PhuninNode server instance for later reference, this plugin doesn't need it but gets it pass anyway due to the interface contract
     * 
     * @param \PhuninNode\Node $node
     */
    public function setNode(\PhuninNode\Node $node) {
        $this->node = $node;
    }
    
    /**
     * Returns the unique slug for this plugin
     * 
     * @return string
     */
    public function getSlug() {
        return 'ratchet_pubsub_statistics';
    }
    
    /**
     * Populate the configuration object, store it in an attribute and pass it into the resolver
     * 
     * @param \React\Promise\DeferredResolver $deferredResolver
     */
    public function getConfiguration(\React\Promise\DeferredResolver $deferredResolver) {
        if ($this->configuration instanceof \PhuninNode\PluginConfiguration) {
            $deferredResolver->resolve($this->configuration);
            return;
        }
        
        $this->configuration = new \PhuninNode\PluginConfiguration();
        $this->configuration->setPair('graph_category', 'ratchet');
        $this->configuration->setPair('graph_title', 'Pub/Sub Statistics');
        
        $this->configuration->setPair('active_topics.label', 'Active Topics');
        $this->configuration->setPair('active_subscribers.label', 'Active Subscribers');
        $this->configuration->setPair('broadcasts.label', 'Broadcasts');
        $this->configuration->setPair('broadcasts_sec.label', 'Broadcasts/Sec');
        $this->configuration->setPair('avg_topics_subscribers.label', 'Average topics per subscriber');
        
        $deferredResolver->resolve($this->configuration);
    }
    
    /**
     * Retrive the current connection count values from the server
     * 
     * @param \React\Promise\DeferredResolver $deferredResolver
     */
    public function getValues(\React\Promise\DeferredResolver $deferredResolver) {
        $command = new RatchetMessageQueueGetPubSubStatisticsCommand();
        $command->setDeferedResolver($this->resultDecorator($deferredResolver));
        $command->setHash(Security::hash(serialize(Configure::read('PhuninCake.Node')), 'sha256', true));
        TransportProxy::instance()->queueMessage($command);
    }
    
}