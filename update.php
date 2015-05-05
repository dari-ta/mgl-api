<?php
/**
 * unofficial MGL App API - Update File
 * ==========================
 * @version 1.3revDR
 * Original by dari-ta
 * Modified and extended by mawalu
 * Reengineered by dari-ta
 */

/*
 * Dataset indices
 * 0:   Class
 * 1:   Period
 * 2:   Subject
 * 3:   Room
 * 4:   Shifted from Date
 * 5:   Shifted from Subject
 * 6:   Comment
 * 7:   Date 
 */

/*
 * Action enum
 * W    'Wechsel',  a shifted lesson
 * E    'Entfall',  a skipped lesson
 * A    'Aufsicht', a lesson without sense
 */
 
define('DB_CONFIG_FILE_NAME', 'db.inc.php');
define('VPL_URL', 'http://www.mgl.lb.bw.schule.de/site/service/vpk/vpk.htm');
define('VPL_TABLE', 'vpl');

global $db;


include(DB_CONFIG_FILE_NAME); 
require('simple_html_dom.php');

$html = new simple_html_dom(); // the full HTML Object of the VPL page
$vpl = Array(Array()); // the resulting VPL

/** 
 * Parse the Date provided by MGL
 * @arg $html  the plain HTML of the Date object
 * @return the parsed Date as plain text
 */
function getDateFromMgl($html) {
    $html = trim(substr($html, 0, 10));

    while (!is_numeric($html[strlen($html)-1])) {
        $html = substr_replace($html ,"",-1);
    }

    return $html;
};

$html->load(utf8_encode(file_get_contents(VPL_URL)));

/* We start with the first of two days, then increment */
for($daynum = 1; $daynum <= 2; $daynum++) {
    $dataset = 0;
    $currdate = ''; // the Date for the set of the current day
    $plan = $html->find('a[name='.$daynum.']', 0);
    $currdate =  date('d.m.Y', strtotime(getDateFromMgl($plan -> find('.mon_title', 0) -> innertext, 0, 10)));

    /* .mon_list  is the table containing the VPL */
    foreach($plan -> find('.mon_list', 0) -> find('tr') as $tr_num => $tr) {
        $currdtset = Array('','','','','','','',$currdate); // the current set of data -> Dataset indices

        foreach($tr -> find('td') as $td_num => $td) {
            $currdtset[$td_num] = $td->plaintext;    
        }

        $vpl[$daynum][$tr_num] = $currdtset;
    }
    
    /* table.info  are the News of the Day (Nachrichten des Tages, NDT) */
    foreach($plan -> find('table.info') as $in_num =>$info) {

        foreach($info -> find('td.info') as $i_num => $iinfo) {

            if($in_num < 1) {
                /* Class is NDT, Period is 0 */
                $currdtset = Array('NDT','0','','','','',$iinfo -> plaintext, $currdate);
                $vpl[$daynum][] = $currdtset;
            }
        }
    }
}

/* Go throught al VPLs */
foreach($vpl as $vpls) {

    foreach($vpls as $dset) {
        for($i = 0; $i < 8; $i++) {
            $dset[$i] = str_replace('&nbsp;','',$dset[$i]);
        }


        $classes = Array();
        $stds = Array();
    
        /* we only get one class: e.g. 10d or 9c */
        if(preg_match('/^([0-9]+)([a-z])$/', $dset[0], $matches)) {
            $classes[] = $matches[0];
        /* we get multiple classes: e.g. 10abcd or 9de */
        } else if(preg_match('/^([0-9]+)([a-z]*)$/', $dset[0], $matches)) {
            $str = $matches[2];
            /* we split the classes and create an object for each class */
            while(strlen($str) >= 1) {
                $classes[] = $matches[1].substr($str,0,1);
                $str = substr($str,1);
            }
        /* we got a 'Jahrgangsstufe': e.g. JG1 or JG2*/
        } else if(preg_match('/^JG([0-9])$/', $dset[0], $matches)) {
            $classes[] = $matches[0];
        /* we got multiple \Jahrgangsstufen': e.g. JG12 */
        /* OK, this case is irrealistic, but it's there */
        } else if(preg_match('/^JG([0-9]*)$/', $dset[0], $matches)) {
            $str = $matches[1];
            /* we split the classes and create an object for each class */
            while(strlen($str) >= 1){
                $classes[] = 'JG'.substr($str,0,1);
                $str = substr($str,1);
            }
        /* Well, maybe we got an NDT, a 'Kurs' or an 'AG'
            Or, umh..., I dont know...
        */
        } else {
            $classes[] = $dset[0];
        }

        /* we got multiple Periods: e.g. 1 - 2 or 5 - 6 */
        if(preg_match('/^([0-9]+) - ([0-9]+)$/', $dset[1], $matches)) {
            $num1 = $matches[1];
            $num2 = $matches[2];
            for($i = $num1; $i <= $num2; $i++) {
                $stds[] = $i;
            }
        /* we got one period, or something different*/
        } else {
            $stds[] = $dset[1];
        }    
    
        $action = 'W';
        if($dset[3] == '---') { // if the new subject is just nothing
            $action = 'E';  // the lesson is skipped
        } else if($dset[2] == 'A') { // if the subject is the mysterious 'A'
            $action = 'A';  // the lesson is 'Aufsicht'
        }

        // Just Date correction
        $date = array_reverse(explode(".", $dset[7]));
        $dset[7] = implode("-", $date);


        /* We write is to the Database */
        foreach($classes as $class) {
            foreach($stds as $std) {
                $hash = md5($class.$std.$dset[2].$dset[3].$dset[4].$dset[5].$dset[6].$action);
                $sql1 = 'SELECT id FROM `' . VPL_TABLE . '` WHERE `hash` = :hash';
                $q1 = $db->prepare($sql1);
                $q1 -> execute(array(':hash'=>$hash));
                if($q1 -> rowCount() == 0) {
                    $sql2 = "INSERT INTO " . VPL_TABLE . " (class,std,subj,room,froom,fsubj,comm,hash,date,act) VALUES (:class,:std,:subj,:room,:froom,:fsubj,:comm,:hash,:date,:act)";
                    $q2 = $db->prepare($sql2);
                    $q2->execute(array(':class'=>$class, ':std'=>$std, ':subj'=>$dset[2], ':room'=>$dset[3], ':froom'=>$dset[4], ':fsubj'=>$dset[5], ':comm'=>$dset[6], ':hash'=>$hash, ':date' => $dset[7], ':act' => $action));
                }
            }
        }
    }
}


//Cleanup
$db -> query("DELETE FROM " . VPL_TABLE . " WHERE date < CURDATE();");

