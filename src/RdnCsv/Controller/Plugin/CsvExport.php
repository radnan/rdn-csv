<?php

namespace RdnCsv\Controller\Plugin;

use Zend\Http\PhpEnvironment\Response as HttpResponse;
use Zend\Mvc\Controller\Plugin\AbstractPlugin;

/**
 * Easily export data to downloadable CSV files.
 */
class CsvExport extends AbstractPlugin
{
	/**
	 * @var string
	 */
	protected $name;

	/**
	 * @var array
	 */
	protected $header;

	/**
	 * @var array|\Traversable
	 */
	protected $content;

	/**
	 * @var callable
	 */
	protected $callback;
        
        protected $delimiter = ',';
        
        protected $enclosure = '"';

	/**
	 * @param string $filename
	 * @param array $header
	 * @param array|\Traversable $records
	 * @param callable $callback
         * @param string $delimiter
         * @param string $enclosure
	 *
	 * @return CsvExport|HttpResponse
	 */
	public function __invoke($filename = null, $header = null, $records = null, callable $callback = null, $delimiter = ',', $enclosure = '"')
	{
            $this->delimiter = $delimiter;
            $this->enclosure = $enclosure;
            
            
		if (func_num_args() == 0)
		{
			return $this;
		}
		elseif (func_num_args() == 1)
		{
			return $this->setName($filename);
		}
		return $this
			->setName($filename)
			->setHeader($header)
			->setContent($records, $callback)
			->getResponse()
		;
	}

	/**
	 * Set the name of the exported file
	 *
	 * @param string $name
	 *
	 * @return CsvExport
	 */
	public function setName($name)
	{
		if (substr($name, -4) == '.csv')
		{
			$name = substr($name, 0, -4);
		}
		$this->name = $name;
		return $this;
	}

	/**
	 * Set the header record
	 *
	 * @param array $record
	 *
	 * @return CsvExport
	 */
	public function setHeader($record)
	{
		$this->header = $record;
		return $this;
	}

	/**
	 * Set the content records along with an optional callback to render them into CSV fields
	 *
	 * @param array|\Traversable $records
	 * @param callable $callback
	 *
	 * @return CsvExport
	 */
	public function setContent($records, callable $callback = null)
	{
		$this->content = $records;
		$this->callback = $callback;
		return $this;
	}

	/**
	 * Prepare the response with the CSV export and return it
	 *
	 * @return HttpResponse
	 * @throws \Exception if any exceptions are thrown within the content callback
	 */
	public function getResponse()
	{
		if (method_exists($this->controller, 'getResponse'))
		{
			/** @var HttpResponse $response */
			$response = $this->controller->getResponse();
		}
		else
		{
			$response = new HttpResponse;
		}

		$fp = fopen('php://output', 'w');
		ob_start();
		fputcsv($fp, $this->header, $this->delimiter, $this->enclosure);
		foreach ($this->content as $i => $item)
		{
			try
			{
				$fields = $this->callback ? call_user_func($this->callback, $item) : $item;
				if (!is_array($fields))
				{
					throw new \RuntimeException('CsvExport can only accept arrays, '. gettype($fields) .' provided at index '. $i .'. Either use arrays when setting the records or use a callback to convert each records into an array.');
				}
				fputcsv($fp, $fields, $this->delimiter, $this->enclosure);
			}
			catch (\Exception $ex)
			{
				ob_end_clean();
				throw $ex;
			}
		}
		fclose($fp);
		$response->setContent(ob_get_clean());

		$response->getHeaders()->addHeaders(array(
			'Content-Type' => 'text/csv',
			'Content-Disposition' => 'attachment;filename="'. str_replace('"', '\\"', $this->name) .'.csv"',
		));

		return $response;
	}
}
