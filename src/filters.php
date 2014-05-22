<?php


use FintechFab\MoneyTransferEmulator\Models\Terminal;

Route::filter('ff-mt-em-term', function () {

	$terminal = Terminal::whereUserId(Config::get('ff-mt-em::user_id'))->first();
	if (!$terminal) {
		return Redirect::route('ff-mt-em-term');
	}

	return null;

});

Route::filter('ff-mt-em-auth', function () {

	$user_id = Config::get('ff-mt-em::user_id');
	$user_id = (int)$user_id;
	if ($user_id <= 0) {
		return Redirect::to(URL::route('ff-mt-em-error', array(), false))
			->with('errorMessage', 'Please configure Auth Identifier into your local config!');
	}

	return null;

});
