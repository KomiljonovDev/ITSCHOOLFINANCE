<?php
	function isManager($token) {
		global $db;
		$manager = $db->selectWhere('finance',[
            [
                'token'=>$token,
                'cn'=>'='
            ],
        ]);
        return ($manager->num_rows) ? true : false;
	}


?>