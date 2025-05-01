<?php

namespace App\Traits;

trait Notify
{


    public function __construct(){
        $this->firebaseConfiguration =  json_decode(site_settings('firebase_api_key'));
    }

    public function generateOAuthToken() {
  
        $url = "https://www.googleapis.com/oauth2/v4/token";
    
        $postData = [
            'grant_type' => 'urn:ietf:params:oauth:grant-type:jwt-bearer',
            'assertion' => $this->generateJWT()
        ];
    
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($postData));
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Content-Type: application/x-www-form-urlencoded'
        ]);
    
        $response = curl_exec($ch);
        curl_close($ch);
    
        $data = json_decode($response, true);
        return isset($data['access_token']) ? $data['access_token'] : null;
    }
    
    public function generateJWT() {


        $firebaseConfiguration = json_decode(site_settings('firebase_api_key'));

        $payload = [
            'iss' => $firebaseConfiguration->client_email,
            'scope' => 'https://www.googleapis.com/auth/firebase.messaging',
            'aud' => 'https://www.googleapis.com/oauth2/v4/token',
            'exp' => time() + 3600, 
            'iat' => time() 
        ];
        $encodedPayload = base64_encode(json_encode($payload));

        $jwtHeader = [
            'alg' => 'RS256',
            'typ' => 'JWT'
        ];
        $encodedHeader = base64_encode(json_encode($jwtHeader));
        $privateKey = $firebaseConfiguration->private_key;
        openssl_sign("$encodedHeader.$encodedPayload", $signature, $privateKey, OPENSSL_ALGO_SHA256);
        $encodedSignature = base64_encode($signature);
        return "$encodedHeader.$encodedPayload.$encodedSignature";
    }
    
    public function fireBaseNotification(string $fcmToken, object $payload): bool {
     
    
        try {
 
            $firebaseConfiguration = json_decode(site_settings('firebase_api_key'));
            $projectId = @$firebaseConfiguration->project_id; 
            $accessToken = $this->generateOAuthToken();
            $url = "https://fcm.googleapis.com/v1/projects/{$projectId}/messages:send";
            $header = [
                "Authorization: Bearer " . $accessToken,
                "Content-Type: application/json"
            ];

            $postData = json_encode([
                "message" => [
                    "token"=> $fcmToken,
                    "data" => [
                        "title" => @$payload->title,
                        "body" => @$payload->message,
                        "image" => @$payload->image,
                        "deliveryman_id" => (string)@$payload->deliveryman_id,
                        "seller_id" => (string)@$payload->seller_id,
                        "user_id" => (string)@$payload->user_id,
                        "order_number" => (string)@$payload->order_number,
                        "order_uid" => (string)@$payload->order_uid,
                        "order_id" => (string)@$payload->order_id,
                        "product_uid" => (string)@$payload->product_uid,
                        "type" => (string)@$payload->type
                    ],
                    "notification" => [
                        "title" => (string)@$payload->title,
                        "body" => (string)@$payload->message,
                        "image" => (string)@$payload->image,
                    
                    ]
                ]
            ]);
    

            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $postData);
            curl_setopt($ch, CURLOPT_HTTPHEADER, $header);

            $result = curl_exec($ch);
            curl_close($ch);
          

            return true;
    
        } catch (\Exception $ex) {

          return false;
        }
    }
    

}