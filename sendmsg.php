<?php

require_once '../private/initialize.php';

use Library\Database\Database as DB;
use Library\Email\Email;

$data = json_decode(file_get_contents('php://input'), true);
$username = \NULL;


$db = DB::getInstance();
$pdo = $db->getConnection();

$token = $data['token'];

if (hash_equals($_SESSION['token'], $data['token'])) {
    /* The Following to get response back from Google recaptcha */
    $url = "https://www.google.com/recaptcha/api/siteverify";

    $remoteServer = filter_input(INPUT_SERVER, 'REMOTE_ADDR', FILTER_SANITIZE_URL);
    $response = file_get_contents($url . "?secret=" . PRIVATE_KEY . "&response=" . $data['response'] . "&remoteip=" . $remoteServer);
    $recaptcha_data = json_decode($response);
    /* The actual check of the recaptcha */
    if (isset($recaptcha_data->success) && $recaptcha_data->success === TRUE) {

        $result = new Email($data);

        if ($result) {
            output(true);
        } else {
            errorOutput(false);
        }
    } else {
        $success = "You're not a human!"; // Not on a production server:
    }
} else {
    output('Token Error');
}

function errorOutput($output, $code = 500) {
    http_response_code($code);
    echo json_encode($output);
}

///*
// * If everything validates OK then send success message to Ajax / JavaScript
// */

/*
 * After converting data array to JSON send back to javascript using
 * this function.
 */
function output($output) {
    http_response_code(200);
    echo json_encode($output);
}
