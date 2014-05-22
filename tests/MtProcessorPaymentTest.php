<?php

use FintechFab\MoneyTransferEmulator\Components\Helpers\Time;
use FintechFab\MoneyTransferEmulator\Components\Processor\PaymentNumbers;
use FintechFab\MoneyTransferEmulator\Components\Processor\Processor;
use FintechFab\MoneyTransferEmulator\Components\Processor\ProcessorException;
use FintechFab\MoneyTransferEmulator\Components\Processor\Secure;
use FintechFab\MoneyTransferEmulator\Components\Processor\Status;
use FintechFab\MoneyTransferEmulator\Components\Processor\Type;
use FintechFab\MoneyTransferEmulator\Models\Payment;

class MtProcessorPaymentTest extends MoneyTransferEmulatorTestCase
{


	public function setUp()
	{
		parent::setUp();
		$this->mockTerminal();
		Payment::truncate();
		$this->makeCity();
		$this->makeFee();
	}


	public function testCheck()
	{

		$input = $this->doPrepareCheckInput();

		// check

		$processor = $this->makeProcessor($input, 'check');
		$processor->response();
		$paymentCheck = $processor->item();
		$this->assertEquals(Status::ENABLED, $paymentCheck->status);
		$this->assertEmpty($paymentCheck->code);

		// pay

		$processor = $this->makeProcessor($input, 'pay');
		$processor->response();
		$paymentPay = $processor->item();
		$this->assertNotEmpty($paymentPay->code);

		// 'check' changed to 'pay'

		$paymentCheck = Payment::find($paymentCheck->id);
		$this->assertEquals(Status::PAY, $paymentCheck->status);

		// status

		$input = array(
			'term' => $paymentPay->term,
			'code' => $paymentPay->code,
			'to'   => $paymentPay->to,
			'time' => Time::ts(),
		);
		Secure::sign($input, 'status', 'secret');
		$processor = $this->makeProcessor($input, 'status');
		$processor->response();
		$paymentStatus = $processor->item();
		// 'status' == data from 'pay'
		$this->assertEquals($paymentPay->id, $paymentStatus->id);
		$this->assertEquals(Status::PROCESSED, $paymentStatus->status);

		// cancel

		$processor = $this->makeProcessor($input, 'cancel');
		$response = $processor->response();
		$this->assertEmpty($response->error());


	}

	public function testDoublePay()
	{

		$input = $this->doPrepareCheckInput();
		$this->makePay($input);

		// pay 2

		$processor = $this->makeProcessor($input, 'pay');
		$response = $processor->response();
		$this->assertEquals(ProcessorException::RC_EXISTS_OPERATION, $response->code);

	}

	public function testDoubleCancel()
	{

		$input = $this->doPrepareCheckInput();
		$paymentPay = $this->makePay($input);

		// cancel

		$input = array(
			'term' => $paymentPay->term,
			'code' => $paymentPay->code,
			'to'   => $paymentPay->to,
			'time' => Time::ts(),
		);

		$processor = $this->makeProcessor($input, 'cancel');
		$processor->response();
		$paymentCancel = $processor->item();
		$this->assertEquals('cancel', $paymentCancel->type);


		// cancel 2

		$processor = $this->makeProcessor($input, 'cancel');
		$response = $processor->response();
		$this->assertEquals(ProcessorException::RC_EXISTS_OPERATION, $response->code);

	}


	/**
	 * @param string $type
	 * @param array  $input
	 * @param array  $dataPayment
	 *
	 * @return void
	 * @dataProvider response
	 */
	public function testPayment($type, $input, $dataPayment = array())
	{
		/**
		 * @var Processor $opProcessor
		 */

		Secure::sign($input, $type, 'secret');
		$opType = new Type($type, $input);
		$opProcessor = App::make(
			'FintechFab\MoneyTransferEmulator\Components\Processor\Processor',
			array($opType)
		);
		$response = $opProcessor->response();

		if ($response->type != Type::ERROR) {
			$response = $opProcessor->item();
		}

		if ($dataPayment) {
			foreach ($dataPayment as $key => $val) {

				if ($val === '#exists#') {
					// custom value, not empty
					$this->assertNotEmpty($response->$key, 'Value of key ' . $key . ' is required here!');
				} else {
					// concrete value
					$this->assertEquals($val, $response->$key, print_r($dataPayment, true) . print_r($response->toArray(), true));
				}
			}
		}

	}

	public static function response()
	{

		$list = array(

			__LINE__ => array(
				'type'        => 'check',
				'input'       => array(
					'term'   => '123456',
					'amount' => '10.00',
					'cur'    => 'rub',
					'city'   => 12,
					'name'   => 'User Name',
					'time'   => self::time(),
					'email'  => uniqid() . '@example.com',
					'to'     => PaymentNumbers::getValidTo(),
					'from'   => PaymentNumbers::getValidFrom(),
				),
				'dataPayment' => array(
					'term'   => '123456',
					'type'   => 'check',
					'code'   => '',
					'amount' => '10.00',
					'cur'    => 'rub',
					'status' => Status::ENABLED,
					'time'   => date('Y-m-d H:i:s', self::time()),
				),
			),

			__LINE__ => array(
				'type'        => 'check',
				'input'       => array(
					'term'   => '123456',
					'amount' => '10.00',
					'cur'    => 'rub',
					'city'   => 13,
					'name'   => 'User Name',
					'time'   => self::time(),
					'email'  => uniqid() . '@example.com',
					'to'     => PaymentNumbers::getValidTo(),
					'from'   => PaymentNumbers::getValidFrom(),
				),
				'dataPayment' => array(
					'type'    => 'error',
					'code'    => 1,
					'time'    => self::time(),
					'message' => 'Undefined city id',
				),
			),

			__LINE__ => array(
				'type'        => 'check',
				'input'       => array(
					'term'   => '123456',
					'amount' => '0.00',
					'cur'    => 'rub',
					'city'   => 13,
					'name'   => 'User Name',
					'time'   => self::time(),
					'email'  => uniqid() . '@example.com',
					'to'     => PaymentNumbers::getValidTo(),
					'from'   => PaymentNumbers::getValidFrom(),
				),
				'dataPayment' => array(
					'type'    => 'error',
					'code'    => 901,
					'time'    => self::time(),
					'message' => 'Invalid parameter value (Invalid amount)',
				),
			),

		);

		// sign4all responses
		foreach ($list as &$value) {
			if (empty($value['input']['sign'])) {
				Secure::sign($value['input'], $value['type'], 'secret');
			}
		}

		return $list;

	}

	/**
	 * @param $input
	 * @param $type
	 *
	 * @return Processor
	 */
	private function makeProcessor($input, $type)
	{
		Secure::sign($input, $type, 'secret');
		$opType = new Type($type, $input);
		$opProcessor = App::make(
			'FintechFab\MoneyTransferEmulator\Components\Processor\Processor',
			array($opType)
		);

		return $opProcessor;
	}

	/**
	 * @param $input
	 *
	 * @return Payment
	 */
	private function makePay($input)
	{

		// check
		$processor = $this->makeProcessor($input, 'check');
		$processor->response();

		// pay

		$processor = $this->makeProcessor($input, 'pay');
		$processor->response();
		$paymentPay = $processor->item();

		return $paymentPay;

	}

}