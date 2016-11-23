//picecoutn
$.validator.addMethod("greaterthan1", function(value,element) {
	if(value >= 1){
		return true;
	}else{
		return false;
	}
},"1以上の数字で入力してください");
//login_id
$.validator.addMethod("loginformart", function(value,element) {
	if(value.match(/[^0-9A-Za-z]+/) == null){
		return true;
	}else{
		return false;
	}
},"30桁の半角英数字以内で入力してください");
//latinh password
$.validator.addMethod("passwordlatinh", function(value,element) {
	if(value.match(/^[a-zA-Z0-9!-/:-@¥[-`{-~]+$/)){
		return true;
	}else{
		return false;
	}
},"半角の文字で入力してください");
$.validator.messages.required = '必須です';
(function($,W,D)
{
    var validation = {};

    validation.util =
    {
        setupFormValidation: function()
        {
            //form validation rules
            $("#staffs-form").validate({
				errorPlacement: function(error, element){
                    var err = element.parents('td');
                    $(err).append(error);
                    var err1 = element.parents('.input-group');
                    $(err1).append(error);
                },
                rules: {
					branch_code: {
						required: true
					},
					staff_name: {
						required: true,
						maxlength: 50
					},
					login_id: {
						required: true,
						loginformart: true,
						maxlength: 30
					},
					password: {
						required: true,
						passwordlatinh: true,
						rangelength: [6,30]
					},
					piece_count: {
						required: true,
						number: true,
						maxlength: 11,
						greaterthan1: true
					}
                },
                messages: {
					branch_code: {
						required: "必須です"
					},
					staff_name: {
						required: "必須です",
						maxlength: "50文字以内で入力してください"
					},
					login_id: {
						required: "必須です",
						maxlength: "30桁の数字以内で入力してください"
					},
					password: {
						required: "必須です",
						rangelength: "6文字から30文字以内で入力してください"
					},
					piece_count: {
						required: "必須です",
						maxlength: "11文字以内で入力してください",
						number: "1以上の数字で入力してください"
					}
                },
                submitHandler: function(form) {
					var datastring = $("#staffs-form").serialize();
					$.ajax({type: "post", url: baseUrl+'/validate', data: datastring, success: function(result){
							if(result != 'true'){
								if(result == 0 || result == 3){
									$('.show_error label').html('1以上の数字で入力してください');
									return false;
								}
								if(result == 5){
									$('.show_error label').html('11文字以内で入力してください');
									return false;
								}
								if(result == 6){
									$('.show_error label').html('年月が正しくありません');
									return false;
								}
								if(result == 4){
									$('.unique-err').html('入力したログインＩＤは既存に存在されてます。');
									return false;
								}
								$('.show_error label').html('年月が正しくありません');
								return false;
							}
							if(!confirm('保存します、よろしいですか？')){
								return false;
							}
							form.submit();
						}
					});
                }
            });
        }
    }

    //when the dom has loaded setup form validation rules
    $(D).ready(function($) {
        validation.util.setupFormValidation();
    });

})(jQuery, window, document);

$(document).ready(function(){
	$('.text-right button.append').click(function () {
		var t = new Date();
		var month = t.getMonth() + 1;
		var year  = t.getFullYear();
		if (month < 10) { month = '0' + month; }
		var appelement = '<div class="div-element"><div class="input-group"><input type="text" class="form-control" name="pice_year[]" size="4" onchange="zen2han(this)"><div class="input-group-addon">年</div></div> <div class="input-group"><input type="text" class="form-control monthvalidate" name="pice_month[]" size="2" onchange="zen2han(this)"><div class="input-group-addon">月</div></div> <div class="input-group"><input type="text" class="form-control" name="pice_counts[]" size="2" onchange="zen2han(this)"><div class="input-group-addon">個</div></div> <button type="button" class="btn btn-danger btn-sm appended"><i class="glyphicon glyphicon-trash icon-white"></i></button></div>';
		$('.append-div-left').append(appelement);
	});
	
	//remove div appended
	$(document).on('click','button.appended', function(){
		var object = $('.append-div-left button.appended');
		//if(object.length > 1){
			$(this).parent().remove();
		//}
	});
	
	//append-div
	$(document).on('click','.div-element input[type=text]', function(){
		$('.show_error label').html('');
	});
	$(document).on('click','input[name=login_id]', function(){
		$('.unique-err').html('');
	});
});