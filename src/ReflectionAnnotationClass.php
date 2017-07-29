<?php


namespace Joking\Annotation;

/**
 * Class ReflectionAnnotationClass
 * @package Joking\Annotation
 */
class ReflectionAnnotationClass extends ReflectionAnnotation {

    protected $reflectionClass;

    protected $classAnnotations = [];

    public function __construct(\ReflectionClass $reflectionClass) {
        $this->reflectionClass = $reflectionClass;
        $this->classAnnotations = $this->match($this->getDocument());
    }

    /**
     * 获取class的注释标签
     * 比如获取 AnnotationClass类的注释标签@Name: AnnotationClass::getAnnotation('Name')
     * @param $name
     * @return object
     */
    public function getAnnotation($name) {
        return $this->hasAnnotation($name) ? $this->createEntity($name, $this->classAnnotations[$name]) : null;
    }

    /**
     * 获取所有 @ 注释标签
     * @return array
     */
    public function getAnnotations() {
        $annotations = [];
        foreach ($this->classAnnotations as $name => $annotation) {
            $annotations[$name] = $this->getAnnotation($name);
        }

        return $annotations;
    }

    /**
     * @param $method
     * @return ReflectionAnnotationMethod
     */
    public function getMethod($method) {
        return new ReflectionAnnotationMethod($this->reflectionClass->getMethod($method));
    }

    /**
     * @return ReflectionAnnotationMethod[]
     */
    public function getMethods() {
        $methods = [];
        foreach ($this->reflectionClass->getMethods() as $reflectionMethod) {
            $methods[] = new ReflectionAnnotationMethod($reflectionMethod);
        }
        return $methods;
    }

    /**
     * @param $name
     * @return ReflectionAnnotationProperty
     */
    public function getProperty($name) {
        return new ReflectionAnnotationProperty($this->reflectionClass->getProperty($name));
    }

    /**
     * @return array
     */
    public function getProperties() {
        $properties = [];
        foreach ($this->reflectionClass->getProperties() as $property) {
            $properties[] = new ReflectionAnnotationProperty($property);
        }
        return $properties;
    }

    /**
     * 判断注释标签是否存在
     * @param $name
     * @return bool
     */
    public function hasAnnotation($name) {
        return isset($this->classAnnotations[$name]);
    }

    public function getDocument() {
        return $this->reflectionClass->getDocComment();
    }
}