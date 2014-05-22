<?php

namespace FintechFab\MoneyTransferEmulator\Components\Processor;


/**
 * @property string $code
 * @property string $type
 * @property string $message
 */
class Response
{

	/**
	 * @var array
	 */
	private $data;


	public static $responseFields = array(
		'term',
		'type',
		'code',
		'amount',
		'cur',
		'status',
		'time',
		'sign',
	);

	/**
	 * @param array  $data
	 * @param string $type
	 * @param string $secret
	 */
	public function __construct(array $data, $type, $secret)
	{

		Secure::sign($data, $type, $secret);
		$this->data = $data;

	}

	public function __get($name)
	{
		return isset($this->data[$name])
			? $this->data[$name]
			: null;
	}

	public function data()
	{
		return $this->data;
	}

	public function toArray()
	{
		return $this->data();
	}

	public function error()
	{
		return ($this->type == Type::ERROR)
			? $this->message
			: null;
	}

}