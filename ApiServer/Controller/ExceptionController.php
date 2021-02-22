<?php
namespace ImiApp\ApiServer\Controller;

use Imi\Controller\HttpController;
use Imi\Server\Route\Annotation\Action;
use Imi\Server\Route\Annotation\Controller;


/**
 * @Controller ("/exception/")
 *
 * Class ExceptionController
 * @package ImiApp\ApiServer\Controller
 */
class ExceptionController extends HttpController {

    /**
     * @Action
     *
     * @return void
     */
    public function error () {
        trigger_error('I am an error',E_USER_ERROR);
    }

    /**
     * @Action
     *
     * @return void
     */
    public function excption() {
        throw new \Exception('i am an exception');

    }
}