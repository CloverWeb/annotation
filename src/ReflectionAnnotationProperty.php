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
     * 获取class的注释标签
     * 比如获取 AnnotationClass类的注释标签@Name: AnnotationClass::getAnnotation('Name')
     * @param $name
     * @return object
     */
    public function getAnnotation($name) {
        return $this->hasAnnotation($name) ? $this->createEntity($name, $this->propertyAnnotations[$name]) : null;
    }

    /**
     * 获取所有 @ 注释标签
     * @return array
     */
    public function getAnnotations() {
        $annotations = [];
        foreach ($this->propertyAnnotations as $name => $annotation) {
            $annotations[$name] = $this->getAnnotation($name);
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