<?php

use Nixxxon\Overloader\Overloader;

class OverloaderTest extends PHPUnit_Framework_TestCase
{
    public function testPublicMethodIsCallableFromOutside()
    {
        $overloader = new Overloader(new Test);
        $this->assertSame(10, $overloader->add(5, 5));
    }

    /**
     * @expectedException \LogicException
     */
    public function testProtectedMethodIsNotCallableFromOutside()
    {
        $overloader = new Overloader(new Test);
        $overloader->getProtectedProperty();
    }

    /**
     * @expectedException \LogicException
     */
    public function testPrivateMethodIsNotCallableFromOutside()
    {
        $overloader = new Overloader(new Test);
        $overloader->getPrivateProperty();
    }

    /**
     * @expectedException \LogicException
     */
    public function testGetParentPrivatePropertyFromChildPublicMethod()
    {
        $overloader = new Overloader(new Test);
        $overloader->getParentPrivateProperty();
    }

    public function testProtectedMethodIsCallableByPublicMethod()
    {
        $overloader = new Overloader(new Test);
        $this->assertSame(
            'protectedProperty',
            $overloader->callProtectedMethodByPublicMethod()
        );
    }

    public function testOverloadPublicMethod()
    {
        $original = new Test;
        $overloader = new Overloader($original);
        $overloader->method('add', function ($a, $b) {
            return $a * $b;
        });
        $this->assertSame(25, $overloader->add(5, 5));
        $this->assertSame(10, $original->add(5, 5));
    }

    public function testOverloadProtectedMethodCalledByPublicMethod()
    {
        $original = new Test;
        $overloader = new Overloader($original);
        $overloader->method('getProtectedProperty', function () {
            return 'foobar';
        });
        $this->assertSame(
            'foobar',
            $overloader->callProtectedMethodByPublicMethod()
        );
    }

    public function testPublicPropertyIsCallableFromOutside()
    {
        $overloader = new Overloader(new Test);
        $this->assertSame('publicProperty', $overloader->publicProperty);
    }

    /**
     * @expectedException \LogicException
     */
    public function testProtectedPropertyIsNotCallableFromOutside()
    {
        $overloader = new Overloader(new Test);
        $overloader->protectedProperty;
    }

    /**
     * @expectedException \LogicException
     */
    public function testPrivatePropertyIsNotCallableFromOutside()
    {
        $overloader = new Overloader(new Test);
        $overloader->protectedProperty;
    }

    public function testSetPublicPropertyFromOutsideIsAllowed()
    {
        $overloader = new Overloader(new Test);
        $overloader->parentPublicProperty = 'changed';
        $this->assertSame('changed', $overloader->parentPublicProperty);
    }

    /**
     * @expectedException \LogicException
     */
    public function testSetProtectedPropertyFromOutsideIsNotAllowed()
    {
        $overloader = new Overloader(new Test);
        $overloader->parentProtectedProperty = 'changed';
    }

    /**
     * @expectedException \LogicException
     */
    public function testSetPrivatePropertyFromOutsideIsNotAllowed()
    {
        $overloader = new Overloader(new Test);
        $overloader->parentPrivateProperty = 'changed';
    }
}

class ParentTest
{
    public $parentPublicProperty = 'parentPublicProperty';
    protected $parentProtectedProperty = 'parentProtectedProperty';
    private $parentPrivateProperty = 'parentPrivateProperty';
    public static $parentPublicStaticProperty = 'parentPublicStaticProperty';
    protected static $parentProtectedStaticProperty = 'parentProtectedStaticProperty';
    private static $parentPrivateStaticProperty = 'parentPrivateStaticProperty';

    public function add($a, $b)
    {
        return $a + $b;
    }
}

class Test extends ParentTest
{
    public $publicProperty = 'publicProperty';
    protected $protectedProperty = 'protectedProperty';
    private $privateProperty = 'privateProperty';
    public static $publicStaticProperty = 'publicStaticProperty';
    protected static $protectedStaticProperty = 'protectedStaticProperty';
    private static $privateStaticProperty = 'privateStaticProperty';

    public function callProtectedMethodByPublicMethod()
    {
        return $this->getProtectedProperty();
    }

    protected function getProtectedProperty()
    {
        return $this->protectedProperty;
    }

    private function getPrivateProperty()
    {
        return $this->privateProperty;
    }

    public function getParentPrivateProperty()
    {
        return $this->parentPrivateProperty;
    }
}
