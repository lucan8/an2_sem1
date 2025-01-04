<?php
    require_once "config/config.php";
    use const Config\config_recaptcha;
    
    class RecaptchaService{
        //Returns the response from Google's reCaptcha API
        private static function getRecapthcaResp(string $recaptcha_response): array{
            $secretKey = config_recaptcha["back_key"];
            
            // Verify the response with Google's API
            $verifyUrl =  config_recaptcha["verify_url"];
            $data = [
                'secret' => $secretKey,
                'response' => $recaptcha_response,
                'remoteip' => $_SERVER['REMOTE_ADDR'], // Optional
            ];

            // Make the POST request to Google's API
            $options = [
                'http' => [
                    'header'  => "Content-type: application/x-www-form-urlencoded\r\n",
                    'method'  => 'POST',
                    'content' => http_build_query($data),
                ],
            ];

            // Create context and send the request
            $context  = stream_context_create($options);
            $result = file_get_contents($verifyUrl, false, $context);
            $response = json_decode($result, true);

            return $response;
        }

        //Validates the recaptcha response and returns an error message if invalid, null otherwise
        public static function validateRecaptchaResp(string $recaptcha_response, string $action): string|null{
            $resp = self::getRecapthcaResp($recaptcha_response);

            if (!$resp["success"])
                return "Recaptcha failed with errors: " . implode(", ", array_values($resp["error-codes"]));

            if ($resp["action"] != $action)
                return "Error: Expected recaptcha action is $action, got " . $resp["action"];

            if ($resp["score"] < config_recaptcha["actions"][$action]["threshold"])
                return "Acces denied: You are a bot";

            return null;
        }
    }
?>