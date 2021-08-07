<?php

namespace backend\tests\functional;

use backend\tests\FunctionalTester;
use common\fixtures\ClientFixture;
use common\fixtures\FamilyFixture;
use common\fixtures\ProgramClientFixture;
use common\fixtures\ProgramFamilyFixture;
use common\fixtures\UserFixture;
use common\helpers\FamilyHelper;
use common\models\Family;

/**
 * Class FamilyCest
 * @package backend\tests\functional
 */
class FamilyCest
{
    public function _fixtures(): array
    {
        return [
            'families' => FamilyFixture::class,
            'clients' => ClientFixture::class,
            'programFamilies' => ProgramFamilyFixture::class,
            'programClients' => ProgramClientFixture::class,
            'users' => UserFixture::class,
        ];
    }

    /**
     * Test the family/index route.
     * @param FunctionalTester $I
     */
    public function index(FunctionalTester $I)
    {
        $id = 1;
        $family = $I->grabRecord(Family::class, ['id' => $id]);
        $I->amLoggedInAs(1);
        $I->amOnPage(['family/index']);
        $I->see('Create Family');
        $I->see(
            FamilyHelper::getFormattedSerial($family) .
            $family->name .
            $family->address .
            $family->remarks);
    }

    /**
     * Test the family/view route.
     * @param FunctionalTester $I
     * @depends index
     */
    public function view(FunctionalTester $I)
    {
        $id = 4;
        $family = $I->grabRecord(Family::class, ['id' => $id]);
        $I->amLoggedInAs(1);
        $I->amOnPage(['family/index']);
        $I->see('Create Family');
        $I->see(
            FamilyHelper::getFormattedSerial($family) .
            $family->name .
            $family->address .
            $family->remarks);
        $I->amGoingTo('follow the link to the family view page');
        $I->seeLink($family->name);
        $I->click($family->name);
        $I->expectTo('see the family details including members');
        $I->see('Serial Number' . FamilyHelper::getFormattedSerial($family));
        $I->see('Client4孩子(not set)');
        $I->see('Client5父亲123 456 7890');
    }

    /**
     * Test the family/create route.
     * @param FunctionalTester $I
     */
    public function create(FunctionalTester $I)
    {
        $name = 'New name';
        $address = 'New family address';
        $remarks = 'New family remarks';
        $category = '会员';
        $membershipDate = '2017-08-25';
        $por = 'POR';

        $I->amLoggedInAs(1);
        $I->amOnPage(['family/create']);
        $I->see('Create Family');

        $I->amGoingTo('create a new family');
//        $I->fillField('#family-name', $name);
//        $I->fillField('#family-address', $address);
//        $I->fillField('#family-remarks', $remarks);
//        $I->selectOption('#family-category', $category);
//        $I->fillField('#family-place_of_residence', $por);
//        $I->fillField('#family-membership_date', $membershipDate);
//        $I->click('Create');

        $I->submitForm('form#family-form', [
            'Family' => [
                'name' => $name,
                'address' => $address,
                'remarks' => $remarks,
                'category' => $category,
                'place_of_residence' => $por,
                'membership_date' => $membershipDate,
                'serial_number' => 'A001007',
            ]
        ]);

        $I->expectTo('see the new record');
        $I->see('Families ' . $name);

        $I->seeRecord(Family::class, [
            'name' => $name,
            'address' => $address,
            'remarks' => $remarks,
            'category' => $category,
            'place_of_residence' => $por,
            'membership_date' => $membershipDate,
            'serial_number' => 'A001007',
        ]);
    }

    /**
     * Test the family/create route.
     * @param FunctionalTester $I
     */
    public function createVIP(FunctionalTester $I)
    {
        $name = 'VIP name';
        $address = 'VIP family address';
        $remarks = 'VIP family remarks';
        $category = '非会员';
        $membershipDate = '2019-09-02';
        $por = 'VIP POR';
        $I->amLoggedInAs(1);
        $I->amOnPage(['family/create']);
        $I->see('Create Family');
        $expectedSerialNumber = FamilyHelper::generateSerialNumber($category);
        $I->amGoingTo('create a new family');
        $I->submitForm('form#family-form', [
            'Family' => [
                'name' => $name,
                'address' => $address,
                'remarks' => $remarks,
                'category' => $category,
                'place_of_residence' => $por,
                'membership_date' => $membershipDate,
                'serial_number' => 'A001007',
            ]
        ]);
        $I->expectTo('see the new record');
        $I->see('Families ' . $name);
        $I->seeRecord(Family::class, [
            'name' => $name,
            'address' => $address,
            'remarks' => $remarks,
            'category' => $category,
            'place_of_residence' => $por,
            'membership_date' => $membershipDate,
            'serial_number' => 'A001007',
        ]);
    }

    /**
     * Test the family/update route.
     * @depends create
     * @param FunctionalTester $I
     */
    public function update(FunctionalTester $I)
    {
        $family = $I->grabRecord(Family::class, [
            'id' => 1,
        ]);
        $name = 'Updated name';
        $address = 'Updatede family address';
        $remarks = 'Updated family remarks';
        $membershipDate = '2017-09-25';
        $I->amLoggedInAs(1);
        $I->amOnPage(['family/update', 'id' => $family->id]);
        $I->see('Save', 'button.btn[type=submit]');
        $I->amGoingTo('update a family');
        $I->fillField('#family-name', $name);
        $I->fillField('#family-address', $address);
        $I->fillField('#family-remarks', $remarks);
        $I->fillField('#family-membership_date', $membershipDate);
        $I->click('Save');
        $I->expectTo('see the new record');
        $I->see('Families ' . $name);
        $I->seeRecord(Family::class, [
            'id' => $family->id,
            'name' => $name,
            'address' => $address,
            'remarks' => $remarks,
            'category' => $family->category,
            'place_of_residence' => $family->place_of_residence,
            'membership_date' => $membershipDate,
        ]);
    }
}
