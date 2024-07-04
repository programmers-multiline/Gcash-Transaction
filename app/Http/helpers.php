<?php

function add_public(){
    if(env("APP_ENV") == 'local'){
         env('ASSET_URL', '/');
    }else{
         env('ASSET_URL', '/public');
    }
}

?>