# annotation

> 拓展PHP注释，在注释上可以使用更多的@标签，并且可以比较简单的读取这些@标签

例如：

>在使用之前需要先创建一个标签类

    class ContainerLabel {
        public $value;
        public $param;
    
        public function __construct($value , $param) {
            $this->value = $value;
            $this->param = $param;
        }
    }

>然后开始注册

    Annotation::register("标签的名字 Container" , "标签的实体类 ContainerLabel::class");
    


    /**
     * @Container('className' , param="lalala")
     */
     class Name {
        
     }

>然后你可以这样读读取它

    $reflectionClass = new ReflectionClass(Name::class);
    $reflectionAnnotationClass = new ReflectionAnnotationClass($reflectionClass);
    $label = $reflectionAnnotationClass->getAnnotation("Container")
    
    echo $label->param;
    
    输出 lalala
-------

>还有更多功能有待开发，等等我想睡觉