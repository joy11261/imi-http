<?php

namespace Imi\Bean;

use Imi\Aop\AfterReturningJoinPoint;
use Imi\Aop\AfterThrowingJoinPoint;
use Imi\Aop\Annotation\AfterThrowing;
use Imi\Aop\Annotation\Aspect;
use Imi\Aop\Annotation\BaseInjectValue;
use Imi\Aop\Annotation\PointCut;
use Imi\Aop\AroundJoinPoint;
use Imi\Aop\JoinPoint;
use Imi\Aop\PointCutType;
use Imi\Bean\Annotation\AnnotationManager;
use Imi\Bean\Parser\BeanParser;
use Imi\Config;
use Imi\Util\Imi;
use Imi\Util\Text;
use Imi\Worker;

class BeanProxy
{
    /**
     * 存储每个类对应的切面关系.
     *
     * @var \SplPriorityQueue[]
     */
    private static $aspects = [];

    /**
     * 切面缓存.
     *
     * @var array
     */
    private static $aspectCache = [];

    /**
     * 工作进程中的切面缓存.
     *
     * @var array
     */
    private static $workerAspectCache = [];

    /**
     * 当前代理类是否属于worker进程.
     *
     * @var bool
     */
    private $isWorker;

    /**
     * 类名.
     *
     * @var string
     */
    private $className;

    public function __construct($object)
    {
        $this->isWorker = null !== Worker::getWorkerID();
        $this->init($object);
    }

    /**
     * 魔术方法.
     *
     * @param object $object
     * @param string $method
     * @param array  $args
     *
     * @return mixed
     */
    public function call($object, $method, $callback, &$args)
    {
        try
        {
            // 先尝试环绕
            if ($this->parseAround($object, $method, $args, $result, $callback))
            {
                return $result;
            }
            else
            {
                // 正常请求
                return $this->callOrigin($object, $method, $args, $callback);
            }
        }
        catch (\Throwable $throwable)
        {
            // 异常
            $this->parseAfterThrowing($object, $method, $args, $throwable);
        }
    }

    /**
     * 初始化.
     *
     * @return void
     */
    private function init($object)
    {
        $this->className = $className = BeanFactory::getObjectClass($object);
        $refClass = ReflectionContainer::getClassReflection($className);
        // 每个类只需处理一次
        $staticAspects = &static::$aspects;
        if (!isset($staticAspects[$className]))
        {
            $staticAspects[$className] = $aspect = new \SplPriorityQueue();
            $aspects = AnnotationManager::getAnnotationPoints(Aspect::class);
            foreach ($aspects as $item)
            {
                $itemClass = $item->getClass();
                $itemPriority = $item->getAnnotation()->priority;
                // 判断是否属于当前类方法的切面
                $pointCutsSet = AnnotationManager::getMethodsAnnotations($itemClass, PointCut::class);
                foreach ($pointCutsSet as $methodName => $pointCuts)
                {
                    foreach ($pointCuts as $pointCut)
                    {
                        switch ($pointCut->type)
                        {
                            case PointCutType::METHOD:
                                foreach ($pointCut->allow as $allowItem)
                                {
                                    if (Imi::checkClassRule($allowItem, $className))
                                    {
                                        $aspect->insert([
                                            'class'     => $itemClass,
                                            'method'    => $methodName,
                                            'pointCut'  => $pointCut,
                                        ], $itemPriority);
                                        break;
                                    }
                                }
                                break;
                            case PointCutType::ANNOTATION:
                                foreach ($refClass->getMethods() as $method)
                                {
                                    $methodAnnotations = AnnotationManager::getMethodAnnotations($className, $method->getName());
                                    foreach ($pointCut->allow as $allowItem)
                                    {
                                        foreach ($methodAnnotations as $annotation)
                                        {
                                            if ($annotation instanceof $allowItem)
                                            {
                                                $aspect->insert([
                                                    'class'     => $itemClass,
                                                    'method'    => $methodName,
                                                    'pointCut'  => $pointCut,
                                                ], $itemPriority);
                                                break 3;
                                            }
                                        }
                                    }
                                }
                                break;
                        }
                    }
                }
                // 判断是否属于当前类的切面
                $pointCuts = AnnotationManager::getMethodsAnnotations($itemClass, PointCut::class);
                foreach ($pointCuts as $methodName => $pointCuts)
                {
                    foreach ($pointCuts as $pointCut)
                    {
                        switch ($pointCut->type)
                        {
                            case PointCutType::CONSTRUCT:
                                // 构造方法
                                foreach ($pointCut->allow as $allowItem)
                                {
                                    if (Imi::checkRuleMatch($allowItem, $className))
                                    {
                                        $aspect->insert([
                                            'class'     => $itemClass,
                                            'method'    => $methodName,
                                            'pointCut'  => $pointCut,
                                        ], $itemPriority);
                                        break;
                                    }
                                }
                                break;
                            case PointCutType::ANNOTATION_CONSTRUCT:
                                // 注解构造方法
                                $classAnnotations = AnnotationManager::getClassAnnotations($className);
                                foreach ($pointCut->allow as $allowItem)
                                {
                                    foreach ($classAnnotations as $annotation)
                                    {
                                        if ($annotation instanceof $allowItem)
                                        {
                                            $aspect->insert([
                                                'class'     => $itemClass,
                                                'method'    => $methodName,
                                                'pointCut'  => $pointCut,
                                            ], $itemPriority);
                                            break 2;
                                        }
                                    }
                                }
                                break;
                        }
                    }
                }
            }
        }
    }

