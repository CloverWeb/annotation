<?php

namespace Joking\Annotation;

/**
 * Class ReflectionAnnotation
 * @package Joking\Annotation
 */
abstract class ReflectionAnnotation {

    protected function match($document) {
        $position = $this->findInitialTokenPosition($document);
        if ($position === null) {
            return [];
        }

        $comment = trim(substr($document, $position), '* /');
        if (preg_match_all('#\@(.[A-Za-z]{0,})\((.*)\)#m', $comment, $lines) === false) {
            return [];
        }

        $target = [];
        for ($i = 0; $i < count($lines[1]); $i++) {
            $target[$lines[1][$i]][] = $lines[2][$i];
        }

        return $this->parseParams($target);
    }

    function findInitialTokenPosition($input) {
        $pos = 0;
        while (($pos = strpos($input, '@', $pos)) !== false) {
            if ($pos === 0 || $input[$pos - 1] === ' ' || $input[$pos - 1] === '*') {
                return $pos;
            }

            $pos++;
        }

        return null;
    }

    /**
     * @param $target
     * @return array
     */
    protected function parseParams($target) {
        $r = array(
            '[a-z_\\\][a-z0-9_\:\\\]*[a-z_][a-z0-9_]*',
            '(?:[+-]?[0-9]+(?:[\.][0-9]+)*)(?:[eE][+-]?[0-9]+)?',
            '"(?:""|[^"])*+"',
        );
        $non = array('\s+', '\*+', '(.)');

        $regex = sprintf('/(%s)|%s/%s', implode(')|(', $r), implode('|', $non), 'i');
        $flags = PREG_SPLIT_NO_EMPTY | PREG_SPLIT_DELIM_CAPTURE | PREG_SPLIT_OFFSET_CAPTURE;

        $result = [];
        foreach ($target as $name => $values) {
            foreach ($values as $value) {
                if (empty($value)) {
                    $result[$name][] = [];
                    continue;
                }

                $matches = preg_split($regex, $value, -1, $flags);

                $array = [];
                $string = '';
                $openh = true;
                $opens = true;
                foreach ($matches as $match) {

                    $match[0] == '{' && $openh = false;
                    $match[0] == '}' && $openh = true;
                    $match[0] == '[' && $opens = false;
                    $match[0] == ']' && $opens = true;

                    if ($match[0] == "," && $openh == true && $opens == true) {
                        $array[] = $string;
                        $string = '';
                        continue;
                    }

                    $string .= $match[0];
                }

                empty($string) || $array[] = $string;
                $num = 1;
                $labelParameters = [];
                for ($i = 0; $i < count($array); $i++) {
                    list($k, $v) = $this->parseParam($array[$i], $num++ === 1);
                    $labelParameters[$k] = $v;
                }

                $result[$name][] = $labelParameters;
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
                $array[$i] = $this->disposeValue($array[$i]);
            }

            return $array;
        }

        if ($hasDefault) {
            return ['firstDefaultParam', $this->disposeValue($param)];
        }

        throw new \Exception('无法格式化参数：' . $param);
    }

    protected function disposeValue($value) {
        $value = trim($value, " \t\n\r\0\x0B\"\'");
        if ($jsonValue = json_decode($value)) {         //字符串不是json不操作,是json则转成数组
            return $jsonValue;
        } else {
            return $value;
        }
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
            } else {
                throw new \Exception('缺少参数：' . $parameter->getName());
            }
        }

        return count($parameters) > 0 ? $reflectionClass->newInstanceArgs($parameters) : $reflectionClass->newInstance();
    }
}