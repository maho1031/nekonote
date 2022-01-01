<?php
 
// テスト対象のPHPファイルを読み込む（絶対パスで取ってくる）
require_once(dirname(__FILE__) . '/../function.php');
 
class FunctionTest extends PHPUnit_Framework_TestCase {
    
    // 半角数字チェック（成功）
    public function testValidNumberTrue() {
      validNumber('123', 'number');
      $results = getErrMsg('number');
      //nullかどうか調べる→nullだったらテスト合格
      $this->assertNull($results);
    }

    // 半角数字チェック（失敗）
    // 全角数字を入力したとき、エラーメッセージが出るか？→エラーメッセージが入っていたら合格
    public function testValidNumberFalse() {
      validNumber('１２３', 'number');
      $results = getErrMsg('number');
      $this->assertEquals(MSG17, $results);
    }

    // 漢数字
    public function testValidNumberFalse2() {
      validNumber('一二三', 'number');
      $results = getErrMsg('number');
      $this->assertEquals(MSG17, $results);
    }

    // 記号
    public function testValidNumberFalse3() {
      validNumber('!#$', 'number');
      $results = getErrMsg('number');
      $this->assertEquals(MSG17, $results);
    }

    public function testGetUserReturnsDbData() {
      $result = getUser(2);
      $this->assertTrue(count($result) == 0);
    }

    public function testGetUserReturnsFalse() {
      $result = getUser(0);
    //   $this->assertFalse($result);
    $this->assertNull($result);
    }
    
}

