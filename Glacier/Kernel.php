<?php
/**
 * Created by PhpStorm.
 * User: jacktablet
 * Date: 2017/1/6
 * Time: 14:28
 */

namespace glacier\framework;
use Illuminate\Pipeline\Pipeline;


class Kernel
{

    public function __construct(Router $router)
    {
        $route = $router->getDestinationInfo();
        $this->controller = $route['controller'];
        $this->action = $route['action'];
        $this->argus = $route['argus'];
    }

    public function run()
    {
        $fullClass = "\\glacier\\controller\\" . $this->controller;
        if (!class_exists($fullClass)) {
            throw new \Exception("控制器文件不存在" . $fullClass, 404);
        }

        if (!method_exists($fullClass, $this->action)) {
            throw new \Exception($this->action . "方法不存在" . $fullClass, 404);
        }

        //实例化控制器类
        $controller = app()->make($fullClass);
        //注册单例到容器供中间件调用
        app()->instance('controller', $controller);

        //注册要用到的中间件
        $this->middlewares=$this->prepareMiddleware();

        //$kernel在下面的匿名函数里==$this
        $kernel = app()['kernel'];

        //调用中间件
        (new Pipeline(app()))->send($controller)->through($this->middlewares)->then(
            function () use ($controller, $kernel) {
                //控制器方法依赖的类实例化
                $this->prepareArgus($controller,$this->action);
                $outPut = call_user_func_array(array($controller, $this->action), $this->argus);
                //处理控制器的输出
                $kernel->response($outPut);
            });

    }

    protected function response($output)
    {
        if (is_string($output)) {
            header('Content-Type: text/html; charset=UTF-8');
            echo $output;
        } elseif (is_array($output)) {
            header('Content-type: application/json');
            echo json_encode($output);
        } elseif ($output instanceof \Generator) {
            $this->resolveGenerator($output);
        } else {
            //没有返回的时候，$outInfo=false
        }
    }

    protected function resolveGenerator(\Generator $gen)
    {
        $content = $gen->current();
        ignore_user_abort(true);
        //清理中间件带来的输出
        ob_clean();
        ob_start();
        header('Connection:close');
        //输出内容
        $this->response($content);
        header('Content-Length:' . ob_get_length());
        ob_end_flush();
        ob_flush(); //必须加这句或者ob_end_flush() 才能立即返回.
        flush();
        while ($gen->valid()) {
            $gen->next();
        }
    }

    protected function prepareMiddleware()
    {
        $globalMiddleware = (new \glacier\framework\Config('middlewares'))->toArray();
        $localMiddleware = $this->prepareLocalMiddleware();
        return array_unique(array_merge(array_diff($globalMiddleware,$localMiddleware['off']),$localMiddleware['on']));
    }

    protected function prepareLocalMiddleware()
    {
        $on=[];
        $off=[];
        $controller = app()['controller'];
        $onMiddleware = isset($controller->middleware) ? $controller->middleware: [];
        foreach($onMiddleware as $key=>$val){
            $type=explode(':',$val);
            if('on'==$type[0]){
                array_push($on,$key);
            }elseif ('off'==$type[0]){
                array_push($off,$key);
            }
            elseif('only'==$type[0]){
                $actions=explode('|',$type[1]);
                if(in_array($this->action,$actions)){
                    array_push($on,$key);
                }
            }elseif('except'==$type[0]) {
                $actions = explode('|', $type[1]);
                if (!in_array($this->action, $actions)) {
                    array_push($on, $key);
                }
            }else{
                throw new \Exception('控制器定义的中间件书写有误');
            }
        }
        return compact('on','off');
    }

    /**
     * 控制器方法可以动态获得hint 的类实例
     */
    protected function prepareArgus($controller,$action)
    {
        $reflect=new \ReflectionMethod($controller,$action);
        $params=$reflect->getParameters();
        if($params){
            $hintInstances=[];
            foreach($params as $para){
                if($hintClass=$para->getClass()){
                    $hintInstances[]=app()->make($hintClass->getName());
                }else{
                    break;
                }
            }
            $this->argus=array_merge($hintInstances,$this->argus);
        }
    }

}