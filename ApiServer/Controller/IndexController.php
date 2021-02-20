<?php
namespace ImiApp\ApiServer\Controller;

use Imi\Controller\HttpController;
use Imi\Server\View\Annotation\View;
use Imi\Server\Route\Annotation\Route;
use Imi\Server\Route\Annotation\Action;
use Imi\Server\Route\Annotation\Controller;

/**
 * @Controller("/")
 * @View(baseDir="index/")
 */
class IndexController extends HttpController
{
    /**
     * @Action
     * @Route(url="/")
     * @View(renderType="html")
     *
     * @return void
     */
    public function index()
    {
        return [
            'hello' =>  'imi',
            'time'  =>  date('Y-m-d H:i:s', time()),
        ];
    }

    /**
     * @Action
     ** @Route("/api")
     *
     * @return void
     */
    public function api()
    {
        return [
            'hello' =>  'imi',
            'time'  =>  date('Y-m-d H:i:s', time()),
        ];
    }

}
