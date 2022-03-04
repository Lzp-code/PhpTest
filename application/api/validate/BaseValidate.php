<?php
/**
 * User: Gkh
 * Date: 2021/11/25
 * Time: 11:04
 */

namespace app\api\validate;

use ReflectionClass;
use think\exception\ValidateException;
use think\Validate;

class BaseValidate extends Validate
{
    protected $eachRule = [];

    protected function getScene($scene = '')
    {
        if (empty($scene)) {
            // 读取指定场景
            $scene = $this->currentScene;
        }

        if (empty($scene)) {
            return;
        }

        $this->only = $this->append = $this->remove = [];

        if (method_exists($this, 'scene' . $scene)) {
            call_user_func([$this, 'scene' . $scene]);
        } elseif (isset($this->scene[$scene])) {
            // 如果设置了验证适用场景
            $scene = $this->scene[$scene];

            if (is_string($scene)) {
                $scene = explode(',', $scene);
            }

            $this->only = $scene;
        }
    }

    protected function checkEach($values, $rule, $data, $field, $title)
    {
        $len = strlen($field);
        $name = $len > 1 && (substr($field, $len - 1, 1)) == 's' ? substr($field, 0, $len - 1) : $field;
        $arr = explode(',', $values);
        $validate = new BaseValidate();
        $validate->rule($this->eachRule);
        foreach ($arr as $value) {
            $result = $validate->only([$name])->check([$name => $value]);
            if (!$result) {
                throw new ValidateException($validate->error);
            }
        }
        return true;
    }

    public function check($data=null, $rules = [], $scene = '')
    {
        if(is_null($data)){
            $data = \request()->param();
        }

        if (!parent::check($data, $rules, $scene)) {
            throw new ValidateException($this->getError());
        }
        return true;
    }

    /**
     * @param array $arrays 通常传入request.post变量数组
     * @return array 按照规则key过滤后的变量数组
     * @throws ParameterException
     */
    public function getDataByRule($array){
        //这里还可以过滤一些一定不能传的参数
        $newArray = array();
        foreach ($this->rule as $key=>$value){
            $newArray[$key] = $array[$key];
        }

        return $newArray;
    }

    protected function checkConstantClass($values, $rule, $data, $field, $title)
    {
        $arr = explode('.', $rule);
        $class = $arr[0];
        $vals = empty($arr[1]) ? 'items' : $arr[1];
        $rc = new ReflectionClass($class);
        $vals = $rc->getConstant($vals);
        $values = (int)$values;
        foreach ($vals as $value) {
            if ($value['id'] === $values) {
                return true;
            }
        }
        throw new ValidateException ('无效' . $title);
    }

    protected function checkUrlInWhiteList($values, $rule, $data, $field, $title)
    {
        $hostWhiteList = config('host_white_list');
        if (empty($hostWhiteList)) {
            return true;
        }
        if (preg_match('/^https?:\/\//i', $values)) {
            $url = parse_url($values);
            $host = empty($url['host']) ? '' : $url['host'];
            $hostWhiteList = is_array($hostWhiteList) ? $hostWhiteList : [$hostWhiteList];
            foreach ($hostWhiteList as $item) {
                if ($item && preg_match($item, $host)) {
                    return true;
                }
            }
            return false;
        }
    }

    protected function checkTimeIn($values, $rule, $data, $field, $title)
    {
        $now = time();
        $max = intval($rule);
        $time = intval($values);
        return abs($time - $now) <= $max;
    }

    protected function idcard($values, $rule, $data, $field, $title)
    {
        if (preg_match('/^[\d]{17}[\dXx]$/', $values)) {
            if ($this->idcard_verify_number(substr($values, 0, 17)) === strtoupper(substr($values, 17, 1)))
                return true;
        }
        return false;
    }

    private function idcard_verify_number($idcard_base)
    {
        $factor = array(7, 9, 10, 5, 8, 4, 2, 1, 6, 3, 7, 9, 10, 5, 8, 4, 2);
        $verify_number_list = array('1', '0', 'X', '9', '8', '7', '6', '5', '4', '3', '2');
        $checksum = 0;
        for ($i = 0; $i < strlen($idcard_base); $i++) {
            $checksum += substr($idcard_base, $i, 1) * $factor[$i];
        }
        $mod = $checksum % 11;
        return $verify_number_list[$mod];
    }


    //整数验证
    protected function isPositiveInteger($value, $rule='', $data='', $field='')
    {
        if (is_numeric($value) && is_int($value + 0) && ($value + 0) > 0) {
            return true;
        }
        return $field . '必须是正整数';
    }
}