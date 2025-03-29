<?php

require_once __DIR__ . "/../Model/User.model.php";
require_once __DIR__ . "/../utils/response/index.php";
require_once __DIR__ . "/../utils/validator/index.php";
require_once __DIR__ . "/../utils/customException/index.php";
require_once __DIR__ . "/../utils/response/index.php";

class AuthController
{
    private $userModel;
    public function __construct()
    {
        try {
            $this->userModel = new UserModel();
        } catch (CustomException $error) {
            sendJsonResponse(
                $error->statusCode,
                $error->message,
                $error->data
            );
        }
    }

    public function login()
    {
        try {
            // Retrieve the login data from the request body
            $loginFormData = json_decode(file_get_contents("php://input"), true);

            // Validate the login form data
            $loginFromValidationErrors = Validator::loginForm($loginFormData);

            if (!empty($loginFromValidationErrors)) {
                // If validation fails, throw a custom exception with the errors
                throw new CustomException(
                    400,
                    "Error: Login Form validation failed",
                    $loginFromValidationErrors,
                    false
                );
            }

            // Retrieve user information from the database using the provided email
            $user = $this->userModel->findByEmail($loginFormData["email"]);

            // If no user is found with this email, throw an exception
            if (!$user) {
                throw new CustomException(
                    404,
                    "Error: There is no account registered with this email.",
                    null,
                    false
                );
            }

            // Verify if the provided password matches the hashed password in the database
            $isCorrectPassword = password_verify(
                $loginFormData["password"],
                $user["password"]
            );

            // If the password is incorrect, throw an exception
            if (!$isCorrectPassword) {
                throw new CustomException(
                    401,
                    "Error: Email or password incorrect",
                    null,
                    false
                );
            }

            // Start a session and store user information in $_SESSION
            $_SESSION["user_id"] = $user["user_id"];
            $_SESSION["role"] = $user["role"];
            $_SESSION["email"] = $user["email"];

            // Send a JSON response with user information
            sendJsonResponse(
                200,
                "Login Successfully.",
                [
                    "user_id" => $user["user_id"],
                    "email" => $user["email"],
                    "first_name" => $user["first_name"],
                    "last_name" => $user["last_name"],
                    "role" => $user["role"]
                ]
            );

        } catch (CustomException $error) {
            // If an error occurs, catch the exception and return an appropriate JSON response
            sendJsonResponse(
                $error->statusCode,
                $error->message,
                $error->data
            );
        }
    }

    public function register()
    {
        try {
            // Retrieve user registration data from the request body
            $userData = json_decode(file_get_contents("php://input"), true);

            // Validate the registration form data
            $registerFormErrors = Validator::registerForm($userData);

            // If validation fails, throw a custom exception with the validation errors
            if (!empty($registerFormErrors)) {
                throw new CustomException(
                    400,
                    "Validation Failed",
                    $registerFormErrors,
                    false
                );
            }

            // Hash the password before storing it in the database for security reasons
            $hashPassword = password_hash($userData["password"], PASSWORD_BCRYPT);
            $userData["password"] = $hashPassword;

            // Create a new user record in the database
            $this->userModel->createUser($userData);

            // Set response headers and return a success message
            sendJsonResponse(200, "Account created successfully.", null);

        } catch (CustomException $error) {
            // If an error occurs, catch the exception and return an appropriate JSON response
            sendJsonResponse(
                $error->statusCode,
                $error->message,
                $error->data
            );
        }
    }

    public function logout()
    {
        session_destroy();
        header("Location: /register");
    }

}
