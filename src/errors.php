<?php
    class Errors{

        var $res;

        function __construct(){
            $this->res = array();
        }

        function FiveHundred($error){
            $this->res['status'] = 'error';
            // $this->res['message'] = $error->getMessage();
            $this->res['message'] = 'Something went wrong';
            http_response_code(500);
            return $this->res;
        }

        function FourFourTwo(){
            $this->res['status'] = 'error';
            $this->res['message'] = 'Something went wrong';
            http_response_code(442);
            return $this->res;
        }

        function FourFourOne(){

        }

        function success($data){

        }

        function FourHundred($message){
            $this->res['status'] = 'error';
            $this->res['message'] = $message;
            http_response_code(400);
            return $this->res;
        }

        function FourZeroFour(){
            $this->res['status'] = 'error';
            $this->res['message'] = 'Not Found';
            http_response_code(404);
            return $this->res;
        }

        function missen($message){
            $res = array();
            $res['status'] = 'error';
            $res['message'] = $message;
            http_response_code(400);
            return json_encode($res);
        }
    }
?>