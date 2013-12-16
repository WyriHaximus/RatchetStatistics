<?php

App::uses('Security', 'Utility');
App::uses('TransportProxy', 'RatchetCommands.Lib/MessageQueue/Transports');
App::uses('RatchetMessageQueueGetConnectionsCommand', 'RatchetStatistics.Lib/MessageQueue/Command/');
App::uses('RatchetMessageQueueGetMemoryUsageCommand', 'RatchetStatistics.Lib/MessageQueue/Command/');

class NagiosShell extends AppShell {

	const STATUS_CODE_OK				= 0;
	const STATUS_CODE_WARNING		= 1;
	const STATUS_CODE_CRITICAL	= 2;
	const STATUS_CODE_UNKNOWN		= 3;

/**
 * Echo no header by overriding the startup method
 *
 * @return void
 */
	public function startup() {
	}

	public function connections() {
		$deferred = new \React\Promise\Deferred();
		$deferred->promise()->then(array($this, 'handleConnectionsResponse'));
		$command = new RatchetMessageQueueGetConnectionsCommand();
		$command->setDeferedResolver($deferred->resolver());
		$command->setHash(Security::hash(serialize(Configure::read('PhuninCake.Node')), 'sha256', true));
		TransportProxy::instance()->queueMessage($command);
	}

	public function handleConnectionsResponse($response) {
		$connections = $response['users'] + $response['guests'];
		$statusCode = self::STATUS_CODE_OK;

		if ($connections >= $this->params['critical']) {
			$statusCode = self::STATUS_CODE_CRITICAL;
		} elseif ($connections >= $this->params['warning']) {
			$statusCode = self::STATUS_CODE_WARNING;
		}

		$statusMessage = sprintf(__d('ratchet_statistics', '%1$d connections = %2$d users + %3$d guests', $connections, $response['users'], $response['guests']));

		$this->_output($statusCode, $statusMessage);
	}

	public function memory() {
		$deferred = new \React\Promise\Deferred();
		$deferred->promise()->then(array($this, 'handleMemoryResponse'));
		$command = new RatchetMessageQueueGetMemoryUsageCommand();
		$command->setDeferedResolver($deferred->resolver());
		$command->setHash(Security::hash(serialize(Configure::read('PhuninCake.Node')), 'sha256', true));
		TransportProxy::instance()->queueMessage($command);
	}

	public function handleMemoryResponse($response) {
		$statusCode = self::STATUS_CODE_OK;

		if ($response['memory_usage'] >= $this->params['critical']) {
			$statusCode = self::STATUS_CODE_CRITICAL;
		} elseif ($response['memory_usage'] >= $this->params['warning']) {
			$statusCode = self::STATUS_CODE_WARNING;
		}

		$statusMessage = sprintf(__d('ratchet_statistics', '%1$d current usage / %2$d peak usage', $response['memory_usage'], $response['memory_peak_usage']));

		$this->_output($statusCode, $statusMessage);
	}

	public function getOptionParser() {
		$parser = parent::getOptionParser();
		return $parser->description(__d('ratchet_statistics', 'Get the list of available shells for this CakePHP application.'))
				->addOption('warning', array(
					'default ' => 500,
					'short' => 'w',
					'help' => __d('ratchet_statistics', 'Warning level'),
				))
				->addOption('critical', array(
					'default' => 1000,
					'short' => 'c',
					'help' => __d('ratchet_statistics', 'Critical level'),
				))
				->addOption('timeout', array(
					'default' => 5,
					'short' => 't',
					'help' => __d('ratchet_statistics', 'Timeout in seconds'),
				));
	}

/**
 * Format and output the right text and return the right exit code
 *
 * @param int $statusCode
 * @param string $message
 */
	protected function _output($statusCode, $statusMessage) {
		$statusText = 'RATCHET ';
		switch ($statusCode) {
			case self::STATUS_CODE_OK:
				$statusText .= 'OK';
				break;
			case self::STATUS_CODE_WARNING:
				$statusText .= 'WARNING';
				break;
			case self::STATUS_CODE_CRITICAL:
				$statusText .= 'CRITICAL';
				break;
			case self::STATUS_CODE_UNKNOWN:
				$statusText .= 'UNKNOWN';
				break;
		}

		$statusText .= ' ' . $statusMessage;

		$this->out($statusText . PHP_EOL . $statusCode);
		exit($statusCode);
	}
}
