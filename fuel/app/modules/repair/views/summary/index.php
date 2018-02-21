<div class="container">
	<h3>
		リペア集計
	</h3>

	<form id="event-form" class="form-inline" method="get" action="summary/downloadevent">

		<div class="panel panel-warning">
			<div class="panel-heading">イベント集計</div>
			<div class="panel-body">
				<div class="row">
					<label class="control-label col-md-2 text-right">所属支店</label>
					<?php
					$branch = array('' => '選択してください');
					foreach (\Constants::$branch as $branch_code => $branch_name)
					{
						$branch[$branch_code] = $branch_name;
					}
					echo Form::select('branch_code', Input::get('branch_code', isset($get) ? $get->branch_code : ''), $branch, array('class'=>'form-control col-md-4'));
					?>
					<label class="control-label col-md-2 text-right">担当者</label>
					<select class="form-control col-md-4" name="repair_staff_id">
						<option value="">全て</option>
					</select>
				</div>
				<div class="row">
						<label class="control-label col-md-2 text-right">対象イベント</label>
						<select class="form-control" name="event_id">
							<option value="">選択してください</option>
						</select>
				</div>
				<div class="row text-center">
					<button type="submit" class="btn btn-success btn-sm" name="download-event-btn"><i class="glyphicon glyphicon-download-alt icon-white"></i> CSVダウンロード</button>
				</div>
			</div>
		</div>

	</form>

	<form id="month-form" class="form-inline" method="get" action="summary/downloadmonth">

		<div class="panel panel-warning">
			<div class="panel-heading">月間集計</div>
			<div class="panel-body">
				<div class="row">
					<label class="control-label col-md-2 text-right">所属支店</label>

					<?php
					echo Form::select('branch_code', Input::get('branch_code', isset($get) ? $get->branch_code : ''), $branch, array('class'=>'form-control col-md-4'));
					?>

					<label class="control-label col-md-2 text-right">担当者</label>
					<select class="form-control col-md-4" name="repair_staff_id">
						<option value="">全て</option>
					</select>
				</div>
				<div class="row">
						<label class="control-label col-md-2 text-right">対象月</label>
						<select class="form-control" name="month">
							<?php foreach ($months as $month) { ?>
							<option value="<?php echo htmlspecialchars($month['year']) ?>-<?php echo htmlspecialchars($month['month']) ?>"><?php echo htmlspecialchars($month['year']) ?>年<?php echo htmlspecialchars($month['month']) ?>月</option>
							<?php } ?>
						</select>
				</div>
				<div class="row text-center">
					<button type="submit" class="btn btn-success btn-sm" name="download-month-btn"><i class="glyphicon glyphicon-download-alt icon-white"></i> CSVダウンロード</button>
				</div>
			</div>
		</div>

	</form>
</div>

<script>
	$(function()
	{
		$('select[name=branch_code]').on('change', function()
		{
			var select = $(this);
			$.get(
				'<?php echo Fuel\Core\Uri::base()?>ajax/common/get_staff/',
				{
					branch : $(this).val()
				}
			).done(function(response)
			{
				var staffs = select.siblings('select[name=repair_staff_id]');
				staffs.find('option[value!=""]').remove();
				staffs.append(response);
				staffs.find('option:empty').remove();
			});
		});

		$('select[name=branch_code]:first').on('change', function()
		{
			$.get(
				'<?php echo Fuel\Core\Uri::base()?>ajax/common/repair_events',
				{
					branch_code : $(this).val()
				}
			).done(function(response)
			{
				var select = $('select[name=event_id]');
				select.find('option[value!=""]').remove();

				$.each($.parseJSON(response), function()
				{
					var option = $('<option></option>');
					option.attr('value', this.event_id);
					option.text(this.event_name + '(' + this.start_date + ' ～ ' + this.end_date + ')');
					select.append(option);
				});
			});
		});

		$('#event-form').on('submit', function()
		{
			var event_id = $(this).find('select[name=event_id]').val();
			if (event_id.length === 0)
			{
				alert('イベントを選択してください');
				return false;
			}
		});

		$('#month-form').on('submit', function()
		{
			var branch_code = $(this).find('select[name=branch_code]').val();
			if (branch_code.length === 0)
			{
				alert('所属支店を選択してください');
				return false;
			}
		});

	});
</script>
