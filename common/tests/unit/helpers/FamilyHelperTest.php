<?php

namespace common\tests\unit\helpers;

use Codeception\Test\Unit;
use common\fixtures\ProgramClientFixture;
use common\fixtures\ProgramFamilyFixture;
use common\helpers\FamilyHelper;
use common\models\Client;
use common\models\Family;
use common\models\ProgramFamily;

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
        $duplicate = Family::findOne(2);
        expect_that(FamilyHelper::mergeFamilies($original, $duplicate));
        expect($original)->isInstanceOf(Family::class);
        expect(Family::findOne(2))->equals(null);
    }

    public function testMergeFamiliesMovesClientsToOriginalFamily()
    {
        $original = Family::findOne(1);
        $duplicate = Family::findOne(2);
        expect_that(FamilyHelper::mergeFamilies($original, $duplicate));
        expect(Family::findOne(2))->equals(null);
        expect(Client::findOne(2)->family_id)->equals(1);
        expect(Client::findOne(3)->family_id)->equals(1);
        expect($original->getClients()->count())->equals(3);
    }

    public function testMergeFamiliesPreservesOldestMembershipDate()
    {
        $original = Family::findOne(1);
        expect($original->membership_date)->equals('2015-01-12');
        $duplicate = Family::findOne(2);
        expect_that(FamilyHelper::mergeFamilies($original, $duplicate));
        expect($original->membership_date)->equals('2014-02-11');
    }

    public function testMergeFamiliesPreservesRemarks()
    {
        $original = Family::findOne(1);
        expect($original->remarks)->equals('REMARKS F1');
        $duplicate = Family::findOne(2);
        expect_that(FamilyHelper::mergeFamilies($original, $duplicate));
        expect($original->remarks)->equals("REMARKS F1\nREMARKS F2");
    }

    public function testMergeProgramFamilies()
    {
        $original = Family::findOne(1);
        $duplicate = Family::findOne(2);
        expect($original->getProgramFamilies()->count())->equals(0);
        expect_that(FamilyHelper::mergeFamilies($original, $duplicate));
        expect($original->getProgramFamilies()->count())->equals(1);
        expect(ProgramFamily::find()->where(['program_id' => 1, 'family_id' => 1])
            ->count())->equals(1);
        expect(ProgramFamily::find()->where(['program_id' => 1, 'family_id' => 2])
            ->count())->equals(0);
    }

    public function testMergeProgramFamilyRecords()
    {
        $original = Family::findOne(2);
        $duplicate = Family::findOne(3);
        expect($original->getProgramFamilies()->count())->equals(1);
        expect_that(FamilyHelper::mergeFamilies($original, $duplicate));
        expect($original->getProgramFamilies()->count())->equals(2);
        expect(ProgramFamily::find()->where(['program_id' => 1, 'family_id' => 2])
            ->count())->equals(1);
        expect(ProgramFamily::find()->where(['program_id' => 2, 'family_id' => 2])
            ->count())->equals(1);
        expect(ProgramFamily::find()->where(['program_id' => 1, 'family_id' => 3])
            ->count())->equals(0);
    }
}
