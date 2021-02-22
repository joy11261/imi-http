<?php
namespace ImiApp\ApiServer\Controller;

use Imi\Aop\Annotation\Inject;
use Imi\Controller\HttpController;
use Imi\Validate\Annotation\Text;
use Imi\Validate\Annotation\InList;
use Imi\Validate\Annotation\Number;
use Imi\Validate\Annotation\Integer;
use Imi\Validate\Annotation\Required;
use Imi\Server\Route\Annotation\Route;
use Imi\Validate\Annotation\Condition;
use Imi\Server\Route\Annotation\Action;
use Imi\Server\Route\Annotation\Controller;
use Imi\HttpValidate\Annotation\HttpValidation;

/**
 * @Controller("/validation/")
 */
class ValidationController extends HttpController
{
    /**
     * @Action
     * @Route(method="POST")
     *
     * @HttpValidation
     * @Required(name="$get.id")
     * @Integer(name="$get.id", min="1", message="id 格式不正确")
     *
     * @Text(name="$post.title", min=6, max=16 ,message="title 长度不符合规范")
     *
     * @InList(name="$post.type", list={1, 2, 3} ,message="type 不在集合中")
     *
     * @Condition(name="$post.content", callable={@Inject("ContentValidation"), "validate"})
     *
     * @Number(name="$post.value", min=1, max=8.88, optional=true , message="value 不在取值范围")
     *
     * @return void
     */
    public function test1($id, $title, $type, $content, $value)
    {
        return compact('id', 'title', 'type', 'content', 'value');
    }

}
