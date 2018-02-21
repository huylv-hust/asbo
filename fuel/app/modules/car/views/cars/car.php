<?php echo Asset::css('jquery.validation.css'); ?>
<?php echo Asset::js('jquery.validation.js'); ?>
<?php echo Asset::js('util.js'); ?>


<div class="container">
    <h3>
        <?php echo $sscode ?> <?php echo $ssname ?>の登録済み代車リスト
        <button type="button" class="btn btn-info btn-sm" name="open-btn" data-id="-1"><i class="glyphicon glyphicon-plus icon-white"></i> 新規追加</button>
    </h3>
		<?php if(count($listCar)>0){?>
        <nav>
            <?php echo Pagination::instance('mypagination'); ?>
        </nav>

        <table class="table table-bordered table-striped">
            <tr>
                <th>車種</th>
                <th>車番</th>
                <th>管理</th>
            </tr>

				<?php foreach ($listCar as $items){ ?>
				<tr>
					<td><?php echo $items['car_name'] ?></td>
					<td><?php echo $items['plate_no'] ?></td>
					<td>
						<div class="btn-group">
							<a href="#" data-toggle="dropdown" class="btn dropdown-toggle btn-sm btn-success">
								処理
								<span class="caret"></span>
							</a>
							<ul name="add-pulldown" class="dropdown-menu">
								<li><a href="#" name="open-btn" class="edit_car" data-id="<?php echo $items['car_id'] ?>"><i class="glyphicon glyphicon-pencil"></i> 編集</a></li>
								<li><a href="#" class="delete-car" data-id="<?php echo $items['car_id'] ?>"><i class="glyphicon glyphicon-trash"></i> 削除</a></li>
							</ul>
						</div>
					</td>
				</tr>
				<?php } ?>
			<?php } else { ?>
				<div class="alert alert-danger" role="alert">
					<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
					データがありません
				</div>
			<?php } ?>
        </table>

    </form>

    <div id="inputform" class="modal fade">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-body">
                    <?php echo Form::open(array('action' =>\Uri::base(true).'car/cars/save_car', 'method' => 'post','id'=>'saveCar','name' => 'saveCar'));?>
					<div class="form-group form-inline">
						<label class="col-md-2">車種</label>
						<input type="text" class="form-control car_name" size="50" name="car_name" data-validation="[NOTEMPTY, L<=50]">

					</div>
					<div class="form-group form-inline">
						<label class="col-md-2">車番</label>
						<input type="text" class="form-control plate_no" size="4" name="plate_no" data-validation="[NOTEMPTY,L==4,NUMERIC]" onchange="Util.zen2han(this),Util.convertPlateNo(this)">

					</div>
					<div class="form-group text-center">
						<button type="submit" class="btn btn-primary btn-sm">
							<i class="glyphicon glyphicon-pencil icon-white"></i>
							保存
						</button>
					</div>
					<input type="hidden" class="car_id" name="car_id"/>
                    <?php echo Form::close(); ?>
                </div>
            </div>
        </div>
    </div>

</div>
<?php echo $ssfinder; ?>
<script>
    $(function (e)
    {
        $('#saveCar [data-toggle="popover"]').popover({trigger: 'focus', placement: 'left'});
		$('#saveCar').validate({
			messages: {
				'NOTEMPTY': '必須です',
				'==': '4桁の数字で入力してください',
				'<=': '50文字以内で入力してください',
				'NUMERIC' : '数字で入力してください'
			},
			 submit: {
				settings: {
					inputContainer: '.form-group',
					errorListClass: "hide",
					clear: 'keypress',
				},
				callback: {
						onBeforeSubmit  : function(){
							var r = confirm("保存します、よろしいですか？");
							if (r == true) {
								$(this).submit();
							}else{
								 $('#inputform').modal('hide');
								 exit();
							}
						},
						onError: function(node, errors) {
							for (var element_name in errors) {
								var error = errors[element_name];
								$('[name=' + element_name + ']')
									.attr('data-content', error)
									.popover('show')
									.on('focus', function()
									{
										$(this).popover('destroy');
									});
							}
						}
					}
			 }
		});
//        function myBeforeSubmitFunction(a, b, node) {
//            console.log(a, b);
//            node.find('input:not([type="submit"]), select, textarea').attr('readonly', 'true');
//            node.append('<div class="ui active loader"></div>');
//        }
        $('.dateform').datepicker();

        $('[name=open-btn]').on('click', function ()
        {
            $('#inputform').modal();
			$("form#saveCar :input").each(function(){
				$(this).val('');

			});
			$('#saveCar').removeError();
			$(".popover").each(function(){
				$(this).remove();
			});
			$('.car_name,.plate_no').on('click', function (){
				$(".popover").each(function(){
					$(this).remove();
				});
			})
            var id = $(this).data('id');
            $('.car_id').val(id);
            if(id!='-1'){
                $.ajax({
                    type: "POST",
                    url : "<?php echo Uri::base(true) ?>car/cars/detail_car",
                    dataType: 'json',
                    data : {id:id},
                    success : function(data){
                       $('.car_name').val(data.car_name);
                       $('.plate_no').val(data.plate_no);
                    }
                });
            }
            return false;
        });

        $('[name=add-btn]').on('click', function ()
        {
            location.href = '<?php echo Uri::base(true) ?>car/reserve';
            return false;
        });

        $('button[name=findss-btn]').on('click', function () {
            $('#ssfinder').modal();
            return false;
        });

        $('#ssfinder div.list-group a').on('click', function () {
            $('#ssfinder').modal('hide');
            return false;
        });

        $('.delete-car').on('click', function (){
            var r = confirm("削除します、よろしいですか?");
            if (r == true) {
                var id = $(this).data('id');
                $.ajax({
                    type: "POST",
                    url : "<?php echo Uri::base(true) ?>car/cars/delete_car",
                    //dataType: 'json',
                    data : {id:id},
                    success : function(data, textStatus, request){
                       if (parseInt(request.getResponseHeader('SUCCESS')) == 0) {
                           alert('関連データが存在するため削除できません');
                           location.reload();
                       }else{
                           location.reload();
                       }

                    }
                });
            }
        })

    });

</script>
