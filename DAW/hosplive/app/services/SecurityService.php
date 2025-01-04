<?php
    require_once "config/config.php";
    use const Config\config_recaptcha;
    
    class SecurityService{
        //Checks if the CSRF token is valid
        public static function checkCSRFToken(string $token): bool{
            if (!isset($_SESSION["csrf_tokens"]))
                return false;

            $filtered_csrf = array_filter($_SESSION["csrf_tokens"], function($t) use ($token){
                return hash_equals($t, $token);
            });

            return count($filtered_csrf) > 0;
        }

        //Generates a CSRF token and adds it to the list of valid tokens stored in the session
        //Returns the generated token
        public static function generateCSRFToken():string{
            $token = bin2hex(random_bytes(32));

            //Setting the first token
            if (!isset($_SESSION["csrf_tokens"]))
                $_SESSION["csrf_tokens"] = [$token];
            else if (count($_SESSION["csrf_tokens"]) > 20) //Keep the last 20 tokens
                array_shift($_SESSION["csrf_tokens"]);

            $_SESSION["csrf_tokens"][] = $token;
            return $token;
        }

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