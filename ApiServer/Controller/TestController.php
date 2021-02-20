<?php
namespace ImiApp\ApiServer\Controller;

use Imi\Controller\HttpController;
use Imi\Server\View\Annotation\View;
use Imi\Server\Route\Annotation\Route;
use Imi\Server\Route\Annotation\Action;
use Imi\Server\Route\Annotation\Controller;


/**
 * @Controller
 */
class TestController extends HttpController
{

    /**
     * @Action
     * @Route("/test")
     *  测试
     * @return void
     */
    public function test() {
        return [
            'hello' =>  'test11',
        ];
    }

    /**
     * @Action
     * @Route(url="/article/{id}")
     *
     * @return void
     */
    public function article($id){

        return [
            'code' => 0,
            'id'   => $id,
        ];
    }

    /**
     * @Action
     * @Route(url="/article/delete/",method="POST")
     *
     * @return void
     */
    public function deleteArticle() {
        return ['data' => 111];
    }

    /**
     * @Action
     * @Route (url="/login",domain="127.0.0.1:8080")
     *
     * @return void
     */
    public function login() {
        return ['data' => 'login'];
    }



    /**
     * @Action
     * @Route(url="/testGet/{name}")
     *
     * @return void
     */
    public function testGet($name)
    {
        return [
            'name'  =>  $name,
        ];
    }


}