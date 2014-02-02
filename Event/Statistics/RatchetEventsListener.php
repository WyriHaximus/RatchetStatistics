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

class RatchetEventsListener implements CakeEventListener {

    private $counters = [];

    public function __construct() {
        $this->reset();
    }

    /**
     * Returns an array with the events this listener hooks into
     *
     * @return array
     */
    public function implementedEvents() {
        return array(
          'Rachet.WampServer.construct' => 'construct',
          'Rachet.WampServer.RpcBlocked' => 'RpcBlocked',
          'Rachet.WampServer.RpcSuccess' => 'RpcSuccess',
          'Rachet.WampServer.RpcFailed' => 'RpcFailed',
          'Rachet.WampServer.broadcast' => 'broadcast',
          'Rachet.WampServer.onPublish' => 'onPublish',
          'RachetStatistics.WebsocketServer.getEvents' => 'getEvents',
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

    public function RpcBlocked(CakeEvent $event) {
        $this->counters['rpc_blocked']++;
    }

    public function RpcSuccess(CakeEvent $event) {
        $this->counters['rpc_success']++;
    }

    public function RpcFailed(CakeEvent $event) {
        $this->counters['rpc_failed']++;
    }

    public function broadcast(CakeEvent $event) {
        $this->counters['broadcasts_outgoing']++;
    }

    public function onPublish(CakeEvent $event) {
        $this->counters['broadcasts_incoming']++;
    }

    /**
     * Returns an array with the current memory usage and the peak memory usage
     *
     * @param CakeEvent $event
     */
    public function getEvents(CakeEvent $event) {
        $event->result = $this->counters;

        $this->wampServer->getLoop()->addTimer(.1, function() {
            $this->reset();
        });
    }

    private function reset() {
        $this->counters = [
          'broadcasts_incoming' => 0,
          'broadcasts_outgoing' => 0,
          'rpc_success' => 0,
          'rpc_failed' => 0,
          'rpc_blocked' => 0,
        ];
    }
}
