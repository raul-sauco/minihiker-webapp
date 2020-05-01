<?php

/* @var $this yii\web\View */

use yii\bootstrap\Html;
use yii\helpers\Url;

?>

<h1 id="man-program">Program</h1>

The program entity represents an event organized by the company with an start
date and end date, that may or not be the same. It is expected, though not 
required that a number of [clients](#man-client) will participate on the 
event and that the event is managed by a number of [guides](#man-user).

<h2 id="man-program-index">Viewing all programs</h2>

To view all the programs we need to go to the route 
[Program](<?= Url::toRoute(['program/index']) ?>). We can access the route
from the main menu.

On that page we have a search box to help find a particular program
as well as links that allow us to perform a series of tasks related
to Programs. By default the system shows twenty entries at a time and 
displays links to the other pages at the bottom of the screen.

The page offers the following links:

1. <?= Html::a(
    Yii::t('app', 'Create Program'),
    ['program/create'], 
    ['class' => 'btn btn-sm btn-success']) ?> A link to create a new program.
    
2. Each entry (a row on the table) has three links related with the 
   corresponding entry:
    
	1. <?= Html::a(Html::icon('eye-open'), ['program/view', 'id' => 2])?>
	   A link to a page with detailed information on the Program.
	
	2. <?= Html::a(Html::icon('pencil'), ['program/update', 'id' => 2])?>
	   A link to a page that allows us to modify information related to the 
	   Program.
	   
	3. [<?= Html::icon('trash')?>](#man-program-delete)
	   A link that allows us to directly delete a Program from the system. 
	   Since this command is not reversible it will ask for confirmation
	   before the Program is deleted.
	   
<h2 id="man-program-view">Viewing one program</h2>

To view one program we have to go to the corresponding route 
<?= Html::a('View', ['program/view' , 'id' => 2]) ?> for example selecting the
<?= Html::a(Html::icon('eye-open'), ['#program-view']) ?> icon on the 
program/index page.

The program/view page offers a series of links to perform tasks related to the 
current program.

1. <?= Html::a('Update', ['program/update', 'id' => 2], 
    ['class' => 'btn btn-primary btn-sm'])?> A link to update the information
   related to this program. See * Updating programs * below.
   
2. <?= Html::a('Update Participants', 
    ['program/update-program-clients', 'id' => 2], 
    ['class' => 'btn btn-primary btn-sm']) ?> A link to update the clients that
   are linked to this program. A * program-client * link is how the system 
   represents the fact that a client participates on a program. See more on
   * Adding clients to a program * and * Adding clients to the system *.
   
3. <?= Html::a('Update Guides', 
    ['program/update-guides', 'id' => 2], 
    ['class' => 'btn btn-primary btn-sm']) ?> A link to update the guides that 
   are linked to this program. A * program-guide * link is how the system
   represents the fact that an employee is working on a program. 
   
   <div class="alert alert-info">
   		<?= Html::icon('info-sign') ?>
   		The funcionality of this part of the application is not completed yet.   
   </div>
   
4. <?= Html::a('Delete', ['#'], ['class' => 'btn btn-sm btn-danger'])?> A link
   to delete the program from the system. If we click it the system will ask 
   for confirmation.
   
   <div class="alert alert-danger">
   		<?= Html::icon('warning-sign')?> Use with care, this action is not
   		reversible and it will delete all the information that depends on the
   		program.
   </div>
   
Below the links the system displays a block with the program's remarks and, below 
that, a list with details on all the clients that are recorded at the moment as 
taking part on the program.

Each entry on the list has links to perform actions related to the client and a 
link to visualize details on the client's family.

A continuation the system displays a similar list with information on the 
guides working the program.

<h2 id="man-program-create">Adding new programs to the system</h2>

To add a new program to the system we need to go to the route 
<?= Html::a('Create Program', ['program/create']) ?> and input the required 
information.

1. ** Name: ** The program name. * It should ** not ** contain * a reference 
   to the Date in which the program takes place or to the Location where it takes
   place to avoid
   [data redundancy](https://en.wikipedia.org/wiki/Data_redundancy#In_database_systems).
   The name field accepts `128 characters`.
   
2. ** Type ** Select the type between the ones offered on the display. The 
   types are obtained from the table `program_type`.
   
3. ** Location: ** The location where the program takes place. This field 
   corresponds to the `id` value of a Location entry and, as such, is limited
   to `12 characters`. The database does not allow for repeated Location names.
   The locations are obtained from the `location` table and have to be 
   inserted on the database before they can be used when creating a program.
	
4. ** Start date ** The date in which the program will start. It is not 
   required by the system since programs have an `id` field but it makes it 
   easier for humans to differenciate programs.
   
5. ** End date ** The date in which the program ends. It can be the same as the
   `start date`. It is recommended that `end_date > start_date` but the system
   doesn't enforce it.
   
6. ** Remarks ** A text field to add any remarks that maybe relevant or
   information that may not have a place in other fields.
   
Once we fill up all the fields and click create the system will check all the 
values to make sure they conform with the rules defined by the system. If
validation passes the new program is recorded in the database and the system
redirects the user to the page that displays the newly created program.

<h2 id="man-program-update">Updating a program</h2>

To update an existing program we can use the links on the index page or the 
Update button on the view page. The system will present us with a page 
identical to the page used to create a new program but the fields will 
contain the information currently in use on the existing program.

The updating process is identical to the creating one, the system will 
validate the information entered, if validation fails it will present
messages informing the user about the problems encountered. If validation
passes the system will redirect to the program/view page.

<h2 id="man-program-delete">Deleting a program</h2>

To delete a program we can use the [<?= Html::icon('trash')?>](#man-program-delete)
link on the [index](#man-program-index) page or the 
<?= Html::a('Delete', '#man-program-delete', 
    ['class' => 'btn btn-sm btn-danger'])?> link on the 
[view](#man-program-view) page. The system will ask for confirmation before
deleting the records since this action is not reversible and deletes all the 
[information related with the record](#man-program-dependents).

<div class="alert alert-danger">
	<?= Html::icon('warning-sign') ?> Deleting a program cannot be undone
	and forces the deletion of all <strong>related records</strong>.
</div>

<h2 id="man-program-manage-clients">Managing clients on a program</h2>

The first step to manage the participants on a program is going to the 
[program's page](#man-program-view) and checking which clients are
participating currently on the program. If we decide that we want to add or 
remove participants we can use the <a class="btn btn-sm btn-primary">
Update participants</a> link.

On the `program-client/update-program-client` page we will see a grid of all 
clients currently on the system as follows:

<table class="table table-striped table-bordered">
	<thead>
		<tr>
			<th>Name</th><th>Family</th><th>Edit participants</th>
		</tr>
	</thead>
	<tbody>
    	<tr>
    		<td><a href="#man-client">Client 1's name</a></td>
    		<td><a href="#man-family">Client 1's family</a></td>
    		<td><a class="btn btn-sm btn-success">Add</a></td>
    	</tr>
    	<tr>
    		<td><a href="#man-client">Client 2's name</a></td>
    		<td><a href="#man-family">Client 1's family</a></td>
    		<td><a class="btn btn-sm btn-danger">Remove</a></td>
    	</tr>
    </tbody>
</table>

* The client's name links to the client's record page.
* The family's name links to the client family's record page.
* The <a class="btn btn-sm btn-success">Add</a> button allows us to add * (link) *
  the client to the current program.
  
  On doing so the system will display briefly a confirmation dialog like this:
  
  <div class="alert alert-success">Added client... to program ...</div>

* The <a class="btn btn-sm btn-danger">Remove</a> informs us that the client
  is currently listed as participating on the program. If we choose to remove
  it by clicking the link, on successful removal the system will display a 
  confirmation dialog as follows:
  
  <div class="alert alert-success">Removed client... from program ...</div>
  
<h2 id="man-program-manage-families">Managing families on a program</h2>

** We don't need to manually manage [families](#man-family) on a program. **
A [client](#man-client) is always linked to a family and the system 
updates that family's participation on the program automatically when the 
status of the family members changes. 

I.e. if we remove all members of a family from a given program that family 
will not be registered as participating on the program any longer. Likewise,
if we add a member of a family to a program, that previously the family was
not taking part on, the system will link the family to the given program.

<h2 id="man-program-internals">Program internals</h2>

<h3 id="man-program-dependencies">Program dependencies</h3>

* `location` required, on delete restrict, on update cascade.
* `program_type` not required, on delete set null, on update cascade.
* `user` required, on delete restrict, on update cascade. The system assigns 
  it automatically.

<h3 id="man-program-dependents">Program dependents</h3>

* `payment` not required.
* `program_client` required, on delete restrict on update cascade.
* `program_family` required, on delete restrict on update cascade.
* `program_guide` required, on delete restrict on update cascade.

<h3 id="man-program-eer">Program EER diagram</h3>

![Program EER diagram](<?= Url::to('@web/img/man/programEER.svg') ?>)

<h3 id="man-program-sql">Program SQL definition script</h3>

    DROP TABLE IF EXISTS `program`;
    CREATE TABLE `program` (
      `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
      `name` varchar(128) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
      `type_id` int(11) unsigned DEFAULT NULL,
      `location_id` varchar(12) COLLATE utf8mb4_unicode_ci NOT NULL,
      `start_date` date DEFAULT NULL,
      `end_date` date DEFAULT NULL,
      `remarks` text COLLATE utf8mb4_unicode_ci,
      `created_by` int(11) unsigned DEFAULT NULL,
      `updated_by` int(11) unsigned DEFAULT NULL,
      `created_at` int(11) unsigned DEFAULT NULL,
      `updated_at` int(11) unsigned DEFAULT NULL,
      PRIMARY KEY (`id`),
      KEY `fk_program_location` (`location_id`),
      KEY `FK_Program_Type` (`type_id`),
      CONSTRAINT `FK_Program_Type` FOREIGN KEY (`type_id`) REFERENCES `program_type` (`id`) 
      	ON DELETE SET NULL ON UPDATE CASCADE,
      CONSTRAINT `fk_program_location` FOREIGN KEY (`location_id`) REFERENCES `location` (`name_zh`) 
      	ON DELETE SET NULL ON UPDATE CASCADE,      
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

