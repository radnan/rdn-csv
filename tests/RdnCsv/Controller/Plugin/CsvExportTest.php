<?php

namespace RdnCsv\Controller\Plugin;

use org\bovigo\vfs\vfsStream;
use Laminas\Mvc\Controller\PluginManager;
use Laminas\ServiceManager\ServiceManager;

class CsvExportTest extends \PHPUnit_Framework_TestCase
{
	private $vfs;

	private $header = array(
		'Year',
		'Make',
		'Model',
		'Description',
		'Price',
	);

	private $records = array(
		array(
			'1997',
			'Ford',
			'E350',
			'ac, abs, moon',
			'3000.00',
		),
	);

	public function setUp()
	{
		$this->vfs = vfsStream::setup('root');
	}

	public function testExport()
	{
		$csv = new CsvExport;
		$response = $csv->__invoke('foo.csv', $this->header, $this->records);

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

	public function testExportCustomControls()
	{
		$csv = new CsvExport;
		$response = $csv->__invoke('foo.csv', $this->header, $this->records, null, ';', "'");

		$content = <<<CSV
Year;Make;Model;Description;Price
1997;Ford;E350;'ac, abs, moon';3000.00

CSV;
		$this->assertEquals($content, $response->getContent());
	}

	public function testPlugin()
	{
		$config = include __DIR__ .'/../../../../config/module.config.php';
		$plugins = new PluginManager(new ServiceManager(), $config['controller_plugins']);

		$plugin = $plugins->get('CsvExport');
		$this->assertInstanceOf('RdnCsv\Controller\Plugin\CsvExport', $plugin);

		$anotherPlugin = $plugins->get('RdnCsv:CsvExport');
		$this->assertNotSame($plugin, $anotherPlugin);
	}
}
