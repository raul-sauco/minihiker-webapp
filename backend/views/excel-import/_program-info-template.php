<?php ?>

<div>链接:
    <a :href="g.baseurl + 'program/' + program.id" target="_blank">
        {{ program.id }}
    </a>
</div>
<div>项目: {{ program.programGroup.name }}</div>
<div>地点: {{ program.programGroup.location_id }}</div>
<div>类型: {{ program.programGroup.type.name }}</div>
<div>开始时间: {{ program.start_date }}</div>
<div>结束日期: {{ program.end_date }}</div>
