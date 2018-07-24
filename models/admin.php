<?php
class AdminModel extends Model{
	public function index(){
		$post = filter_input_array(INPUT_POST, FILTER_SANITIZE_STRING);
		
		return;
	}

	public function constants(){
		// $post = filter_input_array(INPUT_POST, FILTER_SANITIZE_STRING);
		
		return [
			'lang' => $GLOBALS['lang']
		];
	}

	public function wordsEdit(){
		$post = filter_input_array(INPUT_POST, FILTER_SANITIZE_STRING);
		
		header('Content-type: application/json');

		if(!isset($post['action'])){
			exit(json_encode(['Error'=>':P']));
		}

		switch (true):

			case $post['action'] == 'categoryAdd' && isDefined($post['name']):
				$this->query('INSERT INTO categories (cat_name) VALUES (:name)');
				$this->bind(':name', $post['name']);
				$this->execute();
				if($this->lastInsertId()){
					exit(json_encode([
						'Success'=>'A Category has been saved successfully!',
						'id'=>$this->lastInsertId()
					]));
				}else{
					exit(json_encode(['Error'=>'Unsuccessful try!']));
				}
			break;

			case $post['action'] == 'wordAdd' && isDefined($post['cat_id']) && isDefined($post['words']) && isDefined($post['lang']):
				$idCollector = [];
				foreach ($post['words'] as $key => $value) {
					$this->query('INSERT INTO words (cat_id, word, lang) VALUES (:cat_id, :word, :lang)');
					$this->bind(':cat_id', $post['cat_id']);
					$this->bind(':word', $value);
					$this->bind(':lang', $post['lang']);
					$this->execute();
					$idCollector[] = $this->lastInsertId();
				}
				if(sizeof($idCollector) > 0){
					exit(json_encode([
						'Success'=>'The Words have been saved successfully!',
						'ids' => $idCollector
					]));
				}else{
					exit(json_encode(['Error'=>'Unsuccessful try!']));
				}
			break;

			case $post['action'] == 'categoryRemove' && isDefined($post['id']):
				$this->query('DELETE FROM categories WHERE cat_id = :id');
				$this->bind(':id', $post['id']);
				$this->execute();
				exit(json_encode([
					'Success'=>'A Category has been removed successfully!'
				]));
			break;

			case $post['action'] == 'wordRemove' && isDefined($post['id']):
				$this->query('DELETE FROM words WHERE word_id = :id');
				$this->bind(':id', $post['id']);
				$this->execute();
				exit(json_encode([
					'Success'=>'A Word has been removed successfully!'
				]));
			break;

			case $post['action'] == 'wordsByCategory' && isDefined($post['cat_id']) && isDefined($post['lang']):
				$this->query('
						SELECT words.cat_id,words.word_id,  words.word, categories.cat_name FROM words 
						INNER JOIN categories
						ON words.cat_id = categories.cat_id
						WHERE words.lang = :lang 
						AND words.cat_id = :cat_id
						ORDER BY words.word_id DESC
						LIMIT 10000
					');
				$this->bind(':cat_id', $post['cat_id']);
				$this->bind(':lang', $post['lang']);

				exit(json_encode([
					'Words' => $this->resultSet()
				]));
			break;
			
			default:
				exit(json_encode(['Error'=>'Empty Value']));
			break;
		endswitch;

		return;
	}



	public function words(){
		$this->query('SELECT * FROM categories LIMIT 1000');

		$categories = $this->resultSet();

		return [
			'categories' => $categories
		];
	}


	public function editConstants(){
		$post = filter_input_array(INPUT_POST, FILTER_SANITIZE_STRING);

		header('Content-type: application/json');

		if( 
			isset($post['lang']) && !empty($post['lang']) &&
			isset($post['key']) && !empty($post['key']) &&
			isset($post['value']) && !empty($post['value'])
		){
		
			if(!is_writable(__DIR__.'/../core/locale.json')){
				exit(
					json_encode(
						[
							'Error'=> __DIR__.'/../core/locale.json isn\'t writable!',
						]
					)
				);
			}
			if(!is_readable(__DIR__.'/../core/locale.json')){
				exit(
					json_encode(
						[
							'Error'=> __DIR__.'/../core/locale.json isn\'t readable!',
						]
					)
				);
			}

			$CurrentConstants = $GLOBALS['lang'];

			$CurrentConstants[$post['lang']][$post['key']] = htmlspecialchars($post['value']);

			$langFile = fopen(__DIR__.'/../core/locale.json', 'w+');

			fwrite($langFile, json_encode($CurrentConstants));
			@fclose($langFile);

			exit(
				json_encode(['Success' => 'A Constant has beeen changed successfully!'])
			);

		}else{
			exit(
				json_encode(['Error'=> ':P'])
			);
		}


		return;
	}

	public function login(){
		$post = filter_input_array(INPUT_POST, FILTER_SANITIZE_STRING);

		header('Content-type: application/json');

		if( 
			isset($post['action']) && 
			isDefined($post['username']) && 
			isDefined($post['password'])
		){

			$this->query('SELECT * FROM users WHERE username = :u AND password = :p');
			$this->bind(':u', $post['username']);
			$this->bind(':p', $post['password']);

			if( $this->rowCount() > 0){
				$_SESSION['logged_in'] = true;
				exit(
					json_encode(['Success' => 'Yay, correct one!'])
				);
				
			}else{
				exit(
					json_encode(['Error' => 'Wrong Combination'])
				);
				
			}

		}else{
			exit(
				json_encode(['Error'=> ':P'])
			);
		}


		return;
	}

}



