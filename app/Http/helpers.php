<?php

function add_public(){
    if(env("APP_ENV") != 'local'){
     return 'public';
    }else{
        return '';
    }
}

?>