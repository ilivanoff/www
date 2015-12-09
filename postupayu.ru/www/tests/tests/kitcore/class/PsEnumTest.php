<?php

/**
 * Generated by PHPUnit_SkeletonGenerator on 2014-10-17 at 18:27:21.
 */
class PsEnumTest extends BasePsTest {

    /**
     * @covers PsEnum::names
     */
    public function testNames() {
        $this->assertNotEmpty(PsTestEnum::names());
        $this->assertTrue(is_array(PsTestEnum::names()));
        $this->assertCount(4, PsTestEnum::names());
        $this->assertEquals(PsTestEnum::names(), array('INT', 'STRING', 'BOOLEAN', 'DOUBLE'));

        $this->assertNotEmpty(PsTestEnum2::names());
        $this->assertTrue(is_array(PsTestEnum2::names()));
        $this->assertCount(6, PsTestEnum2::names());
        $this->assertEquals(PsTestEnum2::names(), array('ARR', 'FLOAT', 'INT', 'STRING', 'BOOLEAN', 'DOUBLE'));
    }

    /**
     * @covers PsEnum::name
     */
    public function testName() {
        $this->assertEquals(PsTestEnum::INT()->name(), 'INT');
        $this->assertEquals(PsTestEnum::STRING()->name(), 'STRING');
        $this->assertEquals(PsTestEnum::BOOLEAN()->name(), 'BOOLEAN');
        $this->assertEquals(PsTestEnum::DOUBLE()->name(), 'DOUBLE');

        $this->assertEquals(PsTestEnum2::INT()->name(), 'INT');
        $this->assertEquals(PsTestEnum2::STRING()->name(), 'STRING');
        $this->assertEquals(PsTestEnum2::BOOLEAN()->name(), 'BOOLEAN');
        $this->assertEquals(PsTestEnum2::DOUBLE()->name(), 'DOUBLE');
        $this->assertEquals(PsTestEnum2::ARR()->name(), 'ARR');
        $this->assertEquals(PsTestEnum2::FLOAT()->name(), 'FLOAT');
    }

    /**
     * @covers PsEnum::valueOf
     */
    public function testValueOf() {
        $this->assertEquals(PsTestEnum::INT(), PsTestEnum::valueOf('INT'));
        $this->assertEquals(PsTestEnum::STRING(), PsTestEnum::valueOf('STRING'));
        $this->assertEquals(PsTestEnum::BOOLEAN(), PsTestEnum::valueOf('BOOLEAN'));
        $this->assertEquals(PsTestEnum::DOUBLE(), PsTestEnum::valueOf('DOUBLE'));

        $this->assertEquals(PsTestEnum2::INT(), PsTestEnum2::valueOf('INT'));
        $this->assertEquals(PsTestEnum2::STRING(), PsTestEnum2::valueOf('STRING'));
        $this->assertEquals(PsTestEnum2::BOOLEAN(), PsTestEnum2::valueOf('BOOLEAN'));
        $this->assertEquals(PsTestEnum2::DOUBLE(), PsTestEnum2::valueOf('DOUBLE'));
        $this->assertEquals(PsTestEnum2::ARR(), PsTestEnum2::valueOf('ARR'));
        $this->assertEquals(PsTestEnum2::FLOAT(), PsTestEnum2::valueOf('FLOAT'));

        $this->assertEquals(PsTestEnum::getByName('INT'), PsTestEnum::INT());
        $this->assertEquals(PsTestEnum::getByName('INT'), PsTestEnum2::INT());
        $this->assertEquals(PsTestEnum2::getByName('INT'), PsTestEnum::INT());

        $this->assertEquals(PsTestEnum::getByName('STRING'), PsTestEnum::STRING());
        $this->assertEquals(PsTestEnum::getByName('STRING'), PsTestEnum2::STRING());
        $this->assertEquals(PsTestEnum2::getByName('STRING'), PsTestEnum::STRING());

        $this->assertEquals(PsTestEnum2::getByName('ARR'), PsTestEnum2::ARR());

        try {
            PsTestEnum::getByName('ARR');
            $this->brakeNoException();
        } catch (Exception $ex) {
            $this->assertEquals($ex->getMessage(), 'Enum PsTestEnum not contains value ARR');
        }
    }

