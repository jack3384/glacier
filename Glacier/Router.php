<?php

namespace glacier\framework;

class Router
{
    protected $method;
    protected $uri;
    protected $controller;
    protected $action;
    protected $argus = array();

    public function __construct()
    {
        $this->uri = $_SERVER['REQUEST_URI'];
        $this->method = $_SERVER['REQUEST_METHOD'];
        if (isset($_GET['route'])) {
            $route = explode('/', $_GET['route']);
            if (count($route) == 2) {
                $this->controller = $route[0];
                $this->action = empty($route[1])?"index":$route[1];
            } else {
                throw new \Exception("请正确输入控制器与方法名", 404);
                // trigger_error("请正确输入控制器与方法名",E_USER_ERROR);
                //报错代码
            }
            if (!empty($_GET['argu'])) {
                $this->argus = explode('/', $_GET['argu']);
            }
        } else {
            //没有设置控制器名都默认都导入到首页
            $this->controller = "Index";
            $this->action = "index";
            //throw new \Exception("请正确输入控制器与方法名", 404);
        }
    }

    public function getDestinationInfo()
    {
        $controller=$this->controller;
        $action=$this->action;
        $argus=$this->argus;
        return compact('controller','action','argus');
    }

}