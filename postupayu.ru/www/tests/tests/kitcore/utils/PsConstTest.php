<?php

/**
 * Generated by PHPUnit_SkeletonGenerator on 2014-09-18 at 15:27:36.
 */
class PsConstTest extends BasePsTest {

    /**
     * Проверка отличия значений констант
     */
    public function testDifferentConstValues() {
        $this->assertClassHasDifferentConstValues('PsConst', 'EXT_');
        $this->assertClassHasDifferentConstValues('PsConst', 'PHP_TYPE_');
    }

    /**
     * @covers PsConst::getExts
     */
    public function testGetExts() {
        $this->assertTrue(is_array(PsConst::getExts()));
        $this->assertNotEmpty(PsConst::getExts());
    }

    /**
     * @covers PsConst::hasExt
     */
    public function testHasExt() {
        $this->assertTrue(PsConst::hasExt(PsConst::EXT_CSS));
        $this->assertFalse(PsConst::hasExt(self::NOT_ALLOWED_STR));
    }

    /**
     * @covers PsConst::getPhpTypes
     */
    public function testGetPhpTypes() {
        $this->assertTrue(is_array(PsConst::getPhpTypes()));
        $this->assertNotEmpty(PsConst::getPhpTypes());
    }

    /**
     * @covers PsConst::hasPhpType
     */
    public function testHasPhpType() {
        $this->assertTrue(PsConst::hasPhpType(PsConst::PHP_TYPE_ARRAY));
        $this->assertFalse(PsConst::hasPhpType(self::NOT_ALLOWED_STR));
    }

}