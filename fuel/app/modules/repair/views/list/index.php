<div class="container">
			<h3>
				リペア予約リスト
				<a href="<?php echo Fuel\Core\Uri::base().'repair/reserve?sscode='.Cookie::get("sscode");?>&pos=1" ><button type="button" class="btn btn-info btn-sm" name="add-btn"><i class="glyphicon glyphicon-plus icon-white"></i> 新規追加</button></a>
			</h3>

			<p class="text-center">
				<a href="<?php echo Fuel\Core\Uri::base()?>repair/calendar">カレンダー表示</a>
				|
				<a href="<?php echo Fuel\Core\Uri::base()?>repair/list">リスト表示</a>
			</p>

			<form class="form-inline" method="get">
				<input type="hidden" value="1" name="search">
				<div class="panel panel-default">
					<div class="panel-body">
						<div class="row">
							<div class="col-md-6">
								<label class="control-label col-md-3">支店</label>
								<?php echo htmlspecialchars_decode($branch) ?>
							</div>
							<div class="col-md-6">
								<label class="control-label col-md-3">SSコード</label>
								<input type="text" class="form-control" name="sscode" value="<?php echo $is_search ? \Input::param('sscode') : \Cookie::get('sscode') ?>" id="sscode">
								<button type="button" class="btn btn-success btn-sm" name="findss-btn">
									<i class="glyphicon glyphicon-search icon-white"></i>
								</button>
							</div>
						</div>
						<div class="row">
							<div class="col-md-6">
								<label class="control-label col-md-3">期間</label>
								<input type="text" class="form-control dateform" size="8" value="<?php echo $start_time?>" name="start_time" />
								～
								<input type="text" class="form-control dateform" size="8" value="<?php echo $end_time?>" name="end_time" />
							</div>
							<div class="col-md-6">
								<label class="control-label col-md-3">車番</label>
								<input type="text" class="form-control" name="plate_no" onchange="Util.convertPlateNo(this)" value="<?php echo $plate_no?>" size="4">
							</div>
						</div>
						<div class="row">
							<div class="col-md-6">
								<label class="control-label col-md-3">カード番号</label>
								<input type="text" class="form-control" name="card_no" value="<?php echo $card_no?>" size="20" maxlength="16" placaHolder="Usappyカード番号">
							</div>
							<div class="col-md-6">
								<label class="control-label col-md-3">予約番号</label>
								<input type="text" class="form-control" name="reservation_no" value="<?php echo $reservation_no?>" size="20">
							</div>

						</div>
						<div class="row text-center">
							<button type="submit" class="btn btn-default btn-sm"><i class="glyphicon glyphicon-search icon-white"></i> フィルタ</button>
						</div>
					</div>
				</div>

				<nav>
					<?php echo Pagination::instance('reservepagination'); ?>
				</nav>

				<?php if (count($list) > 0) { ?>
				<table class="table table-bordered table-striped">
					<tr>
						<th class="text-center">予約番号</th>
						<th class="text-center">SSコード</th>
						<th class="text-center">SS名</th>
						<th class="text-center">入庫予定時刻<br>納車予定時刻</th>
						<th class="text-center">ピース数<br>金額</th>
						<th class="text-center">代車</th>
						<th class="text-center">送迎</th>
						<th class="text-center">氏名</th>
						<th class="text-center">車番</th>
						<th class="text-center">車種</th>
						<th class="text-center">管理</th>
					</tr>

					<?php
					$listMk = array();
					$listMd = array();
					$list_maker = \Api::get_list_maker();

					foreach($list_maker as $key => $value)
					{
						$listMk[$value['maker_code']] = $value['maker'];
					}

					foreach ($list as $row){
						$maker_name = '';
						$model_name = '';
						if(isset($listMk[$row['car_maker_code']]))
						{
							$maker_name = $listMk[$row['car_maker_code']];
						};

						$list_model = \Api::get_list_model($row['car_maker_code']);
						foreach($list_model as $key => $value)
						{
							$listMd[$value['model_code']] = $value['model'];
						}
						if(isset($listMd[$row['car_model_code']]))
						{
							$model_name = $listMd[$row['car_model_code']];
						};

						echo '<tr>
							<td>'.$row['reservation_no'].'</td>
							<td>'.$row['sscode'].'</td>
							<td>'.(isset($list_ss[$row['sscode']]) ? $list_ss[$row['sscode']] :"").'</td>
							<td>'.substr($row['arrival_time'],0,-3).'<br>'.substr($row['return_time'],0,-3).'</td>
							<td class="text-center">
								<span class="label label-danger">A</span>
								<span class="text-warning">'.$row['a_piece_count'].'</span>
								<span class="label label-primary">B</span>
								<span class="text-warning">'.$row['b_piece_count'].'</span>
								<br>'.number_format($row['price']).'円
							</td>
							<td class="text-center">
							'.($row['is_car_request'] ? '<span class="label label-warning">あり</span>' : '').'
							</td>
							<td class="text-center">
							'.($row['is_shuttle_request'] ? '<span class="label label-warning">あり</span>' : '').'
							</td>
							<td>'.($row['member_id'] ? ( isset($list_cs[$row['member_id']]) ?  $list_cs[$row['member_id']] :"") : ( isset($list_cs[$row['reservation_no']]) ? $list_cs[$row['reservation_no']] :"" ) ).'</td>
							<td>'.$row['plate_no'].'</td>
							<td>'.$maker_name.'&nbsp;&nbsp;'.$model_name.'</td>
							<td>
								<div class="btn-group">
									<a href="#" data-toggle="dropdown" class="btn dropdown-toggle btn-sm btn-success">
										処理
										<span class="caret"></span>
									</a>
									<ul name="add-pulldown" class="dropdown-menu">
										<li><a href="'.Fuel\Core\Uri::base().'repair/reserve?reservation_no='.$row['reservation_no'].'&pos=1" name="add-btn"><i class="glyphicon glyphicon-pencil"></i> 内容編集</a></li>
										<li onclick="delete_repair_reserve(\''.$row['reservation_no'].'\')"><a style="cursor: pointer;"><i class="glyphicon glyphicon-trash"></i> 削除</a></li>
									</ul>
								</div>
							</td>
						</tr>';
					}
					?>
				</table>
				<label class="text-info">合計ピース数</label>
				<span class="label label-danger">A</span>
				<span class="text-warning"><?php echo number_format((int)$total_a_piece_count) ?></span>
				<span class="label label-primary">B</span>
				<span class="text-warning"><?php echo number_format((int)$total_b_piece_count) ?></span>
				<span class="label label-success">全合計</span>
				<span class="text-warning"><?php echo number_format((int)$total_a_piece_count + (int)$total_b_piece_count) ?></span>
				<?php } else { ?>
				<div class="alert alert-danger" role="alert">
					<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
					データがありません
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
	function delete_repair_reserve(id)
	{
		if(confirm("削除します、よろしいですか？"))
		{
			$.post('<?php echo Fuel\Core\Uri::base()?>repair/list/delete/',
				{
					'reservation_no':id
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
<?php //echo Asset::js('jquery.validate.js'); ?>
<?php //echo Asset::js('validate/reserve-list.js'); ?>