    /**
     * 注入属性.
     *
     * @return void
     */
    public function injectProps($object)
    {
        $className = $this->className;
        list($injects, $configs) = static::getInjects($className);
        if (!$injects && !$configs)
        {
            return;
        }
        $refClass = ReflectionContainer::getClassReflection($className);

        // @inject()和@requestInject()注入
        foreach ($injects as $propName => $annotations)
        {
            $annotation = reset($annotations);
            $propRef = $refClass->getProperty($propName);
            $propRef->setAccessible(true);
            $propRef->setValue($object, $annotation->getRealValue());
        }

        // 配置注入
        foreach ($configs as $name => $value)
        {
            $propRef = $refClass->getProperty($name);
            if (null === $propRef)
            {
                continue;
            }
            $propRef->setAccessible(true);
            $propRef->setValue($object, $value);
        }
    }

    /**
     * 获取注入属性的配置们.
     *
     * @param string $className
     *
     * @return array
     */
    public static function getConfigInjects($className)
    {
        // 配置文件注入
        $beanData = BeanParser::getInstance()->getData();
        if (isset($beanData[$className]))
        {
            $beanName = $beanData[$className]['beanName'];
        }
        else
        {
            $beanName = $className;
        }
        $beanProperties = Config::get('@currentServer.beans.' . $beanName);
        if (null === $beanProperties && $beanName !== $className)
        {
            $beanProperties = Config::get('@currentServer.beans.' . $className);
        }

        return $beanProperties ?? [];
    }

    /**
     * 获取注入类属性的注解和配置.
     *
     * @param string $className
     *
     * @return [$annotations, $configs]
     */
    public static function getInjects($className)
    {
        $injects = AnnotationManager::getPropertiesAnnotations($className, BaseInjectValue::class);
        $configs = static::getConfigInjects($className);
        if ($configs)
        {
            foreach ($configs as $key => $value)
            {
                if (isset($injects[$key]))
                {
                    unset($injects[$key]);
                }
            }
        }

        return [$injects, $configs];
    }

    /**
     * 正常请求
     *
     * @param object $object
     * @param string $method
     * @param array  $args
     *
     * @return mixed
     */
    private function callOrigin($object, $method, &$args, $callback)
    {
        $this->parseBefore($object, $method, $args);
        // 原始方法调用
        $result = $callback(...$args);
        $this->parseAfter($object, $method, $args);
        $this->parseAfterReturning($object, $method, $args, $result);

        return $result;
    }

    /**
     * 处理前置.
     *
     * @param object $object
     * @param string $method
     * @param array  $args
     *
     * @return void
     */
    private function parseBefore($object, $method, &$args)
    {
        $this->doAspect($method, 'before', function ($aspectClassName, $methodName) use ($object, $method, &$args) {
            $joinPoint = new JoinPoint('before', $method, $args, $object, $this);
            $aspectObject = new $aspectClassName();
            $aspectObject->$methodName($joinPoint);
        });
    }

