<?php 
class ExecuteImages {
    function copyImages($url = NULL, $name){
        if ($url != NULL) {
            $name_Save = explode('.',$name);
            $contents = file_get_contents($url);
            $saveImage = 'public/images/'.$name_Save[0].'.jpg';
            file_put_contents($saveImage,$contents);
        }
    }
}