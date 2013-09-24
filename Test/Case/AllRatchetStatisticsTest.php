<?php
/**
 * All RatchetStatistics plugin tests
 */
class AllRatchetStatisticsTest extends CakeTestCase {

/**
 * Suite define the tests for this plugin
 *
 * @return void
 */
	public static function suite() {
		$suite = new CakeTestSuite('All RatchetStatistics test');

		$path = CakePlugin::path('RatchetStatistics') . 'Test' . DS . 'Case' . DS;
		$suite->addTestDirectoryRecursive($path);

		return $suite;
	}

}
