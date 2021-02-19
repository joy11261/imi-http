<?php

namespace Imi\Util;

/**
 * 数组数据基类.
 */
class ArrayData implements \ArrayAccess, \Countable
{
    /**
     * 数据.
     */
    protected $__data = [];

    public function __construct($data)
    {
        $this->__data = $data;
    }

    /**
     * 设置数据.
     *
     * @param string $name
     * @param mixed  $value
     */
    public function set($name, $value = null)
    {
        if (\is_array($name))
        {
            // 如果传入数组就合并当前数据
            $this->__data = ArrayUtil::recursiveMerge($this->__data, $name);
        }
        else
        {
            // 设置数据
            $this->__data[$name] = $value;
        }

        return true;
    }

    /**
     * 设置数据.
     *
     * @param string $name
     * @param mixed  $value
     *
     * @return bool
     */
    public function setVal($name, $value = null)
    {
        if (\is_string($name))
        {
            $name = explode('.', $name);
        }
        elseif (!\is_array($name))
        {
            return false;
        }
        $last = array_pop($name);
        $data = &$this->__data;
        foreach ($name as $val)
        {
            if (!isset($data[$val]))
            {
                $data[$val] = [];
            }
            $data = &$data[$val];
        }
        $data[$last] = $value;

        return true;
    }

    /**
     * 获取数据.
     *
     * @param string $name
     * @param mixed  $default
     *
     * @return mixed
     */
    public function &get($name = null, $default = false)
    {
        if (empty($name))
        {
            return $this->__data;
        }
        if (\is_string($name))
        {
            $name = explode('.', $name);
        }
        elseif (!\is_array($name))
        {
            return $default;
        }
        $result = &$this->__data;
        foreach ($name as $value)
        {
            if (\is_array($result))
            {
                // 数组
                if (isset($result[$value]))
                {
                    $result = &$result[$value];
                }
                else
                {
                    return $default;
                }
            }
            elseif (\is_object($result))
            {
                // 对象
                if (property_exists($result, $value))
                {
                    $result = &$result->$value;
                }
                else
                {
                    return $default;
                }
            }
            else
            {
                return $default;
            }
        }
        if (isset($value))
        {
            return $result;
        }
        else
        {
            return $default;
        }
    }

    /**
     * 删除数据.
     *
     * @param string $name
     */
    public function remove($name)
    {
        if (!\is_array($name))
        {
            $name = \func_get_args();
        }
        foreach ($name as $val)
        {
            if (\is_string($val))
            {
                $val = explode('.', $val);
            }
            elseif (!\is_array($val))
            {
                return false;
            }
            $last = array_pop($val);
            $result = &$this->__data;
            foreach ($val as $value)
            {
                if (isset($result[$value]))
                {
                    $result = &$result[$value];
                }
            }
            unset($result[$last]);
        }

        return true;
    }

    /**
     * 清空数据.
     */
    public function clear()
    {
        $this->__data = [];
    }

    /**
     * 获取数据的数量.
     *
     * @return int
     */
    public function length()
    {
        return \count($this->__data);
    }

    /**
     * 获取数据的数量.
     *
     * @return int
     */
    public function count()
    {
        return \count($this->__data);
    }

    /**
     * 键名对应的值是否存在.
     *
     * @param string $name
     *
     * @return bool
     */
    public function exists($name)
    {
        return isset($this->__data[$name]);
    }

    public function &__get($key)
    {
        return $this->get($key);
    }

    public function __set($key, $value)
    {
        $this->set($key, $value);
    }

    public function __isset($key)
    {
        return null !== $this->get($key, null);
    }

    public function __unset($key)
    {
        $this->remove($key);
    }

    public function offsetSet($offset, $value)
    {
        if (null === $offset)
        {
            $this->__data[] = $value;
        }
        else
        {
            $this->setVal($offset, $value);
        }
    }

    public function offsetExists($offset)
    {
        return null !== $this->get($offset, null);
    }

    public function offsetUnset($offset)
    {
        $this->remove($offset);
    }

    public function &offsetGet($offset)
    {
        return $this->get($offset);
    }

    public function &getRawData()
    {
        return $this->__data;
    }
}
