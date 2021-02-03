<!--Pour le "Vider le chat" il faut récréer un fichier "log.html" ou le serveur affiche une erreur 404 dans la "chatbox"...><-->
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "https://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<title>Heroz Chat</title>
<link type="text/css" rel="stylesheet" href="style.css" />
</head>
<?
session_start();
 
function loginForm(){
    echo'
    <div id="loginform">
    <form action="index.php" method="post">
        <p>Entrez votre nom avant de continuer :</p>
        <label for="name">Nom :</label>
        <input type="text" name="name" id="name" />
        <input type="submit" name="enter" style="font-weight: bold;" id="enter" value="Entrer" />
    </form>
    </div>
    ';
}
 
if(isset($_POST['enter'])){
    if($_POST['name'] != ""){
        $_SESSION['name'] = stripslashes(htmlspecialchars($_POST['name']));
        $fp = fopen("log.html", 'a');
        fwrite($fp, "<div class='msgln'><i>". $_SESSION['name'] ." s'est connecté(e).</i><br></div>");
        fclose($fp);
    }
    else{
        echo '<span class="error">Erreur, précisez un nom !</span>';
    }
}
?>
<?php
if(!isset($_SESSION['name'])){
    loginForm();
}
else{
?>
<div id="wrapper">
    <div id="menu">
        <p class="welcome">Connecté en tant que : <b><?php echo $_SESSION['name']; ?></b> !</p>
        <p class="clear"><a id="clear" href="#">Vider le chat</a></p>
        <br>
        <p class="logout"><a id="exit" href="#">Se déconnecter</a></p>
        <div style="clear:both"></div>
    </div>    
    <div id="chatbox">
    <?php
    if(file_exists("log.html") && filesize("log.html") > 0){
        $handle = fopen("log.html", "r");
        $contents = fread($handle, filesize("log.html"));
        fclose($handle);
     
        echo $contents;
    }
    ?>
</div>
    <form name="message" action="">
        <input name="usermsg" type="text" id="usermsg" size="63" />
        <input name="submitmsg" type="submit" style="font-weight: bold;"  id="submitmsg" value="Envoyer" />
    </form>
    <p>Pour envoyer une message vous pouvez aussi appuyer sur "Entrée" !</p>
</div>
<script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/1.3/jquery.min.js"></script>
<script type="text/javascript">
// jQuery Document
$(document).ready(function(){
	//If user wants to end session
	$("#exit").click(function(){
		var exit = confirm("Êtes vous sûr de vouloir vous déconnecter ?");
		if(exit==true){window.location = 'index.php?logout=true';}
	});
    //If user wants to clear the chat
    $("#clear").click(function(){
        var clear = confirm("Êtes vous sûr de vouloir supprimer tout l'historique du chat (définitif) ?");
        if(clear==true){window.location = 'index.php?clear=true';}
    })
});
//If user submits the form
	$("#submitmsg").click(function(){	
		var clientmsg = $("#usermsg").val();
        if(!clientmsg == "") {
            $.post("post.php", {text: clientmsg});				
		    $("#usermsg").attr("value", "");
		    return false;
        }
	});
//Load the file containing the chat log
	function loadLog(){		
		var oldscrollHeight = $("#chatbox").attr("scrollHeight") - 20; //Scroll height before the request
		$.ajax({
			url: "log.html",
			cache: false,
			success: function(html){		
				$("#chatbox").html(html); //Insert chat log into the #chatbox div	
				
				//Auto-scroll			
				var newscrollHeight = $("#chatbox").attr("scrollHeight") - 20; //Scroll height after the request
				if(newscrollHeight > oldscrollHeight){
					$("#chatbox").animate({ scrollTop: newscrollHeight }, 'normal'); //Autoscroll to bottom of div
				}				
		  	},
		});
	}
setInterval (loadLog, 1000);
</script>
<?php
}
?>
<?php
if(isset($_GET['logout'])){
    $fp = fopen("log.html", 'a');
    fwrite($fp, "<div class='msgln'><i>". $_SESSION['name'] ." s'est déconnecté(e).</i><br></div>");
    fclose($fp);
    session_destroy();
    header("Location: index.php");
}
if(isset($_GET['clear'])){
    unlink('log.html');
    $fp = fopen("log.html", 'a');
    fwrite($fp, "<div class='msgln'><b>Système : </b>".stripslashes(htmlspecialchars("Une personne a vidé le tchat..."))."<br></div>");
    fclose($fp);
    header("Location: index.php");
}
?>
</body>
</html>