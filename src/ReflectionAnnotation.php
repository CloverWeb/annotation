<?php

namespace Joking\Annotation;

/**
 * Class ReflectionAnnotation
 * @package Joking\Annotation
 */
abstract class ReflectionAnnotation {

    protected function match($document) {
        if (preg_match('#^/\*\*(.*)\*/#s', $document, $comment) === false || empty($comment)) {
            return [];
        }

        $comment = trim($comment[1]);
        if (preg_match_all('#^\s*\*\s*\@(.[A-Za-z]{1,})\((.*)\)#m', $comment, $lines) === false) {
            return [];
        }

        $target = [];
        for ($i = 0; $i < count($lines[1]); $i++) {
            $target[$lines[1][$i]] = $lines[2][$i];
        }

        return $this->parseParams($target);
    }

    /**
     * @param $target
     * @return array
     */
    protected function parseParams($target) {
        $result = [];
        foreach ($target as $name => $value) {
            if (strpos($value, ',')) {
                $num = 1;
                foreach (explode(',', $value) as $param) {
                    list($k, $v) = $this->parseParam($param, $num === 1);
                    $result[$name][$k] = $v;
                    $num++;
                }
            } else if (empty($value)) {
                $result[$name] = [];
            } else {
                list($k, $v) = $this->parseParam($value, true);
                $result[$name][$k] = $v;
            }
        }

        return $result;
    }

    /**
     * @param $param
     * @param bool $hasDefault
     * @return array
     * @throws \Exception
     */
    protected function parseParam($param, $hasDefault = false) {
        $param = trim($param);
        if (strpos($param, '=')) {
            $array = explode('=', $param);
            for ($i = 0; $i < count($array); $i++) {
                $array[$i] = trim($array[$i], " \t\n\r\0\x0B\"\'");
            }

            return $array;
        }

        if ($hasDefault) {
            return ['firstDefaultParam', trim($param, " \t\n\r\0\x0B\"\'")];
        }

        throw new \Exception('无法格式化参数');
    }

    /**
     * 创建标签实体类
     * @param $name
     * @param array $params
     * @return object
     * @throws \Exception
     */
    protected function createEntity($name, $params = []) {
        $parameters = [];               //最后要注入到实体类的构造方法去
        $reflectionClass = new \ReflectionClass(Annotation::get($name));
        $reflectionParameters = $reflectionClass->getConstructor()->getParameters();

        $num = 0;           //用于计数
        foreach ($reflectionParameters as $parameter) {
            $num++;
            if (isset($params[$parameter->getName()])) {
                $parameters[] = $params[$parameter->getName()];
                continue;
            } else if ($num === 1 && isset($params['firstDefaultParam'])) {
                $parameters[] = $params['firstDefaultParam'];
                continue;
            } else if ($parameter->isDefaultValueAvailable()) {
                $parameters[] = $parameter->getDefaultValue();
                continue;
            }


            throw new \Exception('缺少参数：' . $parameter->getName());
        }

        return count($parameters) > 0 ? $reflectionClass->newInstanceArgs($parameters) : $reflectionClass->newInstance();
    }
}