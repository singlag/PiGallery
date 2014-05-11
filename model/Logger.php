<?php

namespace piGallery\model;


class Logger {

    public static function v($tag, $str){
        error_log("[".date(DATE_RFC2822)."][VERBOSE]"."[".$tag."] ".$str."\n\10", 3, __DIR__ . "./../log/log.txt");
    }
} 