<?php
    $entry = $entry ?? null;
?>
@vite('resources/js/app.js')
<div id="vue-app">
    <detail-deal
        :deal="{{ $entry }}"
    />
</div>
