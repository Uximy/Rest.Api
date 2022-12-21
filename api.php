<?php
$config = file_get_contents("./composer.json");
$array = json_decode($config);

$db = mysqli_connect("localhost", $array -> database[0] -> user, $array -> database[0] -> password, $array -> database[0] -> db);

$valide = null;
switch ($_SERVER['REMOTE_ADDR']) {
    case '185.9.145.129':
        $valide = true;
        break;
    
    case '95.59.93.102':
        $valide = true;
        break;
        
    default:
        $valide = false;
        break;
}

try {
    if ($valide == true) {
        function get_mode_stats(int $number)
        {
            switch ($_POST['mod']) {
                case 1:
                    $mod_stats = 'stats_classic';
                    break;
                case 2:
                    $mod_stats = 'stats_dm_ffa'; 
                    break;
                case 3:
                    $mod_stats = 'stats_clanwar'; 
                    break;
                case 4:
                    $mod_stats = 'stats_duel';
                    break;
            }
            return (string) $mod_stats;
        }
        
        if ($_POST['type'] == 'pl_autoload' && isset($_POST['steamid']) && isset($_POST['ip']) && isset($_POST['mod'])) {

            $sql_account = "SELECT accounts.id, accounts.login, accounts.tokens, accounts.exp, accounts.skill_points, accounts.admin_lvl FROM `accounts` WHERE accounts.steamid = '".$_POST['steamid']."' AND accounts.ip = '".$_POST['ip']."' AND accounts.autologin = 1";
            
            $result = mysqli_query($db, $sql_account);

            $account = mysqli_fetch_object($result);

            if (!$account == null) {
                $sql_banned = "SELECT `account_id`, `reason`, `date`, `duration` FROM `bans` JOIN `accounts` ON bans.account_id = '".$account -> id."' WHERE (`duration` = 0 OR UNIX_TIMESTAMP(`date`) + `duration` > UNIX_TIMESTAMP()) LIMIT 1 ";

                $result_banned = mysqli_query($db, $sql_banned);

                $bans = mysqli_fetch_object($result_banned);

                
                if ((int) $bans -> duration == 0)
                    $end_ban = 0;
                else{
                    $start_ban = strtotime($bans -> date) + $bans -> duration;

                    $end_ban = $start_ban - strtotime(date('H:i:s'));
                }

                if ($bans !== null && $bans -> account_id == $account -> id) {
                    $obj_bans =[
                        "load_status" => "banned",
                        "banned_reason" => (int) $bans -> reason,
                        "banned_time" => (int) $end_ban
                    ];

                    echo json_encode($obj_bans, JSON_UNESCAPED_SLASHES);
                } else{

                    $sql_stats = "SELECT * FROM ".get_mode_stats((int) $_POST['mod'])." JOIN `accounts` ON ".get_mode_stats((int) $_POST['mod']).".id = accounts.id WHERE accounts.steamid = '".$_POST['steamid']."' ";

                    $result = mysqli_query($db, $sql_stats);
        
                    $stats = mysqli_fetch_object($result);
        
                    $sql_pack = "SELECT packs.id, packs.owner_id, packs.type, packs.until FROM `packs` JOIN accounts WHERE accounts.steamid = '".$_POST['steamid']."' AND '".$account -> id."' = packs.owner_id AND (`until` = 0 OR UNIX_TIMESTAMP(`until`) > UNIX_TIMESTAMP())";
        
                    $result = mysqli_query($db, $sql_pack);
        
                    $pack = mysqli_fetch_all($result);
        
                    $sql_items = "SELECT items.owner_id, items.type FROM `items` JOIN `accounts` ON items.owner_id = '".$account -> id."' WHERE accounts.steamid = '".$_POST['steamid']."' ";
        
                    $result = mysqli_query($db, $sql_items);
        
                    $items = mysqli_fetch_all($result);
        
        
                    $sql_skills = "SELECT skills.id, skills.type,skills.level FROM `skills` JOIN `accounts` ON skills.owner_id = '".$account -> id."' WHERE accounts.steamid = '".$_POST['steamid']."' ";
        
                    $result = mysqli_query($db, $sql_skills);
        
                    $skills = mysqli_fetch_all($result);
        
                    $sql_gagged = "SELECT `account_id`, `reason`, `date`, `duration` FROM `gags` JOIN `accounts` ON gags.account_id = '".$account -> id."' WHERE (`duration` = 0 OR UNIX_TIMESTAMP(`date`) + `duration` > UNIX_TIMESTAMP()) LIMIT 1";
        
                    $result = mysqli_query($db, $sql_gagged);
        
                    $gags = mysqli_fetch_object($result);
                    
                    $arr_skills =[];
                    
                    foreach ($skills as $value) {
                        [array_push($arr_skills, [(int) $value[1], (int) $value[2]])];
                    }

                    $arr_items =[];
        
                    for ($i = 0; $i < count($items); $i++) {
                        [array_push($arr_items, (int) $items[$i][1])];
                    }


                    $arr_pack = [];

                    for ($i = 0; $i < count($pack); $i++) {
                        [array_push($arr_pack, (int) $pack[$i][2])];
                    }
                    
                    $object = [
                        "load_status" => "success",
                        'id' => (int) $account -> id,
                        'tokens' => (int) $account -> tokens,
                        'exp'=> (int) $account -> exp,
                        "skill_points" => (int) $account -> skill_points,
                        "admin_lvl" => (int) $account -> admin_lvl,
                        "stats" => [ "kills" => (int) $stats -> kills, "deaths" => (int) $stats -> deaths, "headshots" => (int) $stats -> headshots, "plants" => (int) $stats -> plants, "explosions" => (int) $stats -> explosions, "defusions" => (int) $stats -> defusions ],
                    ];

                    if(!$items == null){
                        $object["items"] = $arr_items;
                    }

                    if (!$skills == null) {
                        $object["skills"] = $arr_skills;
                    }

                    if (!$pack == null) {
                        $object["packs"] = $arr_pack;
                    }

                    if (!$gags == null && $gags -> account_id == $account -> id) {
                        if ((int) $gags -> duration == 0){
                            $end_gag = 0;
                        }
                        else{
                            $start_gag = strtotime($gags -> date) + $gags -> duration;

                            $end_gag = $start_gag - strtotime(date('H:i:s'));
                        }

                        $object['gagged_reason'] = (int) $gags -> reason;
                        $object['gagged_time'] = (int) $end_gag;
                    }

        
                    echo json_encode($object, JSON_UNESCAPED_SLASHES);
                }           
            }else{
                $obj_fail = [
                    "load_status" => 'fail'
                ];
                echo json_encode($obj_fail, JSON_UNESCAPED_SLASHES);
            } 
        }

        if ($_POST['type'] == 'pl_load' && isset($_POST['login']) && isset($_POST['pwd_hash']) && isset($_POST['steamid']) && isset($_POST['ip']) && isset($_POST['mod'])) {
            $sql_account = "SELECT accounts.id, accounts.login, accounts.tokens, accounts.exp, accounts.skill_points, accounts.admin_lvl , accounts.steamid, accounts.ip FROM `accounts` WHERE accounts.login = '".$_POST['login']."' AND accounts.pwd_hash = '".$_POST['pwd_hash']."' ";
            
            $result = mysqli_query($db, $sql_account);

            $account = mysqli_fetch_object($result);

            if (!$account == null) {
                $sql_banned = "SELECT `account_id`, `reason`, `date`, `duration` FROM `bans` JOIN `accounts` ON bans.account_id = '".$account -> id."' WHERE bans.account_id = accounts.id AND (`duration` = 0 OR UNIX_TIMESTAMP(`date`) + `duration` > UNIX_TIMESTAMP()) LIMIT 2 ";

                $result_banned = mysqli_query($db, $sql_banned);

                $bans = mysqli_fetch_object($result_banned);

                if ($bans !== null && $bans -> account_id == $account -> id) {


                    if ((int) $bans -> duration == 0)
                        $end_ban = 0;
                    else{
                        $start_ban = strtotime($bans -> date) + $bans -> duration;

                        $end_ban = $start_ban - strtotime(date('H:i:s'));
                    }

                    $obj_bans =[
                        "load_status" => "banned",
                        "banned_reason" => (int) $bans -> reason,
                        "banned_time" => (int) $end_ban
                    ];

                    echo json_encode($obj_bans, JSON_UNESCAPED_SLASHES);

                } else{

                    if ($account -> steamid !== $_POST['steamid'] || $account -> ip !== $_POST['ip']) {
                        $sql_update_account = "UPDATE `accounts` SET steamid = '".$_POST['steamid']."', ip = '".$_POST['ip']."' WHERE accounts.id = '".$account -> id."'";
    
                        mysqli_query($db, $sql_update_account);
                    }

                    $sql_stats = "SELECT * FROM ".get_mode_stats((int) $_POST['mod'])." JOIN `accounts` ON ".get_mode_stats((int) $_POST['mod']).".id = '".$account -> id."' WHERE accounts.steamid = '".$_POST['steamid']."' ";

                    $result = mysqli_query($db, $sql_stats);
        
                    $stats = mysqli_fetch_object($result);
        
                    $sql_pack = "SELECT packs.id, packs.owner_id, packs.type, packs.until FROM `packs` JOIN accounts WHERE accounts.steamid = '".$_POST['steamid']."' AND '".$account -> id."' = packs.owner_id AND (`until` = 0 OR UNIX_TIMESTAMP(`until`) > UNIX_TIMESTAMP())";
        
                    $result = mysqli_query($db, $sql_pack);
        
                    $pack = mysqli_fetch_all($result);
        
                    $sql_items = "SELECT items.owner_id, items.type FROM `items` JOIN `accounts` ON items.owner_id = '".$account -> id."' WHERE accounts.steamid = '".$_POST['steamid']."' ";
        
                    $result = mysqli_query($db, $sql_items);
        
                    $items = mysqli_fetch_all($result);
        
        
                    $sql_skills = "SELECT skills.id, skills.type,skills.level FROM `skills` JOIN `accounts` ON skills.owner_id = '".$account -> id."' WHERE accounts.steamid = '".$_POST['steamid']."' ";
        
                    $result = mysqli_query($db, $sql_skills);
        
                    $skills = mysqli_fetch_all($result);
        
                    $sql_gagged = "SELECT `account_id`, `reason`, `date`, `duration` FROM `gags` JOIN `accounts` ON gags.account_id = '".$account -> id."' WHERE (`duration` = 0 OR UNIX_TIMESTAMP(`date`) + `duration` > UNIX_TIMESTAMP()) LIMIT 1";
        
                    $result = mysqli_query($db, $sql_gagged);
        
                    $gags = mysqli_fetch_object($result);
                    
        
                    $arr_skills =[];
                    
                    foreach ($skills as $value) {
                        [array_push($arr_skills, [(int) $value[1], (int) $value[2]])];
                    }

                    $arr_items =[];
        
                    for ($i = 0; $i < count($items); $i++) {
                        [array_push($arr_items, (int) $items[$i][1])];
                    }


                    $arr_pack = [];

                    for ($i = 0; $i < count($pack); $i++) {
                        [array_push($arr_pack, (int) $pack[$i][2])];
                    }
                    
                    $object = [
                        "load_status" => "success",
                        'id' => (int) $account -> id,
                        'tokens' => (int) $account -> tokens,
                        'exp'=> (int) $account -> exp,
                        "skill_points" => (int) $account -> skill_points,
                        "admin_lvl" => (int) $account -> admin_lvl,
                        "stats" => [ "kills" => (int) $stats -> kills, "deaths" => (int) $stats -> deaths, "headshots" => (int) $stats -> headshots, "plants" => (int) $stats -> plants, "explosions" => (int) $stats -> explosions, "defusions" => (int) $stats -> defusions ],
                    ];

                    if(!$items == null){
                        $object["items"] = $arr_items;
                    }

                    if (!$skills == null) {
                        $object["skills"] = $arr_skills;
                    }

                    if (!$pack == null) {
                        $object["packs"] = $arr_pack;
                    }

                    if (!$gags == null) {
                        if ((int) $gags -> duration == 0)
                            $end_gag = 0;
                        else{
                            $start_gag = strtotime($gags -> date) + $gags -> duration;

                            $end_gag = $start_gag - strtotime(date('H:i:s'));
                        }

                        $object['gagged_reason'] = (int) $gags -> reason;
                        $object['gagged_time'] = (int) $end_gag;
                    }

        
                    echo json_encode($object, JSON_UNESCAPED_SLASHES);
                }           
            }else{
                $obj_fail = [
                    "load_status" => 'fail'
                ];
                echo json_encode($obj_fail, JSON_UNESCAPED_SLASHES);
            }
        }

        if ($_POST['type'] == 'pl_update' && isset($_POST['acc']) && isset($_POST['mod']) && isset($_POST['type_entity']) && isset($_POST['value'])) {

            if ($_POST['type_entity'] == 'tokens' || $_POST['type_entity'] == 'exp' || $_POST['type_entity'] == 'skill_points') {
                $sql_account = "SELECT accounts.id, accounts.tokens, accounts.exp, accounts.skill_points FROM `accounts` WHERE accounts.id = '".$_POST['acc']."' ";
            
                $result = mysqli_query($db, $sql_account);

                $account = mysqli_fetch_object($result);

                if ((int)$_POST['value'] > 0) {
                    (int)$new_value = (int)$_POST['value'] + $account -> {$_POST['type_entity']};
                    $result = mysqli_query($db, "UPDATE `accounts` SET `".$_POST['type_entity']."` = '".$new_value."' WHERE accounts.id = '".$_POST['acc']."'");
                }else if((int)$_POST['value'] < 0){
                    (int)$new_value =  $account -> {$_POST['type_entity']} - abs((int)$_POST['value']);
                    $result = mysqli_query($db, "UPDATE `accounts` SET `".$_POST['type_entity']."` = '".$new_value."' WHERE accounts.id = '".$_POST['acc']."'");
                }
            }else if($_POST['type_entity'] == 'kills' || $_POST['type_entity'] == 'deaths' || $_POST['type_entity'] == 'headshots' || $_POST['type_entity'] == 'plants' || $_POST['type_entity'] == 'explosions' || $_POST['type_entity'] == 'defusions'){

                if (!$_POST['mod'] == null) {

                    if ($_POST['mod'] <= 4) {

                        $sql_stats_value = "SELECT * FROM `".get_mode_stats((int) $_POST['mod'])."` WHERE id = '".$_POST['acc']."' ";
            
                        $result = mysqli_query($db, $sql_stats_value);
            
                        $stats_value = mysqli_fetch_object($result);

                        switch ($_POST['type_entity']) {
                            case 'kills':
                                $number_value = 1;
                                break;

                            case 'deaths':
                                $number_value = 2;
                                break;

                            case 'headshots':
                                $number_value = 3;
                                break;

                            case 'plants':
                                $number_value = 4;
                                break;

                            case 'explosions':
                                $number_value = 5;
                                break;

                            case 'defusions':
                                $number_value = 6;
                                break;
                        }

                        if ((int)$_POST['value'] > 0) {
                            (int)$new_value = (int)$_POST['value'] + $stats_value -> {$_POST['type_entity']};
                            $result = mysqli_query($db, "UPDATE `".get_mode_stats((int) $_POST['mod'])."` SET `".$_POST['type_entity']."` = '".$new_value."'  WHERE `id` = '".$_POST['acc']."'");
                        }else if((int)$_POST['value'] < 0){
                            (int)$new_value =  $stats_value -> {$_POST['type_entity']} - abs((int)$_POST['value']);
                            $result = mysqli_query($db, "UPDATE `".get_mode_stats((int) $_POST['mod'])."` SET `".$_POST['type_entity']."` =  '".$new_value."' WHERE `id` = '".$_POST['acc']."'");
                        }
                    }
                }
            }


        }
        
        if ($_POST['type'] == 'pl_validate' && isset($_POST['acc']) && isset($_POST['mod']) && isset($_POST['type_entity'])) {

            if ($_POST['type_entity'] == 'tokens' || $_POST['type_entity'] == 'exp' || $_POST['type_entity'] == 'skill_points') {
                $sql_account = "SELECT ".$_POST['type_entity']." FROM `accounts` WHERE id = '".$_POST['acc']."'";
            
                $result = mysqli_query($db, $sql_account);
                $account = mysqli_fetch_object($result);

                echo json_encode($account);

            }else if($_POST['type_entity'] == 'kills' || $_POST['type_entity'] == 'deaths' || $_POST['type_entity'] == 'headshots' || $_POST['type_entity'] == 'plants' || $_POST['type_entity'] == 'explosions' || $_POST['type_entity'] == 'defusions'){

                if (!$_POST['mod'] == null) {
        
                    if ($_POST['mod'] <= 4) {
                        $sql_stats = "SELECT ".$_POST['type_entity']." FROM `".get_mode_stats((int) $_POST['mod'])."` WHERE id = '".$_POST['acc']."'";
                
                        $result = mysqli_query($db, $sql_stats);
                        $stats_result = mysqli_fetch_object($result);
                    
                        echo json_encode($stats_result);
                    }
                }
            }
        }

        if ($_POST['type'] == 'pl_gag' && isset($_POST['acc']) && isset($_POST['reason']) && isset($_POST['duration']) && isset($_POST['set_by']) && isset($_POST['set_etc'])) {

            $sql_gag_ready = "SELECT * FROM `gags` WHERE account_id =  '".$_POST['acc']."' AND (`duration` = 0 OR UNIX_TIMESTAMP(`date`) + `duration` > UNIX_TIMESTAMP()) LIMIT 1";
                
            $result = mysqli_query($db, $sql_gag_ready);

            $ready_gag = mysqli_fetch_object($result);

            $start_gag = strtotime($ready_gag -> date) + $ready_gag -> duration;

            $end_gag = $start_gag - strtotime(date('H:i:s'));

            if ($ready_gag == null) {
                $sql_gags_add = "INSERT INTO `gags`(`account_id`, `reason`, `duration`, `ingame`,`set_by`, `set_etc`) VALUES ('".$_POST['acc']."', '".$_POST['reason']."', '".$_POST['duration']."', '1', '".$_POST['set_by']."', '".$_POST['set_etc']."')";
                
                mysqli_query($db, $sql_gags_add);

                $json = [
                    "status" => "ok"
                ];
                echo json_encode($json);
            }
            else{
                $json = [
                    "status" => "already"
                ];
                echo json_encode($json);
            }
        }

        if ($_POST['type'] == 'pl_ban' && isset($_POST['acc']) && isset($_POST['reason']) && isset($_POST['duration']) && isset($_POST['set_by']) && isset($_POST['set_etc'])) {

            $sql_ban_ready = "SELECT * FROM `bans` WHERE account_id = '".$_POST['acc']."' AND (`duration` = 0 OR UNIX_TIMESTAMP(`date`) + `duration` > UNIX_TIMESTAMP()) LIMIT 1";
                
            $result = mysqli_query($db, $sql_ban_ready);

            $ready_ban = mysqli_fetch_object($result);

            $start_ban = strtotime($ready_ban -> date) + $ready_ban -> duration;

            $end_ban = $start_ban - strtotime(date('H:i:s'));

            if ($ready_ban == null) {
                $sql_bans_add = "INSERT INTO `bans`(`account_id`, `reason`, `duration`, `ingame`,`set_by`, `set_etc`) VALUES ('".$_POST['acc']."', '".$_POST['reason']."', '".$_POST['duration']."', '1','".$_POST['set_by']."', '".$_POST['set_etc']."')";
                
                mysqli_query($db, $sql_bans_add);

                $json = [
                    "status" => "ok"
                ];
                echo json_encode($json);
            }
            elseif(!$end_ban == null && $end_ban > 1 || $ready_ban -> duration == 0){
                $json = [
                    "status" => "already"
                ];
                echo json_encode($json);
            }

            
        }
    }else{
        header('HTTP/1.1 401 Unauthorized');
        exit;
    }    
} catch (Exception $e) {
echo json_encode($e -> getMessage());
}