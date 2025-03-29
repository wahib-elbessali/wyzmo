<?php

class Validator
{
    public static function registerForm($userData)
    {
        $errors = [];
        $firstName =  $userData["first_name"];
        $lastName = $userData["last_name"];
        $email = $userData["email"];
        $password = $userData["password"];
        $confirmPassword = $userData["confirm_password"];
        $role = $userData["role"];

        if (empty($firstName)) {
            $errors["first_name"] = "firstname is required.";
        } elseif (!preg_match("/^[a-zA-Z0-9_]+$/", $firstName)) {
            $errors["last_name"] = "first name must only contain letters, numbers, and underscores.";
        }

        if (empty($lastName)) {
            $errors["last_name"] = "Last name is required.";
        } elseif (!preg_match("/^[a-zA-Z0-9_]+$/", $lastName)) {
            $errors["last_name"] = "Last name must only contain letters, numbers, and underscores.";
        }

        if (empty($email)) {
            $errors["email"] = "Email is required.";
        } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errors["email"] = "Invalid email Format";
        }

        if (empty($password)) {
            $errors["password"] = "password is required.";
        } elseif (strlen($password) < 8) {
            $errors["password"] = "the password must be at least 8 characters long. ";
        } elseif (!preg_match("/[A-Za-z]/", $password) || !preg_match("/[0-9]/", $password)) {
            $errors["password"] = "The password must contain at least one letter and one number";
        }

        if (empty($confirmPassword)) {
            $errors["confirm_password"] = "Confirm password is required.";
        } elseif ($password !== $confirmPassword) {
            $errors["confirm_password"] = "The password and password do not match";
        }

        if (empty($role)) {
            $errors["role"] = "The role is required.";
        } elseif ($role !== "STUDENT" and $role !== "ADMIN" and $role != "PROFESSEUR") {
            $errors["role"] = "Invalid Role.";
        }
        return $errors;
    }

    public static function loginForm($loginForm)
    {
        $errors = [];
        $email = $loginForm["email"];
        $password = $loginForm["password"];
        if (empty($email)) {
            $errors["email"] = "Email is required.";
        } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errors["email"] = "Invalid email Format";
        }

        if (empty($password)) {
            $errors["password"] = "password is required.";
        } elseif (strlen($password) < 8) {
            $errors["password"] = "the password must be at least 8 characters long. ";
        } elseif (!preg_match("/[A-Za-z]/", $password) || !preg_match("/[0-9]/", $password)) {
            $errors["password"] = "The password must contain at least one letter and one number";
        }

        return $errors;
    }

    public static function projectForm($projectData)
    {
        $errors = [];

        $projectName = $projectData["name"];
        $projectDescription = $projectData["description"];
        $projectDateDebut = $projectData["date_debut"];
        $projectDateFin = $projectData["date_fin"];

        if (empty($projectName)) {
            $errors["project_name"] = "Project name is required.";
        } elseif (strlen($projectName) < 3) {
            $errors["project_name"] = "Project name must be at least 3 characters long.";
        }

        if (empty($projectDescription)) {
            $errors["project_description"] = "Project description is required.";
        } elseif (strlen($projectDescription) < 1) {
            $errors["project_description"] = "Project description must be at least 1 characters long.";
        }

        if (empty($projectDateDebut)) {
            $errors["date_debut"] = "Project start date is required.";
        } elseif (!self::isValidDate($projectDateDebut)) {
            $errors["date_debut"] = "Invalid start date format. Use YYYY-MM-DD.";
        }

        if (empty($projectDateFin)) {
            $errors["date_fin"] = "Project end date is required.";
        } elseif (!self::isValidDate($projectDateFin)) {
            $errors["date_fin"] = "Invalid end date format. Use YYYY-MM-DD.";
        } elseif ($projectDateFin < $projectDateDebut) {
            $errors["date_fin"] = "End date cannot be before the start date.";
        }

        return $errors;
    }

    private static function isValidDate($date)
    {
        $d = DateTime::createFromFormat('Y-m-d', $date);
        return $d && $d->format('Y-m-d') === $date;
    }


    public static function taskForm($taskData)
    {
        $errors = [];

        $taskName = $taskData["name"] ?? "";
        $taskDescription = $taskData["description"] ?? "";
        $taskStartDate = $taskData["start_date"] ?? "";
        $taskEndDate = $taskData["end_date"] ?? "";
        $taskPriority = $taskData["priority"] ?? "";
        $taskStatus = $taskData["status"] ?? "";
        $assigned_to = $taskData["assigned_to"] ?? "";

        if (empty($taskName)) {
            $errors["name"] = "Task name is required.";
        } elseif (strlen($taskName) < 3) {
            $errors["name"] = "Task name must be at least 3 characters long.";
        }

        if (empty($taskDescription)) {
            $errors["description"] = "Task description is required.";
        } elseif (strlen($taskDescription) < 1) {
            $errors["description"] = "Task description must be at least 1 characters long.";
        }

        if (empty($assigned_to)) {
            $errors["assigned_to"] = "Assignee is required.";
        }

        if (empty($taskStartDate)) {
            $errors["start_date"] = "Task start date is required.";
        } elseif (!self::isValidDate($taskStartDate)) {
            $errors["start_date"] = "Invalid start date format. Use YYYY-MM-DD.";
        }

        if (empty($taskEndDate)) {
            $errors["end_date"] = "Task end date is required.";
        } elseif (!self::isValidDate($taskEndDate)) {
            $errors["end_date"] = "Invalid end date format. Use YYYY-MM-DD.";
        } elseif ($taskEndDate < $taskStartDate) {
            $errors["end_date"] = "End date cannot be before the start date.";
        }

        $validPriorities = ["high", "medium", "low"];
        if (empty($taskPriority)) {
            $errors["priority"] = "Task priority is required.";
        } elseif (!in_array($taskPriority, $validPriorities)) {
            $errors["priority"] = "Invalid priority. Choose high, medium, or low.";
        }

        $validStatuses = ["todo", "in_progress", "done"];
        if (empty($taskStatus)) {
            $errors["status"] = "Task status is required.";
        } elseif (!in_array($taskStatus, $validStatuses)) {
            $errors["status"] = "Invalid status. Choose Todo, In Progress, or Done.";
        }

        return $errors;
    }

}
