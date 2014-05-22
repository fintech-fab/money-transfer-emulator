<?php

use FintechFab\MoneyTransferEmulator\Components\Helpers\Time;
use FintechFab\MoneyTransferEmulator\Components\Helpers\Views;
use FintechFab\MoneyTransferEmulator\Components\Processor\PaymentNumbers;
use FintechFab\MoneyTransferEmulator\Components\Processor\Type;
use FintechFab\MoneyTransferEmulator\Models\Terminal;

/**
 * @var array    $endpointParams
 * @var Terminal $terminal
 */

?>
<div class="row container">
	<div class="col-md-6">
		<div class="panel panel-primary">
			<div class="panel-heading">
				<h3 class="panel-title">Ваш терминал</h3>
			</div>
			<div class="panel-body">

				<table class="table table-striped table-hover">
					<tr>
						<td>Id</td>
						<td><?= $terminal->id ?></td>
					</tr>
					<tr>
						<td>Ключ secret</td>
						<td><?= $terminal->secret ?></td>
					</tr>
					<tr>
						<td>Режим</td>
						<td><?= $terminal->modeName() ?></td>
					</tr>
				</table>

			</div>
		</div>
	</div>
</div>

<div class="row container">
	<div class="col-md-12">
		<div class="panel panel-primary">
			<div class="panel-heading">
				<h3 class="panel-title">[<?= Type::CITY ?>] Список городов</h3>
			</div>
			<div class="panel-body">

				<div class="col-md-4 post-city">
					<div class="form-group">
						<?php
						Views::label('time');
						Views::text('time', Time::ts());
						?>
					</div>

					<div class="form-group">
						<?php
						Views::label('term');
						Views::text('term', $terminal->id, array('size' => 10, 'style' => 'width: 100px; display: inline; margin-left: 10px;'));
						?>
					</div>

					<div class="form-group">
						<?php
						Views::label('secret');
						Views::text('secret', $terminal->secret);
						?>
					</div>
				</div>

				<div class="col-md-8">
					<button class="btn btn-sm post-city">выполнить запрос</button>
					<div style="padding: 5px 0;">
						<pre class="post-city" style="font-size: .8em;"></pre>
					</div>
				</div>


			</div>
		</div>
	</div>
</div>

<div class="row container">
	<div class="col-md-12">
		<div class="panel panel-primary">
			<div class="panel-heading">
				<h3 class="panel-title">[<?= Type::FEE ?>] Расчет комиссии</h3>
			</div>
			<div class="panel-body">

				<div class="col-md-4 post-fee">

					<div class="form-group">
						<?php
						Views::label('city');
						Views::text('city', 1);
						?>
					</div>

					<div class="form-group">
						<?php
						Views::label('amount', 'Сумма/валюта');
						Views::text('amount', '123.45', array('size' => 10, 'style' => 'width: 100px; display: inline; margin-left: 10px;'));
						Views::text('cur', 'RUB', array('size' => 3, 'style' => 'width: 50px; display: inline; margin-left: 10px;'));
						?>
					</div>

					<div class="form-group">
						<?php
						Views::label('time');
						Views::text('time', Time::ts());
						?>
					</div>

					<div class="form-group">
						<?php
						Views::label('term');
						Views::text('term', $terminal->id, array('size' => 10, 'style' => 'width: 100px; display: inline; margin-left: 10px;'));
						?>
					</div>

					<div class="form-group">
						<?php
						Views::label('secret');
						Views::text('secret', $terminal->secret);
						?>
					</div>
				</div>

				<div class="col-md-8">
					<button class="btn btn-sm post-fee">выполнить запрос</button>
					<div style="padding: 5px 0;">
						<pre class="post-fee" style="font-size: .8em;"></pre>
					</div>
				</div>


			</div>
		</div>
	</div>
</div>

<div class="row container">
	<div class="col-md-12">
		<div class="panel panel-primary">
			<div class="panel-heading">
				<h3 class="panel-title">[<?= Type::CHECK ?>] Проверка возможности платежа</h3>
			</div>
			<div class="panel-body">

				<div class="col-md-4 post-check">

					<div class="form-group">
						<?php
						Views::label('from');
						Views::text('from', $from = PaymentNumbers::getValidFrom());
						?>
					</div>

					<div class="form-group">
						<?php
						Views::label('to');
						Views::text('to', $to = PaymentNumbers::getValidTo());
						?>
					</div>

					<div class="form-group">
						<?php
						Views::label('city');
						Views::text('city', 1);
						?>
					</div>

					<div class="form-group">
						<?php
						Views::label('amount', 'Сумма/валюта');
						Views::text('amount', '123.45', array('size' => 10, 'style' => 'width: 100px; display: inline; margin-left: 10px;'));
						Views::text('cur', 'RUB', array('size' => 3, 'style' => 'width: 50px; display: inline; margin-left: 10px;'));
						?>
					</div>

					<div class="form-group">
						<?php
						Views::label('name');
						Views::text('name', 'Fine order');
						?>
					</div>

					<div class="form-group">
						<?php
						Views::label('email');
						Views::text('email', 'bank@example.com');
						?>
					</div>

					<div class="form-group">
						<?php
						Views::label('time');
						Views::text('time', Time::ts());
						?>
					</div>

					<div class="form-group">
						<?php
						Views::label('term');
						Views::text('term', $terminal->id, array('size' => 10, 'style' => 'width: 100px; display: inline; margin-left: 10px;'));
						?>
					</div>

					<div class="form-group">
						<?php
						Views::label('secret');
						Views::text('secret', $terminal->secret);
						?>
					</div>

				</div>

				<div class="col-md-8">
					<button class="btn btn-sm post-check">выполнить запрос</button>
					<div style="padding: 5px 0;">
						<pre class="post-check" style="font-size: .8em;"></pre>
					</div>
				</div>


			</div>
		</div>
	</div>
