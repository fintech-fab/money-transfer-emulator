<?php

namespace FintechFab\MoneyTransferEmulator\Components\Processor;


use Validator;

/**
 * @property string $term
 * @property string $sign
 */
class Input
{

	/**
	 * @var Type
	 */
	private $type;

	/**
	 * @var array
	 */
	private $input;

	/**
	 * @var \Illuminate\Validation\Validator
	 */
	private $validator = null;

	/**
	 * @var array
	 */
	public static $rules = array(
		'amount' => 'required|min:0.01|regex:/^\d+\.\d{2}$/',
		'cur'    => 'required|alpha|size:3',
		'fee'    => 'required',
		'code'   => 'required',
		'name'   => 'max:50',
		'city'   => 'required|integer|min:1',
		'time'   => 'required|integer',
		'term'   => 'required|digits_between:1,11',
		'sign'   => 'required',
		'email'  => 'email',
		'from'   => 'required|digits_between:11,13',
		'to'     => 'required|digits_between:11,13',
	);

	public static $paramNames = array(
		'amount' => 'Сумма платежа',
		'cur'    => 'Валюта платежа',
		'fee'    => 'Комиссия плательщика',
		'code'   => 'Код платежа',
		'name'   => 'Имя получателя',
		'city'   => 'Город получателя',
		'time'   => 'Время сервера',
		'term'   => 'Терминал ПС',
		'email'  => 'E-mail отправителя',
		'from'   => 'Идентификатор отправителя',
		'to'     => 'Идентификатор получателя',
		'sign' => 'Подпись',
	);

	public function __construct(Type $type, array $input)
	{

		$this->type = $type;
		$this->input = $input;

	}

	public function __get($name)
	{
		return isset($this->input[$name])
			? $this->input[$name]
			: null;
	}

	public function validate()
	{

		$this->initValidator();

		if (!$this->validator->passes()) {
			throw new ProcessorException(ProcessorException::INVALID_PARAM, $this->error());
		}

		$this->validateAmount();
		$this->validateDestination();
		$this->validateTime();


		return true;

	}


	private function initValidator()
	{

		$fields = $this->type->fields();
		$rules = array();

		foreach ($fields as $value) {
			$rules[$value] = self::$rules[$value];
		}

		$this->validator = Validator::make($this->input, $rules);

	}

	public function error()
	{
		if ($this->validator->errors()) {
			return $this->validator->errors()->first();
		}

		return null;
	}


	private function validateDestination()
	{

	}

	private function validateTime()
	{
		if (
			$this->input['time'] < time() - 60 * 60 ||
			$this->input['time'] > time() + 60 * 60
		) {
			throw new ProcessorException(ProcessorException::INVALID_TIME);
		}
	}

	/**
	 * @return array
	 */
	public function all()
	{
		return $this->input;
	}


	public static function name($name)
	{
		return isset(self::$paramNames[$name])
			? self::$paramNames[$name]
			: null;
	}

	private function validateAmount()
	{
		if (empty($this->input['amount'])) {
			return;
		}

		$amount = intval(round($this->input['amount'], 2) * 100);
		if ($amount < 1) {
			throw new ProcessorException(ProcessorException::INVALID_PARAM, 'Invalid amount');
		}
	}

}