<?php

namespace apivp1\tests\api;

use apivp1\tests\ApiTester;
use common\fixtures\AuthAssignmentFixture;
use common\fixtures\AuthItemChildFixture;
use common\fixtures\AuthItemFixture;
use common\fixtures\AuthRuleFixture;
use common\fixtures\LocationFixture;
use common\fixtures\ProgramGroupFixture;
use common\fixtures\ProgramTypeFixture;
use common\fixtures\UserFixture;

/**
 * Class ProgramSearchCest
 * @package apivp1\tests\api
 */
class ProgramSearchCest
{
    public function _fixtures()
    {
        return [
            'user' => [
                'class' => UserFixture::class,
                'dataFile' => codecept_data_dir('user.php')
            ],
            'auth_item' => [
                'class' => AuthItemFixture::class,
                'dataFile' => codecept_data_dir('auth_item.php')
            ],
            'auth_item_child' => [
                'class' => AuthItemChildFixture::class,
                'dataFile' => codecept_data_dir('auth_item_child.php')
            ],
            'auth_rule' => [
                'class' => AuthRuleFixture::class,
                'dataFile' => codecept_data_dir('auth_rule.php')
            ],
            'auth_assignment' => [
                'class' => AuthAssignmentFixture::class,
                'dataFile' => codecept_data_dir('auth_assignment.php')
            ],
            'location' => [
                'class' => LocationFixture::class,
                'dataFile' => codecept_data_dir('location.php')
            ],
            'programType' => [
                'class' => ProgramTypeFixture::class,
                'dataFile' => codecept_data_dir('program_type.php')
            ],
            'programGroup' => [
                'class' => ProgramGroupFixture::class,
                'dataFile' => codecept_data_dir('program_group.php')
            ],
        ];
    }

    /**
     * @param ApiTester $I
     */
    public function indexAsAdmin (ApiTester $I)
    {
        $I->haveHttpHeader('Content-Type', 'application/json');
        $I->haveHttpHeader('Authorization', 'Bearer admin-1-access-token');
        $I->sendGET('wxps');

        $I->seeResponseCodeIs(200);
        $I->seeResponseIsJson();
        $I->seeResponseContainsJson([
//            'id' => 1,
//            'name' => 'PG1'
        ]);
        $I->dontSeeResponseContains('created_at');
    }
}
