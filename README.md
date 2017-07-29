# annotation

> 拓展PHP注释，在注释上可以使用更多的@标签，并且可以比较简单的读取这些@标签

例如：

在使用之前需要先注册

    Annotation::register("标签的名字" , "标签的实体类 Label::class");
---
    /**
     * @Container('className' , params="lalala")
     */
     class Name {
        
     }
-----
然后你可以这样读读取他

    $reflectionClass = new ReflectionClass(Name::class);
    $reflectionAnnotationClass = new ReflectionAnnotationClass();
    $label = $reflectionAnnotationClass->getAnnotation("Container")
    
    echo $label->params;
    
    输出 lalala