<?php


namespace FintechFab\MoneyTransferEmulator\Models;


use Eloquent;

/**
 * @property integer    $id
 * @property string     $name
 * @property string     $country
 *
 * @method static City first()
 * @method static City find($id)
 */
class City extends Eloquent
{

	public $connection = 'ff-mt-em';
	public $table = 'cities';
	public $fillable = array(
		'id', 'name', 'country',
	);

}