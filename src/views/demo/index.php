<?php

use FintechFab\MoneyTransferEmulator\Components\Helpers\Views;

?>
<div class="row container">
	<div class="col-md-7">

		<h2>About</h2>

		<p>Это имитация сервиса адресного перевода денег<br>(по сути похоже на WesternUnion) с процессами:</p>

		<ul>
			<li>Список городов<br> &mdash; <i>
					<small>Получить список городов, куда можно отправить деньги</small>
				</i></li>
			<li>Расчет комиссии<br> &mdash; <i>
					<small>Запрос на сумму комиссии для перевода N единиц валюты в определенный город</small>
				</i></li>
			<li>Проверка возможности перевода<br> &mdash; <i>
					<small>Запрос с реквизитами перевода, ответ "можно" или "нельзя"</small>
				</i></li>
			<li>Регистрация перевода<br> &mdash; <i>
					<small>Запрос на перевод</small>
				</i></li>
			<li>Проверка статуса<br> &mdash; <i>
					<small>Запрос на статус</small>
				</i></li>
			<li>Отмена платежа<br> &mdash; <i>
					<small>Запрос на отмену, если платеж еще не был исполнен</small>
				</i></li>
		</ul>

	</div>

	<div class="col-md-5">

		<h2>Profit</h2>

		<p>Пользуйтесь шлюзом, чтобы отладить/протестировать процесс подключения к подобному "настоящему" сервису в вашем проекте.</p>

		<p>Чтобы начать, <a href="<?= Views::link2Sign() ?>">авторизуйтесь здесь</a>, потом
			<a href="<?= URL::route('ff-mt-em-term') ?>">здесь</a> вам будет сгенерирован банковский терминал с
			id-шником и ключом.</p>

		<p>Там же находятся формы для отладки шлюза.</p>

		<p>Для подключения шлюза к вашему проекту используйте <a href="<?= URL::route('ff-mt-em-sdk') ?>">PHP SDK</a>.
		</p>

		<h2>Tags</h2>

		<p>
			<a href="<?= URL::route('ff-mt-em-docs') ?>">Справочник</a>,
			<a href="<?= URL::route('ff-mt-em-sdk') ?>">PHP SDK</a>,
			<a href="https://github.com/fintech-fab/money-transfer-emulator">GitHub</a>, <a href="http://laravel.com">Laravel</a>
		</p>

	</div>

</div>