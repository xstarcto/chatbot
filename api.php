<?php



//
require_once "chatbot/Chatbot.php";
require_once "chatbot/Config.php";
require_once "chatbot/Database/Connection.php";

// check request type
!isset($_REQUEST['requestType']) && die("requestType is required ...");
!isset($_REQUEST['userInput']) && die("userInput is required ...");



// initialize config
$config = new Config();
define("LOG", $config->log);
header(LOG ? "Content-Type: text/plain; charset=utf-8"  : "Content-Type: application/json; charset=utf-8");
error_reporting(LOG ? E_ALL : JSON_ERROR_NONE);



// 若用户上传了userId则用uid当做用户唯一标识否则用ip
$userId = isset($_REQUEST['userId']) ? $_REQUEST['userId'] : $_SERVER['REMOTE_ADDR'];
LOG && print "userId : " . $userId . "\n";

// initialize chatbot
$chatbot = new Chatbot($config, $userId);

$result = array(
    'status' => 'error',
    'type' => 'empty',
    'message' => 'empty message ...',
    'data' => 'empty',
);


// talk
if ($_REQUEST['requestType'] == 'talk') {
    $res = $chatbot->talk($_REQUEST['userInput']);
    $data = $chatbot->getData();
    $result['status'] = 'success';
    $result['type'] = 'talk';
    $result['message'] = trim(preg_replace("/\s+/", " ", $res));
    $result['data'] = $data;
} elseif ($_REQUEST['requestType'] == 'forget') {
    $chatbot->forget();
    $result['status'] = 'success';
    $result['type'] = 'forget';
    $result['message'] = 'completed successifully ...';
    $result['data'] = 'empty';
} else {
    $result['status'] = 'error';
    $result['type'] = $_REQUEST['requestType'];
    $result['message'] = 'invalid request type ...';
    $result['data'] = 'empty';
}



if (LOG){
    print "\n";
    print_r($result);
    print "\n";
} else {
    echo json_encode($result);
}


