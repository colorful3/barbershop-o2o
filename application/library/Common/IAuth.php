<?php
/**
 * IAuth.php
 * Created By Colorful
 * Date:2018/4/24
 * Time:下午12:04
 * @desc 用户校验类库
 */

class Common_IAuth
{
    /**
     * 对密码进行加密处理
     * @param $pwd
     * @param $salt
     * @return string
     */
    static function pwdEncode($pwd, $salt)
    {
        return md5( $pwd . $salt);
    }

    /**
     * 生成随机字符串，用于用户密码的盐
     * @param int $length
     * @return string
     */
    static function randSalt($length = 32) {
        $chars = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789!@#$%^&*';
        $salt = '';
        for($i=0;$i<$length;$i++) {
            $salt .= $chars[mt_rand(0, strlen($chars)-1)];
        }
        return $salt;
    }

    /**
     * sign验签算法
     * @param array $data
     * @return string
     */
    public static function setSign($data=[]) {
        // 1、对数组进行按照key值进行字典排序
        ksort($data);
        // 2、生成query字符串
        $query_str = http_build_query($data);  // app_name=barbershop&time=152457827566&version=1.0
        // 3、通过ase来加密
        $aes_salt = Yaf_Registry::get('config')->keys->aes_salt;
        $aes_str = ( new Common_Aes( $aes_salt ) )->encrypt($query_str);
        return $aes_str;
    }

    /**
     * sign签名算法
     * @param $data
     * @return bool
     */
    static function checkSignPass($data)
    {
        $aes_salt = Yaf_Registry::get('config')->keys->aes_salt;
        $aes_str = ( new Common_Aes($aes_salt) )->decrypt($data['sign']);
        if(empty($aes_str)) {
            return false;
        }
        // app_name=barbershop&time=152457827566&version=1.0
        parse_str($aes_str, $arr);
        if(!is_array($arr) || empty($arr['app_name'])
            || $arr['app_name'] != $data['app_name'] ) {
            return false;
        }


        /*

        // 此处上线后开启
        // 时间有效性校验
        $sign_time = Yaf_Registry::get('config')->time->sign_time;

        if (time() - ceil($arr['time'] / 1000) > $sign_time ) {
            return false;
        }

        // 唯一性判断
        $signure = Common_Cache::getInstance()->get( $data['sign'] );
        if( $signure ) {
            return false;
        }

        */

        return true;
    }

}