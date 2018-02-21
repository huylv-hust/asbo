<?php echo Asset::js('jquery.validate.js'); ?>
<?php echo Asset::js('validate/event.js'); ?>
<style>
	label.error{color:red}
</style>
<div class="container">
	<form class="form-inline">
		<h3>
			リペアイベント目標
			<select class="form-control select-year" name ="select-year" class="select-year">
				<option value="<?php echo date('Y');?>" <?php if($year == date('Y')){ echo "selected";}?>><?php echo date('Y');?></option>
				<option value="<?php echo date('Y') + 1;?>" <?php if($year == date('Y')+1){ echo "selected";}?>><?php echo date('Y')+1;?></option>
			</select>
			年
			<button type="button" class="btn btn-info btn-sm" name="open-btn" data-id="-1"><i class="glyphicon glyphicon-plus icon-white"></i> 新規追加</button>
		</h3>
		<?php if(count($list)>0){?>
        <nav>
            <?php echo Pagination::instance('mypagination'); ?>
        </nav>
		<table class="table table-striped table-bordered">
			<tr>
				<th class="text-center">イベント名</th>
				<th class="text-center">期間</th>
				<th class="text-center">目標ピース数</th>
				<th class="text-center">目標金額(税込)</th>
				<th class="text-center">予約率</th>
				<th class="text-center">事前予約率</th>
				<th class="text-center">売上</th>
				<th>管理</th>
			</tr>

			<?php foreach($list as $key => $value){ ?>
				<tr>
					<td><?php echo $value['event_name'] ?></td>
					<td><?php echo $value['start_date'] ?> ～ <?php echo $value['end_date'] ?></td>
					<td class="text-right"><?php echo $value['piece_count'] ?>P</td>
					<td class="text-right"><?php echo number_format($value['target_sales'], 0, ',', ',');  ?>円</td>
					<td class="text-right">
						<?php
							if($value['target_sales'] != 0)
							{
								printf('%.1f%%', $value['reserve_sales'] * 100 / $value['target_sales']);
							}
							else
							{
								echo '-' ;
							}
						?>
					</td>
					<td class="text-right">
						<?php
							if($value['target_sales'] != 0)
							{
								printf('%.1f%%', $value['before_sales'] * 100 / $value['target_sales']);
							}
							else
							{
								echo '-' ;
							}
						?>
					</td>
					<td class="text-right"><?php echo number_format($value['price'], 0, ',', ',');  ?>円</td>
					<td>
						<div class="btn-group">
							<a href="#" data-toggle="dropdown" class="btn dropdown-toggle btn-sm btn-success">
								処理
								<span class="caret"></span>
							</a>
							<ul name="add-pulldown" class="dropdown-menu">
								<li><a href="#" name="open-btn" data-id="<?php echo $value['event_id'] ?>"><i class="glyphicon glyphicon-pencil"></i> 内容編集</a></li>
								<li><a href="#" class="delete-event" data-id="<?php echo $value['event_id'] ?>"><i class="glyphicon glyphicon-trash"></i> 削除</a></li>
							</ul>
						</div>
					</td>
				</tr>
			<?php }?>
		</table>
		<?php } else { ?>
		<div class="alert alert-danger" role="alert">
			<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
			データがありません
		</div>
		<?php } ?>

	</form>
	<form id="change-year" action="plan" method="POST">
		<input type="hidden" name="year" id="year">
	</form>
	<div id="inputform" class="modal fade">
				<div class="modal-dialog modal-lg">
					<div class="modal-content">
						<div class="modal-body">
							<?php echo Form::open(array('action' =>'', 'method' => 'post','id'=>'event-form'));?>
								<div class="form-group form-inline">
									<label class="col-md-2">イベント名</label>
									<input type="text" class="form-control event_name" size="50" name="event_name">

									<span class="text-info">※必須</span>
								</div>
								<div class="form-group form-inline">
									<label class="col-md-2">期間</label>
									<input type="text" class="form-control dateform date-form" id="date-form" size="12" name="start_date">
									～
									<input type="text" class="form-control dateform date-to" id="date-to" size="12" name="end_date">
									<span class="text-info">※必須</span>
								</div>
								<span class="err"></span>
								<div class="form-group form-inline">
									<label class="col-md-2">目標ピース数</label>
									<div class="input-group piece_count_error">
										<input type="text" class="form-control piece_count" size="5" name="piece_count" onchange="Util.zen2han(this)">
										<div class="input-group-addon">ピース</div>
									</div>
									<span class="text-info">※必須</span>
								</div>
								<div class="form-group form-inline">
									<label class="col-md-2">目標金額(税込)</label>
									<div class="input-group target_sales_error">
										<input type="text" class="form-control target_sales" name="target_sales" size="5" onchange="Util.zen2han(this)">
										<div class="input-group-addon">円</div>
									</div>
									<span class="text-info">※必須</span>
								</div>
								<span class="err"></span>
								<div class="form-group text-center">
									<button type="submit" class="btn btn-primary btn-sm">
										<i class="glyphicon glyphicon-pencil icon-white"></i>
										保存
									</button>
								</div>

								<input type="hidden" class="event_id" name="event_id"/>
								<input type="hidden" name="year" id="curr_year">
							<?php echo Form::close(); ?>
						</div>
					</div>
				</div>
		</div>

</div>
<script>
	$(function (e)
	{

		$('.dateform').datepicker({
                onSelect: function(dateText) {
                    $("input#date-to").datepicker('option', 'minDate', dateText);
				 }
        });
		$('[name=open-btn]').on('click', function ()
		{
			$('#inputform').modal();
			$("form#event-form :input").each(function(){
				$(this).val('');

			});

			$('input').each(function() {
				$(this).removeClass('error');
				$('label.error').remove();
			});

			var id   = $(this).data('id');
			var year = $('.select-year').val();
            $('.event_id').val(id);
			$('#curr_year').val(year);

			if(id!='-1'){
                $.ajax({
                    type: "POST",
                    url : "<?php echo Uri::base(true) ?>repair/plan/detail_event",
                    dataType: 'json',
                    data : {id:id},
                    success : function(data){
                       $('.event_name').val(data.event_name);
                       $('.date-form').val(data.start_date);
					   $('.date-to').val(data.end_date);
					   $('.piece_count').val(data.piece_count);
					   $('.target_sales').val(data.target_sales);
                    }
                });
            }

			return false;
		});
		$('.select-year').change(function(){

			$("#year").val($(this).val());
			$("#change-year").submit();
		})

		$('.delete-event').on('click', function (){
            var r = confirm("削除します、よろしいですか？");
            if (r == true) {
                var id = $(this).data('id');
                $.ajax({
                    type: "POST",
                    url : "<?php echo Uri::base(true) ?>repair/plan/delete_event",
                    //dataType: 'json',
                    data : {id:id},
                    success : function(data, textStatus, request){
                           location.reload();
                    }
                });
            }
        })

	});

</script>