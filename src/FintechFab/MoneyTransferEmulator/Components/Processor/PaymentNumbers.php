<?php

namespace FintechFab\MoneyTransferEmulator\Components\Processor;


class PaymentNumbers
{


	public static function getValidTo()
	{
		$num = mt_rand(0, 9999990);
		$num = str_repeat('0', 7 - strlen($num)) . $num;

		return '38068' . $num;
	}

	public static function getValidFrom()
	{
		$num = mt_rand(0, 9999990);
		$num = str_repeat('0', 7 - strlen($num)) . $num;

		return '7910' . $num;
	}

}