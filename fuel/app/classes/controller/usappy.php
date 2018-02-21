<?php

class Controller_Usappy extends Controller_Template
{

	public $sscode;
	public $ssname;

	public function before()
	{

		parent::before();

		//Check cookie
		if ( ! Cookie::get('sscode') || ! Cookie::get('ss_name'))
		{
			\Response::redirect(Fuel\Core\Uri::base().'sss');
		}

		$this->sscode = Cookie::get('sscode');
		$this->ssname = Cookie::get('ss_name');

		//head - css, js
		$this->template->head = View::forge('partials/head');
		//navigator
		$this->template->navigator = View::forge('partials/navi');
		//footer - footer of page
		$this->template->footer = View::forge('partials/footer');
	}
	/**
	 * get_cookie_ssinfo
	 * @author MiengTQ
	 * @since 1.0.0
	 * @param
	 * @return ssinfo
	*/
	public function get_cookie_ssinfo($screen_name)
	{
	    $data = array();
	    $data[$screen_name.'_sscode'] = $this->get_cookie_sscode($screen_name.'_sscode');
	    $ssname = $this->get_cookie_sscode($screen_name.'_sscodename');

		if($ssname == '')
		{
			$ssname = \Cookie::get('ss_name');
		}
		$data[$screen_name.'_sscodename'] = $ssname;
	    return $data;
	}
	/**
	 * get_cookie_sscode
	 * @author MiengTQ
	 * @since 1.0.0
	 * @param
	 * @return sscode
	*/

	public function get_cookie_sscode($cookie_name)
	{
	    if(\Cookie::get($cookie_name))
	    {
			$sscode = \Cookie::get($cookie_name);
	    }
	    else
	    {
			$sscode = '';
	    }

	    return $sscode;
	}

	/**
	 * get_cookie_ssname
	 * @author MiengTQ
	 * @since 1.0.0
	 * @param
	 * @return ssname
	*/
	public function get_cookie_ssname($cookie_name)
	{
	    if(\Cookie::get($cookie_name))
	    {
			$ssname = \Cookie::get($cookie_name);
	    }
	    else
	    {
			$ssname = \Cookie::get('ss_name');
	    }

	    return $ssname;
	}
	/**
	 * set_cookie_ssinfo
	 * @author MiengTQ
	 * @since 1.0.0
	 * @param
	 * @return
	*/
	public function set_cookie_ssinfo($screen_name,$sscode,$sscodename)
	{
	    \Cookie::set($screen_name.'_sscode',$sscode, 60 * 60 * 24);
	    \Cookie::set($screen_name.'_sscodename',$sscodename, 60 * 60 * 24);
	}
	/**
	 * set_cookie_ssinfo
	 * @author MiengTQ
	 * @since 1.0.0
	 * @param
	 * @return
	*/
	public function create_cookie_ssinfo($screen_name)
	{
		$sscode = \Cookie::get($screen_name.'_sscode');
	    if( !$sscode )
	    {
			$sscode = \Cookie::get('sscode');
			\Cookie::set($screen_name.'_sscode',$sscode, 60 * 60 * 24);
	    }

		$sscode_name = \Cookie::get($screen_name.'_sscodename');
	    if( ! $sscode_name)
	    {
			$sscode_name = \Cookie::get('ss_name');
			\Cookie::set($screen_name.'_sscodename',$sscode_name, 60 * 60 * 24);
	    }

		$data = array();
		$data[$screen_name.'_sscode'] = $sscode;
		$data[$screen_name.'_sscodename'] = $sscode_name;

		return $data;
	}

}
