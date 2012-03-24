<?php

class CascadingAttributeTest extends PHPUnit_Framework_TestCase
{
    function test()
    {
        $attribute = new CascadingAttribute;
        $attribute->setAttributeType( 'name' , CascadingAttribute::ATTR_STRING );
        $attribute->setAttributeType( 'class' , CascadingAttribute::ATTR_ARRAY );
        $attribute->setAttributeType( 'id' , CascadingAttribute::ATTR_ARRAY );

        $attribute['name'] = 'username';
        $attribute['class'] = array('foo');

        $attribute->class( 'foo', 'bar' );

        is( array('foo','bar') , $attribute['class'] );
        is( 'username' , $attribute['name'] );
        ok( $attribute );
    }
}

