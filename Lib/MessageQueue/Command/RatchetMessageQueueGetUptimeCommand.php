<?php

/*
 * This file is part of Ratchet for CakePHP.
 *
 ** (c) 2012 - 2013 Cees-Jan Kiewiet
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

App::uses('RatchetMessageQueueCommand', 'RatchetCommands.Lib/MessageQueue/Command');

class RatchetMessageQueueGetUptimeCommand extends RatchetMessageQueueCommand {
    
    const DAY_IN_SECONDS = 86400;
    
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
        $event = new CakeEvent('Rachet.WebsocketServer.getUptime', $this, array());
        CakeEventManager::instance()->dispatch($event);
        
        return $event->result;
    }
    
    public function response($response) {
        $response['uptime'] = round(($response['uptime'] / self::DAY_IN_SECONDS), 2);
        $this->resolver->resolve($response);
    }
}