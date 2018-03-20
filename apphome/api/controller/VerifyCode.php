<?php
namespace app\api\controller;

use think\Db;
use think\Request;
use app\common\lib\Token;
use app\common\lib\Helper;
use app\common\lib\ReturnData;
use app\common\logic\VerifyCodeLogic;

class VerifyCode extends Base
{
	public function _initialize()
	{
		parent::_initialize();
    }
    
    public function getLogic()
    {
        return new VerifyCodeLogic();
    }
    
    //手机验证码校验
    public function check()
	{
        //参数
        $where['mobile'] = input('mobile', null); //手机号码
        $where['verifyCode'] = input('verifyCode', null); //手机验证码
        $where['type'] = input('type', null); //验证码类型
        
        if ($where['mobile']==null || $where['verifyCode']==null || $where['type']==null)
		{
            exit(json_encode(ReturnData::create(ReturnData::PARAMS_ERROR)));
        }
        
        if (!Helper::isValidMobile($where['mobile']))
		{
			exit(json_encode(ReturnData::create(ReturnData::MOBILE_FORMAT_FAIL)));
		}
        
		$verifyCode = model('VerifyCode')->isVerify($where['mobile'], $where['verifyCode'], $where['type']);
		if(!$verifyCode)
		{
			exit(json_encode(ReturnData::create(ReturnData::INVALID_VERIFYCODE)));
		}
		
		exit(json_encode(ReturnData::create(ReturnData::SUCCESS)));
    }
    
    //列表
    public function index()
	{
        //参数
        $where = array();
        $limit = input('limit',10);
        $offset = input('offset', 0);
        $orderby = input('orderby','id desc');
        
        $res = $this->getLogic()->getList($where,$orderby,'*',$offset,$limit);
		
		exit(json_encode(ReturnData::create(ReturnData::SUCCESS,$res)));
    }
    
    //详情
    public function detail()
	{
        //参数
        if(input('id', '') !== ''){$where['id'] = input('id');}
        if(!isset($where)){exit(json_encode(ReturnData::create(ReturnData::PARAMS_ERROR)));}
        
		$res = $this->getLogic()->getOne($where);
        if(!$res){exit(json_encode(ReturnData::create(ReturnData::PARAMS_ERROR)));}
        
		exit(json_encode(ReturnData::create(ReturnData::SUCCESS,$res)));
    }
    
    //添加
    public function add()
    {
        if(Helper::isPostRequest())
        {
            $res = $this->getLogic()->add($_POST);
            
            exit(json_encode($res));
        }
    }
    
    //修改
    public function edit()
    {
        if(input('id',null)!=null){$id = input('id');}else{$id='';}if(preg_match('/[0-9]*/',$id)){}else{exit(json_encode(ReturnData::create(ReturnData::PARAMS_ERROR)));}
        
        if(Helper::isPostRequest())
        {
            unset($_POST['id']);
            $where['id'] = $id;
            
            $res = $this->getLogic()->edit($_POST,$where);
            
            exit(json_encode($res));
        }
    }
    
    //删除
    public function del()
    {
        if(input('id',null)!=null){$id = input('id');}else{$id='';}if(preg_match('/[0-9]*/',$id)){}else{exit(json_encode(ReturnData::create(ReturnData::PARAMS_ERROR)));}
        
        if(Helper::isPostRequest())
        {
            unset($_POST['id']);
            $where['id'] = $id;
            
            $res = $this->getLogic()->del($where);
            
            exit(json_encode($res));
        }
    }
}