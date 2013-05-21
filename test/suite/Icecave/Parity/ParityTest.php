<?php
namespace Icecave\Parity;

use PHPUnit_Framework_TestCase;

class ParityTest extends PHPUnit_Framework_TestCase
{
    public function setUp()
    {
        $this->value = 0;
        $this->less  = -1;
        $this->same  = 0;
        $this->more  = 1;
    }

    public function testIsEqualTo()
    {
        $this->assertFalse(Parity::isEqualTo($this->value, $this->less));
        $this->assertTrue(Parity::isEqualTo($this->value, $this->same));
        $this->assertFalse(Parity::isEqualTo($this->value, $this->more));
    }

    public function testIsNotEqualTo()
    {
        $this->assertTrue(Parity::isNotEqualTo($this->value, $this->less));
        $this->assertFalse(Parity::isNotEqualTo($this->value, $this->same));
        $this->assertTrue(Parity::isNotEqualTo($this->value, $this->more));
    }

    public function testIsLessThan()
    {
        $this->assertFalse(Parity::isLessThan($this->value, $this->less));
        $this->assertFalse(Parity::isLessThan($this->value, $this->same));
        $this->assertTrue(Parity::isLessThan($this->value, $this->more));
    }

    public function testIsGreaterThan()
    {
        $this->assertTrue(Parity::isGreaterThan($this->value, $this->less));
        $this->assertFalse(Parity::isGreaterThan($this->value, $this->same));
        $this->assertFalse(Parity::isGreaterThan($this->value, $this->more));
    }

    public function testIsLessThanOrEqualTo()
    {
        $this->assertFalse(Parity::isLessThanOrEqualTo($this->value, $this->less));
        $this->assertTrue(Parity::isLessThanOrEqualTo($this->value, $this->same));
        $this->assertTrue(Parity::isLessThanOrEqualTo($this->value, $this->more));
    }

    public function testIsGreaterThanOrEqualTo()
    {
        $this->assertTrue(Parity::isGreaterThanOrEqualTo($this->value, $this->less));
        $this->assertTrue(Parity::isGreaterThanOrEqualTo($this->value, $this->same));
        $this->assertFalse(Parity::isGreaterThanOrEqualTo($this->value, $this->more));
    }

    public function testIsEqualToWithDefaultComparator()
    {
        $this->assertFalse(Parity::isEqualTo($this->value, $this->less, false));
        $this->assertTrue(Parity::isEqualTo($this->value, $this->same, false));
        $this->assertFalse(Parity::isEqualTo($this->value, $this->more, false));
    }

    public function testIsNotEqualToWithDefaultComparator()
    {
        $this->assertTrue(Parity::isNotEqualTo($this->value, $this->less, false));
        $this->assertFalse(Parity::isNotEqualTo($this->value, $this->same, false));
        $this->assertTrue(Parity::isNotEqualTo($this->value, $this->more, false));
    }

    public function testIsLessThanWithDefaultComparator()
    {
        $this->assertFalse(Parity::isLessThan($this->value, $this->less, false));
        $this->assertFalse(Parity::isLessThan($this->value, $this->same, false));
        $this->assertTrue(Parity::isLessThan($this->value, $this->more, false));
    }

    public function testIsGreaterThanWithDefaultComparator()
    {
        $this->assertTrue(Parity::isGreaterThan($this->value, $this->less, false));
        $this->assertFalse(Parity::isGreaterThan($this->value, $this->same, false));
        $this->assertFalse(Parity::isGreaterThan($this->value, $this->more, false));
    }

    public function testIsLessThanOrEqualToWithDefaultComparator()
    {
        $this->assertFalse(Parity::isLessThanOrEqualTo($this->value, $this->less, false));
        $this->assertTrue(Parity::isLessThanOrEqualTo($this->value, $this->same, false));
        $this->assertTrue(Parity::isLessThanOrEqualTo($this->value, $this->more, false));
    }

    public function testIsGreaterThanOrEqualToWithDefaultComparator()
    {
        $this->assertTrue(Parity::isGreaterThanOrEqualTo($this->value, $this->less, false));
        $this->assertTrue(Parity::isGreaterThanOrEqualTo($this->value, $this->same, false));
        $this->assertFalse(Parity::isGreaterThanOrEqualTo($this->value, $this->more, false));
    }
}