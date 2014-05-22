<?php


use FintechFab\MoneyTransferEmulator\Components\Helpers\Time;
use FintechFab\MoneyTransferEmulator\Components\Processor\PaymentNumbers;
use FintechFab\MoneyTransferEmulator\Models\City;
use FintechFab\MoneyTransferEmulator\Models\Fee;
use FintechFab\MoneyTransferEmulator\Models\Terminal;

class MoneyTransferEmulatorTestCase extends TestCase
{


	public static $time = null;

	public function setUp()
	{
		parent::setUp();
	}

	public static function time()
	{
		return Time::ts();
	}


	protected function mockTerminal()
	{
		$term = Mockery::mock('FintechFab\MoneyTransferEmulator\Models\Terminal');

		// existing terminal
		$term->shouldReceive('find')->withArgs(array('123456'))->andReturn($term);

		// terminal with attributes
		$term->shouldReceive('getAttribute')->withArgs(array('email'))->andReturn('');
		$term->shouldReceive('getAttribute')->withArgs(array('url'))->andReturn('');
		$term->shouldReceive('getAttribute')->withArgs(array('id'))->andReturn('123456');
		$term->shouldReceive('getAttribute')->withArgs(array('secret'))->andReturn('secret');
		$term->shouldReceive('getAttribute')->withArgs(array('mode'))->andReturn(Terminal::C_STATE_ENABLED);
		$term->shouldReceive('newInstance')->andReturn($term);

		// undefined terminal
		$term->shouldReceive('find')->withArgs(array('12345'))->andReturn(null);

		// ioc
		$this->app->bind('FintechFab\MoneyTransferEmulator\Models\Terminal', function () use ($term) {
			return $term;
		});

	}

	protected function mockPayment($type, $input, $dataResponse)
	{
		$payment = Mockery::mock('FintechFab\MoneyTransferEmulator\Models\Payment');

		$payment->shouldReceive('newInstance')->andReturn($payment);
		$payment->shouldReceive('hasGetMutator')->andReturn(false);
		$payment->shouldReceive('setAttribute')->andReturn($payment);
		$payment->shouldReceive('getAttribute')->withArgs(array('id'))->andReturn(1);
		$payment->shouldReceive('getAttribute')->withArgs(array('status'))->andReturn(@$dataResponse['status']);
		$payment->shouldReceive('getAttribute')->withArgs(array('type'))->andReturn($type);
		$payment->shouldReceive('getAttribute')->withArgs(array('term'))->andReturn(@$input['term']);
		$payment->shouldReceive('getAttribute')->withArgs(array('code'))->andReturn(@$dataResponse['code']);
		$payment->shouldReceive('getAttribute')->withArgs(array('city'))->andReturn(@$input['city']);
		$payment->shouldReceive('getAttribute')->withArgs(array('to'))->andReturn(@$input['to']);
		$payment->shouldReceive('getAttribute')->withArgs(array('amount'))->andReturn(@$dataResponse['amount']);
		$payment->shouldReceive('getAttribute')->withArgs(array('cur'))->andReturn(@$dataResponse['cur']);
		$payment->shouldReceive('getAttribute')->withArgs(array('name'))->andReturn(@$dataResponse['name']);
		$payment->shouldReceive('whereTerm')->andReturn($payment);
		$payment->shouldReceive('whereCode')->andReturn($payment);
		$payment->shouldReceive('whereType')->andReturn($payment);
		$payment->shouldReceive('whereIn')->andReturn($payment);
		$payment->shouldReceive('whereTo')->andReturn($payment);
		$payment->shouldReceive('whereAmount')->andReturn($payment);
		$payment->shouldReceive('whereCity')->andReturn($payment);
		$payment->shouldReceive('orderBy')->andReturn($payment);
		$payment->shouldReceive('first')->andReturn($payment);
		$payment->shouldReceive('saveByType')->andReturn();
		$payment->shouldReceive('toArray')->andReturn($dataResponse);
		$payment->shouldReceive('success')->andReturn(true);
		$payment->shouldReceive('save')->andReturn(true);
		$payment->shouldReceive('mask')->andReturn(null);
		$payment->shouldReceive('callbackEmail')->andReturn(null);

		// ioc
		$this->app->bind('FintechFab\MoneyTransferEmulator\Models\Payment', function () use ($payment) {
			return $payment;
		});

	}

	/**
	 * @param $id
	 *
	 * @return City
	 */
	protected function makeCity($id = 12)
	{
		return City::firstOrCreate(array(
			'id'      => $id,
			'name'    => 'Some City',
			'country' => 'RU',
		));
	}

	/**
	 * @param $from
	 * @param $to
	 * @param $value
	 *
	 * @return Fee
	 */
	protected function makeFee($from = 0.01, $to = 10.00, $value = '2.34')
	{
		return Fee::firstOrCreate(array(
			'amount_from' => $from,
			'amount_to'   => $to,
			'value'       => $value,
			'term_id'     => 123456,
			'city_id'     => 12,
			'cur'         => 'rub',
		));
	}


	protected function doPrepareCheckInput()
	{

		return array(
			'term'   => '123456',
			'amount' => '10.00',
			'cur'    => 'rub',
			'city'   => 12,
			'name'   => 'User Name',
			'time'   => self::time(),
			'email'  => uniqid() . '@example.com',
			'to'     => PaymentNumbers::getValidTo(),
			'from'   => PaymentNumbers::getValidFrom(),
		);

	}

} 