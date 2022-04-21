<?php

function in_check($in)
{

    $in = trim($in);
    $in = htmlspecialchars($in);

    return $in;
}

function rand_str($type = "none", int $length = 0)
{

    $validator = 1;

    switch ($type) {
        case 'token':

            $length = 32;
            $validator = 2;
            break;

        case 'id':

            $length = 16;
            break;

        case 'validator':

            $length = 32;
            break;

        case 'none':

            if ($length === 0) {

                $length = 8;
            }
            break;
    }

    $characters = "0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ";

    for ($j = 1; $validator >= $j; $j++) {

        $randomString = "";
        $randomArray = [];

        if ($type === 'id') {

            for ($i = 0; $i < $length; $i++) {

                $randomArray[] = $characters[rand(0, 9)];
            }

            $randomString = implode("", $randomArray);

            return $randomString;
        } else {

            for ($i = 0; $i < $length; $i++) {

                $randomArray[] = $characters[rand(0, strlen($characters) - 1)];
            }

            $randomString = implode("", $randomArray);

            if ($validator === 2) {

                $token[] = $randomString;
            }
        }
    }
    if (isset($token)) {

        return $token;
    } else {

        return $randomString;
    }
}

function hashed_str($type = 'token')
{

    $raw_token = rand_str($type);

    $token[0] = hash('sha256', $raw_token[0]);

    if ($type === "token") {

        $token[1] = hash('sha256', $raw_token[1]);
    }

    return $token;
}
