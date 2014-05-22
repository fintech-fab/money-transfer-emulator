<?php

use FintechFab\MoneyTransferEmulator\Models\City;
use FintechFab\MoneyTransferEmulator\Models\Fee;
use FintechFab\MoneyTransferEmulator\Models\Terminal;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class FfMtEmPayment extends Migration
{

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{

		Schema::connection('ff-mt-em')->dropIfExists('payments');
		Schema::connection('ff-mt-em')->dropIfExists('terminals');
		Schema::connection('ff-mt-em')->dropIfExists('cities');
		Schema::connection('ff-mt-em')->dropIfExists('fee');

		Schema::connection('ff-mt-em')->create('payments', function (Blueprint $table) {
			$table->increments('id');
			$table->string('cur', 3)->default('');
			$table->double('amount', 8, 2)->default(0);
			$table->string('code')->default('');
			$table->integer('city')->default(0);
			$table->string('name', 50)->default('');
			$table->string('email', 50)->default('');
			$table->timestamp('time')->default('0000-00-00 00:00:00');
			$table->string('term', 11)->default('');
			$table->string('to', 32)->default('');
			$table->string('from', 32)->default('');
			$table->string('type', 10)->default('');
			$table->string('status', 10)->default('');
			$table->timestamps();
		});

		Schema::connection('ff-mt-em')->create('terminals', function (Blueprint $table) {
			$table->increments('id');
			$table->integer('user_id')->default(0);
			$table->string('secret')->default('');
			$table->tinyInteger('mode')->default(Terminal::C_STATE_ENABLED);
			$table->timestamps();
		});

		Schema::connection('ff-mt-em')->create('cities', function (Blueprint $table) {
			$table->increments('id');
			$table->string('name')->default('');
			$table->string('country')->default('');
			$table->timestamps();
		});

		Schema::connection('ff-mt-em')->create('fee', function (Blueprint $table) {
			$table->increments('id');
			$table->integer('term_id')->default(0);
			$table->integer('city_id')->default(0);
			$table->double('amount_from', 8, 2)->default(0.00);
			$table->double('amount_to', 8, 2)->default(0.00);
			$table->double('value', 6, 2)->default(0.00);
			$table->string('cur', 3)->default('');
			$table->timestamps();
		});

		City::create(array(
			'name'    => 'London',
			'country' => 'UK',
		));

		City::create(array(
			'name'    => 'Kiev',
			'country' => 'UA',
		));

		City::create(array(
			'name'    => 'Moscow',
			'country' => 'RU',
		));


		Fee::create(array(
			'amount_from' => '0.01',
			'amount_to'   => '1000.00',
			'value'       => '200.00',
		));

		Fee::create(array(
			'amount_from' => '1000.01',
			'amount_to'   => '10000.00',
			'value'       => '250.00',
		));

		Fee::create(array(
			'amount_from' => '10000.01',
			'amount_to'   => '100000.00',
			'value'       => '500.00',
		));

	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::connection('ff-mt-em')->dropIfExists('payments');
		Schema::connection('ff-mt-em')->dropIfExists('terminals');
		Schema::connection('ff-mt-em')->dropIfExists('cities');
		Schema::connection('ff-mt-em')->dropIfExists('fee');
	}

}
