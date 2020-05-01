<?php

use yii\bootstrap\Html;
use yii\helpers\Url;

/* @var $this yii\web\View */

?>

<h1 id="man-location">Location</h1>

Locations are one of the simplest entities on the database, they represent a 
fisical location where a program, or multiple, can take place, for example
Chiang Mai, or Baihe. They store the name of the location in both Chinese
and English (if desired).

The value assigned to a location, once viewed on the program context, should
answer the following question:

** Where does this program take place? **

And none other. For example, it should not include program names, types or
dates.

Some correct examples are:

1. Yangshuo.
2. ChiangMai.
3. Taiwan.

Some ** incorrect ** examples are:

1. 2017 Yangshuo hiking program.
  - _2017_ is the date and belongs on the program `start_date` and `end_date`
    attributes. It can be obtained from there at display time.
  - _hiking program_ could be the program name but it has no relation with
    where the program takes place.
2. 18寒假亲子徒步
  - _18_ is again a reference to the date the program takes place on and
    it has no place on the location name. It doens't answer the question:
    _"where does it take place?"_.
  - _寒假_ is a ** type ** of program and as such has it's place on the
    `program_type` attribute of a `ProgramGroup` model.
    Other types of `ProgramTypes` are: 暑假，清明，国庆，单日活动，夏令营，冬令营，
    and others.
  - _亲子徒步_ at the moment is not a _type_ of program so, if we wish to use it
    we can input it as the program's name. It also doesn't answer the question:
   _"where does it take place?"_ and, as such, it does ** not ** have a place
   the location's name.

<h2 id="man-location-index">Viewing all Locations</h2>

Once all of the locations are created this functionality should be seldom used
but it may be needed if managing the locations is necessary. To view all the 
locations we need to go to the <?= Html::a('locations', ['location/index'])?> 
route. This route displays a similar view as the other indexes on the site, a 
grid with each entry on the database representing a row.

<p><a class="btn btn-success" href="/mh/index.php?r=location%2Fcreate">Create Location</a></p>
<div class="summary">Showing <b>1-4</b> of <b>140</b> Locations.</div>
<table class="table table-striped table-bordered">
    <thead>
    <tr>
        <th><a href="/mh/index.php?r=location%2Findex&amp;page=3&amp;sort=name_zh" data-sort="name_zh">Name</a></th>
        <th>Total Programs</th>
        <th>Total Participants</th>
    </tr>
    <tr id="w0-filters" class="filters">
        <td><input type="text" class="form-control" name="LocationSearch[name_zh]"></td>
        <td>&nbsp;</td>
        <td>&nbsp;</td>
    </tr>
    </thead>
    <tbody>
    <tr data-key="大理">
        <td><a href="/mh/index.php?r=location%2Fview&amp;id=%E5%A4%A7%E7%90%86" data-pjax="0">Dali</a></td>
        <td>5</td>
        <td>126</td>
    </tr>
    <tr data-key="天目山">
        <td><a href="/mh/index.php?r=location%2Fview&amp;id=%E5%A4%A9%E7%9B%AE%E5%B1%B1" data-pjax="0">Chiang Mai</a></td>
        <td>1</td>
        <td>17</td>
    </tr>
    <tr data-key="巴厘岛">
        <td><a href="/mh/index.php?r=location%2Fview&amp;id=%E5%B7%B4%E5%8E%98%E5%B2%9B" data-pjax="0">Bali</a></td>
        <td>5</td>
        <td>45</td>
    </tr>
    <tr data-key="徽州">
        <td><a href="/mh/index.php?r=location%2Fview&amp;id=%E5%BE%BD%E5%B7%9E" data-pjax="0">Weizhou</a></td>
        <td>1</td>
        <td>8</td>
    </tr>
    </tbody>
</table>

The page offers a link <?= Html::a('Create Location',
    ['location/create'], ['class' => 'btn btn-sm btn-success'])?> that allows us
to add a new location to the system.

Each entry (a row on the table) has three columns:
    
