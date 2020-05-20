<?php ?>

<div>链接:
    <a :href="g.baseurl + 'client/' + modal.client.id"
       target="_blank">{{modal.client.id}}</a>
</div>
<div>名称: {{modal.client.name_zh}}</div>
<div>身份证号码: {{modal.client.id_card_number}}</div>
<div>家庭:
    <a :href="g.baseurl + 'family/' + modal.client.family_id"
       target="_blank">{{modal.client.familyName}}</a>
</div>
