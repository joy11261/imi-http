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
}