<?php
namespace ImiApp\ApiServer\Controller;

use Imi\Controller\HttpController;
use Imi\Server\Route\Annotation\Controller;
use Imi\Server\Route\Annotation\Action;
use Imi\Server\Route\Annotation\Route;
use Imi\Util\Http\Consts\StatusCode;


/**
 * @Controller("/rar/")
 */
class RequestAndResponseController extends HttpController
{
    /**
     * @Action
     *
     * @return void
     */
    public function index()
    {
        return [
            'hello1111' =>  'imi',
            'time'  =>  date('Y-m-d H:i:s', time()),
        ];
    }


    /**
     * @Action
     *
     * @return void
     */
    public function getAndPost($id,$title,$age = 11) {
        return [
            'id' => $id,
            'title' => $title,
            'age'   => $age,
            'id_get' => $this->request->get('id'),
            'title_get' => $this->request->post('title'),
            'age_get' => $this->request->get('age'),

            'age_request' => $this->request->request('age',33)
        ];
    }

    /**
     * @Action
     * @param $name
     * @param $value
     * @return void
     */
    public function setCookie ($name,$value) {
        return $this->response->withCookie($name,$value);
    }

    /**
     * @Action
     * @param $name
     * @return void
     */
    public function getCookie ($name) {
        return [
            'value' => $this->request->getCookie($name,'aaa')
        ];
    }

    /**
     * @Action
     *
     * @return void
     */
    public function testHeader() {
        return $this->response->withHeader('abc',
        $this->request->getHeaderLine('def'));
    }

    /**
     * @Action
     *
     * @return void
     */
    public function other() {
        return [
            'url' => $this->request->getUri()->__toString(),
            'server' => $this->request->getServerParams(),
        ];
    }

    /**
     * @Action
     *
     * @return void
     */
    public function testResponse() {
        return $this->response->withStatus(StatusCode::BAD_GATEWAY)->write('niubi');
    }

    /**
     * @Action
     *
     * @return void
     */
    public function upload() {
        $uploadfiles = $this->request->getUploadedFiles();
        $files = [];

        foreach ($uploadfiles as $file) {
            $files[] = [
                'clientFileName' => $file->getClientFilename(),
                'mediaType' => $file->getClientMediaType(),
                'error' => $file->getError(),
                'size' => $file->getSize(),
                'size2' => strlen($file->getStream()),
            ];
            $file->moveTo(__DIR__ . '/' . $file->getClientFilename() );
        }
        return [
            'file' => $files,
        ];
    }

    /**
     * @Action
     *
     * @return void
     */
    public function download () {
        return $this->response->sendFile(__FILE__);
    }


}