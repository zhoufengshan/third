<?php
namespace app\index\controller;
use connect\Qq;
use think\Loader;
use think\Controller;
use think\Request;
use think\Config;
use third\Application;
use pay\Wechat;
class Index extends Controller
{
    public function index()
    {
        return $this->fetch();
    }

    public function third(){
        $action = $this->request->param('action');
        $platform = $this->request->param('platform');
        $config = Config::get('third');
        if (!isset($config[$platform]))
        {
            $this->error(('Invalid parameters'));
            return;
        }
        $thirdapp = new Application();
        if ($action == 'redirect')
        {
            // 跳转到登录授权页面
            $this->redirect($thirdapp->{$platform}->getAuthorizeUrl());
        }
        else if ($action == 'callback')
        {
            //登录成功后操作
            $result = $thirdapp->{$platform}->getUserInfo();
            var_dump($result);
        }
        else
        {
            $this->error(('Invalid parameters'));
        }

        return;
    }

    public function pay(){
        $wpay = new Wechat();
        $url = $wpay->getCodeUrl('test', 123456789, 1200, 963852741, $ext = null);
        Loader::import('pay/phpqrcode', EXTEND_PATH);
        //$img = \QRcode::png(urlencode($url));
        //var_dump($img);
        \QRcode::png($url, false, 'L', 4);
        echo '<img src=http://127.0.0.1/index/index/pay/>';
    }
}
