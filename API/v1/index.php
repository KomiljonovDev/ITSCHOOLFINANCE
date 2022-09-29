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
                		'timestamp'=>strtotime('now')
                	]);
                	$incomedirection = $db->selectWhere('income_direction',[
                		[
                			'id'=>0,
                			'cn'=>'>='
                		]
                	]);
                	$data['ok'] = true;
            		$data['code'] = 200;
            		$data['message'] = 'income direction added successfully';
            		foreach ($incomedirection as $key => $value) $data['result'][$key] = $value;
                }else{
                	$data['code'] = 403;
                	$data['message'] = 'token is invalid';
                }
			}else{
				$data['code'] = 402;
                $data['message'] = 'token and income direction name is required';
			}
		}else if ($action == 'removeincomedirection') {
			if (isset($token) && isset($id)) {
                if (isManager($token)) {
                	$db->delete('income_direction',[
                		[
                			'id'=>$id,
                			'cn'=>"="
                		],
                	]);
                	$incomedirection = $db->selectWhere('income_direction',[
                		[
                			'id'=>0,
                			'cn'=>'>='
                		]
                	]);
                	$data['ok'] = true;
            		$data['code'] = 200;
            		$data['message'] = 'income direction deleted successfully';
            		foreach ($incomedirection as $key => $value) $data['result'][$key] = $value;
                }else{
                	$data['code'] = 403;
                	$data['message'] = 'token is invalid';
                }
			}else{
				$data['code'] = 402;
                $data['message'] = 'token and income direction name is required';
			}
		}else if($action == 'addworker'){
			if (isset($token) && isset($login) && isset($password) && isset($income_direction_id) && isset($name) && isset($lastname)) {
                if (isManager($token)) {
                	$description = ($description) ? $description : '';
                	$percent = ($percent) ? $percent : 50.00;
                	$direction_id = $db->selectWhere('income_direction',[
                		[
                			'id'=>$income_direction_id,
                			'cn'=>'='
                		]
                	]);
                	if ($direction_id->num_rows) {
                		$login = trim($login);
	                	$db->insertInto('workers',[
	                		'income_direction_id'=>$income_direction_id,
	                		'login'=>$login,
	                		'pass_word'=>md5($password),
	                		'name'=>$name,
	                		'lastname'=>$lastname,
	                		'des'=>$description,
	                		'percent'=>$percent,
	                		'token'=>md5(uniqid($login)),
	                		'timestamp'=>strtotime('now')
	                	]);
	                	$incomedirection = $db->selectWhere('workers',[
	                		[
	                			'id'=>0,
	                			'cn'=>'>='
	                		]
	                	]);
	                	$data['ok'] = true;
	            		$data['code'] = 200;
	            		$data['message'] = 'worker added successfully';
	            		foreach ($incomedirection as $key => $value) $data['result'][$key] = $value;
                	}else{
                		$data['code'] = 403;
                		$data['message'] = 'income_direction_id is invalid';
                	}
                }else{
                	$data['code'] = 403;
                	$data['message'] = 'token is invalid';
                }
			}else{
				$data['code'] = 402;
                $data['message'] = 'token,login,password,income_direction_id,worker name and worker lastname name is required';
			}
		}else if($action == 'removeworker'){
			if (isset($token) && isset($id)) {
                if (isManager($token)) {
                	$db->delete('workers',[
                		[
                			'id'=>$id,
                			'cn'=>"="
                		],
                	]);
                	$workers = $db->selectWhere('workers',[
                		[
                			'id'=>0,
                			'cn'=>'>='
                		]
                	]);
                	$data['ok'] = true;
            		$data['code'] = 200;
            		$data['message'] = 'workers deleted successfully';
            		foreach ($workers as $key => $value) $data['result'][$key] = $value;
                }else{
                	$data['code'] = 403;
                	$data['message'] = 'token is invalid';
                }
			}else{
				$data['code'] = 402;
                $data['message'] = 'token and income direction id is required';
			}
		}else if($action == 'workerlogin'){
			if (isset($login) && isset($password)) {
				$password = md5($password);
				$finance = $db->selectWhere('workers',[
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
		}else if($action == 'adddirection'){
			if (isset($token) && isset($name)) {
                if (isManager($token)) {
                	$description = ($description) ? $description : '';
                	$monthly_payment  = ($monthly_payment) ? $monthly_payment : '250000.00';
                	$db->insertInto('directions',[
                		'name'=>$name,
                		'des'=>$description,
                		'monthly_payment'=>$monthly_payment,
                		'timestamp'=>strtotime('now')
                	]);
                	$incomedirection = $db->selectWhere('directions',[
                		[
                			'id'=>0,
                			'cn'=>'>='
                		]
                	]);
                	$data['ok'] = true;
            		$data['code'] = 200;
            		$data['message'] = 'direction added successfully';
            		foreach ($incomedirection as $key => $value) $data['result'][$key] = $value;
                }else{
                	$data['code'] = 403;
                	$data['message'] = 'token is invalid';
                }
			}else{
				$data['code'] = 402;
                $data['message'] = 'token and direction name is required';
			}
		}else if($action == 'removedirection'){
			if (isset($token) && isset($id)) {
                if (isManager($token)) {
                	$db->delete('directions',[
                		[
                			'id'=>$id,
                			'cn'=>"="
                		],
                	]);
                	$workers = $db->selectWhere('directions',[
                		[
                			'id'=>0,
                			'cn'=>'>='
                		]
                	]);
                	$data['ok'] = true;
            		$data['code'] = 200;
            		$data['message'] = 'direction deleted successfully';
            		foreach ($workers as $key => $value) $data['result'][$key] = $value;
                }else{
                	$data['code'] = 403;
                	$data['message'] = 'token is invalid';
                }
			}else{
				$data['code'] = 402;
                $data['message'] = 'token and direction id is required';
			}
		}else if($action == 'newgroup'){
			if (isset($token) && isset($direction_id) && isset($name)) {
				$worker = $db->selectWhere('workers',[
					[
						'token'=>$token,
						'cn'=>'=',
					],
				]);
                if ($worker->num_rows) {
                	$direction = $db->selectWhere('directions',[
						[
							'id'=>$direction_id,
							'cn'=>'=',
						],
					]);
					if ($direction->num_rows) {
						$description = ($description) ? $description : '';
	                	$db->insertInto('groups',[
	                		'direction_id'=>$direction_id,
	                		'name'=>$name,
	                		'des'=>$description,
	                		'timestamp'=>strtotime('now')
	                	]);
	                	$groups = $db->selectWhere('groups',[
	                		[
	                			'id'=>0,
	                			'cn'=>'>='
	                		]
	                	]);
	                	$data['ok'] = true;
	            		$data['code'] = 200;
	            		$data['message'] = 'group added successfully';
	            		foreach ($groups as $key => $value) $data['result'][$key] = $value;
					}else{
						$data['code'] = 403;
                		$data['message'] = 'direction_id is invalid';
					}
                }else{
                	$data['code'] = 403;
                	$data['message'] = 'token is invalid';
                }
			}else{
				$data['code'] = 402;
                $data['message'] = 'token, direction_id and group name (name) is required';
			}
		}else if($action == 'removegroup'){
			if (isset($token) && isset($group_id)) {
				$worker = $db->selectWhere('workers',[
					[
						'token'=>$token,
						'cn'=>'=',
					],
				]);
                if ($worker->num_rows) {
                	$group = $db->selectWhere('groups',[
						[
							'id'=>$group_id,
							'cn'=>'=',
						],
					]);
					if ($group->num_rows) {
	                	$db->delete('groups',[
	                		[
	                			'id'=>$group_id,
	                			'cn'=>"="
	                		],
	                	]);
	                	$groups = $db->selectWhere('groups',[
	                		[
	                			'id'=>0,
	                			'cn'=>'>='
	                		]
	                	]);
	                	$data['ok'] = true;
	            		$data['code'] = 200;
	            		$data['message'] = 'group deleted successfully';
	            		foreach ($groups as $key => $value) $data['result'][$key] = $value;
					}else{
						$data['code'] = 403;
                		$data['message'] = 'group_id is invalid';
					}
                }else{
                	$data['code'] = 403;
                	$data['message'] = 'token is invalid';
                }
			}else{
				$data['code'] = 402;
                $data['message'] = 'token and group_id is required';
			}
		}else if($action == 'addstudent'){
			if (isset($token) && isset($group_id) && isset($name) && isset($lastname)) {
				$worker = $db->selectWhere('workers',[
					[
						'token'=>$token,
						'cn'=>'=',
					],
				]);
                if ($worker->num_rows) {
                	$worker = mysqli_fetch_assoc($worker);
                	$groups = $db->selectWhere('groups',[
						[
							'id'=>$group_id,
							'cn'=>'=',
						],
					]);
					if ($groups->num_rows) {
						$grant_percent = ($grant_percent) ? $grant_percent : '00.00';
	                	$db->insertInto('students',[
	                		'group_id'=>$group_id,
	                		'teacher_id'=>$worker['id'],
	                		'name'=>$name,
	                		'lastname'=>$lastname,
	                		'grant_percent'=>$grant_percent,
	                		'pay_date'=>($grant_percent=='100.00') ? strtotime('now') : '',
	                		'is_paid'=>($grant_percent=='100.00') ? 1 : 0,
	                		'timestamp'=>strtotime('now')
	                	]);
	                	$students = $db->selectWhere('students',[
	                		[
	                			'id'=>0,
	                			'cn'=>'>='
	                		]
	                	]);
	                	$data['ok'] = true;
	            		$data['code'] = 200;
	            		$data['message'] = 'student added successfully';
	            		foreach ($students as $key => $value) $data['result'][$key] = $value;
					}else{
						$data['code'] = 403;
                		$data['message'] = 'group_id is invalid';
					}
                }else{
                	$data['code'] = 403;
                	$data['message'] = 'token is invalid';
                }
			}else{
				$data['code'] = 402;
                $data['message'] = 'teacher token (token),group_id,student name (name), student lastname (lastname) is required';
			}
		}else if($action == 'removestudent'){
			if (isset($token) && isset($student_id)) {
				$worker = $db->selectWhere('workers',[
					[
						'token'=>$token,
						'cn'=>'=',
					],
				]);
                if ($worker->num_rows) {
                	$student = $db->selectWhere('students',[
						[
							'id'=>$student_id,
							'cn'=>'=',
						],
					]);
					if ($student->num_rows) {
						$db->delete('students',[
	                		[
	                			'id'=>$student_id,
	                			'cn'=>"="
	                		],
	                	]);
	                	$students = $db->selectWhere('students',[
	                		[
	                			'id'=>0,
	                			'cn'=>'>='
	                		]
	                	]);
	                	$data['ok'] = true;
	            		$data['code'] = 200;
	            		$data['message'] = 'student deleted successfully';
	            		foreach ($students as $key => $value) $data['result'][$key] = $value;
					}else{
						$data['code'] = 403;
                		$data['message'] = 'student_id is invalid';
					}
                }else{
                	$data['code'] = 403;
                	$data['message'] = 'token is invalid';
                }
			}else{
				$data['code'] = 402;
                $data['message'] = 'teacher token (token) and student_id is required';
			}
		}else if($action == 'studentfee'){
			if (isset($token) && isset($id)) {
                if (isManager($token)) {
                	$student = $db->selectWhere('students',[
                		[
                			'id'=>$id,
                			'cn'=>'='
                		]
                	]);
                	if ($student->num_rows) {
	                	$db->update('students',[
							'pay_date'=>strtotime('next month'),
							'is_paid'=>'true',
						],[
							'id'=>$id,
							'cn'=>'='
						]);
	                	$students = $db->selectWhere('students',[
	                		[
	                			'id'=>0,
	                			'cn'=>'>='
	                		]
	                	]);
	                	$data['ok'] = true;
	            		$data['code'] = 200;
	            		$data['message'] = 'the student has successfully paid the fee';
	            		foreach ($students as $key => $value){
	            			$is_paid = 'false';
	            			if ($key == 'pay_date' && $value > strtotime('now')) $is_paid = 'true';
	            			if ($key == 'is_paid') $data['result'][$key] = $is_paid;
	            			$data['result'][$key] = $value;
	            		}
                	}else{
                		$data['code'] = 403;
                		$data['message'] = 'student id (id) is invalid';
                	}
                }else{
                	$data['code'] = 403;
                	$data['message'] = 'token is invalid';
                }
			}else{
				$data['code'] = 402;
                $data['message'] = 'token and student id (id) is required';
			}
		}else if($action == 'getmystudents'){
			if (isset($token)) {
				$worker = $db->selectWhere('workers',[
					[
						'token'=>$token,
						'cn'=>'=',
					],
				]);
                if ($worker->num_rows) {
                	$worker = mysqli_fetch_assoc($worker);
                	$students = $db->selectWhere('students',[
						[
							'teacher_id'=>$worker['id'],
							'cn'=>'=',
						],
					]);
					$student_count = $students->num_rows;
					if ($students->num_rows) {
						if ($group_id) {
		                	$students = $db->selectWhere('students',[
		                		[
		                			'teacher_id'=>$teacher_id,
		                			'cn'=>'='
		                		]
		                	], " AND group_id='" . $group_id . "'");
						}
	                	$data['ok'] = true;
	            		$data['code'] = 200;
	            		$data['message'] = 'gived your students data';
	            		$grants = 0;
	            		$sum = 0;
	            		$i = 0;
	            		foreach ($students as $key => $value) {
        					$group = mysqli_fetch_assoc($db->selectWhere('groups',[
								[
									'id'=>$value['group_id'],
									'cn'=>'=',
								],
							]));
							$direction = mysqli_fetch_assoc($db->selectWhere('directions',[
								[
									'id'=>$group['direction_id'],
									'cn'=>'=',
								],
							]));
							$i++;
            				if ($value['grant_percent'] == '00.00') {
								$sum+= (($direction['monthly_payment']*$worker['percent'])/100);
            				}else{
            					$grants++;
            					$give_sum = $direction['monthly_payment'] - (($direction['monthly_payment']*$value['grant_percent'])/100);
            					$sum+= (($give_sum*$worker['percent'])/100);
            				}
	            			$data['result'][$key] = $value;
	            		}
	            		$data['result']['students'] = $student_count;
            			$data['result']['grants'] = $grants;
            			$data['result']['salary'] = $sum;
            			$data['result']['i'] = $i;
					}else{
						$data['code'] = 403;
                		$data['message'] = 'teacher_id is invalid';
					}
                }else{
                	$data['code'] = 403;
                	$data['message'] = 'token is invalid';
                }
			}else{
				$data['code'] = 402;
                $data['message'] = 'teacher is required';
			}
		}else if(mb_strripos($action, 'get')!==false){ // eng pastgi qimida bo'lishligi ma'qul
			if (isset($token)) {
                if (isManager($token)) {
                	$table = explode('get', $action)[1];
                	$incomedirection = $db->selectWhere($table,[
                		[
                			'id'=>0,
                			'cn'=>'>='
                		]
                	]);
                	$data['ok'] = true;
            		$data['code'] = 200;
            		$data['message'] = 'gived ' . $table . " data";
            		foreach ($incomedirection as $key => $value) $data['result'][$key] = $value;
                }else{
                	$data['code'] = 403;
                	$data['message'] = 'token is invalid';
                }
			}else{
				$data['code'] = 402;
                $data['message'] = 'token is required';
			}
		}else if(false){

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