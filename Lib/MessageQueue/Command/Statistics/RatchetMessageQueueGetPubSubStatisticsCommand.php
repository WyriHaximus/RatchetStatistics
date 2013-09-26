<?php

/*
 * This file is part of Ratchet for CakePHP.
 *
 ** (c) 2012 - 2013 Cees-Jan Kiewiet
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

App::uses('RatchetMessageQueueCommand', 'Ratchet.Lib/MessageQueue/Command');

class RatchetMessageQueueGetPubSubStatisticsCommand extends RatchetMessageQueueCommand {
    
    protected $hash;
    
    public function serialize() {
        return serialize(array(
            'hash' => $this->hash,
            'id' => $this->id,
        ));
    }
    public function unserialize($commandString) {
        $commandString = unserialize($commandString);
        $this->setHash($commandString['hash']);
        $this->id = $commandString['hash'];
    }
    
    public function setHash($hash) {
        $this->hash = $hash;
    }
    
    public function setDeferedResolver($resolver) {
        $this->resolver = $resolver;
    }
    
    public function execute($eventSubject) {
        $event = new CakeEvent('Rachet.WebsocketServer.getPubSubStatistics', $this, array());
        CakeEventManager::instance()->dispatch($event);
        
        return $event->result;
    }
    
    public function response($response) {
        $values = new \SplObjectStorage;
        
        $active_topics = new \PhuninNode\Value();
        $active_topics->setKey('active_topics');
        $active_topics->setValue($response['active_topics']);
        $values->attach($active_topics);
        
        $active_subscribers = new \PhuninNode\Value();
        $active_subscribers->setKey('active_subscribers');
        $active_subscribers->setValue($response['active_subscribers']);
        $values->attach($active_subscribers);
        
        $broadcasts = new \PhuninNode\Value();
        $broadcasts->setKey('broadcasts');
        $broadcasts->setValue($response['broadcasts']);
        $values->attach($broadcasts);
        
        $broadcasts_sec = new \PhuninNode\Value();
        $broadcasts_sec->setKey('broadcasts_sec');
        $broadcasts_sec->setValue($response['broadcasts_sec']);
        $values->attach($broadcasts_sec);
        
        $avg_topics_subscribers = new \PhuninNode\Value();
        $avg_topics_subscribers->setKey('avg_topics_subscribers');
        $avg_topics_subscribers->setValue($response['avg_topics_subscribers']);
        $values->attach($avg_topics_subscribers);
        
        $this->resolver->resolve($values);
    }
}