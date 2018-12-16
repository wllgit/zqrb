<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006~2017 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: mengxiangchen <mengxiangchen@ata.net.cn>
// +----------------------------------------------------------------------

namespace think\cache\driver;

use think\cache\Driver;

/**
 * Redis缓存驱动，适合单机部署、有前端代理实现高可用的场景，性能最好
 * 有需要在业务层实现读写分离、或者使用RedisCluster的需求，请使用Redisd驱动
 *
 * 要求安装phpredis扩展：https://github.com/nicolasff/phpredis
 * @author    尘缘 <130775@qq.com>
 */
class Redisd extends Driver
{
    protected $options = [
        'host'       => '127.0.0.1',
        'port'       => 6379,
        'password'   => '',
        'select'     => 0,
        'timeout'    => 0,
        'expire'     => 0,
        'persistent' => false,
        'prefix'     => '',
    ];

    /**  
    *类对象实例数组  
    *共有静态变量  
    *@param mixed $_instance存放实例  
    */  
    private static $_instance = array();          
    
    /**
     * 构造函数
     * @param array $options 缓存参数
     * @access public
     */
    public function __construct($options = [])
    {
        $options = config('cache');
        if (!extension_loaded('redis')) {
            throw new \BadFunctionCallException('not support: redis');
        }
        if (!empty($options)) {
            $this->options = array_merge($this->options, $options);
        }
        //此处进行分布式配置
        $params = array(
            'hosts'    => explode(',', $this->options['host']),
            'ports'    => explode(',', $this->options['port']),
            'password' => explode(',', $this->options['password']),
            'select'   => explode(',', $this->options['select']),
        );
        //拼接参数
        $hostsNum = count($params['hosts']);
        $normal = 0;
        for($i = 0; $i < $hostsNum; $i++){
            $host     = $params['hosts'][$i];
            $port     = $params['ports'][$i] ? $params['ports'][$i] : $params['ports'][0];
            $redisParams = array('host' => $host,'port' => $port);
            try{
                self::$_instance[$i] = new \think\cache\driver\Redis($redisParams);
                $normal = $i;
             }catch(\Exception $e){
                continue;
             }
             break;
            /*if(!(self::$_instance[$i] instanceof \think\cache\driver\Redis)) {
                self::$_instance[$i] = new \think\cache\driver\Redis($redisParams);
            }*/
        }
        
        $this->redis=self::$_instance[$normal]->handler();
    }

    /**
     * 判断是否master/slave,调用不同的master或者slave实例  
     * @access public
     * @param boolen $master
     * @return type
     */
    public function is_master($master = true) {
        $count = count(self::$_instance); 
        $i     = $master || 1 == $count ? 0 : rand(1,$count - 1);
        //返回每一个实例的句柄  
        return self::$_instance[$i]->handler();
    }  
    
    /**
     * 判断缓存
     * @access public
     * @param string $name 缓存变量名
     * @return bool
     */
    public function has($name)
    {
        // $redis = $this->is_master(false);
        return $this->redis->get($this->getCacheKey($name)) ? true : false;
    }

    /**
     * 读取缓存
     * @access public
     * @param string $name 缓存变量名
     * @param mixed  $default 默认值
     * @return mixed
     */
    public function get($name, $default = false)
    {
        // $redis = $this->is_master(false);
        $value = $this->redis->get($this->getCacheKey($name));
        if (is_null($value)) {
            return $default;
        }
        $jsonData = json_decode($value, true);
        // 检测是否为JSON数据 true 返回JSON解析数组, false返回源数据 byron sampson<xiaobo.sun@qq.com>
        return (null === $jsonData) ? $value : $jsonData;
    }

    /**
     * 写入缓存
     * @access public
     * @param string    $name 缓存变量名
     * @param mixed     $value  存储数据
     * @param integer   $expire  有效时间（秒）
     * @return boolean
     */
    public function set($name, $value, $expire = null)
    {
        if (is_null($expire)) {
            $expire = $this->options['expire'];
        }
        if ($this->tag && !$this->has($name)) {
            $first = true;
        }

        $key = $this->getCacheKey($name);
        //对数组/对象数据进行缓存处理，保证数据完整性  byron sampson<xiaobo.sun@qq.com>
        $value = (is_object($value) || is_array($value)) ? json_encode($value) : $value;
        // $redis = $this->is_master();        
        if (is_int($expire) && $expire) {
            $result = $this->redis->setex($key, $expire, $value);
        } else {
            $result = $this->redis->set($key, $value);
        }
        isset($first) && $this->setTagItem($key);
        return $result;
    }

    /**
     * 自增缓存（针对数值缓存）
     * @access public
     * @param string    $name 缓存变量名
     * @param int       $step 步长
     * @return false|int
     */
    public function inc($name, $step = 1)
    {
        // $redis = $this->is_master(); 
        $key = $this->getCacheKey($name);
        return $this->redis->incrby($key, $step);
    }

    /**
     * 自减缓存（针对数值缓存）
     * @access public
     * @param string    $name 缓存变量名
     * @param int       $step 步长
     * @return false|int
     */
    public function dec($name, $step = 1)
    {
        // $redis = $this->is_master(); 
        $key = $this->getCacheKey($name);
        return $this->redis->decrby($key, $step);
    }

    /**
     * 删除缓存
     * @access public
     * @param string $name 缓存变量名
     * @return boolean
     */
    public function rm($name)
    {
        // $redis = $this->is_master(); 
        return $this->redis->delete($this->getCacheKey($name));
    }

    /**
     * 清除缓存
     * @access public
     * @param string $tag 标签名
     * @return boolean
     */
    public function clear($tag = null)
    {
        // $redis = $this->is_master(); 
        if ($tag) {
            // 指定标签清除
            $keys = $this->getTagItem($tag);
            foreach ($keys as $key) {
                $this->redis->delete($key);
            }
            $this->rm('tag_' . md5($tag));
            return true;
        }
        return $this->redis->flushDB();
    }

}
