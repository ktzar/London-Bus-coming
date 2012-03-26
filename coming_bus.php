#!/usr/bin/php
<?php
require('lib/simple_html_dom.php');

/**
 * ComingBus 
 * 
 */
class ComingBus
{
    //Constants
    const TFL_BUS_COUNTDOWN_BASEURL = "http://m.countdown.tfl.gov.uk/arrivals/";

    /**
     * getBuses 
     * 
     * @param array $buses 
     * @param int $stopcode 
     * @static
     * @access public
     * @return array
     */
    static function getBuses($buses, $stopcode) {
        $html = file_get_html(self::TFL_BUS_COUNTDOWN_BASEURL.$stopcode);
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
}


//Default params
$stopcode = '75882';
$buses = array(154);

//Parse information in the configuration file
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

//This function shows a help message
function help(){
    echo "bus: usage\n";
    echo "usage: bus [-s stop_number] [-b bus1[:bus2]]\n";
    echo "~/.bus format: \n";
    echo '{"buses":[123],"stop":12345}';
    die;
}

//TODO function to parse parameters
$params = array_slice($argv,1);

for ($i=0;$i<count($params);$i++){
    //Stop parameter
    if ($params[$i] == "-s" ) {
        if (!isset($params[$i+1])) {
            help();
        }else{
            if ( ($stopcode = intval($params[$i+1])) == 0 )
                help();
        }
    }
    //Bus number
    if ($params[$i] == "-b" ) {
        if (!isset($params[$i+1])) {
            help();
        }else{
            $buses = explode(':',$params[$i+1]);
        }
    }
}


try{
    $data = ComingBus::getBuses($buses, $stopcode);
    echo "Stop $stopcode\n";
    foreach ($data as $bus_coming) {
        echo "{$bus_coming['bus']}\t{$bus_coming['time']}\n";
    }
}catch(Exception $e){
    echo $e;
}
die;
