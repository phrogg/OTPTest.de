<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8"> <!-- TODO: Better meta tags -->
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>OTPTest.de</title>
<link href="https://unpkg.com/primer/build/build.css" rel="stylesheet">
<link rel="icon" href="favicon.ico" type="image/gif" sizes="256x256">
<style>
    html,
    body {
        padding-top:10px;
        text-align: center;
        align: center;
    }
    input[type=text],input[type=submit] {
        border-radius: 4px;
        text-align: center;
    }
</style>
<script>
// pre code block

// Remove the cookie with the oldSecret
function del_cookie() { //src: https://stackoverflow.com/a/57644049/5638397
    document.cookie = 'secret=; Max-Age=0; path=/; domain=' + location.host;
    window.location = window.location.href.split("?")[0]; // when changing the secret, don't check the last otp
    //location.reload();
}

// Copy OTP and Secret to Clipboard
function copy2Clip(objectId) {
    var copyText = document.getElementById(objectId);
    copyText.select(); 
    copyText.setSelectionRange(0, 99999);
    document.execCommand("copy");
}

// progressbar update
function prgUpdate() { 
    var element = document.getElementById("prgBar");
    var width = 1; 
    var identity = setInterval(scene, 1000); 
    function scene() {
        if (width >= 30) { 
            location.reload();
            clearInterval(identity); 
        } else { 
            width++;  
            element.value = width;  
        } 
    } 
}
</script>
</head>
<?php
$emojiSize = 15;

require_once('vendor/autoload.php');
use OTPHP\TOTP;
use chillerlan\QRCode\QRCode;

$otp = TOTP::create();
$oldSecret = false;

if(!isset($_COOKIE["secret"])) {
    $secret = substr($otp->getSecret(),96);
    //$secret = $otp->getSecret();
} else {
    $secret = $_COOKIE["secret"];
    $oldSecret = true;
}
$otp = TOTP::create($secret);
setcookie("secret", $secret, time() + (86400 * 30), "/");

$otp->setLabel('OTPTest.de');

