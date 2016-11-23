<style type="text/css">
	label.error{color:red;}
</style>
<div class="container">
			<h3>
				作業予約リスト
				<a href="<?php echo Fuel\Core\Uri::base().'reserve/reserve?sscode='.Cookie::get("sscode");?>&pos=1" ><button name="add-btn" class="btn btn-info btn-sm" type="button"><i class="glyphicon glyphicon-plus icon-white"></i> 新規追加</button></a>
			</h3>

			<p class="text-center">
				<a href="<?php echo Fuel\Core\Uri::base()?>reserve/calendar">カレンダー表示</a>
				|
				<a href="<?php echo Fuel\Core\Uri::base()?>reserve/list/">リスト表示</a>
			</p>

			<form class="form-inline" method="get" id="validation" action="<?php echo Fuel\Core\Uri::base()?>reserve/list/">
				<input type="hidden" value="1" name="search">
				<div class="panel panel-default">
					<div class="panel-body">
						<div class="row">
							<div class="col-md-6">
								<label class="control-label col-md-3">SSコード</label>
								<input type="text" value="<?php echo $is_search ? \Input::param('sscode') : \Cookie::get('sscode') ?>"  class="form-control" name="sscode" id="sscode">
								<button name="findss-btn" class="btn btn-success btn-sm" type="button">
									<i class="glyphicon glyphicon-search icon-white"></i>
								</button>
							</div>
							<div class="col-md-6">
								<label class="control-label col-md-3">期間</label>
								<input type="text" value="<?php echo $start_time?>" size="12" name="start_time" class="form-control dateform" id="dp1432001725956">
								～
								<input type="text" value="<?php echo $end_time?>" size="12" name="end_time" class="form-control dateform" id="dp1432001725957">
							</div>
						</div>
						<div class="row">
							<div class="col-md-6">
								<label class="control-label col-md-3">車番</label>
								<input type="text" size="4" value="<?php echo $plate_no?>" name="plate_no" class="form-control" maxlength="4">
							</div>
							<div class="col-md-6">
								<label class="control-label col-md-3">カード番号</label>
								<input type="text" placaholder="Usappyカード番号" maxlength="16" size="20" value="<?php echo $usappy_id?>" class="form-control" name="usappy_id">
							</div>
						</div>

						<div class="row">
							<div class="col-md-6">
								<label class="control-label col-md-3">予約番号</label>
								<input type="text" size="20" value="<?php echo $reservation_no?>" name="reservation_no" class="form-control">
							</div>
						</div>

						<div class="row text-center">
							<button class="btn btn-default btn-sm" type="submit"s><i class="glyphicon glyphicon-search icon-white"></i> フィルタ</button>
						</div>
					</div>
				</div>

				<nav>
				<?php echo Pagination::instance('reservepagination'); ?>
				</nav>

				<?php if (count($list) > 0) { ?>

				<table class="table table-bordered table-striped" >
					<tbody><tr>
						<th>予約番号</th>
						<th>SSコード</th>
						<th>SS名</th>
						<th>作業予定期間</th>
						<th>メニュー</th>
						<th>作業ピット</th>
						<th>氏名</th>
						<th>車番</th>
						<th>管理</th>
					</tr>
					<?php foreach ($list as $row) { ?>
					<tr>
						<td onclick = "delete('<?php echo $row['reservation_no'] ?>')"><?php echo $row['reservation_no'] ?></td>
						<td><?php echo $row['sscode'] ?></td>
						<td><?php echo htmlspecialchars($list_ss[$row['sscode']]) ?></td>
						<td><?php echo substr($row['start_time'],0,-3) ?> ～ <?php echo substr($row['end_time'],0,-3) ?></td>
						<td><?php echo htmlspecialchars($row['menu_code']=='other' ? $row['menu_name'] : \Constants::$pit_work[$row['menu_code']]) ?></td>
						<td><?php echo htmlspecialchars(isset($list_pit_name[$row['sscode']][$row['pit_no']] ) ?  $list_pit_name[$row['sscode']][$row['pit_no']] : '') ?></td>
						<td>
							<?php if ($row['member_id'] && $row['hashkey'] == null) { ?>
							<?php echo htmlspecialchars(isset($list_cs[$row['member_id']]) ? $list_cs[$row['member_id']] : '') ?>
							<?php } else { ?>
							<?php echo htmlspecialchars(isset($list_cs[$row['reservation_no']]) ? $list_cs[$row['reservation_no']] : '') ?>
							<?php } ?>
							様
						</td>
						<td><?php echo $row['plate_no'] ?></td>
						<td>
							<div class="btn-group">
								<a class="btn dropdown-toggle btn-sm btn-success" data-toggle="dropdown">
									処理
									<span class="caret"></span>
								</a>
								<ul class="dropdown-menu" name="add-pulldown">
									<li><a name="add-btn" id="delete" href="<?php echo Fuel\Core\Uri::base() ?>reserve/reserve?pos=1&reservation_no=<?php echo $row['reservation_no'] ?>"><i class="glyphicon glyphicon-pencil"></i> 内容編集</a></li>
									<li onclick="delete_reserve('<?php echo $row['reservation_no'] ?>')"><a id="<?php echo $row['reservation_no'] ?>"><i class="glyphicon glyphicon-trash"></i> 削除</a></li>
								</ul>
							</div>
						</td>
					</tr>
					<?php } ?>

				</tbody></table>

				<?php } else { ?>
				<div class="alert alert-danger" role="alert">
					<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
					該当するデータがありません
				</div>
				<?php } ?>

			</form>

		</div>
<?php echo $ssfinder; ?>
<script>
	$('.dateform').datepicker();

	$('button[name=findss-btn]').on('click', function () {
		$('#ssfinder').modal();
		return false;
	});

	$('#ssfinder div.list-group a').on('click', function () {
		$('#ssfinder').modal('hide');
		return false;
	});

	$('button[name=findcard-btn]').on('click', function () {
		$('#findcardform').modal();
		return false;
	});
	function delete_reserve(id)
	{
		if(confirm("削除します、よろしいですか？"))
		{
			$.post('<?php echo Fuel\Core\Uri::base()?>reserve/list/delete/',
				{
					'reservation_no':id,
				},
				function(data){

					if(data =='1')
					{
						location.reload();
					}
					else
					{
						alert('error');
					}
				}
		);
		}
	}
</script>