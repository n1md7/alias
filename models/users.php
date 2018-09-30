<?php
class UsersModel extends Model{
	public function index(){
		/* 
			returns json dictionary of words
			based on category selection
		*/
		$cookie = filter_input_array(INPUT_COOKIE, FILTER_SANITIZE_STRING);
		$post = filter_input_array(INPUT_POST, FILTER_SANITIZE_STRING);

		header('Content-type: application/json');

		if( 
			!isset($cookie['lang']) || !isset($post['cat_ids'])
		){
			exit(
				json_encode(
					array('Error' => $GLOBALS['lang'][$_COOKIE['lang']]['ERROR_TEXT_EMPTY_VALS_SEL_SOMETHING'])
				)
			);
		}else{

			$fullDict = array();

			if(sizeof($post['cat_ids']) == 0){
				exit(
					json_encode(
						['Error' => $GLOBALS['lang'][$_COOKIE['lang']]['ERROR_TEXT_PLEASE_SELECT_ATLEAS_ONE']]
					)
				);
			}

			foreach ($post['cat_ids'] as $value) {
				
				$this->query('
						SELECT words.cat_id, words.word, categories.cat_name FROM words 
						INNER JOIN categories
						ON words.cat_id = categories.cat_id
						WHERE words.lang = :lang 
						AND words.cat_id = :cat_id
						LIMIT 10000
					');
				$this->bind(':lang', empty($cookie['lang'])?'en':$cookie['lang']);
				$this->bind(':cat_id', $value['name']);

				$fullDict[] = $this->resultSet();
			}

			exit(
				json_encode(
					$fullDict
				)
			);
		}

		return;
	}

	public function categories(){
		/* 
			returns json categories
		*/
		$cookie = filter_input_array(INPUT_COOKIE, FILTER_SANITIZE_STRING);
		$post = filter_input_array(INPUT_POST, FILTER_SANITIZE_STRING);

		header('Content-type: application/json');

		$this->query('
				SELECT COUNT(*) AS length, categories.cat_name, categories.cat_id FROM categories
				INNER JOIN words
				ON words.cat_id = categories.cat_id
				WHERE words.lang = :lang
				GROUP BY words.cat_id
				LIMIT 300
			');
		$this->bind(':lang', empty($cookie['lang'])?'en':$cookie['lang']);

		exit(
			json_encode(
				$this->resultSet()
			)
		);

		return;
	}
}



