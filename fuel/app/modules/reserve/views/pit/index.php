<style type="text/css">
	label.error{color:red}
</style>
<?php echo Asset::js('jquery.validate.js'); ?>
<script type="text/javascript">
	baseUrl = "<?php echo Uri::base(); ?>reserve/pit";
</script>

<div class="container">
    <h3>
        作業ピット設定
        <button type="button" class="btn btn-info btn-sm" name="open-btn"><i class="glyphicon glyphicon-plus icon-white"></i> 新規追加</button>
    </h3>
	<!-- Pagination -->
	<nav>
		<?php echo Pagination::instance('pitpagination'); ?>
	</nav>

    <form class="form-inline">
		<?php if(isset($listpit) && $listpit != null) { ?>
        <table class="table table-bordered table-striped">
            <tr>
                <th>ピット名</th>
                <th>対応作業メニュー</th>
                <th>WEB予約対象</th>
                <th>備考</th>
                <th>管理</th>
            </tr>
            <?php foreach($listpit as $k => $pit){ ?>
            <tr>
                <td><?php echo $pit['pit_name']; ?></td>
                <td>
                    <?php if(isset($listmenupit[$pit['pit_no']])){ ?>
					<?php
					$menuCode = array();
					foreach($listmenupit[$pit['pit_no']] as $val){
						$menuCode[] = $val['menu_code'];
					}
					?>
					<?php
						$str = null;
						if(in_array('inspection', $menuCode)){
							$str.= '車検,';
						}
						if(in_array('oil', $menuCode)){
							$str.= 'オイル,';
						}
						if(in_array('tire', $menuCode)){
							$str.= 'タイヤ,';
						}
						echo trim($str, ', ');
					?>
					<?php } ?>
                </td>
                <td><?php echo $pit['is_public'] == 1 ? '対象' : '対象外' ?></td>
                <td><?php echo $pit['note'] ?></td>
                <td>
                    <div class="btn-group">
                        <a href="#" data-toggle="dropdown" class="btn dropdown-toggle btn-sm btn-success">
                            処理
                            <span class="caret"></span>
                        </a>
                        <ul name="add-pulldown" class="dropdown-menu">
                            <li><a href="javascript:void(0)" name="edit-btn" rel="<?php echo $pit['pit_no']; ?>"><i class="glyphicon glyphicon-pencil"></i> 編集</a></li>
                            <li><a href="javascript:void(0)" rel="<?php echo $pit['pit_no']; ?>" class="delpit"><i class="glyphicon glyphicon-trash"></i> 削除</a></li>
                        </ul>
                    </div>
                </td>
            </tr>
            <?php } ?>
        </table>
		<?php }else{ ?>
			<div class="alert alert-danger" role="alert">
				<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
				データがありません
			</div>
		<?php } ?>
    </form>

    <div id="inputform" class="modal fade">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-body">
					<?php echo Form::open(array('action' => 'javascript:void(0)', 'method' => 'post', 'name' => 'pit', 'id' => 'preview_form', 'class' => 'form-horizontal')); ?>
                        <div class="form-group form-inline">
							<?php echo Form::input('pit_no', 0, array('type'=>'hidden','id'=>'pit_no')); ?>
                            <label class="col-md-2 control-label">ピット名</label>
                            <div class="col-md-10">
								<?php echo Form::input('pit_name', '', array('class' => 'form-control', 'placeholder' => 'ピット名を入力', 'size' => '50')); ?>
								<label for="form_pit_name" generated="true" style="display:none" class="error show_err">既に同じピット名が登録済みです</label>
								<span class="text-info">※必須</span>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-md-2 control-label">対応作業メニュー</label>
                            <div class="col-md-10">
								<label class="checkbox-inline"><input type="checkbox" name="pitmenu[]" class="pitmenu" value="inspection">車検</label>
                                <label class="checkbox-inline"><input type="checkbox" name="pitmenu[]" class="pitmenu" value="oil">オイル</label>
								<label class="checkbox-inline"><input type="checkbox" name="pitmenu[]" class="pitmenu" value="tire">タイヤ</label>
								<label class="checkbox-inline"><input type="text" name="forpitmenu" size="1" readonly="readonly" style="width:1px; border:none"/></label>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-md-2 control-label">WEB予約対象</label>
                            <div class="col-md-10">
                                <label class="radio-inline"><?php echo Form::radio('is_public', 1, true); ?>対象</label>
                                <label class="radio-inline"><?php echo Form::radio('is_public', 2, false); ?>対象外</label>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-md-2 control-label">備考</label>
                            <div class="col-md-10">
								<?php echo Form::input('note', '', array('class' => 'form-control', 'placeholder' => '備考を入力', 'size' => '50')); ?>
                            </div>
                        </div>
                        <div class="form-group text-center">
                            <button type="submit" class="btn btn-primary btn-sm">
                                <i class="glyphicon glyphicon-pencil icon-white"></i>
                                保存
                            </button>
                        </div>
                    <?php echo Form::close(); ?>
                </div>
            </div>
        </div>
    </div>

</div>
<script>
    $(document).ready(function(){
		$('a[name=edit-btn]').click(function(){
			var pit_no = $(this).attr('rel');
			if(!pit_no){
				return false;
			}

			//find and fill
			$.post('<?php echo Uri::base(); ?>reserve/pit/getpitinfo',{pit_no:pit_no},function(data){
				if(!data){
					return false;
				}
				var result = jQuery.parseJSON(data);
				//fill data to form
				$('input[name=pit_no][id=pit_no]').val(result['pit_no']);
				$('input[name=pit_name]').val(result['pit_name']);
				jQuery.each( result['pitmenu'], function( i, val ) {
					var checkbox = $("input[class=pitmenu][value="+val['menu_code']+"]");
					if(checkbox.length) { // 0 == false; >0 == true
						checkbox.prop('checked', true);
					}
				});
				if(result['is_public'] == 1){
					$('input[name=is_public][value=1]').attr('checked','checked');
				}
				if(result['is_public'] == 2){
					$('input[name=is_public][value=2]').attr('checked','checked');
				}
				$('input[name=note]').val(result['note']);
			});
	   });
	   // delet button
	   $('a.delpit').click(function(){
		   var id = $(this).attr('rel');
		   if(id == ''){ return false; }
		   if(!confirm('削除します、よろしいですか？')){
			   return false;
		   }
		   $.post('<?php echo Uri::base(); ?>reserve/pit/delete',{pit_no:id}, function(data){
			   if(data == 'false'){
				   alert('関連データが存在するため削除できません');
				   return false;
			   }
			   location.reload();
		   });
	   })
    });
    $(function (e)
    {
        $('button[name=open-btn],a[name=edit-btn]').on('click', function ()
        {
			//reset form before fill data
			$("#preview_form input[type=text]").val("");
			$("#preview_form input[type=checkbox]").removeAttr("checked");
			$('label.show_err').hide();
			$('#inputform').modal();
            return false;
        });
    });
</script>

<?php echo Asset::js('validate/pit-validate.js'); ?>