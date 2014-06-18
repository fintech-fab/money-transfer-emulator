<?php

namespace FintechFab\MoneyTransferEmulator\Controllers;


use App;
use Config;
use Controller;
use FintechFab\MoneyTransferEmulator\Components\Processor\Processor;
use FintechFab\MoneyTransferEmulator\Components\Processor\ProcessorException;
use FintechFab\MoneyTransferEmulator\Components\Processor\Secure;
use FintechFab\MoneyTransferEmulator\Components\Processor\Type;
use FintechFab\MoneyTransferEmulator\Models\Payment;
use FintechFab\MoneyTransferEmulator\Models\Terminal;
use Input;
use Log;
use Request;
use View;

class DemoController extends Controller
{


	/**
	 * main info page
	 */
	public function index()
	{
		$this->make('index');
	}

	public function docs()
	{
		$this->make('docs');
	}

	public function sdk()
	{
		$this->make('sdk');
	}

	/**
	 * Terminal info
	 * auto-create new term if not exists
	 */
	public function term()
	{
		$terminal = Terminal::whereUserId($this->userId())->first();
		if (!$terminal) {
			$terminal = new Terminal;
			$terminal->user_id = $this->userId();
			$terminal->secret = md5($terminal->user_id . time() . uniqid());
			$terminal->mode = Terminal::C_STATE_ENABLED;
			$terminal->save();
		}

		$this->make('term', compact('terminal'));
	}

	/**
	 * error page
	 */
	public function error()
	{
		$this->make('error');
	}

	/**
	 * Create signature for payment form
	 */
	public function sign()
	{
		$type = Input::get('type');
		$input = Input::get('input');
		$termId = $input['term'];
		$term = Terminal::find($termId);

		$input = Type::clearInput($type, $input);
		Secure::sign($input, $type, $term->secret);

		return $input['sign'];

	}

	/**
	 * Pull payment callbacks
	 */
	public function callback()
	{
		$input = $this->getVerifiedInput('callback', Input::get('type'), Input::all());
		if ($input) {
			// your business processing
		}

	}

	/**
	 * Payments log
	 */
	public function payments()
	{
		$terminal = Terminal::whereUserId($this->userId())->first();
		$payments = Payment::orderBy('id', 'desc')->whereTerm($terminal->id)->paginate(50);

		$this->make('payments', compact('payments'));
	}

	/**
	 * Approve one payment
	 */
	public function approve()
	{

		$id = Input::get('id');
		$terminal = Terminal::whereUserId($this->userId())->first();
		$payment = Payment::find($id);
		if ($payment->term == $terminal->id) {

			$status = $payment->getPossibleStatus();

			if ($status) {
				$payment->status = $status;
				$payment->save();

				if ($payment->getPossibleStatus()) {
					return array(
						'next'  => true,
						'value' => 'Move to [' . $payment->getPossibleStatus() . ']',
						'status' => $payment->status,
					);
				}

			}

		}

		return array(
			'next' => false,
			'status' => $payment? $payment->status: null,
		);

	}

	/**
	 * Simple debug gate
	 * Current session only
	 */
	public function gate()
	{
		/**
		 * @var Processor $processor
		 */
		try {


			$type = Input::get('type');
			$input = Input::get('input');
			$secret = $input['secret'];
			unset($input['secret']);

			Secure::sign($input, $type, $secret);

			$input = $this->getVerifiedInput('gateway', $type, $input);

			$processor = $this->makeProcessor($type, $input);


			// debug response

			$responseData = null;
			$paymentData = null;

			$response = $processor->response();
			$responseData = $response->data();
			$paymentData = $response->error() ? null : $processor->item()->toArray();

			$return = array(
				'response' => $responseData,
				'payment'  => $paymentData,
			);

		} catch (ProcessorException $e) {
			$return = array(
				'response' => Processor::makeError($e)->data(),
				'payment'  => null,
			);
		}

		$return = json_encode($return, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);

		return $return;

	}

	/**
	 * Production gate
	 * Public access
	 */
	public function gateway()
	{
		/**
		 * @var Processor $processor
		 */

		try {

			$type = Input::get('type');
			$input = $this->getVerifiedInput('gateway', $type, Input::get('input'));

			$term = Terminal::find($input['term']);
			Secure::sign($input, $type, $term->secret);

			$processor = $this->makeProcessor($type, $input);

			$response = $processor->response()->data();

		} catch (ProcessorException $e) {
			$response = Processor::makeError($e)->data();
		}

		$return = json_encode($response);

		return $return;

	}

	/**
	 * Current user id, terminal owner
	 *
	 * @return mixed
	 */
	protected function userId()
	{
		return Config::get('ff-mt-em::user_id');
	}

	/**
	 *
	 * Check, clear and verify input params
	 *
	 * @param $action
	 * @param $type
	 * @param $input
	 *
	 * @throws ProcessorException
	 * @return array
	 */
	private function getVerifiedInput($action, $type, $input)
	{

		$input = Type::clearInput($type, $input);
		$baseInput = $input;
		$termId = $input['term'];
		$term = Terminal::find($termId);
		$sign = $input['sign'];

		Secure::sign($input, $type, $term->secret);

		$isCorrect = ($sign === $input['sign']);

		if (!$isCorrect) {

			Log::warning($action . 'pull', array(
				'message' => 'Invalid signature',
				'sign'    => $input['sign'],
				'input'   => Input::all(),
			));

			throw new ProcessorException(ProcessorException::INVALID_SIGN);

		}

		Log::info($action . 'pull', array(
			'message' => 'Correct signature',
			'input'   => Input::all(),
		));

		return $baseInput;

	}

	/**
	 * @param $type
	 * @param $input
	 *
	 * @return Processor
	 */
	private function makeProcessor($type, $input)
	{

		$opType = new Type($type, $input);

		return App::make(
			'FintechFab\MoneyTransferEmulator\Components\Processor\Processor',
			array($opType)
		);

	}


	protected function setupLayout()
	{
		$this->layout = View::make('ff-mt-em::layouts.default');
	}

	protected function make($sTemplate, $aParams = array())
	{
		if (Request::ajax()) {
			return $this->makePartial($sTemplate, $aParams);
		} else {
			return $this->layout->nest('content', 'ff-mt-em::demo.' . $sTemplate, $aParams);
		}
	}

	protected function makePartial($sTemplate, $aParams = array())
	{
		return View::make('ff-mt-em::demo.' . $sTemplate, $aParams);
	}

} 