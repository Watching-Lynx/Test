<?php   
function newOrder($event_id, $event_date, $ticket_adult_price, $ticket_adult_quantity, 
$ticket_kid_price, $ticket_kid_quantity) {   //гл функция
    function send($url_api, $data){          //взаимодействие со сторонним api
        $ci = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url_api);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $response = curl_exec($ci);
        curl_close($ci);
        $newData = json_decode($response, true);
        return $response;
    }

$good=false;
$url= "https://api.site.com/book";

while ($good==false){
    $barcode=strval(rand(0,99999)).strval(rand(0,99999));
    $response = array("event_id" => $event_id, "event_date" => $event_date, 
    "ticket_adult_price" => $ticket_adult_price, "ticket_adult_quantity" => $ticket_adult_quantity,
    "ticket_kid_price" => $ticket_kid_price, "ticket_kid_quantity" => $ticket_kid_quantity, "barcode" => $barcode);
$response=send($url,$data);
    if ($response['error'!=null]) $good=true;
}

$url="https://api.site.com/approve";
$response=send($url,$barcode);

if ($response['message'] != null){

    $mysqli = new mysqli('127.0.0.1', 'root','', 'nevatrip'); 
 if ($mysqli->connect_error) {
     error_log('Ошибка при подключении: ' . $mysqli->connect_error); 
 return;
 }; 
 
 $sum= $ticket_adult_price*$ticket_adult_quantity+$ticket_kid_price*$ticket_kid_quantity;
 $today = date("Y-m-d H:i:s");
 
 $s =$mysqli->prepare("INSERT INTO  `orders`('event_id','event_date','ticket_adult_price','ticket_adult_quantity',
 'ticket_kid_price','ticket_kid_quantity','barcode','equal_price','created') VALUES (?,?,?,?,?,?,?,?,?)");  //запрос БД
 
 $s->execute([$event_id,$event_date,$ticket_adult_price,$ticket_adult_quantity,
 $ticket_kid_price,$ticket_kid_quantity,$barcode,$sum,$today]);
 }

}
?>