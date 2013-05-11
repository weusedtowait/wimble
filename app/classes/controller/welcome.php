<?php

namespace Controller;

class Welcome extends \Controller_Rest {
	public function get_index() {
		return $this->response(array('lucasgay'));
	}
}

?>