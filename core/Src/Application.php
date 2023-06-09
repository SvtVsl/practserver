<?php
namespace Src;

use Error;
use Illuminate\Container\Container;
use Illuminate\Events\Dispatcher;
use Illuminate\Database\Capsule\Manager as Capsule;
use Src\Auth\Auth;

class Application
{
    private Settings $settings;
    private Route $route;
    private Capsule $dbManager;
    private Auth $auth;

    public function __construct(Settings $settings)
    {
        $this->settings = $settings;   //Привязываем класс со всеми настройками приложения
        $this->route = new Route($this->settings->getRootPath());  //Привязываем класс маршрутизации с установкой префикса
        $this->dbManager = new Capsule();  //Создаем класс менеджера для базы данных
        $this->auth = new $this->settings->app['auth'];  //Создаем класс для аутентификации на основе настроек приложения

        $this->dbRun();  //Настройка для работы с базой данных
        $this->auth::init(new $this->settings->app['identity']);  //Инициализация класса пользователя на основе настроек приложения
    }

    public function __get($key)
    {
        switch ($key) {
            case 'settings':
                return $this->settings;
            case 'route':
                return $this->route;
            case 'auth':
                return $this->auth;
        }
        throw new Error('Accessing a non-existent property');
    }

    private function dbRun()
    {
        $this->dbManager->addConnection($this->settings->getDbSetting());
        $this->dbManager->setEventDispatcher(new Dispatcher(new Container));
        $this->dbManager->setAsGlobal();
        $this->dbManager->bootEloquent();
    }


    public function run(): void
    {
        //Запуск маршрутизации
        $this->route->start();
    }

}

