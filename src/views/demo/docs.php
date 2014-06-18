<?php

use FintechFab\MoneyTransferEmulator\Components\Processor\Input;
use FintechFab\MoneyTransferEmulator\Components\Processor\Response;
use FintechFab\MoneyTransferEmulator\Components\Processor\Type;

?>


<div class="row container">
	<div class="col-md-12">

		<div class="panel panel-primary">
			<div class="panel-heading">
				<h3 class="panel-title">Типы запросов</h3>
			</div>
			<div class="panel-body">
				<table class="table table-striped table-hover">

					<tr>
						<th>Тип запроса</th>
						<th>Комментарий</th>
						<th>Параметры</th>
					</tr>

					<?php foreach (Type::$typeNames as $key => $val) { ?>
						<tr>
							<td><?= $key ?></td>
							<td><?= $val ?></td>
							<td><?= implode(', ', Type::$fields[$key]) ?></td>
						</tr>
					<?php } ?>

				</table>
			</div>
		</div>

	</div>
</div>


<div class="row container">
	<div class="col-md-12">

		<div class="panel panel-primary">
			<div class="panel-heading">
				<h3 class="panel-title">Параметры ответа</h3>
			</div>
			<div class="panel-body">
				<?= implode(', ', Response::$responseFields) ?>
			</div>
		</div>

	</div>
</div>

<div class="row container">
	<div class="col-md-12">

		<div class="panel panel-primary">
			<div class="panel-heading">
				<h3 class="panel-title">Параметры ответа при ошибке</h3>
			</div>
			<div class="panel-body">
				type=error, code, message, time
			</div>
		</div>

	</div>
</div>

<div class="row container">
	<div class="col-md-12">

		<div class="panel panel-primary">
			<div class="panel-heading">
				<h3 class="panel-title">Описание параметров</h3>
			</div>
			<div class="panel-body">
				<table class="table table-striped table-hover">

					<tr>
						<th>Параметр</th>
						<th>Правило</th>
						<th>Описание</th>
					</tr>

					<?php foreach (Input::$rules as $key => $val) { ?>
						<tr>
							<td><?= $key ?></td>
							<td><?= $val ?></td>
							<td><?= Input::$paramNames[$key] ?></td>
						</tr>
					<?php } ?>

				</table>
			</div>
		</div>

	</div>
</div>

<div class="row container">
	<div class="col-md-12">

		<div class="panel panel-primary">
			<div class="panel-heading">
				<h3 class="panel-title">Формирование/проверка подписи</h3>
			</div>
			<div class="panel-body">
				<pre>
					<?php highlight_string(
						"<?php\n" .
						"/**\n" .
						" * @var string \$type   тип запроса\n" .
						" * @var array  \$params параметры запроса\n" .
						" * @var string \$secret ключ продавца\n" .
						" */\n" .
						"ksort(\$params);\n" .
						"\$str4sign = implode('|', \$params);\n" .
						"\$sign = md5(\$str4sign . \$type . \$secret);\n" .
						"?>"
					) ?>
				</pre>
			</div>
		</div>

	</div>
</div>
