//validation
$.validator.addMethod("hankana", function(value, element) {
        //^ァ-ン0-9\-\+\s\(\)]
	if(value.match(/^[\uFF65-\uFF9F0-9\-\+\s\(\)]+$/)){
		return true;
	}else{
		return false;
	}
 }, "半角カタカナを入力してください"
);
$.validator.addMethod("startlessthanenddate", function(value,element) {
	fromdate = $('input[name=start_time]').val();
	todate   = $('input[name=start_time]').val() ;
	if(fromdate < todate){
		return true;
	}else{
		return false;
	}
},"利用の期間が正しくありません");
//date format
$.validator.addMethod("dateformat", function(value,element) {
	if(value.match(/^\d{4}-\d{2}-\d{2}$/)){
		return true;
	}else{
		return false;
	}
},"利用の期間が正しくありません");

(function($,W,D)
{
    var validation = {};

    validation.util =
    {
        setupFormValidation: function()
        {
            //form validation rules
            $("#validation").validate({
                rules: {
					
                                        sscode:{
                                            number: true,
                                            rangelength: [6,6]
                                        },
                                        plate_no:{
                                            number: true,
                                            rangelength: [4,4]
                                        },
                                        start_time: {
						 dateformat:{
                                                        depends: function(element) {
                                                            if($(this).val()=='') return false;
                                                            return true;
							}
                                                }
					},
					end_time: {
						 dateformat:{
                                                        depends: function(element) {
                                                            if($(this).val()=='') return false;
                                                            return true;
							}
                                                }
					}
                },
                messages: {
					plate_no:{
                                            number: "正しくありません",
                                            rangelength: "4桁の数字で入力してください"
                                        },
                                        sscode:{
                                            number: "正しくありません",
                                            rangelength: "6桁の数字で入力してください"
                                        },
                                        start_time: {
						dateformat: "日付がが正しくありません"
					},
					end_time: {
						dateformat: "日付がが正しくありません",
					}
					
                },
                submitHandler: function(form) {
                    form.submit();
                }
            });
        }
    }

    //when the dom has loaded setup form validation rules
    $(D).ready(function($) {
        validation.util.setupFormValidation();
    });

})(jQuery, window, document);

 