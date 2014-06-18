<?php

use FintechFab\MoneyTransferEmulator\Components\Helpers\Views;

?>

<div class="row container">
	<div class="col-md-12">

		<h3>Установка</h3>

		<p>
			Последняя версия SDK для шлюза находится на
			<a href="https://github.com/fintech-fab/money-transfer-emulator" target="_blank">GitHub</a> или
			устанавливается через
			<a href="https://packagist.org/packages/fintech-fab/money-transfer-emulator" target="_blank">Composer</a>.
		</p>

		<p>
			Чтобы воспользоваться нашим шлюзом, необходимо <a href="<?= Views::link2Sign() ?>">авторизоваться</a>, а
			затем <a href="<?= URL::route('ff-mt-em-term') ?>">получить терминал</a> (и тогда все платежные операции
			будут связаны с вашим аккаунтом на нашем сайте). Либо установите шлюз на свой сервер (вам понадобится
			Laravel, <a href="https://github.com/fintech-fab/money-transfer-emulator" target="_blank">GitHub</a> и
			Composer). </p>

	</div>
</div>

