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
        return $this->hasAnnotation($name) ? $this->createEntity($name, $this->methodAnnotations[$name]) : null;
    }

    public function getAnnotations() {
        $annotations = [];
        foreach ($this->methodAnnotations as $name => $annotation) {
            $annotations[$name] = $this->getAnnotation($name);
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