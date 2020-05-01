<?php

/* @var $this yii\web\View */

?>

<h1 id="man-supplier">Supplier</h1>

A Supplier models contains information on a _service provider_ that outsources
company contracts.

The information we are most interested on is the contact information, since a
supplier can have multiple people working for their organization, we will find
the contact information on the [contact](#man-contact) model.

** A `supplier` model represents an organization that provides services for
MiniHiker, and is a _container_ for `contact` models that belong to the same
organization. **

Sometimes there will only be one contact and sometimes there will be multiple
contacts related to a supplier.

The supplier model itself stores it's own name, two addresses and a remarks
field where the user can record remarks that are related to the organization
as a whole.

<h2 id="man-supplier-index">Viewing all Suppliers</h2>

<h2 id="man-supplier-view">Viewing one Supplier</h2>

<h2 id="man-supplier-create">Adding Suppliers to the system</h2>

<h2 id="man-supplier-update">Updating a Supplier</h2>

<h2 id="man-supplier-delete">Deleting a Supplier</h2>

<h2 id="man-supplier-internals">Supplier Internals</h2>

<h3 id="man-supplier-dependencies">Supplier Dependencies</h3>

<h3 id="man-supplier-dependents">Supplier Dependents</h3>

<h3 id="man-supplier-eer">Supplier EER Diagram</h3>

<h3 id="man-supplier-sql">Supplier SQL Definition Script</h3>
