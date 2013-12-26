<?php

return array(
	'controller_plugins' => array(
		'aliases' => array(
			'CsvExport' => 'RdnCsv:CsvExport',
			'CsvImport' => 'RdnCsv:CsvImport',
		),

		'invokables' => array(
			'RdnCsv:CsvExport' => 'RdnCsv\Controller\Plugin\CsvExport',
			'RdnCsv:CsvImport' => 'RdnCsv\Controller\Plugin\CsvImport',
		),

		'shared' => array(
			'RdnCsv:CsvExport' => false,
			'RdnCsv:CsvImport' => false,
		),
	),
);
