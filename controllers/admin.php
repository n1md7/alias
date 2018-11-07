<?php
class Admin extends Controller{
	private function isloggedin(){
		return !isset($_SESSION["logged_in"])?false:true;
	}
	private function restrictview(){
		if(!$this->isloggedin()):
			header('Location: '.ROOT_URL.'admin');
			return;
		endif;
	}
	protected function index(){
		if($this->isloggedin()):
			header('Location: '.ROOT_URL.'admin/words');
		endif;
		$viewmodel = new AdminModel();
		$this->returnView($viewmodel->index(), 'admin.html');
	}

	protected function constants(){
		if(!$this->isloggedin()):
			header('Location: '.ROOT_URL.'admin');
			return;
		endif;
		$viewmodel = new AdminModel();
		$this->returnView($viewmodel->constants(), 'admin.html');
	}

	protected function editConstants(){
		if(!$this->isloggedin()):
			header('Location: '.ROOT_URL.'admin');
			return;
		endif;
		$viewmodel = new AdminModel();
		$this->returnView($viewmodel->editConstants(), false);
	}

	protected function wordsEdit(){
		if(!$this->isloggedin()):
			header('Location: '.ROOT_URL.'admin');
			return;
		endif;
		$viewmodel = new AdminModel();
		$this->returnView($viewmodel->wordsEdit(), false);
	}
	protected function login(){
		$viewmodel = new AdminModel();
		$this->returnView($viewmodel->login(), false);
	}

	protected function words(){
		if(!$this->isloggedin()):
			header('Location: '.ROOT_URL.'admin');
			return;
		endif;
		$viewmodel = new AdminModel();
		$this->returnView($viewmodel->words(), 'admin.html');
	}
	
}
