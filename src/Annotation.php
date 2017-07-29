<?php

namespace Joking\Annotation;


class Annotation {

    protected static $collection;

    /**
     * 注册一个注释标签,供以后使用
     * @param $name
     * @param $annotationClass
     */
    public static function register($name, $annotationClass) {
        static::$collection[$name] = $annotationClass;
    }

    public static function get($name) {
        if (static::has($name)) {
            return static::$collection[$name];
        }

        throw new \Exception('该标签没有被注册：' . $name);
    }

    public static function all() {
        return static::$collection;
    }

    public static function has($name) {
        return isset(static::$collection[$name]);
    }
}