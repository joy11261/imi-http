<?php
namespace ImiApp\ApiServer\Controller;

use Imi\Controller\HttpController;
use Imi\Server\Route\Annotation\Route;
use Imi\Server\Route\Annotation\Action;
use Imi\Server\Route\Annotation\Controller;
use Imi\Server\Session\Session;

/**
 * @Controller("/session/")
 */
class SessionController extends HttpController
{
    /**
     * 登录状态
     * 
     * @Action
     * 
     * @return void
     */
    public function status()
    {
        $username = Session::get('username');
        return [
            'isLogin'   =>  null !== $username,
            'username'  =>  $username,
        ];
    }

    /**
     * 登录
     * 
     * @Action
     *
     * @param string $username
     * @param string $password
     * @return void
     */
    public function login($username, $password)
    {
        if('imi' === $username && '123456' === $password)
        {
            Session::set('username', $username);
            return [
                'success'   =>  true,
            ];
        }
        else
        {
            return [
                'success'   =>  false,
            ];
        }
    }

    /**
     * 退出登录
     * 
     * @Action
     *
     * @return void
     */
    public function logout()
    {
        // Session::delete('username');
        Session::clear();
    }

    /**
     * 获取验证码
     * 
     * @Action
     *
     * @return void
     */
    public function vcode()
    {
        $vcode = (string)mt_rand(1000, 9999);
        Session::set('vcode', $vcode);
        return [
            'vcode' =>  $vcode,
        ];
    }

    /**
     * 验证验证码
     * 
     * @Action
     *
     * @return void
     */
    public function verifyVcode($vcode)
    {
        $storeVCode = Session::once('vcode');
        return [
            'success'   =>  $storeVCode === $vcode,
        ];
    }

}
