<?php
require_once '../private/initialize.php';

use Library\Database\Database as DB;
use Library\Email\Email;

$username = \NULL;
$success = "Contact Form";
$token = $_SESSION['token'];
$db = DB::getInstance();
$pdo = $db->getConnection();

/*
 * Fallback if user disables Javascript
 */
$submit = filter_input(INPUT_POST, 'submit', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
if (isset($submit) && $submit === 'submit') {
    $token = filter_input(INPUT_POST, 'token', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
    if (!empty($token)) {
        if (hash_equals($_SESSION['token'], $token)) {
            /* The Following to get response back from Google recaptcha */
            $url = "https://www.google.com/recaptcha/api/siteverify";

            $remoteServer = filter_input(INPUT_SERVER, 'REMOTE_ADDR', FILTER_SANITIZE_URL);
            $response = file_get_contents($url . "?secret=" . PRIVATE_KEY . "&response=" . \htmlspecialchars($_POST['g-recaptcha-response']) . "&remoteip=" . $remoteServer);
            $recaptcha_data = json_decode($response);
            /* The actual check of the recaptcha */
            if (isset($recaptcha_data->success) && $recaptcha_data->success === TRUE) {
                $success = "Mail was sent!";
                $data['name'] = filter_input(INPUT_POST, 'name', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
                $data['email'] = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);
                $data['phone'] = filter_input(INPUT_POST, 'phone', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
                $data['website'] = filter_input(INPUT_POST, 'website', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
                $data['reason'] = filter_input(INPUT_POST, 'reason', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
                $data['comments'] = filter_input(INPUT_POST, 'comments', FILTER_SANITIZE_FULL_SPECIAL_CHARS);

                $send = new Email($data);
            } else {
                $success = "You're not a human!"; // Not on a production server:
            }
        } else {
            // Log this as a warning and keep an eye on these attempts
        }
    }
}
$server_name = filter_input(INPUT_SERVER, 'SERVER_NAME', FILTER_SANITIZE_URL);
?>
<!DOCTYPE html>

<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="initial-scale=1.0, width=device-width" />
        <title>Contact Page</title>
        <link rel="shortcut icon" href="favicon.ico" >


        <script src='https://www.google.com/recaptcha/api.js'></script>

        <?php if ($pageName === 'triviaGame.php') { ?>
            <link href="https://fonts.googleapis.com/css?family=Teko:400,700&display=swap" rel="stylesheet">
        <?php } ?>
        <link rel="stylesheet" href="assets/css/quizStyling.css">
        <link rel="stylesheet" href="assets/css/countdown.css">

    </head>
    <body>
        <div id="pictureBox" class="shade">

            <div id="picture">
                <div class="play">
                    <button class="controls" id="pause">Play</button>
                </div>



                <img id="pictureELE" src="assets/large/img-photos-1554677976.jpg" alt="Big Screen Picture">


                <div class="exifInfo">
                    <p id="exifData"></p>
                </div>  
                <div class="exitBtn">
                    <a id="exitBtn" class="btn" href="#">&#8592; Exit</a>
                </div>
            </div>
            <div class="prevSlide">
                <a id="preSlide" href="#">&#8592; Prev</a>
            </div>
            <div class="nextSlide">
                <a id="nextSlide" href="#">Next &#8594;</a>  
            </div>

        </div>

        <div id="page">

            <header>
                <a class="logo" title="Miniature Photographer Logo" href="index.php"><span>Miniature Photographer Logo</span></a>
                <div class="intro">
                    <h1>The Miniature Photographer</h1>
                    <a class="btn menuExit" title="My LinkedIn Page" href="https://www.linkedin.com/in/johnpepp/">LinkedIn Page</a>
                </div>
            </header>
            <section class="main">


                <form id="contact" name="contact" action="contact.php" method="post"  autocomplete="on">
                    <div id="message">
                        <h2 id="notice">Form Notification</h2>
                        <a  id="messageSuccess" href="index.php" title="Home Page">Home</a>
                    </div>
                    <fieldset>
                        <legend>Contact Form</legend>
                        <input id="token" type="hidden" name="token" value="<?= $_SESSION['token']; ?>">
                        <label class="labelstyle" for="name" accesskey="U">Name</label>
                        <input name="name" type="text" id="name" tabindex="1" autofocus required="required" />

                        <label class="labelstyle" for="email" accesskey="E">Email</label>
                        <input name="email" type="email" id="email" tabindex="2" required="required" />

                        <label class="labelstyle" for="phone" accesskey="P" >Phone <small>(optional)</small></label>
                        <input name="phone" type="tel" id="phone" tabindex="3">

                        <label class="labelstyle" for="web" accesskey="W">Website <small>(optional)</small></label>
                        <input name="website" type="text"  id="web" tabindex="4">

                        <div id="radio-toolbar">
                            <input type="radio" id="radioMessage" name="reason" value="message" checked>
                            <label for="radioMessage">message</label>

                            <input type="radio" id="radioOrder" name="reason" value="order">
                            <label for="radioOrder">order</label>

                            <input type="radio" id="radioStatus" name="reason" value="status">
                            <label for="radioStatus">status</label> 
                        </div>
                        <p>&nbsp;</p>
                        <label class="textareaLabel" for="comments">Comments Length:<span id="length"></span></label>
                        <textarea name="comments" id="comments" spellcheck="true" tabindex="6" required="required"></textarea> 
                        <?php if (filter_input(INPUT_SERVER, 'SERVER_NAME', FILTER_SANITIZE_URL) == "localhost") { ?>
                            <div id="recaptcha" class="g-recaptcha" data-sitekey="6LcR8OQUAAAAAG1qLKJal22tLlpW4loJ7CIcfrlX" data-callback="correctCaptcha"></div>

                        <?php } else { ?>
                            <!-- Use a data callback function that Google provides -->
                            <div id="recaptcha" class="g-recaptcha" data-sitekey="6LdXNpAUAAAAAMwtslAEqbi9CU3sviuv2imYbQfe" data-callback="correctCaptcha"></div>
                        <?php } ?>
                        <input id="submitForm" type="submit" name="submit" value="submit" tabindex="7" data-response="">
                    </fieldset>
                </form>

            </section>

            <nav>
                <ul>
                    <li>
                        <a class="menuExit"  title="Home Page" href="index.php">Home</a>



                    </li>
                    <li>
                        <a class="menuExit"  title="Blog Page" href="blog.php"aria-haspopup='true'>Wild Side</a>
                        <ul>
                            <li>
                                <a class="menuExit"  title="Gallery" href="gallery.php" aria-haspopup='true'>Gallery</a>
                                <ul>
                                    <?php
                                    if (is_logged_in()) {
                                        echo '<li>';
                                        echo '<a class="menuExit" title="Member Page" href="member_page.php">Member</a>';
                                        echo '</li>';
                                        echo '<li>';
                                        echo '<a id="maintenance" class="menuExit" title="Trivia Maintenance" href="trivMain.php">Maintenance</a>';
                                        echo '</li>';
                                        echo '<li>';
                                        echo '<a id="editPage" class="menuExit" title="Edit Page" href="editTrivia.php">Edit</a>';
                                        echo '</li>';
                                    }
                                    ?>
                                    <li>
                                        <?php echo (is_logged_in()) ? '<a class="menuExit" title="Logout" href="logout.php">Logout</a>' : '<a class="menuExit"  title="Login Page" href="login.php">Login</a>'; ?>
                                        <?php echo (is_logged_in()) ? '<a class="menuExit" title="Facebook Logout" href="FacebookLogout.php">Logout</a>' : '<a class="menuExit"  title="Facebook Login" href="facebookLogin.php">FB Login</a>'; ?>
                                    </li>

                                </ul>
                            </li>
                        </ul>
                    </li>
                    <li>
                        <a class="menuExit"  title="About Me" href="about.php">About</a>
                    </li>
                    <li>
                        <a class="menuExit"  title="Contact Page" href="contact.php">Contact</a>
                    </li>
                    <li>
                        <a id="photography" class="menuExit" data-category="movie" title="Trivia Game" href="game.php">Photography Trivia</a>
                    </li>
                    <li>
                        <a class="menuExit"  title="Astronomy Picture of the Day" href="nasa.php">APOD</a>
                    </li>
                </ul>
            </nav><!-- End of Navigation -->
            <footer>
                &copy; The Miniature Photographer
                <div class="content">
                    <a class="menuExit" title="Facebook Miniature Photographer" href="https://www.facebook.com/Pepster64/">Facebook Miniature Photographer Page</a>
                    <!--        <a title="Terms of Service" href="#">Terms of Service</a>-->
                </div>
            </footer>
        </div>

        <script src="assets/js/contact.js"></script>
        <!-- Fetch the g-response using a callback function -->
        <script>
            var correctCaptcha = function (response) {
                document.querySelector('#submitForm').setAttribute('data-response', response);
            };
        </script>
    </body>
</html>