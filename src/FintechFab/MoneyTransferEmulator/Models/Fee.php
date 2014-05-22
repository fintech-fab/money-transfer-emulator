<?php


namespace FintechFab\MoneyTransferEmulator\Models;


use Eloquent;

/**
 * @property integer    $id
 * @property string     $term_id
 * @property string     $city_id
 * @property string     $cur
 * @property string     $amount_from
 * @property string     $amount_to
 * @property string     $value
 *
 * @method static Fee first()
 * @method static Fee find($id)
 * @method static Fee whereCur($cur)
 * @method static Fee whereCityId($cityId)
 * @method static Fee whereTermId($termId)
 */
class Fee extends Eloquent
{

	public $connection = 'ff-mt-em';
	public $table = 'fee';
	public $fillable = array(
		'term_id', 'city_id', 'cur', 'amount_from', 'amount_to', 'value'
	);

}