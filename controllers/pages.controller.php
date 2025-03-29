<?php

class PageController
{
    public function loginPage()
    {
        require __DIR__  . "/../views/pages/login.html";
    }

    public function registerPage()
    {
        require __DIR__  . "/../views/pages/register.html";
    }


    public function workspacePage()
    {
        $role = $_SESSION["role"];

        if($role == "STUDENT"){
            require __DIR__  . "/../views/pages/student.html";
        }elseif ($role == "PROFESSEUR"){
            require __DIR__  . "/../views/pages/professeur.html";
        }
    }

    public function landingPage()
    {
        require __DIR__  . "/../views/pages/landing_page.html";
    }

    public function profilePage(){
        require __DIR__  . "/../views/pages/profile.html";
    }

    public function adminPage(){
        require __DIR__  . "/../views/pages/admin.html";
    }
}
