$.validator.addMethod("startlessthanenddate", function(value,element) {
	fromdate = $('input[name=start_date]').val() ;
	todate   = $('input[name=end_date]').val() ;
			
	if(fromdate <= todate){
		return true;
	}else{
		return false;
	}
},"利用の期間が正しくありません");

$.validator.addMethod("customDateValidator",

    function(value, element) {
        var bits = value.split('-');
        var d = new Date(bits[0], bits[1] - 1, bits[2]);
        return d.getFullYear() == bits[0] && (d.getMonth() + 1) == bits[1] && d.getDate() == Number(bits[2]);

    },
    "Please enter a valid date"
);

//validation
(function ($, W, D)
{
    var validation = {};

    validation.util =
            {
                setupFormValidation: function ()
                {
                    //form validation rules
                    $("#event-form").validate({
                        rules: {
                            event_name: {
                                required: true,
                                maxlength: 50
                            },
                            start_date: {
                                required: true,
                                customDateValidator: true
                            },
                            end_date: {
                                required: true,
                                customDateValidator: true,
                                startlessthanenddate: true
                            },
                            piece_count: {
                                required: true,
                                maxlength: 10,
                                digits :true
                            },
                            target_sales: {
                                required: true,
                                maxlength: 10,
                                digits :true
                            }
                        },
                        messages: {
                            event_name: {
                                required: "必須です",
                                maxlength: "50文字以内で入力してください"
                            },
                            start_date: {
                                required: "必須です",
                                customDateValidator : "利用の期間が正しくありません"
                            },
                            end_date: {
                                required: "必須です",
                                customDateValidator : "利用の期間が正しくありません"
                            },
                            piece_count: {
                                required: "必須です",
                                maxlength: "10文字以内で入力してください",
                                digits : "数字で入力してください"
                            },
                            target_sales: {
                                required: "必須です",
                                maxlength: "10文字以内で入力してください",
                                digits : "数字で入力してください"
                            }
                        },
                        showErrors: function(errorMap, errorList) {
                            this.defaultShowErrors();
                            var element1 = $("label[for='piece_count']").clone();
                            var element2 = $("label[for='target_sales']").clone();
                            $("label[for='piece_count']").remove();
                            $("label[for='target_sales']").remove();
                            $(".piece_count_error").after(element1);
                            $(".target_sales_error").after(element2);
                        },
                        
                       
                    });
                    
                }
            }

    //when the dom has loaded setup form validation rules
    $(D).ready(function ($) {
        validation.util.setupFormValidation();
    });

})(jQuery, window, document);