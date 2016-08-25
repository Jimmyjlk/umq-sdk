<?php

require_once './sdk.php';

$sdk = new SDK();
$sdk->get_organization_id();
$sdk->publish_msg('Test');
$msgs = $sdk->get_msg(10);

print_r($msgs['Msgs']);

$sdk->ack_msg($msgs['Msgs'][0]['MsgID']);
$msgs = $sdk->get_msg(10);

print_r($msgs['Msgs']);

?>
