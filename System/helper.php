<?php
/**
 * Created by PhpStorm.
 * User: jacktablet
 * Date: 2017/1/6
 * Time: 15:24
 */

//全局helper
function app()
{
    return $GLOBALS['app'];
}

function db($dsnName = "dsn")
{
    if (!isset($GLOBALS['capsule'][$dsnName])) {
        //DB Begin
        $capsule = new Illuminate\Database\Capsule\Manager;
        $dsn = $GLOBALS['app']['config'][$dsnName];
        $capsule->addConnection($dsn);
// Make this Capsule instance available globally via static methods... (optional)
        $capsule->setAsGlobal();
// Setup the Eloquent ORM... (optional; unless you've used setEventDispatcher())
        $capsule->bootEloquent();
        if (!isset($GLOBALS['capsule'])) {
            $GLOBALS['capsule'] = [];
        }
        $GLOBALS['capsule'][$dsnName] = $capsule;
    }
    return $GLOBALS['capsule'][$dsnName];
}

function view($viewName = null, $vars = [])
{
    $viewsPath = dirname(__DIR__) . "/Views";
    $view = new \glacier\framework\Viewer($viewsPath);
    if (null === $viewName) {
        return $view;
    } else {
        return $view->render($viewName, $vars);
    }
}

function config($fileName="default")
{
    return new \glacier\framework\Config($fileName);
}
//全局helper End