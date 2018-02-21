<div class="container">
	<h3>
		リペア技術者リスト
		<button type="button" class="btn btn-info btn-sm" name="open-btn"><i class="glyphicon glyphicon-plus icon-white"></i> 新規追加</button>
	</h3>

		<?php echo Form::open(array('action' => \Uri::base().'repair/staffs/index','method' => 'get', 'class' => 'form-inline')); ?>
			<div class="panel panel-default">
				<div class="panel-body">
					<div class="row">
						<div class="col-md-6">
							<label class="control-label col-md-3">氏名</label>
							<?php echo Form::input('staff_name', Input::get('staff_name', isset($get) ? $get->staff_name : ''), array('class' => 'form-control')); ?>
						</div>
						<?php
							$branch = array('' => '全て');
							foreach (\Constants::$branch as $branch_code => $branch_name)
							{
								$branch[$branch_code] = $branch_name;
							}
						?>
						<div class="col-md-6">
							<label class="control-label col-md-3">所属支店</label>
							<?php
							echo Form::select('branch_code', Input::get('branch_code', isset($get) ? $get->branch_code : ''), $branch, array('class'=>'form-control'));
							?>

						</div>
					</div>
					<div class="row text-center">
						<button type="submit" class="btn btn-default btn-sm"><i class="glyphicon glyphicon-search icon-white"></i> フィルタ</button>
					</div>
				</div>
			</div>
		<?php echo Form::close(); ?>
		<!-- Pagination -->
		<nav>
			<?php echo Pagination::instance('staffs-pagination'); ?>
		</nav>

		<?php if(isset($liststaffs) && $liststaffs != null && count($liststaffs)) { ?>
		<table class="table table-bordered table-striped">
			<tr>
				<th>所属支店</th>
				<th>氏名</th>
				<th>ログインID</th>
				<th>基本対応ピース数</th>
				<th>状態</th>
				<th>管理</th>
			</tr>
			<?php foreach ($liststaffs as $staff) { ?>
			<tr>
				<td><?php echo array_key_exists($staff['branch_code'], $branch) ? $branch[$staff['branch_code']] : ''; ?></td>
				<td><?php echo $staff['staff_name']; ?></td>
				<td><?php echo $staff['login_id']; ?></td>
				<td class="text-right"><?php echo $staff['piece_count']; ?></td>
				<td><?php echo $staff['state'] == 0 ? '有効' : '無効'; ?></td>
				<td>
					<div class="btn-group">
						<a href="#" data-toggle="dropdown" class="btn dropdown-toggle btn-sm btn-success">
							処理
							<span class="caret"></span>
						</a>
						<ul name="add-pulldown" class="dropdown-menu">
							<li><a href="<?php echo \Uri::base().'repair/staff?repair_staff_id='.$staff['repair_staff_id']; ?>" name="edit-btn"><i class="glyphicon glyphicon-pencil"></i> 編集</a></li>
							<?php if($staff['state'] == 1){ ?>
							<li><a href="<?php echo \Uri::base().'repair/staffs/active?repair_staff_id='.$staff['repair_staff_id'].'&status=0'; ?>" class="state" rel="1"><i class="glyphicon glyphicon-ok"></i> 有効化</a></li>
							<?php } ?>
							<?php if($staff['state'] == 0){ ?>
							<li><a href="<?php echo \Uri::base().'repair/staffs/active?repair_staff_id='.$staff['repair_staff_id'].'&status=1'; ?>" class="state" rel="0"><i class="glyphicon glyphicon-remove"></i> 無効化</a></li>
							<?php } ?>
							<li><a href="javascript:void(0)" class="del-staff" rel="<?php echo $staff['repair_staff_id']; ?>"><i class="glyphicon glyphicon-trash"></i> 削除</a></li>
						</ul>
					</div>
				</td>
			</tr>
			<?php } ?>
		</table>
		<?php } else { ?>
			<div class="alert alert-danger" role="alert">
				<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
				該当するデータがありません
			</div>
		<?php } ?>

</div>

<script type="text/javascript">
$(document).ready(function(){
	$('a.del-staff').click(function(){
		var id = $(this).attr('rel');
		if(id == ''){ return false; }
		if(!confirm('削除します、よろしいですか？')){
			return false;
		}
		$.post('<?php echo Uri::base(); ?>repair/staffs/delete',{repair_staff_id:id}, function(data){
			if(data == 'false'){
				alert('関連データが存在するため削除できません');
				return false;
			}
			location.reload();
		});
	});
	//add new
	$('button[name=open-btn]').click(function(){
		window.location = '<?php echo Fuel\Core\Uri::base().'repair/staff'; ?>';
	});

	//confirmChange
	$('a.state').click(function(){
		type = $(this).attr('rel');
		var msg = '無効にします。よろしいですか？';
		if(type == 1){
			msg = '有効にします。よろしいですか？';
		}
		if(!confirm(msg)){
			return false;
		}
	});
});
</script>