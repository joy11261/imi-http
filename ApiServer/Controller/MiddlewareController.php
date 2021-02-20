<?php
namespace ImiApp\ApiServer\Controller;

use Imi\Controller\HttpController;
use Imi\Server\Route\Annotation\Action;
use Imi\Server\Route\Annotation\Controller;
use Imi\Server\Route\Annotation\Middleware;

/**
 * @Controller("/middleware/")
 * @Middleware({"A", "B"})
 */
class MiddlewareController extends HttpController
{
    /**
     * @Action
     *
     * @Middleware("C")
     * @Middleware("D")
     * @return void
     */
    public function test1()
    {
        return [
            'data'  =>  'test1',
        ];
    }

    /**
     * @Action
     *
     * @return void
     */
    public function test2()
    {
        return [
            'data'  =>  'test2',
        ];
    }

    /**
     * @Action
     * 
     * @Middleware("@test1")
     *
     * @return void
     */
    public function test3()
    {
        return [
            'data'  =>  'test3',
        ];
    }

}
