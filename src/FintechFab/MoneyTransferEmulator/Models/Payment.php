<?php

namespace FintechFab\MoneyTransferEmulator\Models;


use Eloquent;
use FintechFab\MoneyTransferEmulator\Components\Processor\Status;
use FintechFab\MoneyTransferEmulator\Components\Processor\Type;

/**
 * FintechFab\MoneyTransferEmulator\Models\Payment
 *
 * @property integer        $id
 * @property string         $cur
 * @property float          $amount
 * @property string         $city
 * @property string         $name
 * @property string         $time
 * @property string         $term
 * @property string         $to
 * @property string         $from
 * @property string         $type
 * @property string         $rc
 * @property string         $status
 * @property string         $code
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 *
 * @method static Payment  where($column, $operator, $value)
 * @method static Payment  whereTo($to)
 * @method static Payment  whereCity($city)
 * @method static Payment  whereAmount($amount)
 * @method static Payment  whereCode($code)
 * @method static Payment  whereTerm($term)
 * @method static Payment  whereType($type)
 * @method static Payment  whereStatus($status)
 * @method static Payment  orderBy($column, $direction)
 * @method static Payment  first()
 * @method static Payment  find($id)
 */
class Payment extends Eloquent
{

	public $connection = 'ff-mt-em';
	public $table = 'payments';
	public $fillable = array(
		'cur',
		'amount',
		'code',
		'city',
		'name',
		'time',
		'term',
		'to',
		'from',
		'type',
		'email',
	);


	public function cityName()
	{
		$city = City::find($this->city);
		if ($city) {
			return $city->name;
		}

		return null;
	}

	public function saveByType()
	{
		switch ($this->type) {

			case Type::CHECK:
			case Type::PAY:
			case Type::CANCEL:

				$this->save();
				break;

		}


	}

	public function getPossibleStatus()
	{

		if (
			$this->type == Type::PAY && $this->status == Status::PROCESSED
		) {
			return Status::SENT;
		}

		if (
			$this->type == Type::PAY && $this->status == Status::SENT
		) {
			return Status::TRANSFERED;
		}

		if (
			$this->type == Type::CANCEL && $this->status == Status::PROCESSED
		) {
			return Status::CANCELED;
		}

		return null;

	}

} 