    /**
     * 处理后置.
     *
     * @param object $object
     * @param string $method
     * @param array  $args
     *
     * @return void
     */
    private function parseAfter($object, $method, &$args)
    {
        $this->doAspect($method, 'after', function ($aspectClassName, $methodName) use ($object, $method, &$args) {
            $joinPoint = new JoinPoint('after', $method, $args, $object, $this);
            $aspectObject = new $aspectClassName();
            $aspectObject->$methodName($joinPoint);
        });
    }

    /**
     * 处理返回值
     *
     * @param object $object
     * @param string $method
     * @param array  $args
     * @param mixed  $returnValue
     *
     * @return void
     */
    private function parseAfterReturning($object, $method, &$args, &$returnValue)
    {
        $this->doAspect($method, 'afterReturning', function ($aspectClassName, $methodName) use ($object, $method, &$args, &$returnValue) {
            $joinPoint = new AfterReturningJoinPoint('afterReturning', $method, $args, $object, $this);
            $joinPoint->setReturnValue($returnValue);
            $aspectObject = new $aspectClassName();
            $aspectObject->$methodName($joinPoint);
            $returnValue = $joinPoint->getReturnValue();
        });
    }

    /**
     * 处理环绕.
     *
     * @param object $object
     * @param string $method
     * @param array  $args
     * @param mixed  $returnValue
     *
     * @return bool
     */
    private function parseAround($object, $method, &$args, &$returnValue, $callback)
    {
        $aroundAspectDoList = [];
        $this->doAspect($method, 'around', function ($aspectClassName, $methodName) use (&$aroundAspectDoList) {
            $aroundAspectDoList[] = [new $aspectClassName(), $methodName];
        });
        if (!isset($aroundAspectDoList[0]))
        {
            return false;
        }
        $aroundAspectDoList = array_reverse($aroundAspectDoList);

        $nextJoinPoint = null;
        $nextAroundAspectDo = null;

        foreach ($aroundAspectDoList as $aroundAspectDo)
        {
            $joinPoint = new AroundJoinPoint('around', $method, $args, $object, $this, (null === $nextJoinPoint ? function ($inArgs = null) use ($object, $method, &$args, $callback) {
                if (null !== $inArgs)
                {
                    $args = $inArgs;
                }

                return $this->callOrigin($object, $method, $args, $callback);
            } : function ($inArgs = null) use ($nextAroundAspectDo, $nextJoinPoint, &$args) {
                if (null !== $inArgs)
                {
                    $args = $inArgs;
                }

                return $nextAroundAspectDo($nextJoinPoint);
            }));
            $nextJoinPoint = $joinPoint;
            $nextAroundAspectDo = $aroundAspectDo;
        }
        $returnValue = $nextAroundAspectDo($nextJoinPoint);

        return true;
    }

    /**
     * 处理异常.
     *
     * @param object     $object
     * @param string     $method
     * @param array      $args
     * @param \Throwable $throwable
     *
     * @return void
     */
    private function parseAfterThrowing($object, $method, &$args, \Throwable $throwable)
    {
        $isCancelThrow = false;
        $this->doAspect($method, 'afterThrowing', function ($aspectClassName, $methodName, AfterThrowing $annotation) use ($object, $method, &$args, $throwable, &$isCancelThrow) {
            // 验证异常是否捕获
            if (isset($annotation->allow[0]) || isset($annotation->deny[0]))
            {
                $throwableClassName = \get_class($throwable);
                if (isset($annotation->allow[0]))
                {
                    $allowResult = false;
                    foreach ($annotation->allow as $rule)
                    {
                        $allowResult = Imi::checkRuleMatch($rule, $throwableClassName);
                        if ($allowResult)
                        {
                            break;
                        }
                    }
                    if (!$allowResult)
                    {
                        return;
                    }
                }
                $denyResult = false;
                foreach ($annotation->deny as $rule)
                {
                    $denyResult = Imi::checkRuleMatch($rule, $throwableClassName);
                    if ($denyResult)
                    {
                        return;
                    }
                }
            }
            // 处理
            $joinPoint = new AfterThrowingJoinPoint('afterThrowing', $method, $args, $object, $this, $throwable);
            $aspectObject = new $aspectClassName();
            $aspectObject->$methodName($joinPoint);
            if (!$isCancelThrow && $joinPoint->isCancelThrow())
            {
                $isCancelThrow = true;
            }
        });
        // 不取消依旧抛出
        if (!$isCancelThrow)
        {
            throw $throwable;
        }
    }

