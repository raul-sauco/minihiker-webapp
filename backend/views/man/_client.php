<?php

use yii\bootstrap\Html;

/* @var $this yii\web\View */

?>

<h1 id="man-client">Client</h1>

The client model represents ** one ** person that has a relationship with the
company. Clients belong on a [family](#man-family) and cannot be recorded
on the system without first
[creating their correspondent family model](#man-family-create).

<h2 id="man-client-index">Viewing all Clients</h2>

A list of all the clients can be found on the `client/index` route, which can
be accessed by clicking the [client](#man-client) link on the main menu bar.

The page will look like the following sample: (_sample has been shortened_)

<div class="client-index alert alert-info">

    <div class="client-search row">

        <form id="w0" action="" method="get">
            <input type="hidden" name="r" value="client/index">
            <div class="col-lg-8">

                <div class="input-group" id="client-index-search-box">

                    <input type="search" class="form-control" name="queryString"
                           aria-label="客户端搜索框"
                           placeholder="在此处输入搜索文本. 查看所有客户的右侧复选框"
                           value="">

                    <span class="input-group-addon"> <input type="checkbox" name="selectAll"
                                                            aria-label="客户端搜索筛选器"
                                                            title="Select only kids">
			</span>

                </div>

            </div>

            <div class="col-lg-4">

                <button type="submit" class="btn btn-primary">搜索</button>
            </div>

        </form>
    </div>

    <div id="p0" data-pjax-container="" data-pjax-push-state data-pjax-timeout="1000">
        <div id="w1" class="grid-view"><div class="summary">第<b>1-20</b>条，共<b>2,965</b>条数据.</div>
            <table class="table table-striped table-bordered"><thead>
                <tr><th>名字</th><th>昵称</th><th>类别</th><th>家庭</th><th>家庭角色</th></tr>
                </thead>
                <tbody>
                <tr data-key="1"><td><a href="">任俊飞</a></td><td>Tiger</td><td>会员</td>
                    <td><a href="" data-pjax="0">贾宁</a></td><td>宝贝</td></tr>
                <tr data-key="4"><td><a href="">郑佳成</a></td><td>成成</td><td>会员</td>
                    <td><a href="" data-pjax="0">周少红</a></td><td>宝贝</td></tr>
                <tr data-key="7"><td><a href="">林子楠</a></td><td>楠楠</td><td>会员</td>
                    <td><a href="" data-pjax="0">陈小燕</a></td><td>宝贝</td></tr>
                </tbody>
            </table>
            <ul class="pagination"><li class="prev disabled"><span>&laquo;</span></li>
                <li class="active"><a href="" data-page="0">1</a></li>
                <li><a href="" data-page="1">2</a></li>
                <li><a href="" data-page="2">3</a></li>
                <li><a href="" data-page="3">4</a></li>
                <li><a href="" data-page="4">5</a></li>
                <li><a href="" data-page="5">6</a></li>
                <li><a href="" data-page="6">7</a></li>
                <li><a href="" data-page="7">8</a></li>
                <li><a href="" data-page="8">9</a></li>
                <li><a href="" data-page="9">10</a></li>
                <li class="next"><a href="" data-page="1">&raquo;</a></li></ul></div>
    </div>
</div>

Below the breadcrumbs, present in all pages of the site, we find a single
search field, the text entered there will be matched to the contents of
the following fields to present results:

- name
- nickname
- English name
- pinyin name

After a search only models with fields that match the search query will be
displayed.

To the right of the search bar is a checkbox that allows us to toogle between
displaying all clients or displaying only kids (_default_)

Under the search bar is the grid display, each row representing a client and
offering two links, the client's name links to the [client's view page](#man-client-view)
and the family's name links to the [family's view page](#man-family-view).

<h2 id="man-client-view">Viewing one Client</h2>

Displays a table with details of **one** client, following that there is a
table that displays programs the client is participating on.

<div class="alert alert-info">
    To edit programs that the client is participating on check
    <a href="#man-program-manage-clients">managing clients on a program</a>
</div>

At the top of the page there is a link that allows us to
<a href="#man-client-create" class="btn btn-sm btn-success">Add family member</a>,
the clients created using that link will *belong* to the same family as the
current client.

<h2 id="man-client-create">Adding Clients to the system</h2>

A `client` model **needs** to be related to a `family` and that poses some
restrictions on the way they can be created. We can find two links to create
a new client:

1. On a [family](#man-family-view)'s page through the
<a href="#man-client-create" class="btn btn-sm btn-success">Add member</a>
button.
2. On an [existing client](#man-client-view)'s page through the
<a href="#man-client-create" class="btn btn-sm btn-success">Add family member</a>
button.

Both links direct to the same page, a form with all the `client` model's field,
we can fill the information that we have at the moment and create the client,
it will be automatically linked to the family.

<div class="alert alert-info">
    <p><?= Html::icon('info-sign') ?>
    <strong>All the fields are optional.</strong></p>
    <p>We could create a client without inputing any information.
    The system would automatically fill the <code>id</code> and
    <code>family_id</code> fields and all other fields would be set to their
    default values or <code>null</code></p>
</div>

<h2 id="man-client-update">Updating a Client</h2>

We can find an update link on the client's
[view page](#man-client-view), the update form is the same as the create form.

<h2 id="man-client-delete">Deleting a Client</h2>

We can find a delete link on the client's
[view page](#man-client-view), the system will ask for confirmation before
deleting the record from the database.

<div class="alert alert-danger">
    <?= Html::icon('warning-sign') ?> Deleting a <strong>client</strong> cannot be undone.
</div>

<h2 id="man-client-internals">Client Internals</h2>

<h3 id="man-client-dependencies">Client Dependencies</h3>

* `family` a client needs to be related to a family record in a * <- 1 relationship.
* `user` system managed relationship. `created_by` and `updated_by`.

<h3 id="man-client-dependents">Client Dependents</h3>

* `family.mother_id` ON DELETE SET NULL ON UPDATE CASCADE
* `family.father_id` ON DELETE SET NULL ON UPDATE CASCADE
* `program_client.client_id` ON DELETE RESTRICT ON UPDATE CASCADE

<h3 id="man-client-eer">Client EER Diagram</h3>

![Program EER diagram](<?= yii\helpers\Url::to('@web/img/man/clientEER.svg') ?>)

<h3 id="man-client-sql">Client SQL Definition Script</h3>

    CREATE TABLE `client` (
        `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
        `name_zh` varchar(12) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
        `nickname` varchar(128) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
        `name_pinyin` varchar(128) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
        `name_en` varchar(128) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
        `birthdate` date DEFAULT NULL,
        `is_male` bit(1) DEFAULT NULL,
        `is_kid` bit(1) DEFAULT NULL,
        `family_role_other` varchar(64) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
        `remarks` text COLLATE utf8mb4_unicode_ci,
        `phone_number` varchar(18) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
        `phone_number_2` varchar(18) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
        `email` varchar(64) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
        `wechat_id` varchar(64) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
        `id_card_number` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
        `passport_number` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
        `passport_issue_date` date DEFAULT NULL,
        `passport_expire_date` date DEFAULT NULL,
        `passport_place_of_issue` varchar(60) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
        `passport_issuing_authority` varchar(120) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
        `place_of_birth` varchar(60) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
        `family_id` int(11) unsigned DEFAULT NULL,
        `created_by` int(11) unsigned DEFAULT NULL,
        `updated_by` int(11) unsigned DEFAULT NULL,
        `created_at` int(11) unsigned DEFAULT NULL,
        `updated_at` int(11) unsigned DEFAULT NULL,
        PRIMARY KEY (`id`),
        KEY `client_ibfk_2` (`family_id`),
        KEY `FK_client_created_by` (`created_by`),
        KEY `FK_client_updated_by` (`updated_by`),
        CONSTRAINT `FK_client_created_by` FOREIGN KEY (`created_by`)
            REFERENCES `user` (`id`) ON UPDATE CASCADE,
        CONSTRAINT `FK_client_updated_by` FOREIGN KEY (`updated_by`)
            REFERENCES `user` (`id`) ON UPDATE CASCADE,
        CONSTRAINT `client_ibfk_2` FOREIGN KEY (`family_id`)
            REFERENCES `family` (`id`) ON UPDATE CASCADE
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;