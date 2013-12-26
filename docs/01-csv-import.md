CsvImport
=========

## Basic usage

Call the plugin with the path to the CSV file. The plugin will return an iterator which you can use to loop over all the rows in the CSV file.

~~~php
// inside a controller action

$rows = $this->csvImport('/path/to/foo.csv');
foreach ($rows as $row)
{
	$header = array_keys($row);
	$record = array_values($row);
}
~~~

## Header

By default the plugin will use the first record as the header for each subsequent record. In this case the keys of each row will be the header record, and the values will be the record itself.

You can toggle this option off by using the seond argument:

~~~php
$rows = $this->csvImport('/path/to/foo.csv', false);
foreach ($rows as $row)
{
	$row == array_values($row);
}
~~~

## CSV controls

You can customize the `delimiter`, `enclosure`, and `escape` controls when importing a CSV:

~~~php
$rows = $this->csvImport('/path/to/foo.csv', true, ',', '"', '\\');
foreach ($rows as $row)
{
}
~~~