    /**
     * 执行切面操作.
     *
     * @param string   $method    方法名
     * @param string   $pointType 切入点类型
     * @param callable $callback  回调
     *
     * @return void
     */
    private function doAspect($method, $pointType, $callback)
    {
        if ($this->isWorker)
        {
            $aspectCache = &static::$workerAspectCache;
        }
        else
        {
            $aspectCache = &static::$aspectCache;
        }
        $className = $this->className;
        if (isset($aspectCache[$className][$method][$pointType]))
        {
            $tmpItem = $aspectCache[$className][$method][$pointType];
        }
        else
        {
            $tmpItem = [];
            $list = clone static::$aspects[$className];
            $methodAnnotations = AnnotationManager::getMethodAnnotations($className, $method);
            foreach ($list as $option)
            {
                $aspectClassName = $option['class'];
                $methodName = $option['method'];
                $pointCut = $option['pointCut'];
                $allowResult = false;
                switch ($pointCut->type)
                {
                    case PointCutType::METHOD:
                        foreach ($pointCut->allow as $rule)
                        {
                            $allowResult = Imi::checkClassMethodRule($rule, $className, $method);
                            if ($allowResult)
                            {
                                break;
                            }
                        }
                        break;
                    case PointCutType::ANNOTATION:
                        foreach ($pointCut->allow as $rule)
                        {
                            foreach ($methodAnnotations as $annotation)
                            {
                                $allowResult = $annotation instanceof $rule;
                                if ($allowResult)
                                {
                                    break 2;
                                }
                            }
                        }
                        break;
                    case PointCutType::CONSTRUCT:
                    case PointCutType::ANNOTATION_CONSTRUCT:
                        if ('__construct' === $method)
                        {
                            $allowResult = true;
                        }
                        break;
                }
                if ($allowResult)
                {
                    $denyResult = false;

                    switch ($pointCut->type)
                    {
                        case PointCutType::METHOD:
                            foreach ($pointCut->deny as $rule)
                            {
                                $denyResult = Imi::checkClassMethodRule($rule, $className, $method);
                                if ($denyResult)
                                {
                                    break;
                                }
                            }
                            break;
                        case PointCutType::ANNOTATION:
                            foreach ($pointCut->deny as $rule)
                            {
                                foreach ($methodAnnotations as $annotation)
                                {
                                    $denyResult = $annotation instanceof $rule;
                                    if ($denyResult)
                                    {
                                        break 2;
                                    }
                                }
                            }
                            break;
                    }

                    if ($denyResult)
                    {
                        continue;
                    }
                    $point = AnnotationManager::getMethodAnnotations($aspectClassName, $methodName, 'Imi\Aop\Annotation\\' . Text::toPascalName($pointType))[0] ?? null;
                    if (null !== $point)
                    {
                        $tmpItem[] = [$aspectClassName, $methodName, $point];
                    }
                }
            }
            $aspectCache[$className][$method][$pointType] = $tmpItem;
        }

        foreach ($tmpItem as $item)
        {
            $callback(...$item);
        }
    }

    /**
     * 获取注入类属性的值
     *
     * @param string $className
     * @param string $propertyName
     *
     * @return mixed
     */
    public static function getInjectValue($className, $propertyName)
    {
        list($annotations, $configs) = static::getInjects($className);
        if (isset($configs[$propertyName]))
        {
            return $configs[$propertyName];
        }
        else
        {
            $annotation = $annotations[0] ?? null;
            if ($annotation)
            {
                return $annotation->getRealValue();
            }
            else
            {
                return null;
            }
        }
    }
}
