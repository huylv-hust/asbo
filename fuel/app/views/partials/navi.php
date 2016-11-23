<nav class="navbar navbar-inverse navbar-fixed-top" role="navigation">
    <div id="login-bar">
        <span class="glyphicon glyphicon-flag"></span>
            <?php if(isset($api[0]['sscode'])){ echo $api[0]['sscode']; }else{ echo Cookie::get('sscode'); } ?>
            <?php if(isset($api[0]['ss_name'])){ echo $api[0]['ss_name']; }else{ echo Cookie::get('ss_name'); } ?>
    </div>
    <div class="container-fluid">
        <div class="navbar-header">
            <a class="navbar-brand" href="<?php echo Uri::base() ?>">Usappyオートサービス</a>
        </div>
        <div class="collapse navbar-collapse">
            <ul class="nav navbar-nav">
                <li class="dropdown">
                    <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-expanded="false">作業 <span class="caret"></span></a>
                    <ul class="dropdown-menu" role="menu">
                        <li><a href="<?php echo Uri::base() ?>reserve/calendar">作業予約状況</a></li>
                        <li><a href="<?php echo Uri::base() ?>reserve/menu">作業メニュー設定</a></li>
                        <li><a href="<?php echo Uri::base() ?>reserve/pit">作業ピット設定</a></li>
                    </ul>
                </li>
                <li class="dropdown">
                    <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-expanded="false">リペア <span class="caret"></span></a>
                    <ul class="dropdown-menu" role="menu">
                        <li><a href="<?php echo Uri::base() ?>repair/calendar">リペア予約状況</a></li>
                        <li><a href="<?php echo Uri::base() ?>repair/staffs">技術者リスト</a></li>
                        <li><a href="<?php echo Uri::base() ?>repair/staffschedule">技術者別スケジュール</a></li>
						<li><a href="<?php echo Uri::base() ?>repair/plan">イベント目標</a></li>
						<li><a href="<?php echo Uri::base() ?>repair/summary">リペア集計</a></li>
					</ul>
                </li>
                <li class="dropdown">
                    <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-expanded="false">代車 <span class="caret"></span></a>
                    <ul class="dropdown-menu" role="menu">
                        <li><a href="<?php echo Uri::base() ?>car/cars">代車リスト</a></li>
                        <li><a href="<?php echo Uri::base() ?>car/calendar">代車予約状況</a></li>
                    </ul>
                </li>
            </ul>
			<ul class="nav navbar-nav navbar-right">
				<li>
					<a href="<?php echo Uri::base() ?>sss">
					<i class="glyphicon glyphicon-home"></i>
					SSサポートサイトへ戻る</a>
				</li>
			</ul>
        </div>
    </div>
</nav>