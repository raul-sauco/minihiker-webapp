<?php

namespace backend\tests\acceptance;

use backend\tests\AcceptanceTester;
use common\fixtures\UserFixture;
use yii\helpers\Url as Url;

/**
 * Class ExcelImportAcceptanceCest
 * @package backend\tests\acceptance
 */
class ExcelImportAcceptanceCest
{
    public function _fixtures(): array
    {
        return [
            UserFixture::class,
        ];
    }
//
//    /**
//     * Login as admin.
//     * @param AcceptanceTester $I
//     */
//    public function login(AcceptanceTester $I)
//    {
//        $I->amOnPage(Url::toRoute('/site/login'));
//        $I->amGoingTo('try to login with correct credentials');
//        $I->fillField('input[name="LoginForm[username]"]', 'user-1');
//        $I->fillField('input[name="LoginForm[password]"]', 'password');
//        $I->click('login-button');
//        $I->wait(1);
//        $I->expectTo('see the main page text');
//        $I->see('我们的客户是谁？');
//    }
//
//    /**
//     * Check that the user can select a existing file from the file system.
//     * @before login
//     * @param AcceptanceTester $I
//     */
//    public function ensureSelectingSpreadsheetWorks(AcceptanceTester $I)
//    {
//        $I->amOnPage(Url::toRoute('/excel-import/index'));
//        $I->see('上传表格');
//        // TODO Attaching a file takes too long and does not trigger data processing
//        // $I->attachFile('//input[@type="file"]', 'import-data.xlsx');
//    }
}
