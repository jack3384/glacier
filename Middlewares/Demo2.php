<?php
/**
 * Created by PhpStorm.
 * User: jacktablet
 * Date: 2017/1/3
 * Time: 17:06
 */

namespace glacier\middleware;
use Closure;


class Demo2
{

    public function handle($controller, Closure $next)
    {
        echo "333";

       $res= $next($controller);

       echo "444";
        return $res;

    }

}