<?php

namespace backend\tests\acceptance;

use backend\tests\AcceptanceTester;
use common\fixtures\ProgramFixture;
use common\models\Program;
use yii\helpers\Url as Url;

/**
 * Class ProgramAcceptanceCest
 * @package backend\tests\acceptance
 */
class ProgramAcceptanceCest
{
    public function _fixtures(): array
    {
        return [
            ProgramFixture::class,
        ];
    }

    /**
     * Login as admin.
     * @param AcceptanceTester $I
     */
    public function login(AcceptanceTester $I)
    {
        $I->amOnPage(Url::toRoute('/site/login'));
        $I->amGoingTo('try to login with correct credentials');
        $I->fillField('input[name="LoginForm[username]"]', 'user-1');
        $I->fillField('input[name="LoginForm[password]"]', 'password');
        $I->click('login-button');
        $I->wait(1);
        $I->expectTo('see the main page text');
        $I->see('我们的客户是谁？');
    }

    /**
     * @before login
     * @param AcceptanceTester $I
     * @throws \Exception
     */
    public function index(AcceptanceTester $I)
    {
        $program = Program::findOne(1);
        $I->amOnPage(Url::toRoute('/program/index'));
        $I->waitForText($program->getNamei18n(), 3);
        $I->see('2017年5月21日');
    }

    /**
     * @before login
     * @param AcceptanceTester $I
     * @throws \Exception
     */
    public function delete(AcceptanceTester $I)
    {
        $program = Program::findOne(1);
        $I->amOnPage(Url::toRoute('/program/1'));
        $I->waitForText($program->getNamei18n(), 3);
        $I->click('删除');
        $I->acceptPopup();
        $I->waitForText('项目', 3);
        $I->dontSee($program->getNamei18n());
    }
}
