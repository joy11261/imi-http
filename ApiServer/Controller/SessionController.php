<?php
namespace ImiApp\ApiServer\Controller;

use Imi\Controller\HttpController;
use Imi\Server\Route\Annotation\Route;
use Imi\Server\Route\Annotation\Action;
use Imi\Server\Route\Annotation\Controller;
use Imi\Server\Session\Session;
use \Imi\JWT\Facade\JWT;
use Imi\JWT\Annotation\JWTValidation;

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
    public function status($jwt)
    {
        $username = Session::get('username');

        $token = JWT::parseToken($jwt); // 仅验证是否合法

        $data = $token->getClaim('data');


        $username = $data->username ?? null;

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
        if('123456' === $password)
        {
            $data = [
                'username'  =>  $username,
            ];
            //JWT签发
            $token = JWT::getToken($data)->__toString();


            return [
                'success'   =>  true,
                'token'     =>  $token,
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

    /**
     * 登录状态2
     * @Action
     *
     * @JWTValidation(tokenParam="token", dataParam="data")
     *
     * @return void
     */
    public function status2($token = null , $data = null) {

        $username = $data->username ?? null;

        return [
            'isLogin'   =>  null !== $username,
            'username'  =>  $username,
        ];
    }

}
