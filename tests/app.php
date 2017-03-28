<?php

namespace  tests;

use app\models\Userb;

require(__DIR__ . '/_bootstrap.php');



class  UserbTest extends TestCases
{
    
    
//     public function testValidateEmptyValues() {
//         
//         $user = new Userb();
//         $this->assertFalse($user->validate(),' empty val ');
//         $this->assertArrayHasKey('password', $user->getErrors(),'check empty password errors');
//         $this->assertArrayHasKey('login', $user->getErrors(),'check empty login errors');
//         
//     }
    
      public function testValidateWrongValues() {
        
        $user = new Userb([
            'password' => 'W',
            'login' => 'wrong@emailru',
        ]);
        $this->assertFalse($user->validate(),' empty val ');
       // $this->res = $user->getErrors();
        $this->assertArrayHasKey('password', $user->getErrors(),'check incorrect password errors');
        $this->assertArrayHasKey('login', $user->getErrors(),'check incorrect login errors');
        
    }
    
//       public function testValidateCorrectValues() {
//         
//         $user = new Userb([
//             'password' => 'CorrectUsername',
//             'email' => 'Correct@email.com',
//         ]);
//         $this->assertTrue($user->validate(),' correct mosel is val ');
//         
//     }
//     public function setUp() {
//         parent::setUp();
//         User::deleteAll();
//         Yii::$app->db->createCommand()->insert(User::tableName(),[
//             'username' => 'user',
//             'email' => 'user@email.com',
//         ])->execute();
//     }    
//     
//      public function testValidateExistedValues() {
//         
//         $user = new User([
//             'username' => 'user',
//             'email' => 'user@email.com',
//         ]);
//         $this->assertFalse($user->validate(),' empty val ');
//         $this->assertArrayHasKey('username', $user->getErrors(),'check empty username arrors');
//         $this->assertArrayHasKey('email', $user->getErrors(),'check empty email arrors');
//         
//     }
//     
//      public function testSaveIntoDataBase() {
//         
//         $user = new User([
//             'username' => 'CorrectUsername',
//             'email' => 'Correct@email.com',
//         ]);
//         $this->assertTrue($user->save(),' mosel is saved ');
//         
//     }
}



$class = new \ReflectionClass('\tests\UserbTest');
foreach($class->getMethods() as $method) {
    if(substr($method->name, 0, 4) == 'test') {
        echo 'Test' . $method->class . '::' . $method->name . PHP_EOL .PHP_EOL;
        $test = new $method->class;
        $test->setUp();
        $test->{$method->name}();
        $test->tearDown();
        echo PHP_EOL;
    } 
    
}

