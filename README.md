# glacier
- 框架定位：轻量级框架，主要用于学习研究，用法类似laravel
- 不推荐用于生产环境，本人用作小项目：比如微信公众号这样的小程序
- 尽量采用的是PHP原生的方式，但是运用了laravel的容器、中间件、eloqunent组件
- 视图采用最原始的方式，与yii1.1用法差不多
- 请求与响应等也没有进行类的封装(laravel用的symfony)，这样便于学习理解http协议。


# 基本用法

请先使用：`composer install`安装依赖

**基本用法：**
访问： `/index.php?route=控制器名/方法名&argu=参数1/参数2...`

.htacesss 做了规则重定向所以可以方便的访问
访问： `url/控制器名/方法名/[参数1/参数2]`

例如`127.0.0.1/Admin/login` 这个时候将不带参数

也可以 `127.0.0.1/Admin/login?getname=myname` 通过$_GET['getname']获得传递的数据

## 配置文件
`Configs/default.php` 为默认配置文件
可配置已经是否显示debug信息和默认数据库
默认数据库为自带的sqlite3类型，可以将`database`信息替换成mysql的，
也可以多定义几个数据库连接信息后面model里会讲怎么用多数据库连接

## 控制器
```
未指定控制器与方法默认访问Index/index

//首先定义命名空间
namespace glacier\controller;

//定义方法
public function yourfuc(){}
//这样就可以通过 127.0.0.1/yourClass/yourfuc 实现访问了

//or 定义依赖有带参数的方法
public function yourfuc(Tool $tool,$argu1,$argu2){}
//Tool类会自动实例化，与laravel用法一致
//这样就可以通过 127.0.0.1/yourClass/yourfuc/参数1的值/参数2的值 实现访问了

//or 定义带参数的方法
public function yourfuc($argu1,$argu2){}
//这样就可以通过 127.0.0.1/yourClass/yourfuc/参数1的值/参数2的值 实现访问了

```
#### 特色功能
使用`yield`属性可以让程序先返回输出到浏览器再倒后台默默执行其他操作。
```
class Demo
{
    //定义控制器中间件
    public $middleware=[\glacier\middleware\Demo2::class=>'on'];
    
    public function index()
    {
        //eloquent的使用
       $demo= db()->table('demo')->first();
       
       //使用yield后，下面的内容会立刻输出到浏览器,剩下的留在后台慢慢执行。
      yield view('demo',['test'=>$demo->message]);
      
      sleep(10);
     // file_put_contents('1.txt','sssssssss');
    }
}

```
#### 根据返回不同的类型输出不同的内容
```
/*----------华丽的分界线-------------*/
//输出
return "字符串"; //输出字符串
return array(); //输出json

//视图函数
return view('demo',['test'=>$demo->message]);
```
## Model
采用laravel的`eloquent`,通过全局函数`db()`可以获得其实例
```
    //eloquent的使用
       $demo= db()->table('demo')->first();
```

## 视图
视图文件存放在`Views`目录下，请注意防范`XSS`攻击
```
YII1.1类似语法
控制器return view("admin",array("key"=>"value"));
//控制器传递进去的数据键值即为视图里的变量名

<?php echo $this->root ?> //输出变量，$this->root表示输出当前的url根目录地址
//or
<?php echo $name ;?>  //也可以是控制器render()方法传递进去的变量

控制器里也可以使用layout
view()->layout('parent')->render('child');;
//父视图里需要嵌入子视图的地方加入这段代码<?php include $layout; ?>
//传入的变量 所有视图是共用的

```

## 中间件
#### 中间件的开关
```
Configs/middlewares.php 设置全局中间件

控制器里通过
public $middleware=[\glacier\middleware\Demo2::class=>'on'];
设置局部中间件
格式：
\glacier\middleware\Demo2::class=>'on'
\glacier\middleware\Demo2::class=>'off'
\glacier\middleware\Demo2::class=>'only:method1|method2'
\glacier\middleware\Demo2::class=>'except:method1|method2'
```
#### 中间件的定义
`Middlewares`目录下定义，方式同laravel
不同的是laravel接收的参数是`$request`,这里接收的是`$controller`获得控制器实例,以方便进行控制器检测等操作
要操作http信息通过`$_GET`等超全局函数来处理
```angular2html

namespace glacier\middleware;
use Closure;

class Demo
{

    public function handle($controller, Closure $next)
    {
        //执行前动作

       $res= $next($controller);

       //执行后动过;
        return $res;

    }

}
```

## 异常
```
/*有任何异常抛出或者错误警告都会中断脚本执行，抛出异常界面通常状态码为500,
如果Configs/default.php 中开启了debug将显示 异常信息与脚本执行的步骤等
入需要手动抛出异常脚本： */
throw new \Exception ("错误提示信息",500); //500为状态码可以修改比如404
```

#### 三个全局函数
- `app()`获得容器实例
- `db()`获得eloquent实例
- `view()` 获得Viewer实例


