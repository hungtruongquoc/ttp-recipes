<?php
namespace Tests\Unit;

use Tests\TestCase;

class SetClassTest extends TestCase {
    public function test_set_class(): void
    {
        $setClass = new SetClass();
        $this->assertInstanceOf(SetClass::class, $setClass);
    }

    public function test_set_class_is_empty(): void {
        $setClass = new SetClass();
        $this->assertTrue($setClass->isEmpty());
    }

    public function test_set_class_add(): void {
        $setClass= new SetClass();
        $this->assertTrue($setClass->isEmpty());
        $setClass->add("item1");
        $this->assertFalse($setClass->isEmpty());
    }

    public function test_set_class_count(): void {
        $setClass= new SetClass();
        $this->assertTrue($setClass->isEmpty());
        $this->assertTrue($setClass->count() === 0, 'Count should be 0');
        $setClass->add("item1");
        $this->assertFalse($setClass->isEmpty());
        $this->assertTrue($setClass->count() !== 0, 'Count should not be 0');
    }

    public function test_set_class_no_duplicate(): void {
        $setClass= new SetClass();
        $this->assertTrue($setClass->isEmpty());
        $this->assertTrue($setClass->count() === 0, 'Count should be 0');
        $setClass->add("item1");
        $setClass->add("item1");
        $this->assertFalse($setClass->isEmpty());
        $this->assertTrue($setClass->count() === 1, 'Count should be 1');
    }

    public function test_set_class_remove(): void {
        $setClass= new SetClass();
        $this->assertTrue($setClass->isEmpty());
        $this->assertTrue($setClass->count() === 0, 'Count should be 0');
        $setClass->add("item1");
        $setClass->add("item2");
        $this->assertFalse($setClass->isEmpty());
        $this->assertTrue($setClass->count() === 2, 'Count should be 2');
        $setClass->remove("item2");
        $this->assertTrue($setClass->count() === 1, 'Count should be 1');
    }

    public function test_set_class_get_by_index(): void {
        $setClass= new SetClass();
        $this->assertTrue($setClass->isEmpty());
        $this->assertTrue($setClass->count() === 0, 'Count should be 0');
        $setClass->add("item1");
        $setClass->add("item2");
        $this->assertFalse($setClass->isEmpty());
        $this->assertTrue($setClass->getByIndex(0) === "item1", 'Item at index 0 should be item1');
    }


}
