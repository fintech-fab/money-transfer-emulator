<?php


use FintechFab\MoneyTransferEmulator\Components\Processor\Processor;
use FintechFab\MoneyTransferEmulator\Components\Processor\ProcessorException;
use FintechFab\MoneyTransferEmulator\Components\Processor\Secure;
use FintechFab\MoneyTransferEmulator\Components\Processor\Status;
use FintechFab\MoneyTransferEmulator\Components\Processor\Type;

class MtProcessorResponseTest extends MoneyTransferEmulatorTestCase
{


	public function setUp()
	{
		parent::setUp();
		$this->mockTerminal();
	}

	/**
	 * @param string $type
	 * @param array  $input
	 * @param array  $dataResponse
	 *
	 * @return void
	 * @dataProvider response
	 */
	public function testResponse($type, $input, $dataResponse = array())
	{
		/**
		 * @var Processor $opProcessor
		 */

		$this->mockPayment($type, $input, $dataResponse);
		Secure::sign($input, $type, 'secret');
		$opType = new Type($type, $input);
		$opProcessor = App::make(
			'FintechFab\MoneyTransferEmulator\Components\Processor\Processor',
			array($opType)
		);
		$response = $opProcessor->response();

		if ($dataResponse) {
			foreach ($dataResponse as $key => $val) {
				$this->assertEquals($val, $response->$key, print_r($dataResponse, true) . print_r($response, true));
			}
		}

	}

	/**
	 * @param string  $type
	 * @param array   $input
	 * @param string  $exceptionCode
	 *
	 * @return void
	 * @dataProvider responseSignFail
	 *
	 * public function testSignFail($type, $input, $exceptionCode = null)
	 * {
	 * /**
	 * @var Processor $opProcessor
	 *
	 *
	 * $opType = new Type($type, $input);
	 * $opProcessor = App::make(
	 * 'FintechFab\MoneyTransferEmulator\Components\Processor\Processor',
	 * array($opType)
	 * );
	 *
	 * try {
	 * $opProcessor->response();
	 * $this->assertFalse(true, 'Exception mus be here with code: ' . $exceptionCode);
	 * } catch (ProcessorException $e) {
	 * $this->assertEquals($exceptionCode, $e->getCode());
	 * }
	 *
	 *
	 * }*/

	/**
	 * @return array
	 */
	public static function response()
	{

		$list = array(
			__LINE__ => array(
				'type'     => 'check',
				'input'    => array(
					'term'   => '123456',
					'city'   => '12',
					'from'   => '79100000000',
					'to'     => '79100000000',
					'amount' => '10.00',
					'cur'    => 'rub',
					'name'   => 'User Name',
					'time'   => self::time(),
				),
				'response' => array(
					'term'   => '123456',
					'type'   => 'check',
					'code'   => '234',
					'amount' => '10.00',
					'cur'    => 'rub',
					'status' => Status::ENABLED,
					'time'   => self::time(),
				),
			),
			__LINE__ => array(
				'type'     => 'pay',
				'input'    => array(
					'term'   => '123456',
					'city'   => '12',
					'from'   => '79100000000',
					'to'     => '79100000000',
					'amount' => '10.00',
					'cur'    => 'rub',
					'name'   => 'User Name',
					'time'   => self::time(),
				),
				'response' => array(
					'type'    => 'error',
					'code'    => 3,
					'message' => 'Already operation',
					'time'    => self::time(),
					'sign'    => '#none#',
				),
			),
			__LINE__ => array(
				'type'     => 'cancel',
				'input'    => array(
					'term' => '123456',
					'to'   => '79100000000',
					'code' => '234',
					'time' => self::time(),
				),
				'response' => array(
					'type'    => 'error',
					'code'    => 3,
					'message' => 'Already operation',
					'time'    => self::time(),
					'sign'    => '#none#',
				),
			),
			__LINE__ => array(
				'type'     => 'status',
				'input'    => array(
					'term' => '123456',
					'to'   => '79100000000',
					'code' => '234',
					'time' => self::time(),
				),
				'response' => array(
					'term'   => '123456',
					'type'   => 'status',
					'code'   => '234',
					'amount' => '10.00',
					'cur'    => 'rub',
					'status' => Status::PROCESSED,
					'time'   => self::time(),
				),
			),
		);

		// sign4all responses
		foreach ($list as &$value) {
			if (empty($value['response']['sign'])) {
				Secure::sign($value['response'], $value['type'], 'secret');
			} elseif ($value['response']['sign'] == '#none#') {
				$value['response']['sign'] = '';
			}
		}

		return $list;

	}

	public static function responseSignFail()
	{

		$list = array(
			__LINE__ => array(
				'type'          => 'auth',
				'input'         => array(
					'term'   => '123456',
					'pan'    => '4024007162441306',
					'year'   => '16',
					'month'  => '06',
					'cvc'    => '123',
					'amount' => '10.00',
					'cur'    => 'rub',
					'order'  => '234',
					'name'   => 'Shop Name',
					'desc'   => 'Excellent Shoes Boutique',
					'url'    => 'http://example.com',
					'email'  => 'money-transfer-emulator@example.com',
					'time'   => self::time(),
					'back'   => 'http://example.com/payment/order/234',
					'sign'   => '123',
				),
				'exceptionCode' => ProcessorException::INVALID_SIGN,
			),
			__LINE__ => array(
				'type'          => 'auth',
				'input'         => array(
					'term'   => '12345',
					'pan'    => '4024007162441306',
					'year'   => '16',
					'month'  => '06',
					'cvc'    => '123',
					'amount' => '10.00',
					'cur'    => 'rub',
					'order'  => '234',
					'name'   => 'Shop Name',
					'desc'   => 'Excellent Shoes Boutique',
					'url'    => 'http://example.com',
					'email'  => 'money-transfer-emulator@example.com',
					'time'   => self::time(),
					'back'   => 'http://example.com/payment/order/234',
				),
				'exceptionCode' => ProcessorException::INVALID_TERMINAL,
			),


		);


		// sign4all requests
		foreach ($list as &$value) {
			if (empty($value['input']['sign'])) {
				Secure::sign($value['input'], $value['type'], 'secret');
			}
		}

		return $list;

	}

}