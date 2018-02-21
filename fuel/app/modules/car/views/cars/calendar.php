<?php
	if(\Fuel\Core\Input::get('redirect') != '1')
	{
		Fuel\Core\Cookie::delete('car_calendar_url_redirect');
	}

?>
<?php echo Asset::js('util.js'); ?>

<div class="container">
    <h3>
        <span <?php if(\Cookie::get('sscode') != $car_sscode){echo "class='ss-name'";} ?>><?php echo $car_sscode ?> <?php echo $car_sscodename ?></span>の代車予約状況
        <button type="button" class="btn btn-info btn-sm" name="findss-btn">
            <i class="glyphicon glyphicon-flag icon-white"></i> 指定SS変更
        </button>
		<button type="button" class="btn btn-warning btn-sm" name="print-btn">
			<i class="glyphicon glyphicon-print icon-white"></i> 印刷
		</button>
    </h3>

    <div class="row">
		<?php if (count($listCar) == 0) { ?>
			<div class="alert alert-danger" role="alert">
				<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
				登録済み代車がありません
			</div>
		<?php } else {  ?>
			<div class="col-md-4">
				<div class="list-group" id="cars">
					<?php foreach ($listCar as $items) { ?>
						<a href="#" class="list-group-item car-list" id="<?php echo $items['car_id'] ?>"><?php echo $items['plate_no'] ?> <?php echo $items['car_name'] ?></a>
					<?php } ?>
				</div>
			</div>
			<div class="col-md-8">
				<p id="calendar"></p>
			</div>
		<?php } ?>
    </div>

</div>

<?php echo $ssfinder; ?>


<script>
    $(function (e)
    {
		$('button[name=print-btn]').on('click', function()
		{
			print();
		});


		car_id = Util.checkCookie("car_id");
		if (Util.checkCookie("currentMonth")!=""){

			month = Util.getCookie('currentMonth');
		}else{
			month = new Date();
		}
        var calendar = $('#calendar').fullCalendar({
            theme: true,
            header: {
                left: 'prev,next today',
                center: 'title',
                right: 'month,agendaDay'
            },
			//defaultDate: moment(month),
            allDaySlot: false,
            axisFormat: 'HH:mm',
            timeFormat: 'HH:mm',
            minTime: '00:00:00',
            maxTime: '24:00:00',
            selectable: true,
            editable: false,
			eventLimit: true,
			defaultDate: moment('<?php if( Fuel\Core\Cookie::get('car_calendar_url_redirect') !='') echo Fuel\Core\Cookie::get('car_calendar_url_redirect'); else echo date('Y-m')  ?>'),
            dayClick: function (date, jsEvent, view)
            {
                if (view.name == 'month') {
                    calendar.fullCalendar('gotoDate', date);
                    calendar.fullCalendar('changeView', 'agendaDay');
                }
            },
            select: function (start, end, jsEvent, view)
            {
                if (
					start.format() != end.add(-1, 's').format() ||
					view.name == 'agendaDay'
				) {
					var allDay = !start.hasTime() && !end.hasTime();
					var month = moment(start).format('YYYY-MM');
					Util.setCookie("currentMonth", month, 1);
					if(allDay){
						var startTime = moment(start).format('YYYY-MM-DD');
						var endTime = moment(end).format('YYYY-MM-DD');
						location.href = '<?php echo Uri::base(true) ?>car/reserve?start='+startTime+'&end='+endTime+'<?php if(Fuel\Core\Input::get('type_check')) echo '&type_check=1'?>';
					}else{
						var start = moment(start).format('YYYY-MM-DD HH:mm:ss');
						var end = moment(end).format('YYYY-MM-DD HH:mm:ss');
						if(moment(end).unix()- moment(start).unix() == '1799' ){
							var startTime = moment(start).format('YYYY-MM-DD HH:mm:ss');
							var endTime = moment(start).format('YYYY-MM-DD');
						}else{
							var startTime = moment(start).format('YYYY-MM-DD HH:mm:ss');
							var endTime =moment.unix(moment(end).unix()+1).format('YYYY-MM-DD HH:mm:ss');
						}
						location.href = '<?php echo Uri::base(true) ?>car/reserve?start='+startTime+'&end='+endTime+'<?php if(Fuel\Core\Input::get('type_check')) echo '&type_check=1'?>';
					}

				}
            },
			displayEventEnd: {
				month: true,
				basicWeek: true,
				"default": true
			},
			viewRender:function (view, element) {

				var b = $('#calendar').fullCalendar('getDate');
				Util.setCookieRedirect('<?php echo Uri::base(true) ?>',b.format('L'),'car_calendar');
			},
			eventClick: function (calEvent, jsEvent, view)
			{
				location.href = '<?php echo Uri::base(true) ?>car/reserve?reservation_no='+calEvent.reservation_no;

			},
			eventMouseover: function(calEvent, jsEvent) {
				var tooltip = '<div class="tooltipevent" style="padding:0px 10px 0px 10px;background:#ccc;position:absolute;z-index:10001;">' + calEvent.title + '</div>';
				$("body").append(tooltip);
				$(this).mouseover(function(e) {
					$(this).css('z-index', 10000);
					$('.tooltipevent').fadeIn('500');
					$('.tooltipevent').fadeTo('10', 1.9);
				}).mousemove(function(e) {
					$('.tooltipevent').css('top', e.pageY + 10);
					$('.tooltipevent').css('left', e.pageX + 20);
				});
			},

			eventMouseout: function(calEvent, jsEvent) {
				$(this).css('z-index', 8);
				$('.tooltipevent').remove();
			},
            lang: 'ja'
        });

        $('.car-list').on('click', function () {
			var myCalendar = $('#calendar');
			myCalendar.fullCalendar('removeEvents');
            var id = $(this).attr('id');
			Util.setCookie("car_id", id, 1)
			getEvent(id);
        });
        $('button[name=findss-btn]').on('click', function () {
            $('#ssfinder').modal();
            return false;
        });

        $('#ssfinder div.list-group a').on('click', function () {
            $('#ssfinder').modal('hide');
            return false;
        });

        $('#cars a').on('click', function () {
            $(this).addClass('active').siblings().removeClass('active');
            return false;
        });
		if(car_id){
			if($( "#cars a#"+car_id ).length){
				$( "#cars a#"+car_id ).trigger('click');
			}else{
				$( "#cars a" ).first().trigger('click');
			}
		}else{
			$( "#cars a" ).first().trigger('click');
		}

    });
	function getEvent(id){
		var myCalendar = $('#calendar');
            myCalendar.fullCalendar();
            $.ajax({
                type: "POST",
                url: "<?php echo Uri::base(true) ?>car/calendar/get_booking_data",
                dataType: 'json',
                data: {id: id},
                success: function (data) {
                    $(data).each(function (index) {
                        myCalendar.fullCalendar('renderEvent', data[index] , true );
                    });

                }
        });
	}

</script>
