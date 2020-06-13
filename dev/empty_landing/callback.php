<?
if (isset($_POST["ajax"]) && $_POST["ajax"] == 1) {
    $to      = "info@localhost";
    $subject = "Отправлен заказ с сайта ". $_SERVER['SERVER_NAME'];
    $message = "ФИО: ".$_POST["name"]."<br>\n".
            "E-mail: ".$_POST["email"]."<br>\n".
            "Телефон: ".$_POST["phone"]."<br>\n";
    $headers = "From: info@localhost" . "\r\n" .
        "Content-type: text/html; charset=utf-8";

    mail($to, $subject, $message, $headers);
    die();
}

?>