1. The Chinese name of the location that doubles up as a link to the
   [view page](#man-location-view).
	
2. The total number of programs taking place at this location that are
   registered right now on the database. Past, present and upcoming
   programs are counted.
	   
3. The total number of clients that are participating on programs taking
   place at the location. Past, present and upcoming programs are counted.

<h2 id="man-location-view">Viewing one location</h2>

We can view each location on the <?= Html::a('location/view', 
['/location/view', 'id' => 'loc1 zh']) ?> route. The page offers two links to 
manage the current location:

1. <a class="btn btn-sm btn-primary">Update</a> to update the location data.

2. <a class="btn btn-sm btn-danger">Delete</a> to delete the location from the
   database. The system will show a dialog asking for confirmation since the 
   action is not reversible.
   
<div class="alert alert-warning">
	<?= Html::icon('warning-sign') ?> The system will not allow any user to 
	delete a location that has any programs associated with it since that 
	would force the deletion of all programs related to the location. 
	See <a href="#man-program-dependencies">Program dependencies</a> for 
	details on the relation.
</div>

The location/view page displays a list of all the programs that have taken 
place, are taking place or will take place on this location. 

For each program the system displays the program name, the number of 
participants registered at the moment, the start date and the end date of the 
program. The program's name doubles up as a link to the 
[program's view page](#man-program-view).

<h2 id="man-location-create">Adding locations to the system</h2>

We can add new locations on the <?= Html::a('create location', 
['location/create']) ?> route, we only need to provide a new location name.

If we try to create a location with an existing name the system will not allow
us and will display a warning as follows:

<div class="alert alert-info">
    <form id="man-location-form" 
    	action="" method="post">
        <div class="form-group field-location-name_zh required has-error">
            <label class="control-label" for="location-name_zh">
            	Chinese name</label>
            <input id="location-name_zh" class="form-control" 
            	name="Location[name_zh]" value="loc1 zh" maxlength="12" 
            	aria-required="true" aria-invalid="true" type="text">        
            <div class="help-block">
            	Chinese name "loc1 zh" has already been taken.</div>
        </div>
        <div class="form-group field-location-name_en">
            <label class="control-label" for="location-name_en">
            	English name</label>
            <input id="location-name_en" class="form-control" 
            	name="Location[name_en]" value="" type="text">        
            <div class="help-block"></div>
        </div>
        <div class="form-group">
            <button type="submit" class="btn btn-success">Create</button>
        </div>
    </form>
</div>

<h2 id="man-location-update">Updating a location</h2>

Updating locations is similar to creating a new one, we will see the same form.

On submit, if the name has been modified, the change will reflect on all the 
programs linked to this location.

<h2 id="man-location-delete">Deleting a location</h2>

The system will ** not allow users to delete locations that have programs **,
if we wish to delete a location that has any programs linked to it, first we 
will need to * unlink * those programs, this can be done either 
[deleting them](#man-program-delete) from the system, or changing the location
that they are linked to by [updating them](#man-program-update).

Once a location has no programs linked to it the system will allow it's 
deletion.

<div class="alert alert-danger">
	<?= Html::icon('warning-sign') ?> Deleting a location is not reversible 
	but, since the system doesn't allow deleting locations that have programs
	linked to them, the only data that gets deleted is the location's name.
</div>

<h2 id="man-location-internals">Location Internals</h2>

<h3 id="man-location-dependencies">Location dependencies</h3>

Location doesn't have any ** explicit ** dependency. Implicitly it depends on 
[user](#man-user) but it does so in a way that is completely transparent to 
the end user. 

<h3 id="man-location-dependents">Location dependents</h3>

`program` required, on delete restrict on update cascade.

<h3 id="man-location-eer">Location EER diagram</h3>

![Location EER diagram](<?= Url::to('@web/img/man/locationEER.png') ?>)

<h3 id="man-location-sql">Location SQL definition script</h3>

    CREATE TABLE `location` (
      `name_zh` varchar(12) COLLATE utf8mb4_unicode_ci NOT NULL,
      `name_en` tinytext COLLATE utf8mb4_unicode_ci,
      `created_by` int(11) unsigned DEFAULT NULL,
      `updated_by` int(11) unsigned DEFAULT NULL,
      `created_at` int(11) unsigned DEFAULT NULL,
      `updated_at` int(11) unsigned DEFAULT NULL,
      PRIMARY KEY (`name_zh`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


	   
	   