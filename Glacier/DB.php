<?php
/**
 * Created by PhpStorm.
 * User: jacktablet
 * Date: 2017/1/4
 * Time: 10:17
 */

namespace glacier\framework;
use Illuminate\Database\Capsule\Manager as Capsule;


class DB
{
    public $capsule;

    public function __construct()
    {
        //DB Begin
        $capsule = new Capsule;
        $capsule->addConnection([
            'driver'    => 'mysql',
            'host'      => 'localhost',
            'database'  => 'python',
            'username'  => 'root',
            'password'  => '123456',
            'charset'   => 'utf8',
            'collation' => 'utf8_unicode_ci',
            'prefix'    => '',
        ]);

// Make this Capsule instance available globally via static methods... (optional)
      //  $capsule->setAsGlobal();

// Setup the Eloquent ORM... (optional; unless you've used setEventDispatcher())
        $capsule->bootEloquent();
//DB END
        $this->capsule=$capsule;
    }

    public function __call($name, $arguments)
    {
        $this->capsule->$name($arguments);
    }

}