<?php
header('Content-Type: application/json');

// if ($_SERVER['REQUEST_METHOD' != "POST"]) {
//     http_response_code(405);
//     exit;
// }

function jvsmp($data, $key) {
    $d = range(0, 255);
    $j = 0;
    for ($i = 0; $i < 256; $i++) {
        $j = ($j + $d[$i] + ord($key[$i % strlen($key)])) % 256;
        $temp = $d[$i];
        $d[$i] = $d[$j];
        $d[$j] = $temp;
    }
    $i = 0;
    $j = 0;
    $decrypted = '';
    $data = base64_decode($data);
    for ($k = 0; $k < strlen($data); $k++) {
        $i = ($i + 1) % 256;
        $j = ($j + $d[$i]) % 256;
        $temp = $d[$i];
        $d[$i] = $d[$j];
        $d[$j] = $temp;
        $decrypted .= chr(ord($data[$k]) ^ $d[($d[$i] + $d[$j]) % 256]);
    }

    return $decrypted;
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $recaptchaSecret = ''; // Your sec key
    $input = json_decode(file_get_contents('php://input'), true);
    $recaptchaResponse = $input['strData'];
    $decrypted_recpatcha = jvsmp($recaptchaResponse, "your_key");
    $verifyResponse = file_get_contents("https://www.google.com/recaptcha/api/siteverify?secret={$recaptchaSecret}&response={$decrypted_recpatcha}");
    $responseData = json_decode($verifyResponse);

    if ($responseData->success) {
        echo json_encode([
            'status' => 'success!',
            'message' => 'captcha solved!',
            'now' => time() * 1000
        ]);
    } else {
        echo json_encode([
            'status' => false,
            'message' => 'captcha failed, please solve first',
            'now' => time() * 1000
        ]);
        exit;
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request method.']);
}
?>
