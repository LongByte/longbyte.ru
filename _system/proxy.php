<?
if (empty($_REQUEST['url'])) {
    $_REQUEST['url'] = $argv[1];
}
ob_start();
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, urldecode($_REQUEST['url']));
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HEADER, false);
curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);

//устанавливаем реферер
curl_setopt($ch, CURLOPT_REFERER, 'http://yandex.ru');

//шлем заголовки
$headers = array();
$headers[] = 'GET http:' . urldecode($_REQUEST['url']);
$headers[] = 'Accept: image/gif, image/jpeg, image/pjpeg, image/pjpeg, application/x-shockwave-flash, application/x-ms-application, application/x-ms-xbap, 
application/vnd.ms-xpsdocument, application/xaml+xml, application/vnd.ms-excel, application/vnd.ms-powerpoint, application/msword, */*';
$headers[] = 'Accept-Language: ru';
$headers[] = 'User-Agent: Mozilla/4.0 (compatible; MSIE 8.0; Windows NT 5.1; Trident/4.0)';
$headers[] = 'Accept-Encoding: gzip, deflate';
$headers[] = 'Connection: Keep-Alive';
$headers[] = 'Host: yell.ru';
curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

$htmlProduct = curl_exec($ch);
if ($htmlProduct === false) {
    $htmlProduct = 'Error: ' . curl_error($ch);
}
curl_close($ch);
ob_end_clean();
echo $htmlProduct;
?>