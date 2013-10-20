<?php

/**
 * This file is part of RatchetStatistics for CakePHP.
 *
 ** (c) 2013 Cees-Jan Kiewiet
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

App::uses('RatchetPhuninUptime', 'RatchetStatistics.Lib/Phunin');
App::uses('AbstractPhuninPluginTest', 'RatchetStatistics.Test/Case/Lib/Phunin');

class RatchetPhuninUptimeTest extends AbstractPhuninPluginTest {

	public function setUp() {
		parent::setUp();
		$this->plugin = new RatchetPhuninUptime($this->loop);
		$this->node->addPlugin($this->plugin);
	}

}
