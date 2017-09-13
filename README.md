RdnCsv
======

The **RdnCsv** ZF2 and ZF3 module makes it really easy to export and import CSV files.

## How to install

1. Use `composer` to require the `radnan/rdn-csv` package:

   ~~~bash
   $ composer require radnan/rdn-csv:1.*
   ~~~

2. Activate the module by including it in your `application.config.php` file:

   ~~~php
   <?php

   return array(
       'modules' => array(
           'RdnCsv',
           // ...
       ),
   );
   ~~~

## How to use

The module comes with two plugins - `csvExport()` and `csvImport()`.

### `csvExport()`

Export data into a downloadable CSV file using this plugin.

~~~php
// inside a controller action

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

return $this->CsvExport('foo.csv', $header, $records);
~~~

The plugin will return a response object which you can then return from your controller action.

[Read more documentation on `csvExport()`](docs/00-csv-export.md)

### `csvImport()`

Import data from a CSV file using this plugin.

~~~php
// inside a controller action

$csv = $this->CsvImport('/path/to/foo.csv');

foreach ($csv as $row)
{
	var_dump($row);
	// array(
	//     'Year' => '1997',
	//     'Make' => 'Ford',
	//     'Model' => 'E350',
	//     'Description' => 'ac, abs, moon',
	//     'Price' => '3000.00',
	// )
}
~~~

The plugin returns an iterator that can be used to loop over all the rows in the CSV file.

[Read more documentation on `csvImport()`](docs/01-csv-import.md)
