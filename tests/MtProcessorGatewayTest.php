<?php

use FintechFab\MoneyTransferEmulator\Components\Helpers\Time;
use FintechFab\MoneyTransferEmulator\Components\Processor\Secure;
use FintechFab\MoneyTransferEmulator\Components\Processor\Status;
use FintechFab\MoneyTransferEmulator\Components\Processor\Type;
use FintechFab\MoneyTransferEmulator\Models\Payment;
use FintechFab\MoneyTransferEmulator\Models\Terminal;

class MtProcessorGatewayTest extends MoneyTransferEmulatorTestCase
{

	/**
	 * @var Terminal
	 */
	private $term;

	public function setUp()
	{

		parent::setUp();

		Terminal::truncate();
		Payment::truncate();
		$this->term = new Terminal;
		$this->term->id = 123;
		$this->term->secret = 'secret';
		$this->term->save();

		$this->makeCity();
		$this->makeFee();
	}


	public function testCorrect()
	{
		$input = $this->doPrepareCheckInput();

		// check
		$dataCheck = $this->callGateway(Type::CHECK, $input);
		$this->assertEquals(Type::CHECK, $dataCheck->type, print_r($dataCheck, true));

		// double
		$dataCheck = $this->callGateway(Type::CHECK, $input);
		$this->assertEquals(Type::CHECK, $dataCheck->type, print_r($dataCheck, true));

		// pay
		$dataPay = $this->callGateway(Type::PAY, $input);
		$this->assertEquals(Type::PAY, $dataPay->type, print_r($dataPay, true));
		$this->assertNotEmpty($dataPay->code, print_r($dataPay, true));

		// status
		$inputStatus = array(
			'term' => $input['term'],
			'to'   => $input['to'],
			'code' => $dataPay->code,
			'time' => Time::ts(),
		);
		$dataStatus = $this->callGateway(Type::STATUS, $inputStatus);
		$this->assertEquals(Type::STATUS, $dataStatus->type, print_r($dataStatus, true));
		$this->assertEquals(Status::PROCESSED, $dataStatus->status, print_r($dataStatus, true));

		// cancel
		$dataCancel = $this->callGateway(Type::CANCEL, $inputStatus);
		$this->assertEquals(Type::CANCEL, $dataCancel->type, print_r($dataCancel, true));
		$this->assertEquals(Status::PROCESSED, $dataCancel->status, print_r($dataCancel, true));

		// status
		$dataStatus = $this->callGateway(Type::STATUS, $inputStatus);
		$this->assertEquals(Type::STATUS, $dataStatus->type, print_r($dataStatus, true));
		$this->assertEquals(Status::CANCELED, $dataStatus->status, print_r($dataStatus, true));


	}

	public function testFail()
	{
		$input = $this->doPrepareCheckInput();
		$input['amount'] = '0.00';

		// amount
		$dataCheck = $this->callGateway(Type::CHECK, $input);
		$this->assertEquals(Type::ERROR, $dataCheck->type, print_r($dataCheck, true));
		$this->assertContains('amount', $dataCheck->message, print_r($dataCheck, true));
		$input['amount'] = '10.00';

		// sign
		$input['sign'] = 'fail';
		$dataCheck = $this->callGateway(Type::CHECK, $input);
		$this->assertEquals(Type::ERROR, $dataCheck->type, print_r($dataCheck, true));
		$this->assertContains('sign', $dataCheck->message, print_r($dataCheck, true));
		unset($input['sign']);

		// time
		$input['time'] = 123;
		$dataCheck = $this->callGateway(Type::CHECK, $input);
		$this->assertEquals(Type::ERROR, $dataCheck->type, print_r($dataCheck, true));
		$this->assertContains('time', $dataCheck->message, print_r($dataCheck, true));
		$input['time'] = Time::ts();


	}

	private function callGateway($type, $input)
	{
		if (empty($input['sign'])) {
			Secure::sign($input, $type, $this->term->secret);
		}
		$response = $this->call(
			'POST',
			URL::route('ff-mt-em-gateway'),
			array(
				'type'  => $type,
				'input' => $input,
			)
		);
		$json = $response->getContent();
		$data = json_decode($json);

		return $data;
	}

	protected function doPrepareCheckInput()
	{
		$input = parent::doPrepareCheckInput();
		$input['term'] = 123;

		return $input;
	}

}