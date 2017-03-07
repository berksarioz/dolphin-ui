<?php

class BrowseController extends VanillaController {

	function beforeAction() {

	}

	function index() {
		$this->set('field', "Browse");
		$this->set('segment', "index");
		$this->set('uid', $_SESSION['uid']);


  }

	function afterAction() {

	}

}