</div>

<div class="row container">
	<div class="col-md-12">
		<div class="panel panel-primary">
			<div class="panel-heading">
				<h3 class="panel-title">[<?= Type::PAY ?>] Регистрация платежа</h3>
			</div>
			<div class="panel-body">

				<div class="col-md-4 post-pay">

					<div class="form-group">
						<?php
						Views::label('from');
						Views::text('from', $from);
						?>
					</div>

					<div class="form-group">
						<?php
						Views::label('to');
						Views::text('to', $to);
						?>
					</div>

					<div class="form-group">
						<?php
						Views::label('city');
						Views::text('city', 1);
						?>
					</div>

					<div class="form-group">
						<?php
						Views::label('amount', 'Сумма/валюта');
						Views::text('amount', '123.45', array('size' => 10, 'style' => 'width: 100px; display: inline; margin-left: 10px;'));
						Views::text('cur', 'RUB', array('size' => 3, 'style' => 'width: 50px; display: inline; margin-left: 10px;'));
						?>
					</div>

					<div class="form-group">
						<?php
						Views::label('name');
						Views::text('name', 'Fine order');
						?>
					</div>

					<div class="form-group">
						<?php
						Views::label('email');
						Views::text('email', 'bank@example.com');
						?>
					</div>

					<div class="form-group">
						<?php
						Views::label('time');
						Views::text('time', Time::ts());
						?>
					</div>

					<div class="form-group">
						<?php
						Views::label('term');
						Views::text('term', $terminal->id, array('size' => 10, 'style' => 'width: 100px; display: inline; margin-left: 10px;'));
						?>
					</div>

					<div class="form-group">
						<?php
						Views::label('secret');
						Views::text('secret', $terminal->secret);
						?>
					</div>

				</div>

				<div class="col-md-8">
					<button class="btn btn-sm post-pay">выполнить запрос</button>
					<div style="padding: 5px 0;">
						<pre class="post-pay" style="font-size: .8em;"></pre>
					</div>
				</div>


			</div>
		</div>
	</div>
</div>

<div class="row container">
	<div class="col-md-12">
		<div class="panel panel-primary">
			<div class="panel-heading">
				<h3 class="panel-title">[<?= Type::STATUS ?>] Статус платежа</h3>
			</div>
			<div class="panel-body">

				<div class="col-md-4 post-status">

					<div class="form-group">
						<?php
						Views::label('to');
						Views::text('to', $to);
						?>
					</div>

					<div class="form-group">
						<?php
						Views::label('code');
						Views::text('code', '');
						?>
					</div>

					<div class="form-group">
						<?php
						Views::label('time');
						Views::text('time', Time::ts());
						?>
					</div>

					<div class="form-group">
						<?php
						Views::label('term');
						Views::text('term', $terminal->id, array('size' => 10, 'style' => 'width: 100px; display: inline; margin-left: 10px;'));
						?>
					</div>

					<div class="form-group">
						<?php
						Views::label('secret');
						Views::text('secret', $terminal->secret);
						?>
					</div>

				</div>

				<div class="col-md-8">
					<button class="btn btn-sm post-status">выполнить запрос</button>
					<div style="padding: 5px 0;">
						<pre class="post-status" style="font-size: .8em;"></pre>
					</div>
				</div>


			</div>
		</div>
	</div>
</div>

<div class="row container">
	<div class="col-md-12">
		<div class="panel panel-primary">
			<div class="panel-heading">
				<h3 class="panel-title">[<?= Type::CANCEL ?>] Отмена платежа</h3>
			</div>
			<div class="panel-body">

				<div class="col-md-4 post-cancel">

					<div class="form-group">
						<?php
						Views::label('to');
						Views::text('to', $to);
						?>
					</div>

					<div class="form-group">
						<?php
						Views::label('code');
						Views::text('code', '');
						?>
					</div>

					<div class="form-group">
						<?php
						Views::label('time');
						Views::text('time', Time::ts());
						?>
					</div>

					<div class="form-group">
						<?php
						Views::label('term');
						Views::text('term', $terminal->id, array('size' => 10, 'style' => 'width: 100px; display: inline; margin-left: 10px;'));
						?>
					</div>

					<div class="form-group">
						<?php
						Views::label('secret');
						Views::text('secret', $terminal->secret);
						?>
					</div>

				</div>

				<div class="col-md-8">
					<button class="btn btn-sm post-cancel">выполнить запрос</button>
					<div style="padding: 5px 0;">
						<pre class="post-cancel" style="font-size: .8em;"></pre>
					</div>
				</div>


			</div>
		</div>
	</div>
</div>
