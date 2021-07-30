<?php

namespace common\tests\unit\helpers;

use Codeception\Test\Unit;
use common\fixtures\ProgramClientFixture;
use common\fixtures\ProgramFamilyFixture;
use common\helpers\FamilyHelper;
use common\models\Family;

/**
 * Class FamilyHelperTest
 * @package common\tests\unit\helpers
 */
class FamilyHelperTest extends Unit
{
    public function _fixtures(): array
    {
        return [
            ProgramClientFixture::class,
            ProgramFamilyFixture::class,
        ];
    }

    public function testMergeFamiliesWorksWithIdealConditions()
    {
        $original = Family::findOne(1);
        $double = Family::findOne(2);
        expect_that(FamilyHelper::mergeFamilies($original, $double));
        expect($original)->isInstanceOf(Family::class);
        expect(Family::findOne(2))->equals(null);
    }

    public function testAreTripExpensesOpenForUserByDate()
    {

    }
}
