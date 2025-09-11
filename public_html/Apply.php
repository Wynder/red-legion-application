<?php
session_destroy();
session_start();
include( __DIR__ . '/../lib/resources.php' );
include( __DIR__ . '/../lib/discord.php' );
include( __DIR__ . '/../lib/functions.php' );

if(!isset($_SESSION['state']))
{
	gen_state();
}

d($_SESSION);

//Logic flow for the application process.
// 1. Direct user to apply on the RSI site.
// 2. Authenticate with Discord.
//	a. Upon Discord authentication, we pull their joined 
//		servers to check against the blacklist.
// 3. Fill out application form.
// 4. Application submitted, join our Discord server.

//Trap for any error messages.
if($_GET['error'])
{
	$error = $_GET['error'];
}

//If it's the first visit to the page, start with step 1.
if(!$_SESSION['Application']['Step'])
{
	$_SESSION['Application']['Step'] = 1;
}
else
{
	//If there's a value in Application/Step, check to see
	//if they're onto the next step (ns).
	if($_POST['ns'])
	{
		$_SESSION['Application']['Step'] = $_POST['ns'];
	}

	//If they're on step 2 and there's a code in the URL,
	//this is the return from Discord authentication.
	if($_SESSION['Application']['Step'] == 2 && $_GET['code'])
	{
		//This runs after authentication.
		init($apply_redirect_url, $client_id, $secret_id, $bot_token);
		get_user();
		$guilds = get_guilds();

		d($guilds);
		//Clean up the array a bit.

		

		exit;

		if($_SESSION['user']['id'] || $_SESSION['Application']['DiscordID'])
		{
			//This shouldn't happen, but it's a safeguard.
			if(!$_SESSION['Application']['DiscordID'])
			{
				$_SESSION['Application']['DiscordID'] = $_SESSION['user']['id'];
				$_SESSION['Application']['DiscordUsername'] = $_SESSION['user']['username'];
	
				if($_SESSION['user']['avatar'] != "")
				{
					$_SESSION['Application']['avatar'] = "https://cdn.discordapp.com/avatars/{$_SESSION['user']['id']}/{$_SESSION['user']['avatar']}" . is_animated($member['user']['avatar']);
				}
				else
				{
					$_SESSION['Application']['avatar'] = 'https://cdn.discordapp.com/embed/avatars/0.png';
				}
			}

			$_SESSION['Application']['Step'] = 3;
		}
		else
		{
			//Wierdness happened with Discord authentication.
			$error = '<strong>Oddness Happened.</strong> It looks like a rampant Vanduul got in the engine.  Try that once more...';
		}
	}
}

if($error)
{
	$error = "<div class='alert alert-danger'>$error</div>";
}



