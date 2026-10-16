<?php

class PushNotification {

    var $api_key = 'AAAAEe6RZCY:APA91bGXto_BwWAlaSA33lSY2NHGxLAZyTw1iWpUCm0Aa33k4hg_k2xoBDIsuKMX6GbPO6CiQFavyuJ2_Ozfd1fk2_9g593VedaMg_hLH2r8hyH40qYMGGxxmw6_cwvGNhX2WB6cQgPp';
    var $notif_color = '#441a62';

    public function send_notification($device_ids, $message) {
        //$device_ids shoud be array
        $device_ids = $this->get_list_deviceID($subtopic_id);
        //message is an associative array
        $message = [
            'title' => $message['title'],
            'body' => $message['body'],
            'subtitle' => $message['subtitle'],
            'tag' => 'tagname',
            'color' => $this->notif_color,
            'icon' => 'myicon',
            'sound' => 'default',
            'sound' => 'soundfilename', //must be in res/raw
            'badge' => '5',
            'click_action' => 'INTENT_NAME'
        ];

        $fields = [
            'registration_ids' => $device_ids,
            'collapse_key' => '',
            'priority' => 'high', //normal or high
            'notification' => $message,
            'data' => ['news_id' => $news_id]
        ];

        $headers = [
            'Authorization: key=' . $this->api_key,
            'Content-Type: application/json'
        ];

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, 'https://fcm.googleapis.com/fcm/send');
        //curl_setopt($ch, CURLOPT_URL, 'https://android.googleapis.com/gcm/send');  //GCM DEPRECATED URL
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($fields));
        $result = curl_exec($ch);
        curl_close($ch);

        echo $result;
    }

}
