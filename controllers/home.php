<?php
class Home extends Controller{
	protected function Index(){
		$viewmodel = new HomeModel();
		$this->returnView($viewmodel->Index(), isMobile()?'index_mobile.html':'index.html');
	}
	
}
