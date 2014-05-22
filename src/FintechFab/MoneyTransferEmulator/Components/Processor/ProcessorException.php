<?php

namespace FintechFab\MoneyTransferEmulator\Components\Processor;

use Exception;

class ProcessorException extends Exception
{

	const UNDEFINED = -99;

	const INVALID_TYPE = 900;
	const INVALID_PARAM = 901;
	const INVALID_TIME = 904;
	const INVALID_TERMINAL = 905;
	const INVALID_SIGN = 906;
	const TERMINAL_DISABLED = 907;

	const PAYMENT_NOT_FOUND = 104;

	const RC_NO_SUCH_CITY = 1;
	const RC_NO_SUCH_FEE = 2;
	const RC_EXISTS_OPERATION = 3;
	const RC_PAY_NOT_FOUND = 4;
	const RC_CHECK_NOT_FOUND = 5;

	public static $errors = array(

		self::INVALID_TYPE        => 'Invalid type',
		self::INVALID_PARAM       => 'Invalid parameter value',
		self::INVALID_TIME        => 'Mismatch time',
		self::INVALID_TERMINAL    => 'Invalid terminal number',
		self::INVALID_SIGN        => 'Invalid signature',
		self::TERMINAL_DISABLED   => 'Terminal disabled',

		self::PAYMENT_NOT_FOUND   => 'Payment not found',

		self::UNDEFINED           => 'Undefined failure',
		self::RC_PAY_NOT_FOUND    => 'Undefined pay request',
		self::RC_NO_SUCH_CITY     => 'Undefined city id',
		self::RC_NO_SUCH_FEE      => 'Undefined fee',
		self::RC_EXISTS_OPERATION => 'Already operation',
		self::RC_CHECK_NOT_FOUND  => 'Undefined check request',

	);

	public function __construct($code, $message = null)
	{
		$errorMessage = self::getCodeMessage($code);
		if ($message) {
			$errorMessage .= ' (' . $message . ')';
		}
		parent::__construct($errorMessage, $code);
	}

	public static function getCodeMessage($code)
	{
		return isset(self::$errors[$code])
			? self::$errors[$code]
			: null;
	}

}