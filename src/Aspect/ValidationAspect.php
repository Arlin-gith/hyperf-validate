<?php


namespace Arlin\Validate\Aspect;

use Arlin\Validate\Annotations\RequestValidation;
use Arlin\Validate\Exception\ValidateException;
use Arlin\Validate\Validate\BaseValidate;
use Hyperf\Di\Annotation\Aspect;
use Hyperf\Di\Aop\AbstractAspect;
use Hyperf\Di\Aop\ProceedingJoinPoint;
use Hyperf\Utils\Context;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ServerRequestInterface;

/**
 * Class ValidationAspect
 * @package App\Aspect
 * @Aspect()
 */
class ValidationAspect extends AbstractAspect
{
    protected $container;

    protected $request;

    public $annotations = [
        RequestValidation::class
    ];

    public function __construct(ContainerInterface $container, ServerRequestInterface $Request)
    {
        $this->container = $container;
        $this->request = $this->container->get(ServerRequestInterface::class);
    }

    /**
     * @param ProceedingJoinPoint $proceedingJoinPoint
     * @return mixed
     * @throws \Hyperf\Di\Exception\Exception
     */
    public function process(ProceedingJoinPoint $proceedingJoinPoint)
    {
        foreach ($proceedingJoinPoint->getAnnotationMetadata()->method as $validation)
        {
            if($validation instanceof RequestValidation) {
                if (empty($validation->validate)) {
                    if (!empty($validation->mode)) {
                        $class = $validation->mode;
                    } else {
                        $class = class_basename($proceedingJoinPoint->className);
                    }
                    $validation->validate = '\\App\\Validate\\' . $class . 'Validation';
                }
                $verData = $this->request->all();
                $this->validationData($validation, $verData, $validation->validate, $proceedingJoinPoint);
            }
        }

        return $proceedingJoinPoint->process();
    }

    /**
     * 处理验证
     * @param $validation
     * @param $verData
     * @param $class
     * @param $proceedingJoinPoint
     */
    private function validationData($validation, $verData, $class, $proceedingJoinPoint)
    {
        /** @var BaseValidate $validate */
        if (class_exists($class)) {
            $validate = new $class;
        } else {
            throw new ValidateException('class not exists:' . $class);
        }
        /** @var RequestValidation $validation */
        if ($validation->scene == '') {
            $validation->scene = $proceedingJoinPoint->methodName;
        }
        //获取规则
        $rules = $validate->getSceneRule($validation->scene);
        //验证数据
        if ($validate->check($verData, $rules) === false) {
            throw new ValidateException($validate->getError());
        }
        //过滤多余参数
        if ($validation->filter) {
            foreach ($verData as $key=>$item){
                if (!in_array($key,$rules)){
                    unset($verData[$key]);
                }
            }
        }
        //返回验证过的数据
        Context::override(ServerRequestInterface::class, function (ServerRequestInterface $request) use ($verData) {
            return $request->withParsedBody(json_decode(json_encode($verData)));
        });

    }

}