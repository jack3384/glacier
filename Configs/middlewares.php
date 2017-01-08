<?php

//全局中间件，对所有控制器方法生效
return [
    \glacier\middleware\Demo::class,
    \glacier\middleware\Demo2::class,
];