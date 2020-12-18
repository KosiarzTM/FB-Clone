<?php

class ResponseHelper {

    const SUCCESS = 200;
    const ERROR = 201; 

    public static function responseContent($status,$msg = '', $data = [], $errors = []) {
        $responseTemplate = [];
        $responseTemplate['status'] = $status;
        
        if($msg !== '')
            $responseTemplate['message'] = $msg;

        if(!empty($data))
            $responseTemplate['data'] = $data;

        if(!empty($errors))
            $responseTemplate['errors'] = $errors;




        return $responseTemplate;
    }
}

 