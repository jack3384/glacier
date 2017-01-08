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
    return  $GLOBALS['app'];
}

function db()
{
    if(!isset($GLOBALS['capsule'])){
        //DB Begin
        $capsule = new Illuminate\Database\Capsule\Manager;
        $dsn=$GLOBALS['app']['config']['dsn'];
        $capsule->addConnection($dsn);
// Make this Capsule instance available globally via static methods... (optional)
        $capsule->setAsGlobal();
// Setup the Eloquent ORM... (optional; unless you've used setEventDispatcher())
        $capsule->bootEloquent();
        $GLOBALS['capsule']=$capsule;
    }
    return $GLOBALS['capsule'];
}

function view($viewName=null,$vars=[])
{
    $viewsPath=dirname(__DIR__)."/Views";
    $view=new \glacier\framework\Viewer($viewsPath);
    if(null===$viewName){
        return $view;
    }else{
        return $view->render($viewName,$vars);
    }
}
//全局helper End