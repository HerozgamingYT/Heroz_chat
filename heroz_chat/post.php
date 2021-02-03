<?
session_start();
if(isset($_SESSION['name'])){
    $text = $_POST['text'];
     
    $fp = fopen("log.html", 'a');
    fwrite($fp, "<div class='msgln'>(<i>".date("d/m/y H:i:s")."</i>
    ) <b>".$_SESSION['name']."</b>: ".stripslashes(htmlspecialchars($text))."<br></div>");
    fclose($fp);
}
?>