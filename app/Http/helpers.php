<?php

function add_public(){
    if(env("APP_ENV") != 'local'){
     echo 'public';
    }else{
        echo '';
    }
}

?>