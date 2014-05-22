<?php

namespace FintechFab\MoneyTransferEmulator\Components\Processor;


use App;
use FintechFab\MoneyTransferEmulator\Components\Helpers\Time;
use FintechFab\MoneyTransferEmulator\Models\City;
use FintechFab\MoneyTransferEmulator\Models\Fee;
use FintechFab\MoneyTransferEmulator\Models\Terminal;

class Processor
{

	/**
	 * @var Type
	 */
	private $type;

	/**
	 * @var Terminal
	 */
	private $term;

	/**
	 * @var \FintechFab\MoneyTransferEmulator\Components\Processor\Payment
	 */
	private $payment;

	/**
	 * @var Response
	 */
	private $response;

	/**
	 * @var City
	 */
	private $city;

	/**
	 * @var Fee
	 */
	private $fee;

	public function __construct(Type $type, Terminal $term)
	{
		$this->type = $type;
		$this->term = $term;
	}

	private function run()
	{

		$this->type->validate();
		$this->initPayment();
		$this->initTerm();

		$this->type->validateSign($this->term->secret);
		$this->type->validateTermEnabled($this->term->mode);

		$this->initCity();
		$this->initFee();

		$this->payment->doProcess();

	}

	private function initTerm()
	{
		$term = $this->term->newInstance();
		$this->term = $term->find($this->type->termId());

		if (!$this->term) {
			throw new ProcessorException(ProcessorException::INVALID_TERMINAL);
		}

	}

	private function initCity()
	{

		if (
			!$this->type->isFinanceRequest() &&
			!$this->type->isFeeRequest()
		) {
			return;
		}

		$inputData = $this->type->inputs();
		$cityId = $inputData['city'];

		$this->city = City::find($cityId);

		if (!$this->city) {
			throw new ProcessorException(ProcessorException::RC_NO_SUCH_CITY);
		}

	}

	private function initFee()
	{

		if (!$this->type->isFinanceRequest()) {
			return;
		}

		$inputData = $this->type->inputs();
		$amount = $inputData['amount'];
		$cur = $inputData['cur'];

		$this->findFee($amount, $cur);

		if (!$this->fee) {
			throw new ProcessorException(ProcessorException::RC_NO_SUCH_FEE);
		}

	}

	private function initPayment()
	{
		/**
		 * @var Payment $payment
		 */
		$payment = App::make('FintechFab\MoneyTransferEmulator\Components\Processor\Payment', array(
			$this->type->sid(),
			$this->type->inputs()
		));

		$this->payment = $payment;

	}

	/**
	 * @return Response|null
	 */
	public function response()
	{

		try {
			$this->run();
		} catch (ProcessorException $e) {
			return $this->exception($e);
		}

		switch ($this->type->sid()) {

			case Type::CITY:
				return $this->city();
				break;

			case Type::FEE:
				return $this->fee();
				break;

			case Type::CHECK:
				return $this->check();
				break;

			case Type::PAY:
				return $this->pay();
				break;

			case Type::CANCEL:
				return $this->cancel();
				break;

			case Type::STATUS:
				return $this->status();
				break;

		}

		return null;

	}

	/**
	 * @return Response
	 */
	private function city()
	{

		$cities = City::all(array('id', 'name', 'country'));
		$countries = array(
			'RU', 'UA', 'UK',
		);
		$data = array(
			'type'      => Type::CITY,
			'time'      => Time::ts(),
			'list'      => $cities->toArray(),
			'countries' => $countries,
		);

		$this->response = new Response($data, Type::CITY, null);

		return $this->response;
	}

	/**
	 * @return Response
	 */
	private function fee()
	{

		$inputData = $this->type->inputs();
		$amount = $inputData['amount'];
		$cur = $inputData['cur'];

		$this->findFee($amount, $cur);

		$data = array(
			'type'  => Type::FEE,
			'time'  => Time::ts(),
			'value' => $this->fee ? $this->fee->value : null,
		);

		$this->response = new Response($data, Type::CITY, null);

		return $this->response;
	}

	/**
	 * @return Response
	 */
	private function check()
	{
		$data = $this->getResponseData();
		$this->response = new Response($data, Type::CHECK, $this->term->secret);

		return $this->response;
	}

	/**
	 * @return Response
	 */
	private function cancel()
	{
		$data = $this->getResponseData();
		$this->response = new Response($data, Type::CANCEL, $this->term->secret);

		return $this->response;
	}

	/**
	 * @return Response
	 */
	private function pay()
	{

		$data = $this->getResponseData();
		$this->response = new Response($data, Type::PAY, $this->term->secret);

		return $this->response;

	}

	/**
	 * @return Response
	 */
	private function status()
	{

		$data = $this->getResponseData();
		$this->response = new Response($data, Type::STATUS, $this->term->secret);

		return $this->response;

	}

	/**
	 * @param ProcessorException $e
	 *
	 * @return Response
	 */
	private function exception(ProcessorException $e)
	{

		$data = array(
			'type'    => Type::ERROR,
			'code'    => $e->getCode(),
			'message' => $e->getMessage(),
			'time'    => Time::ts(),
		);
		$this->response = new Response($data, Type::ERROR, null);

		return $this->response;

	}

	/**
	 * @param ProcessorException $e
	 *
	 * @return Response
	 */
	public static function makeError(ProcessorException $e)
	{

		$data = array(
			'type'    => Type::ERROR,
			'code'    => $e->getCode(),
			'message' => $e->getMessage(),
			'time'    => Time::ts(),
		);
		$response = new Response($data, Type::ERROR, null);

		return $response;

	}

	public function item()
	{
		return $this->payment->item();
	}

	private function getResponseData()
	{
		$data = array(
			'term'   => $this->payment->item()->term,
			'type'   => $this->payment->item()->type,
			'code'   => $this->payment->item()->code,
			'amount' => $this->payment->item()->amount,
			'cur'    => $this->payment->item()->cur,
			'status' => $this->payment->item()->status,
			'time'   => Time::ts(),
		);

		Secure::sign($data, $this->type->sid(), $this->term->secret);

		return $data;

	}

	private function findFee($amount, $cur)
	{

		$this->fee = Fee::
			whereCityId(
				$this->city->id
			)
			->orWhere('city_id', '=', 0)
			->whereTermId($this->term->id)
			->orWhere('term_id', '=', 0)
			->whereCur($cur)->orWhere('cur', '=', '')
			->where('amount_from', '<=', $amount)
			->where('amount_to', '>=', $amount)
			->first();

	}

}