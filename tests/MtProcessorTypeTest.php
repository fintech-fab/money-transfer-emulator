<?php


use FintechFab\MoneyTransferEmulator\Components\Processor\ProcessorException;
use FintechFab\MoneyTransferEmulator\Components\Processor\Secure;
use FintechFab\MoneyTransferEmulator\Components\Processor\Type;

class MtProcessorTypeTest extends MoneyTransferEmulatorTestCase
{


	public function setUp()
	{
		parent::setUp();
	}

	/**
	 * @param array  $input
	 * @param int    $code
	 * @param string $message
	 *
	 * @return void
	 *
	 * @dataProvider check
	 */
	public function testCheck($input, $code = null, $message = null)
	{

		Secure::sign($input, 'check', 'secret');

		$opType = new Type('check', $input);

		try {
			$this->assertTrue($opType->validate());

			if ($code) {
				$this->assertFalse(true, 'Exception must be there with code ' . $code);
			}

		} catch (ProcessorException $e) {
			$this->assertEquals($code, $e->getCode(), $e->getMessage());
			$this->assertContains($message, $e->getMessage());
		}

	}


	public static function check()
	{
		return array(
			__LINE__ => array(
				'input' => array(
					'term'   => '123456',
					'city'   => '12',
					'from'   => '79100000000',
					'to'     => '79100000000',
					'amount' => '10.00',
					'cur'    => 'rub',
					'name'   => 'User Name',
					'time'   => self::time(),
				),
			),
			__LINE__ => array(
				'input'     => array(
					'term'   => '123456',
					'city'   => '12',
					'from'   => '79100000000',
					'to'     => '79100000000',
					'amount' => '0.00',
					'cur'    => 'rub',
					'name'   => 'User Name',
					'time'   => self::time(),
				),
				'code'      => ProcessorException::INVALID_PARAM,
				'exception' => 'amount',
			),
			__LINE__ => array(
				'input'   => array(
					'term'   => '123456',
					'city'   => '12',
					'from'   => '79100000000',
					'to'     => '79100000000',
					'amount' => '10.00',
					'cur'    => 'rub',
					'name'   => 'User Name',
					'time'   => self::time() - 60 * 60 * 60,
				),
				'code'    => ProcessorException::INVALID_TIME,
				'message' => 'Mismatch time',
			),
		);
	}

}