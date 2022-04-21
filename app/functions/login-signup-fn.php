<?php

function validation($input, $name)
{

    if ($name == "username") {

        if (preg_match('/^[a-zA-Z\_\.]{3,32}$/', $input) == true) {

            return true;
        } else {

            return false;
        }
    } elseif ($name == "phone") {

        if (preg_match('/^0[0-9]{10}$/', $input) == true) {

            return true;
        } else {

            return false;
        }
    } elseif ($name == "name") {

        if (preg_match('/^[a-zA-Z\s]{3,32}$/', $input) == true) {

            return true;
        } else {

            return false;
        }
    } elseif ($name == "email") {

        if (preg_match('/^[a-zA-Z0-9\_\.]+@[a-zA-Z0-9]+\.[a-zA-Z]+$/i', $input) == true) {

            return true;
        } else {

            return false;
        }
    } elseif ($name == "password") {

        if (preg_match('/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)[a-zA-Z\d]{4,32}$/', $input) == true) {

            return true;
        } else {

            return false;
        }
    } elseif ($name == "about") {

        if (strlen($input) <= 150) {

            return true;
        } else {

            return false;
        }
    } elseif($name == "nickname"){

        if (strlen($input) <= 32) {

            return true;
        } else {

            return false;
        }
    } else{

        return NULL;
    }
}
