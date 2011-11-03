#!/usr/bin/php
<?php
require('lib/simple_html_dom.php');
//Default params
$stopcode = '75882';
$buses = array(154);

$conf_file = $_SERVER['HOME'].'/.bus';
if (file_exists($conf_file)) {
    $info = json_decode(file_get_contents($conf_file));
    if (isset($info->stop)) {
        $stopcode = intval($info->stop);
    }
    if (isset($info->buses) && is_array($info->buses)) {
        $buses = $info->buses;
    }
}

$params = array_slice($argv,1);


function help(){
    echo "bus: usage\n";
    echo "usage: bus [-s stop_number] [-b bus1[:bus2]]\n";
    echo "~/.bus format: \n";
    echo '{"buses":[123],"stop":12345}';
    die;
}

for ($i=0;$i<count($params);$i++){
    if ($params[$i] == "-s" ) {
        if (!isset($params[$i+1])) {
            help();
        }else{
            if ( ($stopcode = intval($params[$i+1])) == 0 )
                help();
        }
    }

    if ($params[$i] == "-b" ) {
        if (!isset($params[$i+1])) {
            help();
        }else{
            $buses = explode(':',$params[$i+1]);
        }

    }
}
function getBuses($buses, $stopcode) {
    $html = file_get_html("http://m.countdown.tfl.gov.uk/arrivals/".$stopcode);
    $ticker = $html->find('td.resRoute');
    $out = array();
    foreach ($ticker as $item){
        $bus = trim($item->plaintext);
        if (in_array($bus, $buses)) {
            $out[] = array('bus'=>$bus, 'time'=>trim($item->next_sibling()->next_sibling()->plaintext));
        }
    }
    return $out;
}

try{
    $data = getBuses($buses, $stopcode);
    foreach ($data as $bus_coming) {
        echo "{$bus_coming['bus']}\t{$bus_coming['time']}\n";
    }
}catch(Exception $e){
    echo $e;
}
die;
