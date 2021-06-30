<?php

function GetCardNoAnswer($card)
{
  $ret = $card;
  switch ($card['type']) {
    case 'Simple':
      $ret['answer'] = "";
      break;
    case 'Choice':
      for ($i=0; $i < count($ret['choices']); $i++) { 
        $ret['choices'][$i]['selected'] = false;
        $ret['answer'] = false;
      } 
      break;
    case 'Orthoepy':
      $ret['word'] =  mb_strtolower($ret['word']);
      $ret['word'] = str_replace('ё', 'е', $ret['word']);
      break;
  }
  return $ret;
}

//Проверка ответа
function checkCard($origin, $draft)
{
  $res = $draft;
  switch ($origin['type']) {
    case 'Simple':
      if ($origin['answer'] == $draft['answer']) {
        $res['score'] = $origin['score'];
      } else {
        $res['score'] = 0;
      }
      break;
    case 'Choice':
      $isCorrect = true;
      if($origin['isMultiple']){
        for ($i=0; $i < count($draft['choices']); $i++) { 
          for ($j=0; $j < count($origin['choices']); $j++) { 
            if($draft['choices'][$i]['id'] == $origin['choices'][$j]['id']){
              //Правильная отметка
              if($draft['choices'][$i]['selected'] == true 
                && $origin['choices'][$i]['selected'] == true ){
                $res['choices'][$i]['state'] = 'correct';
              }
              //Неверная отметка
              if($draft['choices'][$i]['selected'] == true && 
                $origin['choices'][$i]['selected'] == false){
                  $res['choices'][$i]['state'] = 'incorrect' ;
                  $isCorrect = false;
              }
              //Не отмечен правильный вариант
              if($draft['choices'][$i]['selected'] == false &&
                $origin['choices'][$i]['selected'] == true){
                  $res['choices'][$i]['state'] = 'not_marked';
                  $isCorrect = false;
              }
            }
          }
        }
      }else{
        $isCorrect = false;
        for ($i=0; $i < count($draft['choices']); $i++) {
          if($draft['choices'][$i]['id'] == $origin['answer'] && $origin['answer'] == $draft['answer'] ){
            $res['choices'][$i]['state'] = 'correct';
            $isCorrect = true;
          }else if($draft['choices'][$i]['id'] == $draft['answer'] && $draft['answer'] != $origin['answer'] ){
            $res['choices'][$i]['state'] = 'incorrect';
          }else if($draft['choices'][$i]['id'] == $origin['answer'] && $origin['answer'] != $draft['answer'] ){
            $res['choices'][$i]['state'] = 'not_marked';
          }
        }
      }
      if($isCorrect) $res['score'] = $origin['score'];
      else $res['score'] = 0;
      break;
      case 'Orthoepy':
        $res['word'];
        $arWord = preg_split('//u', $origin['word'], -1, PREG_SPLIT_NO_EMPTY);
        $glas = ['А', 'О', 'Э', 'Е', 'И', 'Ы', 'У', 'Ё', 'Ю', 'Я'];
        $correctWord = '';
        for ($i=0; $i < count($arWord); $i++) {
          $isyy = false;
          for ($j=0; $j < count($glas); $j++) { 
            if($arWord[$i] == $glas[$j]){
              $isyy = true; break;
            }
          }
          if($isyy == false){
            $arWord[$i] = mb_strtolower($arWord[$i]);
          }
          $correctWord.=$arWord[$i];
        }
        $a = str_replace('Е', 'ё', $draft['word']);
        $b = str_replace(['Ё', 'Е'], 'ё', $correctWord );
        if( strcmp($a, $b) == 0 ){
          $res['score'] = $origin['score'];
          $res['word'] = $origin['word'];
        }
        else $res['score'] = 0;
        $res['correct'] = $correctWord; //$correctWord;
        break;
    default:
      # code...
      break;
  }
  return $res;
}

