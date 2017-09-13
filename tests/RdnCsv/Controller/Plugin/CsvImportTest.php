<?php

namespace RdnCsv\Controller\Plugin;

use org\bovigo\vfs\vfsStream;
use Zend\Mvc\Controller\PluginManager;
use Zend\ServiceManager\Config;
use Zend\ServiceManager\ServiceManager;

class CsvImportTest extends \PHPUnit_Framework_TestCase
{
	private $vfs;

	public function setUp()
	{
		$this->vfs = vfsStream::setup('root', null, array(
			'foo.csv' => <<<CSV
Year,Make,Model,Description,Price
1997,Ford,E350,"ac, abs, moon",3000.00
1999,Chevy,"Venture ""Extended Edition""","",4900.00
1999,Chevy,"Venture ""Extended Edition, Very Large""",,5000.00
1996,Jeep,Grand Cherokee,"MUST SELL!
air, moon roof, loaded",4799.00
CSV
		));
	}

	public function testImport()
	{
		$csv = new CsvImport;
		$csv = $csv->__invoke(vfsStream::url('root/foo.csv'));

		foreach ($csv as $row)
		{
			$this->assertArrayHasKey('Year', $row);
			$this->assertArrayHasKey('Make', $row);
			$this->assertArrayHasKey('Model', $row);
			$this->assertArrayHasKey('Description', $row);
			$this->assertArrayHasKey('Price', $row);

			$this->assertEquals('1997', $row['Year']);
			$this->assertEquals('Ford', $row['Make']);
			$this->assertEquals('E350', $row['Model']);
			$this->assertEquals('ac, abs, moon', $row['Description']);
			$this->assertEquals('3000.00', $row['Price']);

			break;
		}
	}

	public function testCount()
	{
		$csv = new CsvImport;
		$csv = $csv->__invoke(vfsStream::url('root/foo.csv'));

		$this->assertEquals(4, count($csv));
	}

	public function testPlugin()
	{
        $configArray = include __DIR__ .'/../../../../config/module.config.php';
        $pluginManager = new PluginManager(new ServiceManager(), $configArray['controller_plugins']);

		$plugin = $pluginManager->get('CsvImport');
		$this->assertInstanceOf('RdnCsv\Controller\Plugin\CsvImport', $plugin);

		$anotherPlugin = $pluginManager->get('RdnCsv:CsvImport');
		$this->assertNotSame($plugin, $anotherPlugin);
	}
}
