<?php

function add_public(){
    if(env("APP_ENV") == 'local'){
        echo env('ASSET_URL', '/');
    }else{
        echo env('ASSET_URL', '/public');
    }
}

?>