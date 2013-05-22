<?php

namespace Controller;

class Views extends \Controller
{
	public function action_index()
	{
		return \Response::forge(\View::forge('index'));
	}

	public function action_404()
	{
		return \Response::forge(\View::forge('404'));
	}
}

?>