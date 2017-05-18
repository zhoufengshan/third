<?php
namespace app\index\controller;
use connect\Qq;
use think\Log;
use think\Loader;
use think\Controller;
use think\Request;
use think\Config;
use third\Application;
use pay\Wechat;
use think\Db;
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
       	if(Request::instance()->isAjax()){
            $wpay = new Wechat();
            $out_trade_no = mt_rand(00000,99999);
            $product_id = mt_rand(00000,99999);
            $total_fee = Request::instance()->param('total_fee');
            if(empty($total_fee)){
                return json(['code'=>0,'url'=>'','status'=>'false','msg'=>'Please enter the amount']);
            }
            $url = $wpay->getCodeUrl('test', $out_trade_no, $total_fee*100, $product_id, $ext = null);
            if($url !== null){
                Db::name('order')->insert(['product_id'=>$product_id,'total_fee'=>$total_fee,'order_time'=>time(),'status'=>0,'out_trade_no'=>$out_trade_no]);
                return json(['code'=>1,'url'=>$url,'status'=>'success','product_id'=>$product_id,'out_trade_no'=>$out_trade_no]);
            }else{
                return json(['code'=>0,'url'=>'','status'=>'false']);
            }
        }else{
            return json(['code'=>0,'url'=>'','status'=>'false']);
        }
    }
    public function callback(){
        $xml = file_get_contents("php://input");
        $data = $this->xml2array($xml);
        $wechat = new Wechat();
        Db::name('test')->insert(['content'=>$xml]);
        if($data['result_code'] == 'SUCCESS' && $data['result_code'] == 'SUCCESS'){
            $res = Db::name('order')->where(['out_trade_no'=>$data['out_trade_no']])->setField(['status'=>1,'pay_time'=>time()]);
            if($res){
                $wechat->response_back('SUCCESS');
            }else{
                $wechat->response_back('FAIL');
            }
        }else{
            $wechat->response_back('FAIL');
        }
    }
    public function test(){
        $rs = Db::name('test')->where(['id'=>2])->find();
        $data = $this->xml2array($rs['content']);
        dump($data);
        dump(session(''));
    }

    public function confirmOrder(){
        if(Request::instance()->isAjax()){
            $out_trade_no = Request::instance()->param('out_trade_no');
            $data = Db::name('order')->where(['out_trade_no'=>$out_trade_no])->find();
            if($data && $data['status'] == 1){
                return json(['code'=>1,'data'=>$data,'status'=>'success']);
            }else{
                return json(['code'=>0,'data'=>'','status'=>'fail']);
            }
        }else{
            return json(['code'=>0,'data'=>'Failed request mode','status'=>'fail']);
        }
    }

    public function chenggong(){
        return $this->fetch();
    }

    protected function xml2array($xml)
    {
        $array = array();
        $tmp = null;
        try
        {
            $tmp = (array) simplexml_load_string($xml);
        }
        catch (\Exception $e)
        {

        }
        if ($tmp && is_array($tmp))
        {
            foreach ($tmp as $k => $v)
            {
                $array[$k] = (string) $v;
            }
        }
        return $array;
    }

    protected function array2xml($array)
    {
        $xml = "<xml>" . PHP_EOL;
        foreach ($array as $k => $v)
        {
            if ($v && trim($v) != '')
                $xml .= "<$k><![CDATA[$v]]></$k>" . PHP_EOL;
        }
        $xml .= "</xml>";
        return $xml;
    }

    public function order(){
        return $this->fetch();
    }
}
