<?php


namespace FintechFab\MoneyTransferEmulator\Models;


use Eloquent;

/**
 * @property integer    $id
 * @property string     $secret
 * @property string     $email
 * @property integer    $mode
 * @property integer    $user_id
 * @property integer    $url
 *
 * @method static Terminal whereUserId($user_id)
 * @method static Terminal first()
 * @method static Terminal find()
 */
class Terminal extends Eloquent
{

	const C_STATE_DISABLED = 0;
	const C_STATE_ENABLED = 1;

	public static $stateNames = array(
		self::C_STATE_DISABLED => 'Выключен',
		self::C_STATE_ENABLED  => 'Включен',
	);

	public $connection = 'ff-mt-em';
	public $table = 'terminals';

	public function modeName()
	{
		return self::$stateNames[$this->mode];
	}


}