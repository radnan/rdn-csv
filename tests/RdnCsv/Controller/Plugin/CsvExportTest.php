<?php

namespace RdnCsv\Controller\Plugin;

use org\bovigo\vfs\vfsStream;
use Zend\Mvc\Controller\PluginManager;
use Zend\ServiceManager\Config;

class CsvExportTest extends \PHPUnit_Framework_TestCase
{
	private $vfs;

	public function setUp()
	{
		$this->vfs = vfsStream::setup('root');
	}

	public function testImport()
	{
		$csv = new CsvExport;
		$header = array(
			'Year',
			'Make',
			'Model',
			'Description',
			'Price',
		);
		$records = array(
			array(
				'1997',
				'Ford',
				'E350',
				'ac, abs, moon',
				'3000.00',
			),
		);
		$response = $csv->__invoke('foo.csv', $header, $records);

		$type = $response->getHeaders()->get('Content-Type')->getFieldValue();
		$this->assertEquals('text/csv', $type);

		$disposition = $response->getHeaders()->get('Content-Disposition')->getFieldValue();
		$this->assertEquals('attachment;filename="foo.csv"', $disposition);

		$content = <<<CSV
Year,Make,Model,Description,Price
1997,Ford,E350,"ac, abs, moon",3000.00

CSV;
		$this->assertEquals($content, $response->getContent());
	}

	public function testPlugin()
	{
		$config = include __DIR__ .'/../../../../config/module.config.php';
		$plugins = new PluginManager(new Config($config['controller_plugins']));

		$plugin = $plugins->get('CsvExport');
		$this->assertInstanceOf('RdnCsv\Controller\Plugin\CsvExport', $plugin);

		$anotherPlugin = $plugins->get('CsvExport');
		$this->assertFalse($plugin === $anotherPlugin);
	}
}
