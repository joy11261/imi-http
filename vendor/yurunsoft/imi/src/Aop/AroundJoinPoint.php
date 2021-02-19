<?php

namespace Imi\Aop;

class AroundJoinPoint extends JoinPoint
{
    /**
     * process调用的方法.
     *
     * @var callable
     */
    private $nextProceed;

    public function __construct($type, $method, &$args, $target, $_this, $nextProceed)
    {
        parent::__construct($type, $method, $args, $target, $_this);
        $this->nextProceed = $nextProceed;
    }

    /**
     * 调用下一个方法.
     *
     * @return mixed
     */
    public function proceed($args = null)
    {
        if (null === $args)
        {
            $args = $this->getArgs();
        }
        $result = ($this->nextProceed)($args);

        $this->args = $args;

        return $result;
    }
}
