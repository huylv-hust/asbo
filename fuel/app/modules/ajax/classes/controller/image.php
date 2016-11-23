<?php
namespace Ajax;

class Controller_Image extends \Controller_Template
{
	/**
	 * save image.
	 * @author Y.Hasegawa
	 * @since 1.0.0
	 * @param
	 * @return string result json
	*/
	public function post_save()
	{
		$config = array(
			'max_size' => 10240000,
			'ext_whitelist' => array('jpg', 'jpeg'),
		);

		\Upload::process($config);

		$output = array();

		if (\Upload::is_valid()) {
			$output['key'] = Model_Image::save(\Upload::get_files(0));
		} else {
			$error = \Upload::get_errors(0);
			if ($error['errors'][0]['error'] == '101') {
				$output['error'] = 'データ容量オーバーです';
			} else {
				$output['error'] = '画像アップロードに失敗しました';
			}
		}

		return new \Response(json_encode($output, 200));
	}

	/**
	 * output image.
	 * @author Y.Hasegawa
	 * @since 1.0.0
	 * @param
	 * @return string image data
	*/
	public function get_index()
	{
		$url = \Input::server('REQUEST_URI');

		// first check cache.
		$file = Model_Image::get($url);

		if ($file == false) {
			$key = $_match = null;
			if (preg_match('/([0-9a-z]{64})(\?.*){0,1}$/', $url, $_match))
			{
				$key = $_match[1];
			} else {
				return \Response::forge(null, 404);
			}

			$file = Model_Image::get($key);

			$max_width = intval(\Input::get('w', 0));
			if ($max_width > 0) {
				$file = Model_Image::saveResized($file, $max_width, $url);
			}
		}

		return new \Response($file['image'], 200, array(
			'Content-Type' => $file['type'],
			'Content-Length' => strlen($file['image']),
		));
	}
}
