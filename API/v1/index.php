<?php
	error_reporting(0);
	header('Access-Control-Allow-Origin: *');
	header('Content-Type: application/json');

	$data = ['ok'=>false, 'code'=>null, 'message'=>null, 'result'=>[]];
	if ($_SERVER['REQUEST_METHOD'] == 'POST') {
		require './config/config.php';
		$db = new dbmysqli;
    	$db->dbConnect();
    	require './helpers/functions.php';
		extract($_REQUEST);
		$action = strtolower(trim(getenv('ORIG_PATH_INFO') ? : getenv('PATH_INFO'), '/'));
		if ($action == 'financelogin') {
			if (isset($login) && isset($password)) {
				$password = md5($password);
				$finance = $db->selectWhere('finance',[
                    [
                        'login'=>$login,
                        'cn'=>'='
                    ],
                    [
                        'pass_word'=>$password,
                        'cn'=>'='
                    ],
                ]);
                if ($finance->num_rows) {
                	$finance = mysqli_fetch_assoc($finance);
                	if (md5($finance['pass_word']) == md5($password)) {
                		$data['ok'] = true;
                		$data['code'] = 200;
                		$data['message'] = 'Loggid in successfully';
                		foreach ($finance as $key => $value) $data['result'][$key] = $value;
                	}else{
                		$data['code'] = 403;
                		$data['message'] = 'password is invalid';
                	}
                }else{
                	$data['code'] = 403;
                	$data['message'] = 'login or password is invalid';
                }
			}else{
				$data['code'] = 402;
                $data['message'] = 'login and password is required';
			}
		}else if ($action == 'addincomedirection') {
			if (isset($token) && isset($name)) {
                if (isManager($token)) {
                	$description = ($description) ? $description : '';
                	$db->insertInto('income_direction',[
                		'name'=>$name,
                		'des'=>$description,
                	]);
                	$incomedirection = $db->selectWhere('income_direction',[
                		[
                			'id'=>0,
                			'cn'=>'=>'
                		]
                	]);
                	$data['ok'] = true;
            		$data['code'] = 200;
            		$data['message'] = 'income direction added successfully';
            		foreach ($incomedirection as $key => $value) $data['result'][$key] = $value;
                }else{
                	$data['code'] = 403;
                	$data['message'] = 'login or password is invalid';
                }
			}else{
				$data['code'] = 402;
                $data['message'] = 'token and income direction name is required';
			}
		}else{
			$data['code'] = 401;
            $data['message'] = 'Method not found';
		}
	}else{
		$data['code'] = 400;
		$data['message'] = "Method not allowed. Allowed Method: POST";
	}
	unset($data['result']['pass_word']);
	echo json_encode($data,  JSON_PRETTY_PRINT);
?>