switch($_SESSION['Application']['Step'])
{
	case 1: 
		$tabTitle = "Step 1: RSI Site Application";
		$tabContent = "<h3>Apply on RSI's Site</h3><ol><li><strong>For the first step of the process, please visit <a href='https://robertsspaceindustries.com/orgs/REDLEGN' target='_blank'>The Red Legion's organization page</a> on the RSI website and click the \"Join Us Now!\" button.</strong></li></ol>  This will allow us to confirm your RSI ID and, once accepted, you'll be connected to the corporation in the game.  The Red Legion is not an \"Exclusive Membership\" group -- that means you are free to join other organizations and enjoy the game how you wish to enjoy it!<br>&nbsp<p>Once you've completed this step, click below to move on to the next step!</p><form method='post' action='Apply'><input type='submit' class='btn btn-lg btn-primary' value='Next Step'><input type='hidden' name='ns' value='2'></form>";
		break;
	case 2:
		$tabTitle = 'Step 2: Discord Authentication';
		$tabContent = "<h3>Authenticate with Discord</h3><ol><li value='2'><strong>Authenticate using Discord by clicking the button below.</strong></li></ol> Discord provides secure authentication services for third party applications such as ours.  We collect a minimal amount of information (primarily your Discord ID and Handle) so that we know what person matches up with what application.</p><a href='https://discordapp.com/oauth2/authorize?response_type=code&client_id=$client_id&redirect_uri=$apply_redirect_url&scope=identify+guilds&state={$_SESSION['state']}' class='btn btn-lg btn-primary'><i class='fa-brands fa-discord' style='position:relative; top:5px;'></i> Authenticate with Discord</a>";
		break;

        case 3:
                $tabTitle = 'Step 3: Submit Your Application';
		$tabContent = "<h3>Authenticate with Discord</h3><ol><li value='3'><strong>Submit Your Application.</strong></li></ol> We have most of the information we need now, just a couple additional questions and you'll be good to go! Please bear in mind that in order to apply, you must:<ul><li>Be 18 years of age.</li><li>Agree to no trolling, scamming, or trash talk.</li><li>Represent The Red Legion with professionalism and respect.</ul><p>

			<table>
<tbody>
			<tr>
			<td width='50%'>
			    <form name='Application' id='Application' method='POST' action='doApply.php'>

  				    <label for='RSIName'>RSI Handle (<em>This is the RSI account name by which people can add you</em>):</label>
				    <input type='text' name='RSIName' id='RSIName' class='form-control' placeholder='Your RSI Handle...' required>
				
				<br>

				    <label for='Timezone'>Timezone:</label>
<select name='Timezone' id='timezone-offset' class='form-control'>
        <option value='-12:00'>(GMT -12:00) Eniwetok, Kwajalein</option>
        <option value='-11:00'>(GMT -11:00) Midway Island, Samoa</option>
        <option value='-10:00'>(GMT -10:00) Hawaii</option>
        <option value='-09:50'>(GMT -9:30) Taiohae</option>
        <option value='-09:00'>(GMT -9:00) Alaska</option>
        <option value='-08:00'>(GMT -8:00) Pacific Time (US &amp; Canada)</option>
        <option value='-07:00'>(GMT -7:00) Mountain Time (US &amp; Canada)</option>
        <option value='-06:00'>(GMT -6:00) Central Time (US &amp; Canada), Mexico City</option>
        <option value='-05:00' selected='selected'>(GMT -5:00) Eastern Time (US &amp; Canada), Bogota, Lima</option>
        <option value='-04:50'>(GMT -4:30) Caracas</option>
        <option value='-04:00'>(GMT -4:00) Atlantic Time (Canada), Caracas, La Paz</option>
        <option value='-03:50'>(GMT -3:30) Newfoundland</option>
        <option value='-03:00'>(GMT -3:00) Brazil, Buenos Aires, Georgetown</option>
        <option value='-02:00'>(GMT -2:00) Mid-Atlantic</option>
        <option value='-01:00'>(GMT -1:00) Azores, Cape Verde Islands</option>
        <option value='+00:00'>(GMT) Western Europe Time, London, Lisbon, Casablanca</option>
        <option value='+01:00'>(GMT +1:00) Brussels, Copenhagen, Madrid, Paris</option>
        <option value='+02:00'>(GMT +2:00) Kaliningrad, South Africa</option>
        <option value='+03:00'>(GMT +3:00) Baghdad, Riyadh, Moscow, St. Petersburg</option>
        <option value='+03:50'>(GMT +3:30) Tehran</option>
        <option value='+04:00'>(GMT +4:00) Abu Dhabi, Muscat, Baku, Tbilisi</option>
        <option value='+04:50'>(GMT +4:30) Kabul</option>
        <option value='+05:00'>(GMT +5:00) Ekaterinburg, Islamabad, Karachi, Tashkent</option>
        <option value='+05:50'>(GMT +5:30) Bombay, Calcutta, Madras, New Delhi</option>
        <option value='+05:75'>(GMT +5:45) Kathmandu, Pokhara</option>
        <option value='+06:00'>(GMT +6:00) Almaty, Dhaka, Colombo</option>
        <option value='+06:50'>(GMT +6:30) Yangon, Mandalay</option>
        <option value='+07:00'>(GMT +7:00) Bangkok, Hanoi, Jakarta</option>
        <option value='+08:00'>(GMT +8:00) Beijing, Perth, Singapore, Hong Kong</option>
        <option value='+08:75'>(GMT +8:45) Eucla</option>
        <option value='+09:00'>(GMT +9:00) Tokyo, Seoul, Osaka, Sapporo, Yakutsk</option>
        <option value='+09:50'>(GMT +9:30) Adelaide, Darwin</option>
        <option value='+10:00'>(GMT +10:00) Eastern Australia, Guam, Vladivostok</option>
        <option value='+10:50'>(GMT +10:30) Lord Howe Island</option>
        <option value='+11:00'>(GMT +11:00) Magadan, Solomon Islands, New Caledonia</option>
        <option value='+11:50'>(GMT +11:30) Norfolk Island</option>
        <option value='+12:00'>(GMT +12:00) Auckland, Wellington, Fiji, Kamchatka</option>
        <option value='+12:75'>(GMT +12:45) Chatham Islands</option>
        <option value='+13:00'>(GMT +13:00) Apia, Nukualofa</option>
        <option value='+14:00'>(GMT +14:00) Line Islands, Tokelau</option>
</select>

				<br>


				    <label for='Division'>Division Preference (<em>This is your primary interest</em>):</label>

				    <select name='Division' class='form-control'>
					<option value='Adam and Eves'>Adam & Eves - Industry, Mining, and Logistics</option>
					<option value=\"Hell's Angels\">Hell's Angels - Protective, Privateer, and Security Services</option>
					<option value='Panda Bears'>Panda Bears - Intelligence, Exploration, and Covert Operations</option>
				    </select>
				
				<br>

				    <label for='Bio'>Biography:</label><br>
				    <textarea name='Bio' style='width:100%' rows='5' placeholder='Tell us about yourself and your interest in The Red Legion!' required></textarea>

				<br>
				
				    <input type='hidden' name='DiscordID' value='{$_SESSION['Application']['DiscordID']}'>
				    <input type='hidden' name='DiscordUsername' value='{$_SESSION['Application']['DiscordUsername']}'>
				    <input type='hidden' name='Avatar' value='{$_SESSION['Application']['avatar']}'>

				<br>

 				    <span class='g-recaptcha' data-sitekey='6Ld_OuQSAAAAAHWjy6DNJI6SjCF_muGmkgbssZRp'></span>

				<br>

                                    <input type='submit' id='send_message' value='Submit Application' class='btn btn-primary'>
			    </form>
			</td>
			<td valign='top' tdalign='center' style='padding:10px;'> 
				<center>
				<h3>Welcome, {$_SESSION['Application']['DiscordUsername']}!</h3><br>
				<img src='{$_SESSION['Application']['avatar']}'>
				</center>
			</td>
			</tr>
			</tbody>
			</table>
			";
                break;

			case 5: 
				unset($_SESSION['Application']);
				$tabTitle = 'Step 4: Join Discord';
				$tabContent = "<h3>Join our Discord</h3>Your application has been submitted and will be reviewed as soon as possible!  The final step is for you to join our Discord server and introduce yourself -- we're looking forward to meeting you!<br>&nbsp;<br><center><a href='https://discord.gg/JZxSPTt' target='_blank' class='btn btn-lg btn-primary'><i class='fa-brands fa-discord' style='position:relative; top:5px;'></i>Join The Red Legion's Discord Server</a></center>";
			break;
}

