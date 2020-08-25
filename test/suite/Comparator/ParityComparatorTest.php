<?php

namespace Icecave\Parity\Comparator;

use Eloquent\Liberator\Liberator;
use Icecave\Parity\AnyComparable;
use Icecave\Parity\RestrictedComparable;
use Icecave\Parity\SelfComparable;
use Icecave\Parity\SubClassComparable;
use Phake;
use PHPUnit\Framework\TestCase;
use stdClass;

class ParityComparatorTest extends TestCase
{
    public function setUp(): void
    {
        $this->fallbackComparator = Phake::mock(Comparator::class);
        $this->comparator = new ParityComparator($this->fallbackComparator);

        Phake::when($this->fallbackComparator)
            ->compare(Phake::anyParameters())
            ->thenReturn(-1);
    }

    public function testInvoke()
    {
        $this->assertSame(
            -1,
            call_user_func(
                $this->comparator,
                [1, 2, 3],
                [1, 2, 3]
            )
        );
    }

    public function testCompareInversion()
    {
        $lhs = Phake::mock(AnyComparable::class);

        Phake::when($lhs)
            ->compare(Phake::anyParameters())
            ->thenReturn(-1);

        $this->assertSame(-1, $this->comparator->compare($lhs, 10));
        $this->assertSame(+1, $this->comparator->compare(10, $lhs));
    }

    public function testCompareWithFallback()
    {
        $result = $this->comparator->compare(10, 20);

        Phake::verify($this->fallbackComparator)->compare(10, 20);

        $this->assertSame($result, -1);
    }

    public function testCompareWithAnyComparable()
    {
        $comparable = Phake::mock(AnyComparable::class);

        Phake::when($comparable)
            ->compare(Phake::anyParameters())
            ->thenReturn(-10);

        $result = $this->comparator->compare($comparable, 10);

        Phake::verify($comparable)->compare(10);
        Phake::verifyNoInteraction($this->fallbackComparator);

        $this->assertSame($result, -10);
    }

    public function testCompareWithRestrictedComparable()
    {
        $comparable = Phake::mock(RestrictedComparable::class);

        Phake::when($comparable)
            ->compare(Phake::anyParameters())
            ->thenReturn(-10);

        Phake::when($comparable)
            ->canCompare(Phake::anyParameters())
            ->thenReturn(true);

        $result = $this->comparator->compare($comparable, 10);

        Phake::inOrder(
            Phake::verify($comparable)->canCompare(10),
            Phake::verify($comparable)->compare(10)
        );

        Phake::verifyNoInteraction($this->fallbackComparator);

        $this->assertSame($result, -10);
    }

    public function testCompareWithRestrictedComparableAndUnsupportedOperand()
    {
        $comparable = Phake::mock(RestrictedComparable::class);

        Phake::when($comparable)
            ->compare(Phake::anyParameters())
            ->thenReturn(-10);

        Phake::when($comparable)
            ->canCompare(Phake::anyParameters())
            ->thenReturn(false);

        $result = $this->comparator->compare($comparable, 10);

        Phake::verify($comparable)->canCompare(10);
        Phake::verify($comparable, Phake::never())->compare(Phake::anyParameters());
        Phake::verify($this->fallbackComparator)->compare($comparable, 10);

        $this->assertSame($result, -1);
    }

    public function testCompareWithSelfComparable()
    {
        $lhsComparable = Phake::mock(SelfComparable::class);
        $rhsComparable = clone $lhsComparable;

        Phake::when($lhsComparable)
            ->compare(Phake::anyParameters())
            ->thenReturn(-10);

        $result = $this->comparator->compare($lhsComparable, $rhsComparable);

        Phake::verify($lhsComparable)->compare($rhsComparable);
        Phake::verifyNoInteraction($this->fallbackComparator);

        $this->assertSame($result, -10);
    }

    public function testCompareWithSelfComparableAndSubClass()
    {
        $lhsComparable = new SelfComparableImpl();
        $rhsComparable = new SelfComparableSubClass();

        $result = $this->comparator->compare($lhsComparable, $rhsComparable);

        Phake::verify($this->fallbackComparator)->compare($lhsComparable, $rhsComparable);

        $this->assertSame($result, -1);
    }

    public function testCompareWithSelfComparableAndNonObject()
    {
        $comparable = Phake::mock(SelfComparable::class);

        $result = $this->comparator->compare($comparable, 10);

        Phake::verify($comparable, Phake::never())->compare(Phake::anyParameters());
        Phake::verify($this->fallbackComparator)->compare($comparable, 10);

        $this->assertSame($result, -1);
    }

    public function testCompareWithSelfComparableAndUnrelatedType()
    {
        $comparable = Phake::mock(SelfComparable::class);

        $result = $this->comparator->compare($comparable, new stdClass());

        Phake::verify($comparable, Phake::never())->compare(Phake::anyParameters());
        Phake::verify($this->fallbackComparator)->compare($comparable, new stdClass());

        $this->assertSame($result, -1);
    }

    public function testCompareWithSubClassComparable()
    {
        $lhsComparable = Phake::mock(SubClassComparable::class);
        $rhsComparable = clone $lhsComparable;

        Phake::when($lhsComparable)
            ->compare(Phake::anyParameters())
            ->thenReturn(-10);

        $result = $this->comparator->compare($lhsComparable, $rhsComparable);

        Phake::verify($lhsComparable)->compare($rhsComparable);
        Phake::verifyNoInteraction($this->fallbackComparator);

        $this->assertSame($result, -10);
    }

    public function testCompareWithSubClassComparableAndSubClass()
    {
        $lhsComparable = new SubClassComparableImpl();
        $rhsComparable = new SubClassComparableSubClass();

        $result = $this->comparator->compare($lhsComparable, $rhsComparable);

        Phake::verifyNoInteraction($this->fallbackComparator);

        $this->assertSame($result, -10);
    }

    public function testCompareWithSubClassComparableUsesCache()
    {
        $lhsComparable = Phake::mock(SubClassComparable::class);
        $rhsComparable = clone $lhsComparable;

        Phake::when($lhsComparable)
            ->compare(Phake::anyParameters())
            ->thenReturn(-10);

        $this->assertSame(-10, $this->comparator->compare($lhsComparable, $rhsComparable));
        $this->assertSame(-10, $this->comparator->compare($lhsComparable, $rhsComparable));

        $this->assertSame(
            Liberator::liberate($this->comparator)->compareImplementationClasses[get_class($lhsComparable)],
            get_class($lhsComparable)
        );
    }

    public function testCompareWithSubClassComparableAndNonObject()
    {
        $comparable = Phake::mock(SubClassComparable::class);

        $result = $this->comparator->compare($comparable, 10);

        Phake::verify($comparable, Phake::never())->compare(Phake::anyParameters());
        Phake::verify($this->fallbackComparator)->compare($comparable, 10);

        $this->assertSame($result, -1);
    }

    public function testCompareWithSubClassComparableAndUnrelatedType()
    {
        $comparable = Phake::mock(SubClassComparable::class);

        $result = $this->comparator->compare($comparable, new stdClass());

        Phake::verify($comparable, Phake::never())->compare(Phake::anyParameters());
        Phake::verify($this->fallbackComparator)->compare($comparable, new stdClass());

        $this->assertSame($result, -1);
    }
}
