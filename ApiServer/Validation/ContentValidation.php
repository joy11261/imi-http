<?php
namespace ImiApp\ApiServer\Validation;

use Imi\Bean\Annotation\Bean;

/**
 * @Bean("ContentValidation")
 */
class ContentValidation
{
    /**
     * 实现违禁词
     * @param $content
     * @return bool
     */
    public function validate($content):bool
    {
        $lists = [
            'abc',
            'xxx',
        ];

        foreach ($lists as $list)
        {
            if (stripos($content,$list) !== false)
            {
                return false;
            }
            return true;
        }
    }
}