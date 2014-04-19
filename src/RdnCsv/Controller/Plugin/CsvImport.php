<?php

namespace RdnCsv\Controller\Plugin;

use SplFileObject;
use Zend\Mvc\Controller\Plugin\AbstractPlugin;

/**
 * Quickly import CSV files and iterate over each record.
 */
class CsvImport extends AbstractPlugin implements \Countable, \Iterator
{
	/**
	 * @var SplFileObject
	 */
	protected $file;

	/**
	 * @var bool
	 */
	protected $useFirstRecordAsHeader;

	/**
	 * @var array
	 */
	protected $header;

	/**
	 * Create a CSV iterator. Will use the first record as the header by default.
	 *
	 * @param string $filepath
	 * @param bool $useFirstRecordAsHeader
	 * @param string $delimiter
	 * @param string $enclosure
	 * @param string $escape
	 *
	 * @return CsvImport
	 */
	public function __invoke($filepath, $useFirstRecordAsHeader = true, $delimiter = ',', $enclosure = '"', $escape = '\\')
	{
		$this->file = new SplFileObject($filepath);
		$this->file->setFlags(SplFileObject::READ_CSV | SplFileObject::READ_AHEAD | SplFileObject::SKIP_EMPTY | SplFileObject::DROP_NEW_LINE);

		$this->file->setCsvControl($delimiter, $enclosure, $escape);

		$this->useFirstRecordAsHeader = $useFirstRecordAsHeader;

		return $this;
	}

	/**
	 * Fetch the CSV file object.
	 *
	 * @return SplFileObject
	 */
	public function getFile()
	{
		return $this->file;
	}

	/**
	 * Rewind the Iterator to the first element. Optionally consumes the first record as the header.
	 *
	 * @return void
	 * @throws \RuntimeException
	 */
	public function rewind()
	{
		$this->file->rewind();
		if ($this->useFirstRecordAsHeader)
		{
			if (!$this->file->valid())
			{
				throw new \RuntimeException('Expected first row to be header, but reached EOF instead');
			}
			$this->header = $this->file->current();
			$this->file->next();
		}
	}

	/**
	 * Return the row number of the current element. Note: does not start at zero.
	 *
	 * @return int
	 */
	public function key()
	{
		return $this->file->key() + 1;
	}

	/**
	 * Return the current record. Optionally uses the header record as keys.
	 *
	 * @return array
	 */
	public function current()
	{
		$line = $this->file->current();
		if ($this->useFirstRecordAsHeader)
		{
			$header = $this->header;
			if (count($line) != count($header))
			{
				$size = min(count($header), count($line));
				$header = array_slice($this->header, 0, $size);
				$line = array_slice($line, 0, $size);
			}
			return array_combine($header, $line);
		}
		return $line;
	}

	/**
	 * Move forward to next element
	 *
	 * @return void
	 */
	public function next()
	{
		$this->file->next();
	}

	/**
	 * Checks if current position is valid
	 *
	 * @return boolean true if not reached EOF, false otherwise.
	 */
	public function valid()
	{
		return $this->file->valid();
	}

    /**
     * Count elements of an object
     *
     * @return int The custom count as an integer.
     */
    public function count()
    {
        $total = 0;
        foreach ($this->file as $line)
        {
            $total++;
        }
        if ($this->useFirstRecordAsHeader && $total > 0)
        {
            $total--;
        }
        return $total;
    }
}
