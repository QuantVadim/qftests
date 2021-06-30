<?php

function login(){
    global $R, $DB, $ME, $RET;
    
    $q = $DB->prepare("SELECT usr_id, first_name, last_name, avatar, mykey FROM users where usr_id = :usr_id limit 1");
    $q->bindValue('usr_id', $R['data']['usr_id'], PDO::PARAM_INT);
    $q->execute();
    if($row = $q->fetch(PDO::FETCH_ASSOC)){
        if($row['mykey'] == $R['data']['mykey']){
            $RET = ['data' => $row ];
        }else{  
            $RET = ['error' => 'Ошибка авторизации'];
        }
    }else{
        $RET = ['error' => 'Пользователь не найден'];
    }

}

function get_user(){
    global $R, $DB, $ME, $RET;
    
    $q = $DB->prepare("SELECT usr_id, first_name, last_name, avatar FROM users where usr_id = :usr_id limit 1");
    $q->bindValue('usr_id', $R['data']['usr_id'], PDO::PARAM_INT);
    $q->execute();
    if($row = $q->fetch(PDO::FETCH_ASSOC)){
        $RET = ['data' => $row ];
    }else{
        $RET = ['error' => 'Пользователь не найден'];
    }

}

?>