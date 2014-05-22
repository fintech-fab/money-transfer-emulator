<?php

namespace FintechFab\MoneyTransferEmulator\Components\Processor;


use FintechFab\MoneyTransferEmulator\Models\Terminal;

class Type
{

	const CITY = 'city';
	const FEE = 'fee';
	const CHECK = 'check';
	const PAY = 'pay';
	const STATUS = 'status';
	const CANCEL = 'cancel';
	const ERROR = 'error';

	private static $typeList = array(
		self::CITY,
		self::FEE,
		self::CHECK,
		self::PAY,
		self::STATUS,
		self::CANCEL,
	);

	public static $typeNames = array(
		self::CITY   => 'Список городов',
		self::FEE    => 'Комиссия за перевод',
		self::CHECK  => 'Проверка возможности платежа',
		self::PAY    => 'Регистрация платежа',
		self::STATUS => 'Проверка статуса платежа',
		self::CANCEL => 'Отмена платежа',
	);


	public static $fields = array(

		self::CITY   => array(
			'term',
			'time',
			'sign',
		),

		self::FEE    => array(
			'term',
			'city',
			'amount',
			'cur',
			'time',
			'sign',
		),

		self::CHECK  => array(
			'term',
			'city',
			'from',
			'to',
			'amount',
			'cur',
			'name',
			'email',
			'time',
			'sign',
		),

		self::PAY    => array(
			'term',
			'city',
			'from',
			'to',
			'amount',
			'cur',
			'name',
			'email',
			'time',
			'sign',
		),

		self::STATUS => array(
			'term',
			'code',
			'to',
			'time',
			'sign',
		),

		self::CANCEL => array(
			'term',
			'code',
			'to',
			'time',
			'sign',
		),

	);

	private $type;

	/**
	 * @var Input
	 */
	private $input;

	public function __construct($type, $input)
	{

		$this->type = $type;
		$this->input = new Input($this, $input);

		if (!in_array($this->type, self::$typeList)) {
			throw new ProcessorException(ProcessorException::INVALID_TYPE);
		}

	}

	public static function clearInput($type, $input)
	{
		$fields = self::$fields[$type];
		foreach ($input as $k => $v) {
			if (!in_array($k, $fields)) {
				unset($input[$k]);
			}
		}

		return $input;
	}


	public function validate()
	{
		$this->input->validate();

		return true;
	}

	public function error()
	{
		return $this->input->error();
	}

	public function sid()
	{
		return $this->type;
	}

	public function fields()
	{
		return self::$fields[$this->type];
	}

	public function termId()
	{
		return $this->input->term;
	}

	public function inputs()
	{

		$fields = self::$fields[$this->sid()];
		$inputs = $this->input->all();
		foreach ($inputs as $key => $value) {
			if (!in_array($key, $fields)) {
				unset($inputs[$key]);
			}
		}
		if (isset($inputs['sign'])) {
			unset($inputs['sign']);
		}

		if (empty($inputs)) {
			dd($inputs);
		}

		return $inputs;
	}

	/**
	 * @param string $secret
	 *
	 * @throws ProcessorException
	 */
	public function validateSign($secret)
	{

		$inputData = $this->input->all();
		Secure::sign($inputData, $this->sid(), $secret);
		if ($inputData['sign'] !== $this->input->sign) {
			throw new ProcessorException(ProcessorException::INVALID_SIGN);
		}

	}


	public function validateTermEnabled($mode)
	{

		if ($mode === Terminal::C_STATE_DISABLED) {
			throw new ProcessorException(ProcessorException::TERMINAL_DISABLED);
		}

	}


	/**
	 * это финансовый запрос?
	 *
	 * @return boolean
	 */
	public function isFinanceRequest()
	{
		return in_array(
			$this->sid(),
			array(
				self::PAY,
				self::CHECK,
			)
		);
	}

	public function isFeeRequest()
	{
		return $this->type == Type::FEE;
	}

}