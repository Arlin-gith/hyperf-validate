# hyperf-validate
通过注解、切面验证表单

安装方法  composer require Arlin-gith/hyperf-validate

注解: @RequestValidation

例如：

    /**
     * validate 验证规则类，默认取验证器为当前控制器类文件名的验证器文件
     * scene  验证场景
     * filter true过滤掉规则外无用参数
     * @RequestValidation(validate="",scene="index",filter=true)
     * @return array
     */
		 
    public function index()
    {
        //获取验证后数据
        /** @var IndexControllerValidation $param */
        $param = $this->request->getParsedBody();

        return [
            'username' => $param->username,
            'password' => $param->password
        ];
    }
    
    具体可参考 https://gitee.com/lphkxd/hyperf-validate/tree/master/

   
