<?php


?>
<script type="application/javascript">

	$(document).ready(function () {
		setActiveUrl();
		postCityButton();
		postCheckButton();
		postPayButton();
		postFeeButton();
		postApproveButton();
		postStatusButton();
		postCancelButton();
	});


	function setActiveUrl() {
		var href = '<?= URL::current() ?>';
		var $links = $('.nav.navbar-nav');
		var $link = $links.find('a[href="' + href + '"]');
		$link.parent().addClass('active');
	}

	function postCityButton() {

		var $div = $('div.post-city');
		var $button = $('button.post-city');
		var $pre = $('pre.post-city');
		var $fields = $div.find('.form-control');
		var url = '<?= URL::route('ff-mt-em-gate') ?>';

		$button.off('click');
		$button.on('click', function () {
			$button.button('loading');
			var json = {type: 'city'};
			json = jsonFromInputs($fields, json);
			$.post(url, json, function (data) {
				$pre.html(data);
				$button.button('reset');
			});
		});

	}

	function postFeeButton() {

		var $div = $('div.post-fee');
		var $button = $('button.post-fee');
		var $pre = $('pre.post-fee');
		var $fields = $div.find('.form-control');
		var url = '<?= URL::route('ff-mt-em-gate') ?>';

		$button.off('click');
		$button.on('click', function () {
			$button.button('loading');
			var json = {type: 'fee'};
			json = jsonFromInputs($fields, json);
			$.post(url, json, function (data) {
				$pre.html(data);
				$button.button('reset');
			});
		});

	}

	function postCheckButton() {

		var $div = $('div.post-check');
		var $button = $('button.post-check');
		var $pre = $('pre.post-check');
		var $fields = $div.find('.form-control');
		var url = '<?= URL::route('ff-mt-em-gate') ?>';

		$button.off('click');
		$button.on('click', function () {
			$button.button('loading');
			var json = {type: 'check'};
			json = jsonFromInputs($fields, json);
			$.post(url, json, function (data) {
				$pre.html(data);
				$button.button('reset');
			});
		});

	}

	function postPayButton() {

		var $div = $('div.post-pay');
		var $button = $('button.post-pay');
		var $pre = $('pre.post-pay');
		var $fields = $div.find('.form-control');
		var url = '<?= URL::route('ff-mt-em-gate') ?>';

		$button.off('click');
		$button.on('click', function () {
			$button.button('loading');
			var json = {type: 'pay'};
			json = jsonFromInputs($fields, json);
			$.post(url, json, function (data) {
				$pre.html(data);
				$button.button('reset');
			});
		});

	}

	function postStatusButton() {

		var $div = $('div.post-status');
		var $button = $('button.post-status');
		var $pre = $('pre.post-status');
		var $fields = $div.find('.form-control');
		var url = '<?= URL::route('ff-mt-em-gate') ?>';

		$button.off('click');
		$button.on('click', function () {
			$button.button('loading');
			var json = {type: 'status'};
			json = jsonFromInputs($fields, json);
			$.post(url, json, function (data) {
				$pre.html(data);
				$button.button('reset');
			});
		});

	}

	function postCancelButton() {

		var $div = $('div.post-cancel');
		var $button = $('button.post-cancel');
		var $pre = $('pre.post-cancel');
		var $fields = $div.find('.form-control');
		var url = '<?= URL::route('ff-mt-em-gate') ?>';

		$button.off('click');
		$button.on('click', function () {
			$button.button('loading');
			var json = {type: 'cancel'};
			json = jsonFromInputs($fields, json);
			$.post(url, json, function (data) {
				$pre.html(data);
				$button.button('reset');
			});
		});

	}

	function postApproveButton() {

		var $button = $('button.post-approve');
		var url = '<?= URL::route('ff-mt-em-approve') ?>';

		$button.off('click');
		$button.on('click', function () {
			var $thisButton = $(this);
			$thisButton.button('loading');
			var json = {id: $thisButton.data('id')};
			$.post(url, json, function (json) {
				if(json && json.status){
					$thisButton.parents('tr').find('td.status').html(json.status);
				}
				if (json && json.next) {
					$thisButton.button('reset');
					$thisButton.text(json.value);
					postApproveButton();
				} else {
					$thisButton.remove();
				}

			});
		});

	}


	function jsonFromInputs($fields, json) {
		if (!json) {
			json = {};
		}
		if (!json.input) {
			json.input = {};
		}
		$fields.each(function () {
			json.input[$(this).attr('name')] = $(this).val();
		});
		return json;
	}


</script>