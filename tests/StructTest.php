<?php
declare(strict_types = 1);

require_once 'src/Struct.php';

/**
 * @group dozuki
 * @group ifixit
 * @group onprem
 */
class StructTest extends PHPUnit\Framework\TestCase {
   public function testBasicBehavior() {
      $struct = new MyStruct([
         'a' => 1,
         'b' => 'string',
      ]);

      $this->assertSame(1, $struct->a);
      $this->assertSame('string', $struct->b);
   }

   public function testConstructorExtraArgument() {
      $this->expectException(InvalidField::class);

      new MyStruct([
         'a' => 1,
         'b' => 2,
         'c' => 2,
      ]);
   }

   public function testConstructorMissingArgument() {
      $this->expectException(MissingField::class);

      new MyStruct(['a' => 1]);
   }

   public function testGetInvalidProperty() {
      $this->expectException(InvalidField::class);

      $struct = new MyStruct(['a' => 1, 'b' => 2]);
      $struct->c;
   }

   public function testSettingExistingProperty() {
      $this->expectException(UnsupportedOperation::class);

      $struct = new MyStruct(['a' => 1, 'b' => 2]);
      $struct->a = 2;
   }

   public function testSettingNonexistentProperty() {
      $this->expectException(UnsupportedOperation::class);

      $struct = new MyStruct(['a' => 1, 'b' => 2]);
      $struct->c = 'c';
   }

   public function testUnsettingProperty() {
      $this->expectException(UnsupportedOperation::class);

      $struct = new MyStruct(['a' => 1, 'b' => 2]);
      unset($struct->a);
   }

   public function testEquals() {
      // same
      $struct1 = new MyStruct(['a' => 1, 'b' => 2]);
      $struct2 = new MyStruct(['b' => 2, 'a' => 1]);
      // different values
      $struct3 = new MyStruct(['a' => 2, 'b' => 1]);
      // different class
      $struct4 = new YourStruct(['a' => 2, 'b' => 1]);

      $this->assertTrue($struct1->equals($struct2));
      $this->assertTrue($struct2->equals($struct1));

      $this->assertFalse($struct1->equals($struct3));
      $this->assertFalse($struct3->equals($struct1));

      $this->assertFalse($struct1->equals($struct4));
      $this->assertFalse($struct4->equals($struct1));
   }
}

class MyStruct extends Struct {
   protected static function getFieldList(): array {
      return ['a', 'b'];
   }
}

class YourStruct extends Struct {
   protected static function getFieldList(): array {
      return ['a', 'b'];
   }
}
