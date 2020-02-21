<?php
declare(strict_types = 1);

/**
 * Immutable object for storing structured data.
 */
abstract class Struct {
   // This is where the structure of the object is defined. Should be an array
   // of field names, like: ['name', 'type', 'url'].
   protected const FIELDS = [];

   private static $fieldList = [];
   private $data = [];

   /**
    * Takes an array of data keyed with the values in FIELDS, like:
    *    [
    *       'name' => 'David',
    *       'type' => 'Human',
    *       'url' => 'davidrans.com',
    *    ]
    */
   public function __construct(array $data) {
      $fields = $this->getCachedFieldList();

      if ($diff = array_diff($fields, array_keys($data))) {
         throw new MissingField('Missing fields: ' . implode(',', $diff));
      }

      foreach ($data as $field => $value) {
         $this->validateProperty($field);
         $this->data[$field] = $value;
      }
   }

   protected function getCachedFieldList(): array {
      $classname = get_called_class();

      if (isset(self::$fieldList[$classname])) {
         return self::$fieldList[$classname];
      }

      // Subclasses derived from Struct inherit all their parents' FIELDS.
      $parentsFieldsLists = array_map(function($parent_class) {
         return $parent_class::FIELDS;
      }, array_keys(class_parents($classname)));

      $allFields = array_merge(static::FIELDS, ...$parentsFieldsLists);

      return self::$fieldList[$classname] = $allFields;
   }

   private function validateProperty($field): void {
      if (!in_array($field, $this->getCachedFieldList())) {
         throw new InvalidField("Field {$field} not defined on " . get_class($this));
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
      throw new UnsupportedOperation('This struct is immutable.');
   }

   public function __unset($field) {
      throw new UnsupportedOperation("This struct is immutable.");
   }
}

class StructException extends Exception {}
class MissingField extends StructException {}
class InvalidField extends StructException {}
class UnsupportedOperation extends StructException {}
