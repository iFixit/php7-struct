<?php
declare(strict_types = 1);

/**
 * Immutable object for storing structured data.
 */
abstract class Struct {
   private static $fieldList = [];
   private $data = [];

   /**
    * Takes an array of data keyed with the values returned by getFieldList():
    *    [
    *       'name' => 'David',
    *       'type' => 'Human',
    *       'url' => 'davidrans.com',
    *    ]
    */
   public function __construct(array $data) {
      $fields = $this->getCachedFieldList();

      if ($diff = array_diff($fields, array_keys($data))) {
         throw new Exception('Missing fields: ' . implode($diff, ','));
      }

      foreach ($data as $field => $value) {
         $this->validateProperty($field);
         $this->data[$field] = $value;
      }
   }

   /**
    * This is where the structure of the object is defined. Should return an
    * array of field names, like: ['name', 'type', 'url'].
    */
   abstract protected static function getFieldList(): array;

   protected function getCachedFieldList(): array {
      $classname = get_called_class();

      if (isset(self::$fieldList[$classname])) {
         return self::$fieldList[$classname];
      }

      return self::$fieldList[$classname] = static::getFieldList();
   }

   private function validateProperty($field): void {
      if (!in_array($field, $this->getCachedFieldList())) {
         throw new Exception("Field {$field} not defined on " . get_class($this));
      }
   }

   public function equals(self $other): bool {
      if (get_called_class() !== get_class($other)) {
         return false;
      }

      foreach ($this->data as $field => $value) {
         if ($value !== $other->$field) {
            return false;
         }
      }

      return true;
   }

   public function __get($field) {
      $this->validateProperty($field);
      return $this->data[$field];
   }

   public function __set($field, $value) {
      throw new Exception('This struct is immutable.');
   }

   public function __unset($field) {
      throw new Exception("This struct is immutable.");
   }
}
