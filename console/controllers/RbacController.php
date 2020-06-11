<?php

namespace console\controllers;

use common\models\User;
use common\rbac\UserAndClientAreFamilyRule;
use common\rbac\UserBelongsToFamilyRule;
use common\rbac\UserIsThisClientRule;
use Yii;
use yii\console\Controller;
use yii\db\Exception;

/**
 * Class RbacController
 * @package console\controllers
 */
class RbacController extends Controller
{
    /**
     * @throws Exception
     * @throws \yii\base\Exception
     * @throws \Exception
     */
    public function actionInit(): void
    {
        $db = Yii::$app->db->dsn;
        if (strpos($db, 'test') !== false) {
            echo "Initializing RBAC on TEST database\n";
        } else {
            echo "Initializing RBAC on PRODUCTION database\n";
        }
        $this->cleanTables();
        $auth = Yii::$app->authManager;

        /* ************* ROLES ************* */

        echo "Adding roles\n";

        $adminRole = $auth->createRole('admin');
        $auth->add($adminRole);

        $userRole = $auth->createRole('user');
        $auth->add($userRole);

        $clientRole = $auth->createRole('client');
        $auth->add($clientRole);

        // Admin inheritance
        $auth->addChild($adminRole, $userRole);

        /* *************** Site *************** */
        echo "Adding generic site permissions\n";

        $viewIndex = $auth->createPermission('viewIndex');
        $viewIndex->description = 'View the index page';
        $auth->add($viewIndex);
        $auth->addChild($userRole, $viewIndex);

        $viewMan = $auth->createPermission('viewMan');
        $viewMan->description = 'View the manual page';
        $auth->add($viewMan);
        $auth->addChild($userRole, $viewMan);

        /* ************* Program ************** */
        echo "Adding program related permissions\n";

        $createProgram = $auth->createPermission('createProgram');
        $createProgram->description = 'Create a program';
        $auth->add($createProgram);
        $auth->addChild($userRole, $createProgram);	// Only admins can create new programs

        $updateProgram = $auth->createPermission('updateProgram');
        $updateProgram->description = 'Update a program';
        $auth->add($updateProgram);
        $auth->addChild($userRole, $updateProgram);

        $viewProgram = $auth->createPermission('viewProgram');
        $viewProgram->description = 'View a single program details';
        $auth->add($viewProgram);
        $auth->addChild($userRole, $viewProgram);

        $listPrograms = $auth->createPermission('listPrograms');
        $listPrograms->description = 'View a list of programs';
        $auth->add($listPrograms);
        $auth->addChild($userRole, $listPrograms);

        $deleteProgram = $auth->createPermission('deleteProgram');
        $deleteProgram->description = 'Delete a program';
        $auth->add($deleteProgram);
        $auth->addChild($adminRole, $deleteProgram);

        /* ************* ProgramPrice ************** */
        echo "Adding program-price related permissions\n";

        $createProgramPrice = $auth->createPermission('createProgramPrice');
        $createProgramPrice->description = 'Create a programPrice';
        $auth->add($createProgramPrice);
        $auth->addChild($userRole, $createProgramPrice);

        $updateProgramPrice = $auth->createPermission('updateProgramPrice');
        $updateProgramPrice->description = 'Update a programPrice';
        $auth->add($updateProgramPrice);
        $auth->addChild($userRole, $updateProgramPrice);

        $viewProgramPrice = $auth->createPermission('viewProgramPrice');
        $viewProgramPrice->description = 'View a single programPrice details';
        $auth->add($viewProgramPrice);
        $auth->addChild($userRole, $viewProgramPrice);

        $listProgramPrices = $auth->createPermission('listProgramPrices');
        $listProgramPrices->description = 'View a list of programPrices';
        $auth->add($listProgramPrices);
        $auth->addChild($userRole, $listProgramPrices);

        $deleteProgramPrice = $auth->createPermission('deleteProgramPrice');
        $deleteProgramPrice->description = 'Delete a programPrice';
        $auth->add($deleteProgramPrice);
        $auth->addChild($userRole, $deleteProgramPrice);

        /* *************** QA **************** */
        echo "Adding Q/A related permissions\n";
        
        $createQa = $auth->createPermission('createQa');
        $createQa->description = 'Create a Q/A';
        $auth->add($createQa);
        $auth->addChild($clientRole, $createQa);
        $auth->addChild($userRole, $createQa);

        $viewQa = $auth->createPermission('viewQa');
        $viewQa->description = 'View a single qa details';
        $auth->add($viewQa);
        $auth->addChild($userRole, $viewQa);
        
        $listQas = $auth->createPermission('listQas');
        $listQas->description = 'List Q/As';
        $auth->add($listQas);
        $auth->addChild($clientRole, $listQas);
        $auth->addChild($userRole, $listQas);

        $updateQa = $auth->createPermission('updateQa');
        $updateQa->description = 'Update a qa';
        $auth->add($updateQa);
        $auth->addChild($userRole, $updateQa);

        $deleteQa = $auth->createPermission('deleteQa');
        $deleteQa->description = 'Delete a qa';
        $auth->add($deleteQa);
        $auth->addChild($userRole, $deleteQa);

        /* ************* Client ************** */
        echo "Adding client related permissions\n";

        $createClient = $auth->createPermission('createClient');
        $createClient->description = 'Create a client';
        $auth->add($createClient);
        $auth->addChild($userRole, $createClient);

        // Allow clients to create new Clients on their own family
        $auth->addChild($clientRole, $createClient);

        $updateClient = $auth->createPermission('updateClient');
        $updateClient->description = 'Update a client';
        $auth->add($updateClient);
        $auth->addChild($userRole, $updateClient);

        $viewClient = $auth->createPermission('viewClient');
        $viewClient->description = 'View a single client details';
        $auth->add($viewClient);
        $auth->addChild($userRole, $viewClient);

        $listClients = $auth->createPermission('listClients');
        $listClients->description = 'View a list of clients';
        $auth->add($listClients);
        $auth->addChild($userRole, $listClients);

        $deleteClient = $auth->createPermission('deleteClient');
        $deleteClient->description = 'Delete a client';
        $auth->add($deleteClient);
        $auth->addChild($adminRole, $deleteClient);
        $auth->addChild($userRole, $deleteClient);

        // START // Add userIsThisClientRule
        echo "Adding user is this client rule\n";

        $userIsThisClientRule = new UserIsThisClientRule();
        $auth->add($userIsThisClientRule);

        $userIsThisClient = $auth->createPermission('userIsThisClient');
        $userIsThisClient->description = 'Determine if the current application user is this client';
        $userIsThisClient->ruleName = $userIsThisClientRule->name;
        $auth->add($userIsThisClient);

        // Let clients see and update their own details
        $auth->addChild($userIsThisClient, $viewClient);
        $auth->addChild($userIsThisClient, $updateClient);

        $auth->addChild($clientRole, $userIsThisClient);

        // END // Add user is this client rule

        // START // Add userAndClientAreFamilyRule
        echo "Adding user and client are family rule\n";

        $userAndClientAreFamilyRule = new UserAndClientAreFamilyRule();
        $auth->add($userAndClientAreFamilyRule);

        $userAndClientAreFamily = $auth->createPermission('userAndClientAreFamily');
        $userAndClientAreFamily->description = 'Determine if the current user application and the client belong to the same family';
        $userAndClientAreFamily->ruleName = $userAndClientAreFamilyRule->name;
        $auth->add($userAndClientAreFamily);

        // Let clients see and update their familiars details
        $auth->addChild($userAndClientAreFamily, $viewClient);
        $auth->addChild($userAndClientAreFamily, $updateClient);
        $auth->addChild($userAndClientAreFamily, $deleteClient);

        $auth->addChild($clientRole, $userAndClientAreFamily);

        // END // Add user and client are family

        /* ************* Family ************** */
        echo "Adding family related permissions\n";

        $createFamily = $auth->createPermission('createFamily');
        $createFamily->description = 'Create a family';
        $auth->add($createFamily);
        $auth->addChild($userRole, $createFamily);

        $updateFamily = $auth->createPermission('updateFamily');
        $updateFamily->description = 'Update a family';
        $auth->add($updateFamily);
        $auth->addChild($userRole, $updateFamily);

        $viewFamily = $auth->createPermission('viewFamily');
        $viewFamily->description = 'View a single family details';
        $auth->add($viewFamily);
        $auth->addChild($userRole, $viewFamily);

        $listFamilies = $auth->createPermission('listFamilies');
        $listFamilies->description = 'View a list of families';
        $auth->add($listFamilies);
        $auth->addChild($userRole, $listFamilies);

        $deleteFamily = $auth->createPermission('deleteFamily');
        $deleteFamily->description = 'Delete a family';
        $auth->add($deleteFamily);
        $auth->addChild($adminRole, $deleteFamily);

        // START // Add userBelongsToFamilyRule
        echo "Adding user belongs to family rule\n";

        $userBelongsToFamilyRule = new UserBelongsToFamilyRule();
        $auth->add($userBelongsToFamilyRule);

        $userBelongsToFamily = $auth->createPermission('userBelongsToFamily');
        $userBelongsToFamily->description = 'Determine if the current application user belongs to the family';
        $userBelongsToFamily->ruleName = $userBelongsToFamilyRule->name;
        $auth->add($userBelongsToFamily);

        // Let users see and update their own family details
        $auth->addChild($userBelongsToFamily, $viewFamily);
        $auth->addChild($userBelongsToFamily, $updateFamily);

        $auth->addChild($clientRole, $userBelongsToFamily);

        // END // Add userBelongsToFamilyRule

        /* ************* Image ************** */
        echo "Adding image related permissions\n";

        $createImage = $auth->createPermission('createImage');
        $createImage->description = 'Create an image';
        $auth->add($createImage);
        $auth->addChild($userRole, $createImage);

        $updateImage = $auth->createPermission('updateImage');
        $updateImage->description = 'Update an image';
        $auth->add($updateImage);
        $auth->addChild($userRole, $updateImage);

        $viewImage = $auth->createPermission('viewImage');
        $viewImage->description = 'View a single image details';
        $auth->add($viewImage);
        $auth->addChild($userRole, $viewImage);

        $listImages = $auth->createPermission('listImages');
        $listImages->description = 'View a list of images';
        $auth->add($listImages);
        $auth->addChild($userRole, $listImages);

        $deleteImage = $auth->createPermission('deleteImage');
        $deleteImage->description = 'Delete a image';
        $auth->add($deleteImage);
        $auth->addChild($userRole, $deleteImage);

        /* ************* Location ************** */
        echo "Adding location related permissions\n";

        $createLocation = $auth->createPermission('createLocation');
        $createLocation->description = 'Create a location';
        $auth->add($createLocation);
        $auth->addChild($userRole, $createLocation);

        $updateLocation = $auth->createPermission('updateLocation');
        $updateLocation->description = 'Update a location';
        $auth->add($updateLocation);
        $auth->addChild($userRole, $updateLocation);

        $viewLocation = $auth->createPermission('viewLocation');
        $viewLocation->description = 'View a single location details';
        $auth->add($viewLocation);
        $auth->addChild($userRole, $viewLocation);

        $listLocations = $auth->createPermission('listLocations');
        $listLocations->description = 'View a list of locations';
        $auth->add($listLocations);
        $auth->addChild($userRole, $listLocations);

        $deleteLocation = $auth->createPermission('deleteLocation');
        $deleteLocation->description = 'Delete a location';
        $auth->add($deleteLocation);
        $auth->addChild($adminRole, $deleteLocation);


        /* ************* Supplier ************** */
        echo "Adding supplier related permissions\n";

        $createSupplier = $auth->createPermission('createSupplier');
        $createSupplier->description = 'Create a supplier';
        $auth->add($createSupplier);
        $auth->addChild($userRole, $createSupplier);

        $updateSupplier = $auth->createPermission('updateSupplier');
        $updateSupplier->description = 'Update a supplier';
        $auth->add($updateSupplier);
        $auth->addChild($userRole, $updateSupplier);

        $viewSupplier = $auth->createPermission('viewSupplier');
        $viewSupplier->description = 'View a single supplier details';
        $auth->add($viewSupplier);
        $auth->addChild($userRole, $viewSupplier);

        $listSuppliers = $auth->createPermission('listSuppliers');
        $listSuppliers->description = 'View a list of suppliers';
        $auth->add($listSuppliers);
        $auth->addChild($userRole, $listSuppliers);

        $deleteSupplier = $auth->createPermission('deleteSupplier');
        $deleteSupplier->description = 'Delete a supplier';
        $auth->add($deleteSupplier);
        $auth->addChild($adminRole, $deleteSupplier);


        /* ************* User ************** */
        echo "Adding user related permissions\n";

        $createUser = $auth->createPermission('createUser');
        $createUser->description = 'Create a user';
        $auth->add($createUser);
        $auth->addChild($adminRole, $createUser);	// Only admins can create new users

        $updateUser = $auth->createPermission('updateUser');
        $updateUser->description = 'Update a user';
        $auth->add($updateUser);
        $auth->addChild($adminRole, $updateUser);

        $viewUser = $auth->createPermission('viewUser');
        $viewUser->description = 'View a single user details';
        $auth->add($viewUser);
        $auth->addChild($adminRole, $viewUser);

        $listUsers = $auth->createPermission('listUsers');
        $listUsers->description = 'View a list of users';
        $auth->add($listUsers);
        $auth->addChild($adminRole, $listUsers);

        $deleteUser = $auth->createPermission('deleteUser');
        $deleteUser->description = 'Delete a user';
        $auth->add($deleteUser);
        $auth->addChild($adminRole, $deleteUser);


        /* ******** ROLE ASSIGNMENT *********** */

        echo "Assigning roles to users based on user type\n";

        $admins = User::find()->where(['user_type' => 1])->all();
        foreach ($admins as $admin)
        {
            $auth->assign($adminRole, $admin->id);
        }

        $users = User::find()->where(['user_type' => 2])->all();
        foreach ($users as $user)
        {
            $auth->assign($userRole, $user->id);
        }

        $users = User::find()->where(['user_type' => 4])->all();
        foreach ($users as $user)
        {
            $auth->assign($clientRole, $user->id);
        }

        echo "All done\n";

    }

    /**
     * Deletes all data from auth_* tables
     * @throws Exception
     */
    private function cleanTables (): void
    {
        echo "Cleaning the tables\n";
        Yii::$app->db->createCommand()->delete('auth_assignment')->execute();
        Yii::$app->db->createCommand()->delete('auth_item_child')->execute();
        Yii::$app->db->createCommand()->delete('auth_item')->execute();
        Yii::$app->db->createCommand()->delete('auth_rule')->execute();
    }
}
