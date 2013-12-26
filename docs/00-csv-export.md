CsvExport
=========

## Basic usage

Call the plugin with the name of the downloadable CSV file, the header, and the records.

~~~php
// inside a controller action

$response = $this->csvExport($filename, $header, $records);

return $response;
~~~

The plugin will return a response object which you can then return from your controller action.

## Callback

In case a record is not a simple array, you can provide an optional callback that will transform the given record into a simple array.

~~~php
$entries = array(
	new Entry('Foo', 'Bar'),
	new Entry('Michael', 'Bluth'),
);

$header = array(
	'First Name',
	'Last Name',
);

return $this->csvExport('foo.csv', $header, $entries, function(Entry $entry)
{
	return array(
		$entry->getFirstName(),
		$entry->getLastName(),
	);
});
~~~
