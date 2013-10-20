<?php

/**
 * This file is part of RatchetStatistics for CakePHP.
 *
 ** (c) 2013 Cees-Jan Kiewiet
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

App::uses('RatchetPhuninMemoryUsage', 'RatchetStatistics.Lib/Phunin');
App::uses('AbstractPhuninPluginTest', 'RatchetStatistics.Test/Case/Lib/Phunin');

class RatchetPhuninMemoryUsageTest extends AbstractPhuninPluginTest {

	public function setUp() {
		parent::setUp();
		$this->plugin = new RatchetPhuninMemoryUsage($this->loop);
		$this->node->addPlugin($this->plugin);
	}

}
