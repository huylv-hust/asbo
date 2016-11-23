<?php echo Asset::js('util.js'); ?>

<div id="stafffinder" class="modal fade">
	<div class="modal-dialog modal-lg">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
				<h4 class="modal-title">リペア技術者検索</h4>
			</div>
			<div class="modal-body">
				<?php echo Form::open(array('action' => '', 'method' => 'get','class'=>'form-horizontal', 'id' => 'staff-form'));?>

					<div class="row">
						<label class="col-sm-2 control-label">支店</label>
						<div class="col-sm-3">
							<?php
								$branch = array('' => '全て');
						        foreach (\Constants::$branch as $branch_code => $branch_name)
								{
									$branch[$branch_code] = $branch_name;
								}
								echo Form::select('branch','none',
									$branch,
									array( // attributes
										'class' => 'form-control branch-id'
								   )
								);
							?>
						</div>
						<label class="col-sm-2 control-label">キーワード</label>
						<div class="col-sm-3">
							<input type="text" class="form-control name" placeholder="氏名で検索" size="50">
						</div>
						<div class="col-sm-2">
							<button type="submit" class="btn btn-primary btn-sm search-staff">
								<i class="glyphicon glyphicon-search icon-white"></i>
							</button>
						</div>
					</div>
					<div class="row container-fluid">
						<div class="list-group" id="iteams-staff">
						</div>
					</div>
				</form>
			</div>
		</div>
	</div>
</div>
<script>
	$(function (e)
    {
        $('.search-staff').on('click', function (){
            var branch = $('.branch-id').val();
			var name = $('.name').val();
			$.ajax({
				type: "POST",
				url : "<?php echo Uri::base(true) ?>repair/staffschedule/search_staff",
				dataType: 'json',
				data : {branch:branch,name:name},
				success : function(data, textStatus, request){
					var option = ' <a  class="list-group-item disabled">検索結果</a>';
					for (var i = 0; i < data.length; i++) {
						var name = Util.htmlspecialchars(data[i]['staff_name']);
						var id =  Util.htmlspecialchars(data[i]['repair_staff_id']);
						option += '<a  class="list-group-item" id="'+id+'">'+name+'</a>';

					}
					$('.list-group-item ').remove();
					$('#iteams-staff').append(option);
				}
			});

        });
		$(document).on('click','#iteams-staff a', function(){
			var repair_staff_id = $(this).attr('id');
			var staff_name = $(this).text();
			$.ajax({
			type: "POST",
			url : "<?php echo Uri::base(true) ?>repair/staffschedule/set_cookie_staff",
			//dataType: 'json',
			data : {repair_staff_id:repair_staff_id,staff_name:staff_name},
			success : function(data){
				var url ='<?php echo Uri::current()?>';
				if(url=='<?php echo Uri::base(true) ?>repair/schedule')
				{
					$("#staff").val(staff_name);
					$("#staff_id").val(repair_staff_id);
					$('#stafffinder').modal('hide');
				}
				else
				{
					location.reload();
				}
			}
		});

		})
		$('#staff-form').on('submit', function(){
			$('.search-staff').trigger('click');
			return false;
		});
    });



</script>