<?php
namespace Controller;

use Src\View;

class Site
{
    public function index(): string
    {
        $view = new View();
        return $view->render('site.hello', ['message' => 'Я умер, когда это делал']);

    }

    public function hello(): string
    {
        return new View('site.hello', ['message' => 'Fine']);
    }
}
