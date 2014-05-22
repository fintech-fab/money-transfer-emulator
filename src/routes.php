<?php


Route::group(

	array(
		'prefix'    => 'mt/emulator/demo',
		'namespace' => 'FintechFab\MoneyTransferEmulator\Controllers'
	),

	function () {

		Route::get('', array(
			'as'   => 'ff-mt-em-demo',
			'uses' => 'DemoController@index',
		));
		Route::get('error', array(
			'as'   => 'ff-mt-em-error',
			'uses' => 'DemoController@error',
		));

		Route::get('term', array(
			'before' => 'ff-mt-em-auth',
			'as'     => 'ff-mt-em-term',
			'uses'   => 'DemoController@term'
		));

		Route::post('gate', array(
			'before' => 'ff-mt-em-auth',
			'as'     => 'ff-mt-em-gate',
			'uses'   => 'DemoController@gate'
		));

		Route::post('gateway', array(
			'as'   => 'ff-mt-em-gateway',
			'uses' => 'DemoController@gateway'
		));

		Route::post('sign', array(
			'before' => 'ff-mt-em-auth|ff-mt-em-term',
			'as'     => 'ff-mt-em-sign',
			'uses'   => 'DemoController@sign'
		));

		Route::post('callback', array(
			'uses' => 'DemoController@callback'
		));

		Route::get('payments', array(
			'before' => 'ff-mt-em-auth|ff-mt-em-term',
			'as'     => 'ff-mt-em-payments',
			'uses'   => 'DemoController@payments',
		));

		Route::post('approve', array(
			'before' => 'ff-mt-em-auth|ff-mt-em-term',
			'as'     => 'ff-mt-em-approve',
			'uses'   => 'DemoController@approve'
		));

	}

);
