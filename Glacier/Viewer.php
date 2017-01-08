<?php

namespace glacier\framework;


class Viewer
{
    protected $templatesPath;
    protected $layout;
    protected $layoutVar=array();
    protected $root;/* 视图里面加上 <?php echo $this->root ?> 就可以输出跟目录地址*/
    protected $csrfToken;


    public function __construct($templatesPath)
    {
        if (is_dir($templatesPath)) {
            $this->templatesPath = $templatesPath;
        }else{
            throw new \Exception ("templatesPath Not a dir",500);
        }
        $this->root=str_replace("\\","",dirname($_SERVER['PHP_SELF']));
    }

    /** 分层脚本加上<?php include $layout ?> **/
    public function layout($fileName, array $var = array())
    {
        $this->layoutVar=$var;
        $filePath = $this->templatesPath . "/" . $fileName . ".php";
        if(is_file($filePath)){
            $this->layout=$filePath;
        }else{
            throw new \Exception ("Layout template Not found in".$filePath,404);
        }
        return $this;
    }

    public function clearLayout()
    {
        $this->layout=null;
        return $this;
    }


    public function render($fileName, array $var = array())
    {
        $filePath = $this->templatesPath . "/" . $fileName . ".php";
        if (is_file($filePath)) {
            ob_start();
            extract($var);
            if(!empty($this->layout)){
                $layout=$filePath;//此处不能删，此变量用在layout里面的
                extract($this->layoutVar);
                include $this->layout;
            }else{
                include $filePath;
            }
            $content = ob_get_clean();
            return $content;
        } else {
            throw new \Exception ("template Not found in".$filePath,404);
        }
    }

    public function xml(array $array)
    {
        $xml = "<root>";
        foreach ($array as $key => $val) {
            if (is_array($val)) {
                $xml .= "<" . $key . ">" . xml($val) . "</" . $key . ">";
            } else {
                $xml .= "<" . $key . ">" . $val . "</" . $key . ">";
            }
        }
        $xml .= "</root>";
        echo $xml;
    }

    public function csrfTag()
    {
        $csrfToken=$this->getCsrfToken();
        echo "<input type='hidden' name='csrfToken' id='csrfToken' value='{$csrfToken}'>";
    }

    public function getCsrfToken()
    {
        Tool::startSession();
        $csrfToken=md5(uniqid("token"));
        $_SESSION['csrfToken']=$csrfToken;
        return $csrfToken;
    }
}