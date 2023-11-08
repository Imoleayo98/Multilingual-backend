<?php

require_once 'errors.php';
class Translato extends Errors {
    var $id;
    var $description;
    var $title;
    var $severity;
    var $date;
    var $status;
    var $to;
    var $from;

    function __construct($db){
        $this->conn = $db;
    }

    function createIncident(){
        $result = array();
        try{
            $query2 = $this->conn->prepare("INSERT INTO incidents SET severity=:severity, title=:title, description=:description,status='Pending'");
            if($this->to == 'en'){
                $query2->bindParam(':description', $this->description);
                $des = $this->getSeverity($this->description);
                if($des == 'error'){
                    return $this->FourHundred('Error getting severity');
                }
                $query2->bindParam(':severity', $des);
                $query2->bindParam(':title', $this->title);
            }else{
                $query2->bindParam(':description', $this->translateText('en', $this->to, $this->description));
                $des = $this->getSeverity($this->translateText('en', $this->to, $this->severity));
                if($des == 'error'){
                    return $this->FourHundred('Error getting severity');
                }
                $query2->bindParam(':severity', $des);
                $query2->bindParam(':title', $this->translateText('en', $this->to, $this->title));
            }
            if($query2->execute()){
                if($this->to == 'en'){
                    $result['message'] = "Incident created";
                    $result['status'] = "success";
                }else{
                    $result['message'] = $this->translateText($this->to, 'en', "Incident created");
                    $result['status'] = "success";
                }
            }else{
                return $this->FourFourTwo();
            }
        }catch(PDOException $e){
            return $this->FiveHundred($e);
        }
        return $result;
    }

    function getIncidents(){
        $result = array();
        $result['data'] = array();
        //return $this->to;
        try{
            $con = $this->conn;
            $query = $con->prepare("SELECT id, `severity`,title,date,status FROM incidents");
            if($query->execute()){
                $count = $query->rowCount();
                if($count>0){
                    while($row = $query->fetch(PDO::FETCH_ASSOC)){
                        extract($row);
                        $all_incidents= array(
                            'id' => $id,
                            'title' => $this->translateText($this->to, 'en', $title),
                            'severity' => $this->translateText($this->to, 'en', $severity),
                            'date' => $this->translateText($this->to, 'en', $date),
                            'status' => $this->translateText($this->to, 'en', $status),
                        );
                        array_push($result['data'], $all_incidents);
                    }
                    $result['status'] = 'success';
                }else{
                    $result['message'] = $this->translateText($this->to, 'en', "No incidents available");
                    $result['status'] = 'success';
                }
                
            }else{
                return $this->FourFourTwo();
            }
        }catch(PDOException $e){
            return $this->FiveHundred($e);
        }
        return $result;
    }

    function updateIncident(){
        $result = array();
        try {
            $query = $this->conn->prepare("UPDATE incidents SET `status` =:status WHERE id = $this->id");
            $query->bindParam(':status', $this->translateText('en', $this->from, $this->status));
            if($query->execute()){
                $count = $query->rowCount();
                if($count > 0){
                    $result['message'] = $this->translateText($this->to, 'en', 'Incident updated');
                    $result['status'] = 'success';
                }else{
                    $result['message'] = $this->translateText($this->to, 'en', 'No update made');
                    $result['status'] = 'success';
                    // http_response_code(400);
                }
            }else{
                return $this->FiveHundred();
            }

        }catch(PDOException $e) {
            return $this->FiveHundred($e);
        }

        return $result;
    }

    function getSingleIncident(){
        $result = array();
        $result['data'] = array();
        try{
            $con = $this->conn;
            $query = $con->prepare("SELECT id, `severity`,title,date,status,description FROM incidents WHERE id = $this->id");
            if($query->execute()){
                $count = $query->rowCount();
                if($count>0){
                        $row = $query->fetch(PDO::FETCH_ASSOC);
                        extract($row);
                        $all_incidents= array(
                            'id' => $id,
                            'title' => $this->translateText($this->to, 'en', $title),
                            'severity' => $this->translateText($this->to, 'en', $severity),
                            'date' => $this->translateText($this->to, 'en', $date),
                            'status' => $this->translateText($this->to, 'en', $status),
                            'status2' => $status,
                            'description' => $this->translateText($this->to, 'en', $description) 
                        );
                        $result['data'] = $all_incidents;
                    $result['status'] = 'success';
                }else{
                    $result['message'] = $this->translateText($this->to, 'en', 'no incident found');
                    $result['status'] = 'success';
                }
                
            }else{
                return $this->FourFourTwo();
            }
        }catch(PDOException $e){
            return $this->FiveHundred($e);
        }
        return $result;
    }

    function translateText($to, $from, $text){
        try{
            $api_endpoint = 'https://api.cognitive.microsofttranslator.com/translate?api-version=3.0&to='.$to.'&from='.$from;
            $subscription_key = "095c98e9454542e099e666f699d33425";
            $text = $text;

            $data = array(
                array('Text' => $text)
            );

            $request_data = json_encode($data);
            $ch = curl_init($api_endpoint);

            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $request_data);
            curl_setopt($ch, CURLOPT_HTTPHEADER, array(
                'Content-Type: application/json',
                'Ocp-Apim-Subscription-Key: ' . $subscription_key,
                'Content-Length: ' . strlen($request_data),
                "Ocp-Apim-Subscription-Region:westeurope"
            ));
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

            $response = curl_exec($ch);

            if (curl_errno($ch)) {
                // return 'cURL Error: ' . curl_error($ch);
                return 'error';
            }
            curl_close($ch);

            if ($response) {
                $translated_text = json_decode($response, true);
                //return $translated_text;
                return $translated_text[0]['translations'][0]['text'];
            } else {
                return 'error';
            }
        }catch(PDOException $e){
            // return $this->FiveHundred($e);
            return 'error';
        }
    }

    function getSeverity($text){
        try{
            $api_endpoint = 'https://mcairs.onrender.com/get-severity';
            $data = [
                "data" => [
                    $text,
                ]
            ];

            $request_data = json_encode($data);
            $ch = curl_init($api_endpoint);

            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $request_data);
            curl_setopt($ch, CURLOPT_HTTPHEADER, array(
                'Content-Type: application/json',
            ));
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            $response = curl_exec($ch);
            if (curl_errno($ch)) {
                // return 'cURL Error: ' . curl_error($ch);
                return 'error';
            }
            curl_close($ch);

            if ($response) {
                $sev = json_decode($response, true);
                return $sev[0]['severity'];
            } else {
                return 'error';
            }
        }catch(PDOException $e){
            // return $this->FiveHundred($e);
            return 'error';
        }
    }
}