?>
    <body>
    <h1>OTPTest.de</h1>
    <div class="Box Box--spacious col-6 mx-auto text-center" style="margin-top: 20px;">
    <div class="Box-body" style="padding-top: 0px;">
    <!-- <progress id="prgBar" value="0" max="30" style="width: 30%;">Test</progress> TODO: Fix time -->
    <h3 class="f1-light">
    <font size=4>Token:</font><br>
    <input type="text" value="<?php echo $otp->now();?>" id="clip1" size=3 style="outline: none; border: none; width:auto;"><button type="button" onclick="copy2Clip('clip1');" class="text-gray right-0 top-0 m-2 js-zeroclipboard btn btn-sm zeroclipboard-button tooltipped tooltipped-s" aria-label="Copy" data-copied-hint="Copied!">
    <svg class="octicon octicon-clippy js-icon" viewBox="0 0 14 16" version="1.1" width="14" height="16" aria-hidden="true"><path fill-rule="evenodd" d="M2 13h4v1H2v-1zm5-6H2v1h5V7zm2 3V8l-3 3 3 3v-2h5v-2H9zM4.5 9H2v1h2.5V9zM2 12h2.5v-1H2v1zm9 1h1v2c-.02.28-.11.52-.3.7-.19.18-.42.28-.7.3H1c-.55 0-1-.45-1-1V4c0-.55.45-1 1-1h3c0-1.11.89-2 2-2 1.11 0 2 .89 2 2h3c.55 0 1 .45 1 1v5h-1V6H1v9h10v-2zM2 5h8c0-.55-.45-1-1-1H8c-.55 0-1-.45-1-1s-.45-1-1-1-1 .45-1 1-.45 1-1 1H3c-.55 0-1 .45-1 1z"></path></svg>
    </button>
    </h3>
    <dl class="form-group mb-4">
    <?php
    echo "<dt><label>".'<img src="'.(new QRCode)->render($otp->getProvisioningUri()).'" alt="QR Code" /><br>'."</label></dt>";
    ?>
    <dt><label>
    <font size=2>Secret:</font><br>
    <input type="text" value="<?php echo $otp->getSecret();?>" id="clip2" size=6 style="outline: none; border: none; width:auto;"><button type="button" onclick="copy2Clip('clip2');" class="text-gray right-0 top-0 m-2 js-zeroclipboard btn btn-sm zeroclipboard-button tooltipped tooltipped-s" aria-label="Copy" data-copied-hint="Copied!">
    <svg class="octicon octicon-clippy js-icon" viewBox="0 0 14 16" version="1.1" width="14" height="16" aria-hidden="true"><path fill-rule="evenodd" d="M2 13h4v1H2v-1zm5-6H2v1h5V7zm2 3V8l-3 3 3 3v-2h5v-2H9zM4.5 9H2v1h2.5V9zM2 12h2.5v-1H2v1zm9 1h1v2c-.02.28-.11.52-.3.7-.19.18-.42.28-.7.3H1c-.55 0-1-.45-1-1V4c0-.55.45-1 1-1h3c0-1.11.89-2 2-2 1.11 0 2 .89 2 2h3c.55 0 1 .45 1 1v5h-1V6H1v9h10v-2zM2 5h8c0-.55-.45-1-1-1H8c-.55 0-1-.45-1-1s-.45-1-1-1-1 .45-1 1-.45 1-1 1H3c-.55 0-1 .45-1 1z"></path></svg>
    </button>
    </label></dt>
    </dl>
    <?php if($oldSecret) { ?>
        <button class="btn btn-primary btn-block" onclick='del_cookie()'>
        Create new Secret
        </button>
    <?php } ?>
        For your convenience we created a cookie that lasts one day.
        </div>
        <div class="Box-body">
            <h3 class="f1-light">Verify a token</h3>
            <form action="" method="GET">
                <input type="text" minlength="6" maxlength="6" id="otp" size="5" name="otp" placeholder="<?php echo $_GET['otp']; ?>" required/>
                <input type="submit" value="Verify"/>
            </form>
            <?php
            if(isset($_GET['otp'])) {
                if($otp->verify($_GET['otp']) == 1) {
                    ?>
                    <script>document.getElementById("otp").style.border = "2px solid green"</script>
                    <h2 style='color: green;'>This token is valid!</h2>
                    <?php
                } else {
                    ?>
                    <script>document.getElementById("otp").style.border = "2px solid red"</script>
                    <h2 style='color: red;'>This token is invalid!</h2>
                    <?php
                }
            }
            ?>
        </div>
        <div class="Box-body">
            <img src="https://img.shields.io/github/release/phrogg/OTPTest.de.svg?logo=github" />
            <img src="https://img.shields.io/github/license/phrogg/OTPTest.de.svg" />
            <img src="https://img.shields.io/github/issues/phrogg/OTPTest.de.svg" />
            <br>Based on:<br>
            <a href="https://github.com/chillerlan/php-qrcode" target="_BLANK"><img src="https://img.shields.io/badge/chillerlan-QRCode-blue.svg" /></a>
            <a href="https://github.com/Spomky-Labs/otphp/" target="_BLANK"><img src="https://img.shields.io/badge/SpomkyLabs-otphp-orange.svg" /></a><br>
            <a href="https://www.iconfinder.com/icons/1930259/green_hole_key_keyhole_icon" target="_BLANK"><img src="https://img.shields.io/badge/Keyhole-favicon-green.svg" /></a>
        </div>
        </div>
        <br><img class="emoji" alt="wrench" height="<?php echo $emojiSize; ?>" width="<?php echo $emojiSize; ?>" src="https://github.githubassets.com/images/icons/emoji/unicode/1f527.png"> with <img class="emoji" alt="heart" height="<?php echo $emojiSize; ?>" width="<?php echo $emojiSize; ?>" src="https://github.githubassets.com/images/icons/emoji/unicode/2764.png"> in <img class="emoji" alt="de" height="<?php echo $emojiSize; ?>" width="<?php echo $emojiSize; ?>" src="https://github.githubassets.com/images/icons/emoji/unicode/1f1e9-1f1ea.png">
        <br><br><a href="https://paypal.me/proggenbuck" target="_BLANK"><img src="https://img.shields.io/badge/Donate-Here-green.svg" /></a>
        </body>
    <script>
        // post code block
        // prgUpdate(); TODO: fix time
    </script>
    </html>