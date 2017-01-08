<?php
namespace glacier\controller;

use glacier\framework\Tool;

class Demo
{
    public $middleware=[\glacier\middleware\Demo2::class=>'on'];
    public function index()
    {
       $demo= db()->table('demo')->first();
      yield view('demo',['test'=>$demo->message]);
      //上面的内容会立刻输出到浏览器,剩下的留在后台慢慢执行。
      sleep(10);
     // file_put_contents('1.txt','sssssssss');
    }

}