<?php
/**
 * Created by PhpStorm.
 * User: jacktablet
 * Date: 2017/1/3
 * Time: 17:06
 */

namespace glacier\middleware;
use Closure;


class Demo
{

    public function handle($controller, Closure $next)
    {
        echo "1111";

       $res= $next($controller);

       echo "2222";
        return $res;

    }

}