<?php

namespace Tests\Feature;

use App\Rules\LanguageCode;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class LanguageCodeValidationTest extends TestCase
{
    use DatabaseTransactions;

    public function testThatCorrectCodesReturnTrue()
    {
        $rule = new LanguageCode();

        $this->assertTrue($rule->passes('language code', 'en-US'),
            "'en-US' should pass language validator");
        $this->assertTrue($rule->passes('language code', 'es-MX'),
            "'es-MX' should pass language validator");
    }

    public function testThatInvalidCodesReturnFalse()
    {
        $rule = new LanguageCode();

        $this->assertFalse($rule->passes('language code', 'just some text'),
            "'just some text' shouldn't pass language validation");
        $this->assertFalse($rule->passes('language code', 'en-US22'),
            "'en-US22' shouldn't pass language validation");
        $this->assertFalse($rule->passes('language code', 'ES-MX'),
            "'ES-MX' shouldn't pass language validation");
        $this->assertFalse($rule->passes('language code', 'ES-mx'),
            "'ES-mx' shouldn't pass language validation");
    }
}
