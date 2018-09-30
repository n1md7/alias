<?php
class Users extends Controller{
	protected function index(){
		$viewmodel = new UsersModel();
		$this->returnView($viewmodel->index(), false);
	}

	protected function categories(){
		$viewmodel = new UsersModel();
		$this->returnView($viewmodel->categories(), false);
	}
}