    /**
     * @covers PsEnum::unique
     */
    public function testUnique() {
        $this->assertEquals(PsTestEnum::INT()->unique(), 'PsTestEnum::INT');
        $this->assertEquals(PsTestEnum::STRING()->unique(), 'PsTestEnum::STRING');
        $this->assertEquals(PsTestEnum::BOOLEAN()->unique(), 'PsTestEnum::BOOLEAN');
        $this->assertEquals(PsTestEnum::DOUBLE()->unique(), 'PsTestEnum::DOUBLE');

        $this->assertEquals(PsTestEnum2::INT()->unique(), PsTestEnum::INT()->unique());
        $this->assertEquals(PsTestEnum2::STRING()->unique(), PsTestEnum::STRING()->unique());
        $this->assertEquals(PsTestEnum2::BOOLEAN()->unique(), PsTestEnum::BOOLEAN()->unique());
        $this->assertEquals(PsTestEnum2::DOUBLE()->unique(), PsTestEnum::DOUBLE()->unique());
        $this->assertEquals(PsTestEnum2::ARR()->unique(), 'PsTestEnum2::ARR');
        $this->assertEquals(PsTestEnum2::FLOAT()->unique(), 'PsTestEnum2::FLOAT');
    }

    /**
     * @covers PsTestEnum::getValue
     */
    public function testGetValue() {
        $this->assertEquals(PsTestEnum::INT()->getValue(), 1);
        $this->assertEquals(PsTestEnum::STRING()->getValue(), 'STRING');
        $this->assertEquals(PsTestEnum::BOOLEAN()->getValue(), true);
        $this->assertEquals(PsTestEnum::DOUBLE()->getValue(), 1.1);
    }

    /**
     * @covers PsTestEnum2::getValue
     */
    public function testGetValue2() {
        $this->assertEquals(PsTestEnum2::INT()->getValue(), 1);
        $this->assertEquals(PsTestEnum2::STRING()->getValue(), 'STRING');
        $this->assertEquals(PsTestEnum2::BOOLEAN()->getValue(), true);
        $this->assertEquals(PsTestEnum2::DOUBLE()->getValue(), 1.1);
        $this->assertEquals(PsTestEnum2::ARR()->getValue(), array(1, 2, 3));
        $this->assertEquals(PsTestEnum2::FLOAT()->getValue(), 1.2);
    }

    /**
     * @covers PsTestEnum::isINT
     */
    public function testIsInt() {
        $this->assertTrue(PsTestEnum::isINT(PsTestEnum::INT()));
        $this->assertTrue(PsTestEnum::isINT(PsTestEnum2::INT()));
        $this->assertTrue(PsTestEnum2::isINT(PsTestEnum::INT()));
        $this->assertTrue(PsTestEnum2::isINT(PsTestEnum2::INT()));

        $this->assertFalse(PsTestEnum::isINT(PsTestEnum::STRING()));
        $this->assertFalse(PsTestEnum::isINT(PsTestEnum::BOOLEAN()));
        $this->assertFalse(PsTestEnum::isINT(PsTestEnum::DOUBLE()));
    }

    /**
     * @covers PsTestEnum2::isINT
     */
    public function testIsInt2() {
        $this->assertTrue(PsTestEnum2::isINT(PsTestEnum2::INT()));
        $this->assertFalse(PsTestEnum2::isINT(PsTestEnum2::STRING()));
        $this->assertFalse(PsTestEnum2::isINT(PsTestEnum2::BOOLEAN()));
        $this->assertFalse(PsTestEnum2::isINT(PsTestEnum2::DOUBLE()));
        $this->assertFalse(PsTestEnum2::isINT(PsTestEnum2::ARR()));
        $this->assertFalse(PsTestEnum2::isINT(PsTestEnum2::FLOAT()));
    }

    /**
     * @covers PsTestEnum2::isARR
     */
    public function testIsArr() {
        $this->assertFalse(PsTestEnum2::isARR(PsTestEnum2::INT()));
        $this->assertFalse(PsTestEnum2::isARR(PsTestEnum2::STRING()));
        $this->assertFalse(PsTestEnum2::isARR(PsTestEnum2::BOOLEAN()));
        $this->assertFalse(PsTestEnum2::isARR(PsTestEnum2::DOUBLE()));
        $this->assertTrue(PsTestEnum2::isARR(PsTestEnum2::ARR()));
        $this->assertFalse(PsTestEnum2::isARR(PsTestEnum2::FLOAT()));
    }

}
