<?php

namespace Arlin\Validate\Annotations;


use Doctrine\Common\Annotations\Annotation\Target;
use Hyperf\Di\Annotation\AbstractAnnotation;


/**
 * Class RequestValidation
 * @package App\Annotation
 * @Annotation
 * @Target("METHOD")
 */
class RequestValidation extends AbstractAnnotation
{
    /**
     * 验证规则类
     * @var string
     */
    public $validate = '';

    /**
     * 场景
     * @var string
     */
    public $scene = '';

    /**
     * 是否过滤多余字段
     * @var bool
     */
    public $filter = false;

    public function __construct($value = null)
    {
        parent::__construct($value);
        $this->bindMainProperty('scene', $value);
    }

}