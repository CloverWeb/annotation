<?php

namespace Joking\Annotation;

/**
 * Class ReflectionAnnotationMethod
 * @package Joking\Annotation
 */
class ReflectionAnnotationMethod extends ReflectionAnnotation {

    protected $reflectionMethod;

    protected $methodAnnotations = [];

    public function __construct(\ReflectionMethod $reflectionMethod) {
        $this->reflectionMethod = $reflectionMethod;
        $this->methodAnnotations = $this->match($this->getDocument());
    }

    public function getAnnotation($name) {
        return $this->hasAnnotation($name) ? $this->createEntity($name, $this->methodAnnotations[$name][0]) : null;
    }

    /**
     * 获取多个 @ 注释标签
     * @param $name
     * @return array
     */
    public function getAnnotations($name) {
        $annotations = [];
        if ($this->hasAnnotation($name)) {
            foreach ($this->methodAnnotations[$name] as $annotation) {
                $annotations[] = $this->createEntity($name, $annotation);
            }
        }
        return $annotations;
    }

    /**
     * @return array
     */
    public function getAll() {
        $annotations = [];
        foreach ($this->methodAnnotations as $name => $values) {
            foreach ($values as $annotation) {
                $annotations[$name][] = $this->createEntity($name, $annotation);
            }
        }
        return $annotations;
    }

    public function getName() {
        return $this->reflectionMethod->getName();
    }

    public function getDocument() {
        return $this->reflectionMethod->getDocComment();
    }

    public function hasAnnotation($name) {
        return isset($this->methodAnnotations[$name]);
    }
}