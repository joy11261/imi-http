<?php

namespace Imi\Model\Annotation;

use Imi\Bean\Annotation\Base;
use Imi\Bean\Annotation\Parser;

/**
 * 值不为 null 时才序列化到 json.
 *
 * @Annotation
 * @Target("PROPERTY")
 * @Parser("Imi\Model\Parser\ModelParser")
 */
class JsonNotNull extends Base
{
}
