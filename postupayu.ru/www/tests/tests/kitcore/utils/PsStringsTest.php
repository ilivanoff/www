<?php

/**
 * Generated by PHPUnit_SkeletonGenerator on 2014-09-22 at 17:51:52.
 */
class PsStringsTest extends BasePsTest {

    /**
     * @covers PsStrings::pregReplaceCyclic
     */
    public function testPregReplaceCyclic() {
        $text = 'axbxcxdxe';
        $this->assertEquals(PsStrings::pregReplaceCyclic('/x/', $text, array(1, 2)), 'a1b2c1d2e');
        $this->assertEquals(PsStrings::pregReplaceCyclic('/x/', $text, array(1, 2, 3)), 'a1b2c3d1e');
        $this->assertEquals(PsStrings::pregReplaceCyclic('/x/', $text, array(1, 2, 3, 4)), 'a1b2c3d4e');
    }

    /**
     * @covers PsStrings::replaceWithParams
     */
    public function testReplaceWithParams() {
        $this->assertEquals(PsStrings::replaceWithParams('+', 'a+b+c+d+e+f', array(1, 2)), 'a1b2c+d+e+f');
        $this->assertEquals(PsStrings::replaceWithParams('+', 'a+b+c+d+e+f', array(1, 2, 3)), 'a1b2c3d+e+f');
        $this->assertEquals(PsStrings::replaceWithParams('+', 'a+b+c+d+e+f', array(1, 2, 3, 'X')), 'a1b2c3dXe+f');

        $this->assertNotEquals(PsStrings::replaceWithParams('+', 'a+b+c+d+e+f', array(1, 2, 3, 4)), 'a1b2c3d+e+f');

        try {
            PsStrings::replaceWithParams('+', 'a+b+c+d+e+f', array(1, 2, 3, 'X'), true);
            $this->brakeNoException();
        } catch (PException $ex) {
            
        }

        try {
            PsStrings::replaceWithParams('+', 'a+b+c+d+e+f', array(1, 2, 3, 4, 5, 6), true);
            $this->brakeNoException();
        } catch (PException $ex) {
            
        }
    }

    /**
     * @covers PsStrings::replaceWithBraced
     */
    public function testReplaceWithBraced() {
        $this->assertEquals(PsStrings::replaceWithBraced('{}a{}b{}c', 'x', 'y'), 'xayb{}c');
        $this->assertEquals(PsStrings::replaceWithBraced('{}a{}b{}c', 'x', 'y', 'z'), 'xaybzc');
        $this->assertEquals(PsStrings::replaceWithBraced('{}a{}b{}c', 'x', 'y', 'z', 'w'), 'xaybzc');
        $this->assertEquals(PsStrings::replaceWithBraced('{}{}{}={}', 1, '+', 2, 3), '1+2=3');
        $this->assertEquals(PsStrings::replaceWithBraced('{}{}{}={}', 1, '-', 2, '-1'), '1-2=-1');
    }

    /**
     * @covers PsStrings::replaceMap
     */
    public function testReplaceMap() {
        $params['a'] = 1;
        $params['b'] = 2;
        $params['c'] = 3;

        $this->assertEquals(PsStrings::replaceMap('a+b=c', $params), '1+2=3');
        $this->assertEquals(PsStrings::replaceMap('a+a=b', $params), '1+1=2');
        $this->assertEquals(PsStrings::replaceMap('c-b=a', $params), '3-2=1');
        $this->assertEquals(PsStrings::replaceMap('c-b=a|d', $params), '3-2=1|d');

        $this->assertEquals(PsStrings::replaceMap('{a}+{b}={c}', $params, '{', '}'), '1+2=3');
        $this->assertEquals(PsStrings::replaceMap('{a+{b={c', $params, '{'), '1+2=3');
        $this->assertEquals(PsStrings::replaceMap('a}+b}=c}', $params, null, '}'), '1+2=3');
    }

    /**
     * @covers PsStrings::replaceMapBracedKeys
     */
    public function testReplaceMapBracedKeys() {
        $params['a'] = 1;
        $params['b'] = 2;
        $params['c'] = 3;

        $this->assertEquals(PsStrings::replaceMapBracedKeys('{a}+{b}={c}', $params), '1+2=3');
        $this->assertEquals(PsStrings::replaceMapBracedKeys('{a}', $params), '1');
    }

}