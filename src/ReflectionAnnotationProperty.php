<?php


namespace Joking\Annotation;

/**
 * Class ReflectionAnnotationProperty
 * @package Joking\Annotation
 */
class ReflectionAnnotationProperty extends ReflectionAnnotation {

    protected $reflectionProperty;

    protected $propertyAnnotations;

    public function __construct(\ReflectionProperty $reflectionProperty) {
        $this->reflectionProperty = $reflectionProperty;
        $this->propertyAnnotations = $this->match($this->getDocument());
    }


    /**
     * 获取属性的注释标签
     * 比如获取 AnnotationClass类的注释标签@Name: AnnotationClass::getAnnotation('Name')
     * @param $name
     * @return object
     */
    public function getAnnotation($name) {
        return $this->hasAnnotation($name) ? $this->createEntity($name, $this->propertyAnnotations[$name][0]) : null;
    }

    /**
     * 获取多个 @ 注释标签
     * @param $name
     * @return array
     */
    public function getAnnotations($name) {
        $annotations = [];
        if ($this->hasAnnotation($name)) {
            foreach ($this->propertyAnnotations[$name] as $annotation) {
                $annotations[] = $this->createEntity($name, $annotation);
            }
        }
        return $annotations;
    }

    public function getAll() {
        $annotations = [];
        foreach ($this->propertyAnnotations as $name => $values) {
            foreach ($values as $annotation) {
                $annotations[$name][] = $this->createEntity($name, $annotation);
            }
        }
        return $annotations;
    }


    /**
     * 判断注释标签是否存在
     * @param $name
     * @return bool
     */
    public function hasAnnotation($name) {
        return isset($this->propertyAnnotations[$name]);
    }

    public function getDocument() {
        return $this->reflectionProperty->getDocComment();
    }
}