function test_send()
{
  global $R, $DB, $ME, $RET;
  $test = $R['test'];
  $cards = $R['test']['body'];
  if(isset($test['test_id'])){
    $qt = $DB->prepare('SELECT * from tests where test_id = :test_id limit 1');
    $qt->bindValue('test_id', $test['test_id'], PDO::PARAM_INT);
  }else if(isset($test['gt_id'])){
    $qt = $DB->prepare('SELECT tests.* from gtests inner join tests on gtests.ref_test_id = tests.test_id where gtests.gt_id = :gt_id limit 1');
    $qt->bindValue('gt_id', $test['gt_id'], PDO::PARAM_INT);
  }
  
  $qt->execute();
  if ($origin = $qt->fetch(PDO::FETCH_ASSOC)) {
    $originCards = json_decode($origin['body'], true);
    $max_score = 0;
    $score = 0;
    for ($i = 0; $i < count($cards); $i++) {
      for ($j = 0; $j < count($originCards); $j++) {
        if ($cards[$i]['id'] == $originCards[$j]['id']) {
          $cards[$i] = checkCard($originCards[$j], $cards[$i]);
          $max_score += $originCards[$j]['score'];
          $score += $cards[$i]['score'];
        }
      }
    }
    $qs = $DB->prepare("INSERT INTO results (name, description, usr_id_auditor, ref_test_id, usr_id, score, max_score, body, gr_id) 
    VALUES (:name, :description, :usr_id_auditor, :ref_test_id, :usr_id, :score, :max_score, :body, :gr_id)");
    $qs->bindValue('name', $origin['name'], PDO::PARAM_STR);
    $qs->bindValue('description', $origin['description'], PDO::PARAM_STR);
    $qs->bindValue('usr_id_auditor', $origin['usr_id'], PDO::PARAM_INT);
    $qs->bindValue('ref_test_id', isset($test['gr_id']) ? $test['gt_id'] : $origin['test_id'], PDO::PARAM_INT);
    $qs->bindValue('usr_id', $ME['usr_id'], PDO::PARAM_INT);
    $qs->bindValue('score', $score, PDO::PARAM_INT);
    $qs->bindValue('max_score', $max_score, PDO::PARAM_INT);
    $qs->bindValue('body', json_encode($cards, JSON_UNESCAPED_UNICODE), PDO::PARAM_STR);
    $qs->bindValue('gr_id', isset($test['gr_id']) ? $test['gr_id'] : null, PDO::PARAM_INT);
    $qs->execute();
    if (empty($qs->errorInfo()[1])) {
      $qr = $DB->prepare("SELECT res_id FROM results where usr_id = :usr_id order by res_id desc limit 1");
      $qr->bindValue("usr_id", $ME['usr_id'], PDO::PARAM_INT);
      $qr->execute();
      $row = $qr->fetch(PDO::FETCH_ASSOC);
      $RET = ['data' => $row['res_id']];
    } else {
      $RET = ['error' => $qs->errorInfo()[2]];
    }
  }else{
    $RET = ['error' => 'Тест был удален', 'info'=>$qt->errorInfo()[2]];
  }
}

function test_save()
{
  global $R, $DB, $ME, $RET;
  $name = $R['test']['name'];
  $description = $R['test']['description'];
  $body = $R['test']['body'];
  if ($R['test']['test_id'] == 'new') {
    $q = $DB->prepare("INSERT INTO tests (usr_id, name, description, body) VALUES(:usr_id, :name, :description, :body)");
  } else {
    $q = $DB->prepare("UPDATE tests SET usr_id = :usr_id, name = :name, description = :description, body = :body where test_id = :test_id");
    $q->bindValue("test_id", $R['test']['test_id'], PDO::PARAM_INT);
  }
  $q->bindValue("usr_id", $ME['usr_id'], PDO::PARAM_INT);
  $q->bindValue("name", $name, PDO::PARAM_STR);
  $q->bindValue('description', $description, PDO::PARAM_STR);
  $q->bindValue('body',  json_encode($body, JSON_UNESCAPED_UNICODE), PDO::PARAM_STR);
  $q->execute();
  if (empty($q->errorInfo()[1])) {
    if ($R['test']['test_id'] == 'new') {
      $q2 = $DB->prepare("SELECT test_id from tests where usr_id = :usr_id order by test_id desc limit 1");
      $q2->bindValue('usr_id', $ME['usr_id'], PDO::PARAM_INT);
      $q2->execute();
      $test_id = $q2->fetch(PDO::FETCH_ASSOC)['test_id'];
      $RET = ['data' => $test_id];
    } else {
      $RET = ['data' => $R['test']['test_id']];
    }
  } else {
    $RET = ['error' => $q->errorInfo()[1]];
  }
}

function get_test_editor()
{
  global $R, $DB, $ME, $RET;
  $test_id = $R['test_id'];
  $q = $DB->prepare("SELECT * from tests where test_id = :test_id and usr_id = :usr_id limit 1");
  $q->bindValue(':test_id', $test_id, PDO::PARAM_INT);
  $q->bindValue(':usr_id', $ME['usr_id'], PDO::PARAM_INT);
  $q->execute();
  if ($row = $q->fetch(PDO::FETCH_ASSOC)) {
    $row['body'] = json_decode($row['body']);
    $RET = ['data' => $row];
  } else {
    $RET = ['error' => 'Тест не найден'];
  }
}

function get_test_basic()
{
  global $R, $DB, $ME, $RET;
  $test_id = $R['test_id'];
  $gtest_id = $R['gtest_id'];

  if (isset($test_id)){ //Прямой тест
    $q = $DB->prepare("SELECT * from tests where test_id = :test_id limit 1");
    $q->bindValue(':test_id', $test_id, PDO::PARAM_INT);
    $q->execute();
  }else if (isset($gtest_id)) { //Тест группы
    $q = $DB->prepare("SELECT gtests.*, requests.req_id, tests.name, tests.description, tests.body, tests.ico, tests.usr_id
      from gtests inner join tests on gtests.ref_test_id = tests.test_id 
      left join requests on (gtests.gr_id = requests.gr_id and requests.usr_id = :usr_id and requests.accepted = true)
      where gtests.gt_id = :gt_id limit 1");
    $q->bindValue(':gt_id', $gtest_id, PDO::PARAM_INT);
    $q->bindValue(':usr_id', $ME['usr_id'], PDO::PARAM_INT);
    $q->execute();
  }
  if ($row = $q->fetch(PDO::FETCH_ASSOC)) {
    $isSend = false;
    if ($row['req_id'] != '' || isset($test_id)){
      $isSend = true;
    }else{
      $q2 = $DB->prepare("SELECT usr_id from groups where gr_id = :gr_id limit 1");
      $q2->bindValue("gr_id", $row['gr_id'], PDO::PARAM_INT);
      $q2->execute();
      if($rg = $q2->fetch(PDO::FETCH_ASSOC)){
        if($rg['usr_id'] == $ME['usr_id'])
        $isSend = true;
      }
    }
    if($isSend){
      $cards = json_decode($row['body'], true);
      for ($i = 0; $i < count($cards); $i++) {
        $cards[$i] = GetCardNoAnswer($cards[$i]);
      }
      /////

      if($row['gr_id'] > 0 ){
        $qg = $DB->prepare("SELECT groups.name as \"group_name\", groups.gr_id, groups.description,
          users.first_name, users.last_name, users.avatar as \"user_avatar\" from groups
          left join users on users.usr_id = groups.usr_id where gr_id = :gr_id limit 1");
        $qg->bindValue('gr_id', $row['gr_id'], PDO::PARAM_INT);
        $qg->execute();
        if($group = $qg->fetch(PDO::FETCH_ASSOC)){
          $row['group'] = $group;
        }
      }else{
        $qa = $DB->prepare("SELECT first_name, last_name, avatar, usr_id from users where usr_id = :usr_id limit 1");
        $qa->bindValue('usr_id', $row['usr_id'], PDO::PARAM_INT);
        $qa->execute();
        if($autor = $qa->fetch(PDO::FETCH_ASSOC)){
          $row['autor_test'] = $autor;
        }
      }

      /////
      $row['body'] = $cards;
      $RET = ['data' => $row];
    }else{
      $RET = ['error' => 'Тест не найден', 'info'=> $test_id];
    }
  } else {
    $RET = ['error' => 'Тест не найден', 'info' => $q->errorInfo()[2]];
  }
}

//Получение моих решений
function get_test_result()
{
  global $R, $DB, $ME, $RET;
  $res_id = $R['res_id'];
  $q = $DB->prepare("SELECT * from results where res_id = :res_id limit 1");
  $q->bindValue(':res_id', $res_id, PDO::PARAM_INT);
  $q->execute();
  if ($row = $q->fetch(PDO::FETCH_ASSOC)) {
    $cards = json_decode($row['body'], true);
    $row['body'] = $cards;
    if($row['gr_id'] > 0 ){
      $qg = $DB->prepare("SELECT groups.name as \"group_name\", groups.gr_id, groups.description,
        users.first_name, users.last_name, users.avatar as \"user_avatar\" from groups
        left join users on users.usr_id = groups.usr_id where gr_id = :gr_id limit 1");
      $qg->bindValue('gr_id', $row['gr_id'], PDO::PARAM_INT);
      $qg->execute();
      if($group = $qg->fetch(PDO::FETCH_ASSOC)){
        $row['group'] = $group;
      }
    }else{
      $qa = $DB->prepare("SELECT first_name, last_name, avatar, usr_id from users where usr_id = :usr_id limit 1");
      $qa->bindValue('usr_id', $row['usr_id_auditor'], PDO::PARAM_INT);
      $qa->execute();
      if($autor = $qa->fetch(PDO::FETCH_ASSOC)){
        $row['autor_test'] = $autor;
      }
    }
    $qu = $DB->prepare("SELECT first_name, last_name, usr_id, avatar from users where usr_id = :usr_id limit 1");
    $qu->bindValue('usr_id', $row['usr_id'], PDO::PARAM_INT);
    $qu->execute();
    if($user = $qu->fetch(PDO::FETCH_ASSOC)){
      $row['user'] = $user; 
    }
    $RET = ['data' => $row];
  } else {
    $RET = ['error' => 'Решение не найдено'];
  }
}

//Получение моих тестов
function get_my_tests()
{
  global $R, $DB, $ME, $RET;

  $count = empty($R['count']) ? 20 : $R['count'];
  $count = $count > 100 ? 100 : $count;
  $sign = empty($R['desc']) ? '>' : '<';
  $insertDesc = empty($R['desc']) ? '' : 'desc';

  if (empty($R['point'])) {
    $q = $DB->prepare("SELECT tests.*, users.first_name, users.last_name from tests left join users on tests.usr_id = users.usr_id where tests.usr_id = :usr_id
      order by tests.test_id $insertDesc limit :count");
  } else {
    $q = $DB->prepare("SELECT tests.*, users.first_name, users.last_name from tests left join users on tests.usr_id = users.usr_id where tests.usr_id = :usr_id and tests.test_id $sign :point 
      order by tests.test_id $insertDesc limit :count");
    $q->bindValue('point', $R['point'], PDO::PARAM_INT);
  }
  $q->bindValue('usr_id', $ME['usr_id'], PDO::PARAM_INT);
  $q->bindValue('count', $count, PDO::PARAM_INT);
  $q->execute();
  if (empty($q->errorInfo()[1])) {
    $rows = $q->fetchALL(PDO::FETCH_ASSOC);
    for($i = 0; $i< count($rows); $i++){
      $rows[$i]['date_created'] = NormalTime( $rows[$i]['date_created']);
    }
    $RET = ['data' => $rows];
  } else {
    $RET = ['error' => $q->errorInfo()[2]];
  }
}

//Получение моих результатов
function get_my_results(){
  global $R, $DB, $ME, $RET;

  $count = empty($R['count']) ? 20 : $R['count'];
  $count = $count > 100 ? 100 : $count;
  $sign = empty($R['desc']) ? '>' : '<';
  $insertDesc = empty($R['desc']) ? '' : 'desc';

  if (empty($R['point'])) {
    $q = $DB->prepare("SELECT results.*, users.first_name, users.last_name from results left join users on results.usr_id = users.usr_id where results.usr_id = :usr_id
      order by results.res_id $insertDesc limit :count");
  } else {
    $q = $DB->prepare("SELECT results.*, users.first_name, users.last_name from results left join users on results.usr_id = users.usr_id where results.usr_id = :usr_id and results.res_id $sign :point 
      order by results.res_id $insertDesc limit :count");
    $q->bindValue('point', $R['point'], PDO::PARAM_INT);
  }
  $q->bindValue('usr_id', $ME['usr_id'], PDO::PARAM_INT);
  $q->bindValue('count', $count, PDO::PARAM_INT);
  $q->execute();
  if (empty($q->errorInfo()[1])) {
    $rows = $q->fetchALL(PDO::FETCH_ASSOC);
    for($i = 0; $i< count($rows); $i++){
      $rows[$i]['date_created'] = NormalTime( $rows[$i]['date_created']);
    }
    $RET = ['data' => $rows];
  } else {
    $RET = ['error' => $q->errorInfo()[2]];
  }
}


function share_tests(){
  global $R, $DB, $ME, $RET;
  $groups = $R['groups'];
  $ids = isset($groups) ? $groups : [];
  $sttg = $R['settings'];
  if(count($ids) > 0){
    $in  = getBindLine(':gr_id', count($ids));
    $q = $DB->prepare("SELECT gr_id FROM groups WHERE gr_id IN ($in) and usr_id = :usr_id limit :count");
    $q->bindValue('usr_id', $ME['usr_id'], PDO::PARAM_INT);
    $q->bindValue('count', count($ids), PDO::PARAM_INT);
    for ($i=0; $i < count($ids); $i++) { 
      $q->bindValue('gr_id_'.$i, $ids[$i], PDO::PARAM_INT);
    }
    $q->execute();
    $nids = [];
    while($row = $q->fetch(PDO::FETCH_ASSOC)){
      $nids[] = $row['gr_id'];
    }
    $others = [];//Названия других параметров
    $oValues = [];//Значения других параметров
    if(isset($R['comment'])){
      $others[] = 'comment';
      $oValues['comment'] = $R['comment'];
    }
    //Проверка на наличия параметров
    if(isset($sttg)){
      if(isset($sttg['is_limit_attempts'])){
        $others[] = 'attempts';
        $oValues['attempts'] = NULL;
        if($sttg['is_limit_attempts'] == true){
          if(isset($sttg['limit_attempts'])){
            $oValues['attempts'] = $sttg['limit_attempts'] >= 0 ? $sttg['limit_attempts'] : 0;
          }
        }
      }
    }
    //end
    $insertSettings = ''; //, comment, attempts
    $insertBindSettings = ''; //, :comment, :attempts
    for ($i=0; $i < count($others) ; $i++) { 
      $insertSettings.=', '.$others[$i];
      $insertBindSettings.=', :'.$others[$i];
    }

    $query = "INSERT into gtests (gr_id, ref_test_id $insertSettings) VALUES ";
    for ($i=0; $i < count($nids); $i++) { 
      $query.="( :gr_id_$i, :ref_test_id $insertBindSettings)".($i < count($nids)-1 ? ', ' : '');
    }
    $qr=$DB->prepare($query);
    $qr->bindValue('ref_test_id', $R['test_id'], PDO::PARAM_INT);
    for ($i=0; $i < count($nids); $i++){ 
      $qr->bindValue('gr_id_'.$i, $nids[$i], PDO::PARAM_INT);
    }
    //Бинд соответствующих значений
    for ($i=0; $i < count($others) ; $i++) { 
      switch ($others[$i]) {
        case 'comment': 
          $qr->bindValue('comment', $oValues['comment'], PDO::PARAM_STR); 
          break;
        case 'attempts':
          $qr->bindValue('attempts',
            $oValues['attempts'],
            $oValues['attempts'] == NULL ? PDO::PARAM_NULL : PDO::PARAM_INT);
        default:
          break;
      }
    }//end
    $qr->execute();
    if( empty($qr->errorInfo()[1]) ){
      $RET = ['data'=>'Все ок'];
    }else{
      $RET = ['error'=>$qr->errorInfo()[2]];
    }
    //$qt = $DB->prepare($query);
  }
}

function delete_test(){
  global $R, $DB, $ME, $RET;

  $q = $DB->prepare("SELECT usr_id from tests where test_id = :test_id limit 1");
  $q->bindValue('test_id', $R['test_id'], PDO::PARAM_INT);
  $q->execute();
  if($row = $q->fetch(PDO::FETCH_ASSOC)){
    if($row['usr_id'] == $ME['usr_id']){
      $qd = $DB->prepare("DELETE FROM tests where test_id = :test_id");
      $qd->bindValue('test_id', $R['test_id'], PDO::PARAM_INT);
      $qd->execute();
      if( empty($qd->errorInfo()[1]) ){
        $RET = ['data'=>'Тест удален успешно'];
      }else{
        $RET = ['error'=> $qd->errorInfo()[2]];
      }
    }else{
      $RET = ['error'=> 'Нет доступа'];
    }
  }else{
    $RET = ['error'=> 'Тест не найден'];
  }

}
