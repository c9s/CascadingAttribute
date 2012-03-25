<?php

class CascadingAttribute
    implements ArrayAccess
{
    const  ATTR_ANY = 0;
    const  ATTR_ARRAY = 1;
    const  ATTR_STRING = 2;
    const  ATTR_INTEGER = 3;
    const  ATTR_FLOAT = 4;
    const  ATTR_CALLABLE = 5;
    const  ATTR_FLAG = 6;

	/**
	 * @var array $supportedAttributes
	 */
    public $supportedAttributes = array();

    public $allowUndefinedAttribute = true;

    public $attributes = array();

	/**
	 * @var string $name column name (id)
	 */
    public function __construct()
    {
        $this->supportedAttributes = array(
            /*
            'label' => self::ATTR_ANY,
            'refer' => self::ATTR_STRING,
             */
        );
    }

    public function setAttributeType( $name , $type ) 
    {
        $this->supportedAttributes[ $name ] = $type;
    }

    public function removeAttributeType($name)
    {
        unset( $this->supportedAttributes[ $name ] );
    }

    public function __get($name)
    {
        if( isset( $this->attributes[ $name ] ) )
            return $this->attributes[ $name ];
    }

    public function __set($name,$value)
    {
        $this->attributes[ $name ] = $value;
    }

    public function setAttribute($name,$args)
    {
        if( isset($this->supportedAttributes[ $name ]) ) {
            $c = count($args);
            $t = $this->supportedAttributes[ $name ];

            if( $t != self::ATTR_FLAG && $c == 0 ) {
                throw new Exception( 'Attribute value is required.' );
            }

            switch( $t ) {

                case self::ATTR_ANY:
                    $this->attributes[ $name ] = $args[0];
                    break;

                case self::ATTR_ARRAY:
                    if( $c > 1 ) {
                        $this->attributes[ $name ] = $args;
                    }
                    elseif( is_array($args[0]) ) 
                    {
                        $this->attributes[ $name ] = $args[0];
                    } 
                    else
                    {
                        $this->attributes[ $name ] = (array) $args[0];
                    }
                    break;

                case self::ATTR_STRING:
                    if( is_string($args[0]) ) {
                        $this->attributes[ $name ] = $args[0];
                    }
                    else {
                        throw new Exception("attribute value of $name is not a string.");
                    }
                    break;

                case self::ATTR_INTEGER:
                    if( is_integer($args[0])) {
                        $this->attributes[ $name ] = $args[0];
                    }
                    else {
                        throw new Exception("attribute value of $name is not a integer.");
                    }
                    break;

                case self::ATTR_CALLABLE:

                    /**
                     * handle for __invoke, array($obj,$name), 'function_name 
                     */
                    if( is_callable($args[0]) ) {
                        $this->attributes[ $name ] = $args[0];
                    } else {
                        throw new Exception("attribute value of $name is not callable type.");
                    }
                    break;

                case self::ATTR_FLAG:
                    $this->attributes[ $name ] = true;
                    break;

                default:
                    throw new Exception("Unsupported attribute type: $name");
            }
            return $this;
        }

        // save unknown attribute by default
        if( $this->allowUndefinedAttribute ) {
            $this->attributes[ $name ] = $args[0];
        }
        else {
            throw new Exception("Undefined attribute $name, Do you want to use allowUndefinedAttribute option?");
        }

    }

    public function __call($method,$args)
    {
        $this->setAttribute($method,$args);
        return $this;
    }


    public function offsetSet($name,$value)
    {
        $this->setAttribute( $name, array($value) );
    }
    
    public function offsetExists($name)
    {
        return isset($this->attributes[ $name ]);
    }
    
    public function offsetGet($name)
    {
        if( ! isset( $this->attributes[ $name ] ) ) {
            // detect type for setting up default value.
            $type = @$this->supportedAttributes[ $name ];
            if( $type == self::ATTR_ARRAY ) {
                $this->attributes[ $name ] = array();
            }
        }
        $val =& $this->attributes[ $name ];
        return $val;
    }
    
    public function offsetUnset($name)
    {
        unset($this->attributes[$name]);
    }

    public function __toString() {
        return var_export( $this->attributes , true );
    }
    
    
    
}

