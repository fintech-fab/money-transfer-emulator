<?php

namespace FintechFab\MoneyTransferEmulator\Components\Processor;

use FintechFab\MoneyTransferEmulator\Components\Helpers\Time;
use FintechFab\MoneyTransferEmulator\Models\Payment as PaymentModel;

class Payment
{

	/**
	 * @var PaymentModel
	 */
	private $payment;

	/**
	 * @var PaymentModel
	 */
	private static $paymentStatic;

	public function __construct($type, $data, PaymentModel $payment)
	{
		$data['type'] = $type;
		$data['time'] = Time::dt($data['time']);
		$this->payment = $payment->newInstance($data);
		self::$paymentStatic = $payment->newInstance();
	}

	/**
	 * Init exists payment if double operation
	 */
	private function checkDouble()
	{

		$type = new Type($this->payment->type, $this->payment->toArray());
		$payment = null;

		switch ($type->sid()) {

			case Type::PAY:

				$payment = self::findDoublePay(
					$this->payment->term,
					$this->payment->city,
					$this->payment->amount,
					$this->payment->to,
					Status::PROCESSED
				);

				break;

			case Type::CANCEL:

				$payment = self::findDoubleCancel(
					$this->payment->term,
					$this->payment->code,
					Status::PROCESSED
				);

				break;

		}

		if ($payment) {
			throw new ProcessorException(ProcessorException::RC_EXISTS_OPERATION);
		}


	}

	/**
	 * payment processing
	 */
	public function doProcess()
	{

		// double request errors
		$this->checkDouble();

		// process type errors
		$rc = $this->doProcessType();
		if ($rc !== '00') {
			$this->payment->status = Status::ERROR;
			$this->payment->save();

			return;
		}

		$this->payment->saveByType();

	}

	/**
	 * Process logic by payment Type
	 *
	 * @throws ProcessorException
	 */
	private function doProcessType()
	{
		$type = new Type($this->payment->type, $this->payment->toArray());

		switch ($type->sid()) {

			case Type::CITY:

				break;

			case Type::FEE:

				break;

			case Type::CHECK:

				// todo check 'check' request

				// register check request
				$this->payment->status = Status::ENABLED;

				break;

			case Type::PAY:


				// pay after 'check' request
				$payment = self::findCheck(
					$this->payment->term,
					$this->payment->city,
					$this->payment->amount,
					$this->payment->to,
					Status::ENABLED
				);

				if (!$payment) {
					throw new ProcessorException(ProcessorException::RC_CHECK_NOT_FOUND);
				}

				// close 'check' request
				$payment->status = Status::PAY;
				$payment->saveByType();

				// processed 'payment' request
				$this->payment->status = Status::PROCESSED;
				$this->payment->code = uniqid();

				break;

			case Type::CANCEL:

				// find pay in status 'processed'
				$payment = self::findPayTo(
					$this->payment->term,
					$this->payment->code,
					$this->payment->to,
					Status::PROCESSED
				);

				if ($payment) {

					// cancel 'pay' request
					$payment->status = Status::CANCELED;
					$payment->saveByType();

					// save cancel request in 'processed'
					$this->payment->status = Status::PROCESSED;
					$this->payment->amount = $payment->amount;
					$this->payment->name = $payment->name;
					$this->payment->to = $payment->to;

				} else {
					throw new ProcessorException(ProcessorException::RC_PAY_NOT_FOUND);
				}

				break;

			case Type::STATUS:

				// status for pay
				$payment = self::findPayTo(
					$this->payment->term,
					$this->payment->code,
					$this->payment->to
				);

				// init data from 'pay' request into current 'status' request
				if ($payment) {
					$this->payment->id = $payment->id;
					$this->payment->status = $payment->status;
					$this->payment->amount = $payment->amount;
					$this->payment->to = $payment->to;
					$this->payment->name = $payment->name;
				} else {
					throw new ProcessorException(ProcessorException::RC_PAY_NOT_FOUND);
				}

				break;

		}

		return '00';

	}

	/**
	 * @return PaymentModel
	 */
	public function item()
	{
		return $this->payment;
	}


	/**
	 * search payment
	 *
	 * @param string       $term
	 * @param string       $code
	 * @param string|array $status
	 *
	 * @return PaymentModel|null
	 */
	public static function findPay($term, $code = null, $status = null)
	{
		$payment = self::$paymentStatic
			->newInstance()
			->whereTerm($term)
			->whereType(Type::PAY)
			->whereCode($code);

		if (!is_array($status)) {
			$status = (array)$status;
		}
		if ($status) {
			$payment->whereIn('status', $status);
		}
		$payment->orderBy('id', 'desc');

		$payment = $payment->first();

		return $payment;

	}

	/**
	 * search payment
	 *
	 * @param string $term
	 * @param string $code
	 * @param string $to
	 *
	 * @param null   $status
	 *
	 * @return PaymentModel|null
	 */
	public static function findPayTo($term, $code, $to, $status = null)
	{
		$payment = self::$paymentStatic
			->newInstance()
			->whereTerm($term)
			->whereTo($to)
			->whereType(Type::PAY)
			->whereCode($code);

		if (!is_array($status)) {
			$status = (array)$status;
		}
		if ($status) {
			$payment->whereIn('status', $status);
		}
		$payment->orderBy('id', 'desc');

		$payment = $payment->first();

		return $payment;

	}

	/**
	 * search payment
	 *
	 * @param string       $term
	 * @param integer      $city
	 * @param float        $amount
	 * @param string       $to
	 * @param string|array $status
	 *
	 * @return PaymentModel|null
	 */
	public static function findDoublePay($term, $city, $amount, $to, $status = null)
	{
		$payment = self::$paymentStatic
			->newInstance()
			->whereTerm($term)
			->whereTo($to)
			->whereAmount($amount)
			->whereCity($city)
			->whereType(Type::PAY);

		if (!is_array($status)) {
			$status = (array)$status;
		}
		if ($status) {
			$payment->whereIn('status', $status);
		}
		$payment->orderBy('id', 'desc');

		$payment = $payment->first();

		return $payment;

	}

	/**
	 * search payment
	 *
	 * @param string       $term
	 * @param string       $code
	 * @param string|array $status
	 *
	 * @return PaymentModel|null
	 */
	public static function findDoubleCancel($term, $code, $status = null)
	{
		$payment = self::$paymentStatic
			->newInstance()
			->whereTerm($term)
			->whereCode($code)
			->whereType(Type::CANCEL);

		if (!is_array($status)) {
			$status = (array)$status;
		}
		if ($status) {
			$payment->whereIn('status', $status);
		}
		$payment->orderBy('id', 'desc');

		$payment = $payment->first();

		return $payment;

	}

	/**
	 * search payment
	 *
	 * @param string       $term
	 * @param integer      $city
	 * @param float        $amount
	 * @param string       $to
	 * @param string|array $status
	 *
	 * @return PaymentModel|null
	 */
	public static function findCheck($term, $city, $amount, $to, $status = null)
	{
		$payment = self::$paymentStatic
			->newInstance()
			->whereTerm($term)
			->whereCity($city)
			->whereAmount($amount)
			->whereTo($to)
			->whereType(Type::CHECK);


		if (!is_array($status)) {
			$status = (array)$status;
		}
		if ($status) {
			$payment->whereIn('status', $status);
		}
		$payment->orderBy('id', 'desc');

		$payment = $payment->first();

		return $payment;

	}


}