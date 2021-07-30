<?php

namespace backend\tests\acceptance;

use backend\tests\AcceptanceTester;
use common\fixtures\UserFixture;
use yii\helpers\Url as Url;

/**
 * Class LoginCest
 * @package backend\tests\acceptance
 */
class LoginCest
{
    public function _fixtures(): array
    {
        return [
            UserFixture::class,
        ];
    }

    /**
     * Check that an existing user can login and access the site/index page.
     * @param AcceptanceTester $I
     */
    public function ensureThatLoginWorks(AcceptanceTester $I)
    {
        $I->amOnPage(Url::toRoute('/site/login'));
        $I->see('Login', 'h1');
        $I->amGoingTo('try to login with correct credentials');
        $I->fillField('input[name="LoginForm[username]"]', 'user-1');
        $I->fillField('input[name="LoginForm[password]"]', 'password');
        $I->click('login-button');
        $I->wait(1);
        $I->expectTo('see user info');
        $I->see('Logout (user-1)');
        $I->expectTo('see the main page text');
        $I->see('Who are our clients?');
    }
}
