<?php

namespace FintechFab\MoneyTransferEmulator\Components\Processor;


class Secure
{

	public static function sign(&$input, $type, $secret)
	{
		if (empty($secret)) {
			return;
		}

		if (!empty($input['sign'])) {
			unset($input['sign']);
		}

		$sortInput = $input;
		ksort($sortInput);
		$str4sign = implode('|', $sortInput);
		$input['sign'] = md5($str4sign . $type . $secret);

	}

}