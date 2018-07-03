<?php
foreach ($_REQUEST AS $key=>$value) {

    if (preg_match('/^[a-z]/i', $key)) {

        $$key = $value;

    }
}