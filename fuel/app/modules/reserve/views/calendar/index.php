<?php
	if(\Fuel\Core\Input::get('redirect') != '1')
	{
		Fuel\Core\Cookie::delete('reserve_calendar_url_redirect');
	}

?>
<div class="container">
	<h3>
		<span <?php if(\Cookie::get('sscode') != $reserve_sscode){echo "class='ss-name'";} ?>><?= $reserve_sscode ?> <?= $reserve_sscodename ?></span>の作業予約状況
		<button type="button" class="btn btn-info btn-sm" name="findss-btn">
			<i class="glyphicon glyphicon-flag icon-white"></i> 指定SS変更
		</button>
		<button type="button" class="btn btn-warning btn-sm" name="print-btn">
			<i class="glyphicon glyphicon-print icon-white"></i> 印刷
		</button>
	</h3>

	<p class="text-center">
		<a href="<?php echo Uri::base(true) ?>reserve/calendar">カレンダー表示</a>
		|
		<a href="<?php echo Uri::base(true) ?>reserve/list">リスト表示</a>
	</p>

	<p id="calendar"></p>

</div>

<?php echo $ssfinder; ?>

<script>
    $(function (e)
    {
		$('button[name=print-btn]').on('click', function()
		{
			print();
		});

		if (Util.checkCookie("reserve_sscode")!=""){
			sscode = Util.getCookie('reserve_sscode');
		}else{
			sscode = Util.getCookie('sscode');
		}
        var calendar = $('#calendar').fullCalendar({
			events: <?php echo json_encode($events) ?>,
            theme: true,
            header: {
                left: 'prev,next today',
                center: 'title',
                right: 'month,agendaDay'
            },
            dayClick: function (date, jsEvent, view)
            {
                if (view.name == 'month') {
                    calendar.fullCalendar('gotoDate', date);
                    calendar.fullCalendar('changeView', 'agendaDay');
                }
            },
            allDaySlot: false,
            axisFormat: 'HH:mm',
            timeFormat: 'HH:mm',
            minTime: '00:00:00',
            maxTime: '24:00:00',
            scrollTime: '08:00:00',
            selectable: true,
            editable: false,
			eventLimit: true,
			defaultDate: moment('<?php if( Fuel\Core\Cookie::get('reserve_calendar_url_redirect') !='') echo Fuel\Core\Cookie::get('reserve_calendar_url_redirect'); else echo date('Y-m')  ?>'),
			eventMouseover: function(calEvent, jsEvent) {
				var tooltip = '<div class="tooltipevent" style="padding:0px 10px 0px 10px;background:#ccc;position:absolute;z-index:10001;">' + calEvent.title + '</div>';
				$("body").append(tooltip);
				$(this).mouseover(function(e) {
					// $(this).css('z-index', 10000);
					$('.tooltipevent').fadeIn('500');
					$('.tooltipevent').fadeTo('10', 1.9);
				}).mousemove(function(e) {
					$('.tooltipevent').css('top', e.pageY + 10);
					$('.tooltipevent').css('left', e.pageX + 20);
				});
			},

			eventMouseout: function(calEvent, jsEvent) {
				// $(this).css('z-index', 8);
				$('.tooltipevent').remove();
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
						location.href = '<?php echo Uri::base(true) ?>reserve/reserve?start='+startTime+'&end='+endTime+'&sscode='+sscode+'<?php if(Fuel\Core\Input::get('type_check')) echo '&type_check=1'?>';
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
						location.href = '<?php echo Uri::base(true) ?>reserve/reserve?start='+startTime+'&end='+endTime+'&sscode='+sscode+'<?php if(Fuel\Core\Input::get('type_check')) echo '&type_check=1'?>';
					}

				}
            },
			viewRender:function (view, element) {
				var b = $('#calendar').fullCalendar('getDate');
				Util.setCookieRedirect('<?php echo Uri::base(true) ?>',b.format('L'),'reserve_calendar');
				if (calendar != undefined)
				{
					setBorderColor(view.name === 'month' ? null : '#fff', b);
				}
			},
			eventClick: function (calEvent, jsEvent, view)
			{
				Util.pleaseWait();
				location.href = '<?php echo Uri::base(true) ?>reserve/reserve?reservation_no='+calEvent.reservation_no;
			},
            lang: 'ja'
        });

		var setBorderColor = function(color, target)
		{
			var targetDate = target.format('L');
			var prevData = target.add(-1, 'days').format('L');
			var nextDate = target.add(2, 'days').format('L');

			$.each(calendar.fullCalendar('clientEvents'), function()
			{
				if (targetDate == this.start.format('L'))
				{
					this.borderColor = color;
					calendar.fullCalendar('renderEvent', this);
				}
				else if (prevData == this.start.format('L') || nextDate == this.start.format('L'))
				{
					this.borderColor = null;
					calendar.fullCalendar('renderEvent', this);
				}
			});
		}

        $('button[name=add-btn]').on('click', function () {
            location.href = 'reserve';
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

	});

</script>
