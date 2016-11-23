<?php
namespace Fuel\Tasks;

class Backup
{
	public static function run()
	{
		$sql = 'SELECT * FROM reservation_personal';

		$rows = \Fuel\Core\DB::query($sql)->execute('oracle')->as_array();

		$filename = '/www/backup/oracle.'.date('d').'.zip';

		$zip = new \ZipArchive();
		$zip->open($filename, \ZipArchive::CREATE);
		$zip->addFromString('reservation_personal.php', serialize($rows));
		$zip->close();
	}
}