?>

<style>
a:visited {color:white}
</style>

<body id="default-page" class="dark">
    <!-- header begin -->
    <header>

    </header>
    <!-- header close -->

    <!-- subheader -->
    <section id="subheader" class="jarallax">
        <h1>Apply to <span class="id-color">The Red Legion</span></h1>
    </section>
    <!-- subheader close -->

    <!-- content begin -->
    <div id="content">
	<div class="container">

<?php echo $error; ?>
            <div class="row">
		<div class="col-md-12">

                   <div class="de_tab">
                        <ul class="de_nav">
			<li><span class="active"><?php echo $tabTitle; ?></span></li>
			</ul>

                       <div class="de_tab_content">

                            <div id="tab1">
			    	</span><?php echo $tabContent; ?>
                            </div>

                        </div>
		   </div>

                </div>

            </div>
        </div>
    </div>

<script src="https://www.google.com/recaptcha/api.js" async defer></script>

<!-- Below here makes the recaptch a required field.
  -- feel free to steal this. ;p -->
<style>
#g-recaptcha-response {
  display: block !important;
  position: absolute;
  margin: -78px 0 0 0 !important;
  width: 302px !important;
  height: 76px !important;
  z-index: -999999;
  opacity: 0;
}
</style>
<script>
	window.addEventListener('load', () => {
	  const $recaptcha = document.querySelector('#g-recaptcha-response');
	  if ($recaptcha) {
	    $recaptcha.setAttribute('required', 'required');
	  }
	})
</script>


<script src='https://www.google.com/recaptcha/api.js' async defer></script>