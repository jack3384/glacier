<?php
//注册异常处理类
set_exception_handler(array('\glacier\framework\ExceptionHandler', 'handle')); //设置异常处理器
set_error_handler(array('\glacier\framework\ErrorHandler', 'handle'));//设置错误处理类

//实例化容器
$app=new \Illuminate\Container\Container();
$app->instance('app',$app);
$app->instance('\Illuminate\Container\Container',$app);

$config = new \glacier\framework\Config(); //注册配置为全部函数
$kernel = $app->make(\glacier\framework\Kernel::class); //获得kernel实例
$app->instance('config',$config);
$app->instance('kernel',$kernel);

//注册provider提供的内容alias 和 服务绑定
$providers= require_once dirname(__DIR__)."/Configs/providers.php";

foreach ($providers['alias'] as $itemName=>$class){
    $app->alias($itemName,$class);
}
foreach ($providers['bindings'] as $itemName=>$class){
    $app->bind($itemName,$class);
}

require_once __DIR__."/helper.php";

return $app;