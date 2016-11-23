<?php
namespace Ajax;

class Model_Image
{
	/**
	 * save the image on redis.
	 * @author Y.Hasegawa
	 * @since 1.0.0
	 * @param array upload image
	 * @return string saved key
	*/
	public static function save($file)
	{
		$file['image'] = file_get_contents($file['file']);

		$imagick = new \Imagick();
		try
		{
			$imagick->readImageBlob($file['image']);
			$imagick->stripImage();

			$orientation = $imagick->getImageOrientation();

			switch ($orientation)
			{
				case \Imagick::ORIENTATION_BOTTOMRIGHT:
					$imagick->rotateimage("#000", 180); // rotate 180 degrees
				break;

				case \Imagick::ORIENTATION_RIGHTTOP:
					$imagick->rotateimage("#000", 90); // rotate 90 degrees CW
				break;

				case \Imagick::ORIENTATION_LEFTBOTTOM:
					$imagick->rotateimage("#000", -90); // rotate 90 degrees CCW
				break;
			 }

			 $file['image'] = $imagick->getImageBlob();

		}
		catch (\ImagickException $ex)
		{
			throw new Exception('wrong image data:'.$ex->getMessage());
		}

		$key = hash('sha256', $file['image']);

		$redis = \Redis_Db::forge();
		$redis->set($key, serialize($file));

		return $key;
	}

	/**
	 * load the image from redis.
	 * @author Y.Hasegawa
	 * @since 1.0.0
	 * @param string saved key
	 * @return array image data
	*/
	public static function get($key)
	{
		$redis = \Redis_Db::forge();
		return unserialize($redis->get($key));
	}

	/**
	 * save the resized image on redis.
	 * @author Y.Hasegawa
	 * @since 1.0.0
	 * @param array upload image
	 * @param array max width(px)
	 * @return string saved key
	*/
	public static function saveResized($file, $max_width, $url)
	{
		$imagick = new \Imagick();
		try
		{
			$imagick->readImageBlob($file['image']);
			$imagick->stripImage();
			$width = $imagick->getImageWidth();
			$height = $imagick->getImageHeight();

			if ($width > $max_width)
			{
				$rate = $max_width / (float)$width;
				$imagick->resizeImage(
					$width * $rate,
					$height * $rate,
					\Imagick::FILTER_CUBIC,
					1
				);
			}
			$file['image'] = $imagick->getImageBlob();

			// save
			$redis = \Redis_Db::forge();
			$redis->setex($url, 300, serialize($file));

			return $file;
		}
		catch (\ImagickException $ex)
		{
			throw new Exception('wrong image data:'.$ex->getMessage());
		}
	}

}
