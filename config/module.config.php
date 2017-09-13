<?php

use RdnCsv\Controller\Plugin;
use Zend\ServiceManager\Factory\InvokableFactory;

return array(
	'controller_plugins' => array(
		'aliases' => array(
			'CsvExport'        => Plugin\CsvExport::class,
			'CsvImport'        => Plugin\CsvImport::class,
			'RdnCsv:CsvExport' => Plugin\CsvExport::class,
			'RdnCsv:CsvImport' => Plugin\CsvImport::class,
		),

		'factories' => array(
			Plugin\CsvExport::class => InvokableFactory::class,
			Plugin\CsvImport::class => InvokableFactory::class,
		),

		'shared' => array(
			'CsvExport'             => false,
			'CsvImport'             => false,
            Plugin\CsvExport::class => false,
            Plugin\CsvImport::class => false,
			'RdnCsv:CsvExport'      => false,
			'RdnCsv:CsvImport'      => false,
		),
	),
);
