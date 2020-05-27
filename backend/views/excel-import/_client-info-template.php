<?php ?>

<div>链接:
    <a :href="g.baseurl + 'client/' + client.id"
       target="_blank">{{client.id}}</a>
</div>
<div>名称: {{client.name_zh}}</div>
<div>身份证号码: {{client.id_card_number}}</div>
<div>家庭:
    <a :href="g.baseurl + 'family/' + client.family_id"
       target="_blank">{{client.familyName}}</a>
</div>
