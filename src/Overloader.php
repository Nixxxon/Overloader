<?php

/*
 * (c) Niclas Åberg <niclas.aberg@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Nixxxon\Overloader;

/**
 * Overload methods and properties of a object
 *
 * @author Niclas Åberg <niclas.aberg@gmail.com>
 */
class Overloader
{
    /**
     * The object to overload
     *
     * @var object
     */
    protected $object;

    /**
     * The overloaded methods
     *
     * @var array
     */
    protected $overloadedMethods;

    /**
     * The overloaded properties
     *
     * @var array
     */
    protected $overloadedProperties;


    /**
     * Constructor
     *
     * @param $object the object to overload
     * @throws \LogicException if the object is not a object
     */
    public function __construct($object)
    {
        if (!is_object($object)) {
            throw new \LogicException('Must be an object');
        }

        $this->object = $object;
        $this->overloadedMethods = [];
        $this->overloadedProperties = [];
    }

    /**
     * Factory method to create an instance of Overload
     *
     * @param  object $object
     * @return Overload
     */
    public static function init($object)
    {
        return new Overloader($object);
    }


    /**
     * Magic method for handling and redirecting incoming method calls to the
     * object
     *
     * @param  string $method
     * @param  array  $arguments
     * @throws \LogicException if method is not accessible from the outside
     * @return mixed
     */
    public function __call($method, $arguments)
    {
        $backtrace = debug_backtrace(false, 3);
        $methodReflection = $this->getMethodReflection($method);

        if (isset($backtrace[2]['class'])
            && $backtrace[2]['class'] != get_class($this->object)
            && !$methodReflection->isPublic()
        ) {
            throw new \LogicException(sprintf(
                'Method "%s" is not public',
                $method
            ));
        }

        if (isset($this->overloadedMethods[$method])) {
            return call_user_func_array(
                $this->overloadedMethods[$method],
                $arguments
            );
        }

        return call_user_func_array(
            $methodReflection->getClosure($this->object)->bindTo($this),
            $arguments
        );
    }

    /**
     * Magic method for handling and redirecting incoming property calls to the
     * object
     *
     * @param  string $property
     * @throws \LogicException if property is not accessible from the outside
     * @return mixed
     */
    public function __get($property)
    {
        $backtrace = debug_backtrace(false, 2);
        $propertyReflection = $this->getPropertyReflection($property);

        if (isset($backtrace[1]['class'])
            && $backtrace[1]['class'] != get_class($this->object)
            && !$propertyReflection->isPublic()
        ) {
            throw new \LogicException(sprintf(
                'Property "%s" is not public',
                $property
            ));
        }

        if (isset($this->overloadedProperties[$property])) {
            return $this->overloadedProperties[$property];
        }

        $propertyReflection->setAccessible(true);
        return $propertyReflection->getValue($this->object);
    }

    /**
     * Magic method for handling and redirecting incoming property calls to the
     * object
     *
     * @param  string $property
     * @throws \LogicException if property is not accessible from the outside
     * @return mixed
     */
    public function __set($property, $value)
    {
        $backtrace = debug_backtrace(false, 2);
        $propertyReflection = $this->getPropertyReflection($property);

        if (isset($backtrace[1]['class'])
            && $backtrace[1]['class'] != get_class($this->object)
            && !$propertyReflection->isPublic()
        ) {
            throw new \LogicException(sprintf(
                'Property "%s" is not public',
                $property
            ));
        }

        if (isset($this->overloadedProperties[$property])) {
            $this->overloadedProperties[$property] = $value;
            return $this;
        }

        $propertyReflection->setAccessible(true);
        $propertyReflection->setValue($this->object, $value);
        return $this;
    }

   /**
     * Overload a method
     *
     * @param  string   $method
     * @param  \Closure $function
     * @throws \LogicException if method does not exist
     * @return self
     */
    public function method($method, \Closure $function)
    {
        $this->ensureMethodExists($method);
        $this->overloadedMethods[$method] = $function->bindTo($this);
        return $this;
    }

    /**
     * Overload a property
     *
     * @param  string $property
     * @param  mixed  $value
     * @throws \LogicException if property does not exist
     * @return self
     */
    public function property($property, $value)
    {
        $this->ensurePropertyExists($property);
        $this->overloadedProperties[$property] = $value;
        return $this;
    }

    /**
     * Get the reflection of the object
     *
     * @return \ReflectionClass
     */
    private function getReflection()
    {
        return new \ReflectionClass(get_class($this->object));
    }

    /**
     * Get the reflection of a method
     *
     * @param  string $method
     * @throws \LogicException if method does not exist
     * @return \ReflectionMethod
     */
    private function getMethodReflection($method)
    {
        $this->ensureMethodExists($method);
        return $this->getReflection()->getMethod($method);
    }

    /**
     * Get the reflection of a property
     *
     * @param  string $property
     * @throws \LogicException if property does not exist
     * @return \ReflectionProperty
     */
    private function getPropertyReflection($property)
    {
        $this->ensurePropertyExists($property);
        return $this->getReflection()->getProperty($property);
    }

    /**
     * Ensure that the method exists
     *
     * @param  string $method
     * @throws \LogicException if method does not exist
     * @return void
     */
    private function ensureMethodExists($method)
    {
        if (!$this->getReflection()->hasMethod($method)) {
            throw new \LogicException('Method does not exist');
        }
    }

    /**
     * Ensure that the property exists
     *
     * @param  string $property
     * @throws \LogicException if property does not exist
     * @return void
     */
    private function ensurePropertyExists($property)
    {
        if (!$this->getReflection()->hasProperty($property)) {
            throw new \LogicException('Property does not exist');
        }
    }
}
