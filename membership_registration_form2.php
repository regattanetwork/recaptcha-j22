<?php

####################################################################
####        mgmt_member_detail.php
####                (C) 2006 Regatta Network
####                    Authors: Matthew Niemann
####                    Created: 11/28/2006
####                    Last Updated: 11/28/2006
####
####                Update History:
####                    11/28/2006:  Created
####
####
####
########
####################################################################

define('IN_LASERMGMT', true);
include("common.php");
##ReCaptcha Code
if (isset($_POST['submitNew'])){
require_once('recaptchalib.php'); // reCAPTCHA Library
$pubkey = "6LeWyfQSAAAAAMQcObhcteBSKKG4uMxmFEyVP5FE"; // Public API Key
$privkey = "6LeWyfQSAAAAAAUduY2bk-hj3XoM_Zq6Rl1AlDGh"; // Private API Key

if ($_POST['doVerify']) {
  $verify = recaptcha_check_answer($privkey, $_SERVER['REMOTE_ADDR'], $_POST['recaptcha_challenge_field'], $_POST['recaptcha_response_field']);
  if ($verify->is_valid) {
    # Enter Success Code
    echo "Your response was correct!";
  }
  else {
    # Enter Failure Code
    echo "You did not enter the correct words.  Please try again.";
  }
}
}
##end ReCaptcha

$mydb = new database($DBhost,$DB,$DBuser,$DBpass);
$mydb->open_database();  ##open
unset($DBpass);

if (isset($HTTP_POST_VARS['address']) ) {
   $mymember = new db_entry($DB,$DBmemberTable,"MEMBER_ID");
    if ($HTTP_POST_VARS['renew_id'] ) {
      $mymember->get_entry($HTTP_POST_VARS['renew_id'], $mydb->link, "MEMBER_TOKEN");
      if ($mymember->read("YEAR_VALID") >= $SITE_REGISTERING_YEAR) {
        #$SITE_REGISTERING_YEAR = $mymember->read("YEAR_VALID") + 1;
      }
    }

    //Various Member types and charge amounts
    if ($MEMBER_TYPE_OVERRIDE_COST[$HTTP_POST_VARS['member_type']] ) {
      $amount_due = $MEMBER_TYPE_OVERRIDE_COST[$HTTP_POST_VARS['member_type']];
    }
    else {
      $amount_due = $MEMBER_TYPE_DEFAULT_COST;
    }
    if ($MEMBER_FAMILY_CHARGE) {
      $family_increase = $MEMBER_FAMILY_CHARGE * $HTTP_POST_VARS['family_member_count'];
      $amount_due += $family_increase;
    }

    $institutional_increase = 10 * $HTTP_POST_VARS['institutional_boat_count'];
    $amount_due += $institutional_increase;

    //Non Edit Fields
    if (! $HTTP_POST_VARS['edit']) {
      $mymember->write("YEAR_VALID", $SITE_REGISTERING_YEAR);
      $mymember->write("MEMBER_TYPE", $HTTP_POST_VARS['member_type']);
      $mymember->write("MEMBERSHIP_DATE_STAMP", time());
      $mymember->write("FIRST_NAME", $HTTP_POST_VARS['first_name']);
      $mymember->write("LAST_NAME", $HTTP_POST_VARS['last_name']);

      $mymember->write("AMOUNT_PAID", $amount_due);
      $mymember->write("CURRENCY_CODE", $HTTP_POST_VARS['currency_code']);
      $mymember->write("PAYMENT_STATUS", 1);
      $mymember->write("PAYMENT_DETAILS", "");
      $mymember->write("INSTITUTIONAL_BOAT_COUNT", $HTTP_POST_VARS['institutional_boat_count']);
    }

    //Account standards

    $mymember->write("ADDRESS", $HTTP_POST_VARS['address']);
    $mymember->write("CITY", $HTTP_POST_VARS['city']);
    $mymember->write("STATE", strtoupper($HTTP_POST_VARS['state']));
    $mymember->write("ZIP", $HTTP_POST_VARS['zip']);
    $mymember->write("COUNTRY", strtoupper($HTTP_POST_VARS['country']));
    $mymember->write("PHONE", $HTTP_POST_VARS['phone']);
    $mymember->write("WORK_PHONE", $HTTP_POST_VARS['work_phone']);
    $mymember->write("CELL_PHONE", $HTTP_POST_VARS['cell_phone']);
    $mymember->write("GENDER", $HTTP_POST_VARS['gender']);
    $mymember->write("EMAIL", $HTTP_POST_VARS['email']);
    $mymember->write("BIRTH_DATE", $HTTP_POST_VARS['birth_date']);
    $mymember->write("DATE_MODIFIED", date("m/d/Y",time()));







    //Custom Account Fields
    $mymember->write("DISTRICT", strtoupper($HTTP_POST_VARS['district']));
    $mymember->write("USSA", strtoupper($HTTP_POST_VARS['ussa']));
    $mymember->write("YACHT_CLUB", $HTTP_POST_VARS['yacht_club']);
    $mymember->write("BOAT_TYPE", $HTTP_POST_VARS['boat_type']);
    $mymember->write("SAIL_NUMBER", strtoupper($HTTP_POST_VARS['sail_number']));

    $mymember->write("FAMILY_MEMBER_COUNT", $HTTP_POST_VARS['family_member_count']);
    //Family Data
    for ($x=1; $x<=$MEMBER_FAMILY_MAX; $x++) {
      if ($MEMBER_TYPE_FAMILY[$HTTP_POST_VARS['member_type']] >= $x){
        $mymember->write("ADD" . $x . "FIRST", $HTTP_POST_VARS['add' . $x . 'first']);
        $mymember->write("ADD" . $x . "LAST", $HTTP_POST_VARS['add' . $x . 'last']);
        $mymember->write("ADD" . $x . "GENDER", $HTTP_POST_VARS['add' . $x . 'gender']);
        $mymember->write("ADD" . $x . "DOB", $HTTP_POST_VARS['add' . $x . 'dob']);
        $mymember->write("ADD" . $x . "USSA", $HTTP_POST_VARS['add' . $x . 'ussa']);
        $mymember->write("ADD" . $x . "EMAIL", $HTTP_POST_VARS['add' . $x . 'email']);
        $mymember->write("ADD" . $x . "PHONE", $HTTP_POST_VARS['add' . $x . 'phone']);
        $mymember->write("ADD" . $x . "BOAT", $HTTP_POST_VARS['add' . $x . 'boat']);
        $mymember->write("ADD" . $x . "SAIL", $HTTP_POST_VARS['add' . $x . 'sail']);
      }
      else {
        $mymember->write("ADD" . $x . "FIRST", "");
        $mymember->write("ADD" . $x . "LAST", "");
        $mymember->write("ADD" . $x . "GENDER", "");
        $mymember->write("ADD" . $x . "DOB", "");
        $mymember->write("ADD" . $x . "USSA", "");
        $mymember->write("ADD" . $x . "EMAIL", "");
        $mymember->write("ADD" . $x . "PHONE", "");
        $mymember->write("ADD" . $x . "BOAT", "");
        $mymember->write("ADD" . $x . "SAIL", "");
      }
    }

    //Custom Questions
    if ($CUSTOM_QUESTION_ARRAY) {
      foreach ($CUSTOM_QUESTION_ARRAY as $key => $value) {
        $mymember->write("$key", $HTTP_POST_VARS[$key]);
      }
    }

    // Variable stuff new member

    if (! $mymember->read("MEMBER_SINCE") ) {
      $mymember->write("MEMBER_SINCE", date("Y",time()));
    }
    if (! $mymember->read("CLASS_ID") ) {
      $this_member = get_object_vars($mymember);
      $mymember->write("CLASS_ID",create_class_id($this_member,$mydb->link, $DBmemberTable));
    }
    if (! $mymember->read("MEMBER_TOKEN") ) {
      $mymember->write("MEMBER_TOKEN", create_token());
    }
    ////////////////

    if ($HTTP_POST_VARS['renew_id'] ) {
      $mymember->write("MEMBER_TYPE2", "RENEWAL");
      $mymember->alter_entry($mydb->link);
      $MEMBER_ID = $mymember->read("MEMBER_ID");
    }
    else {
      $mymember->write("MEMBER_TYPE2", "NEW");
      $MEMBER_ID = $mymember->add_entry($mydb->link);
    }
    $mydb->close_database();

    if (! $HTTP_POST_VARS['edit']) {
    //ADMIN EMAILS
    $to = $SITE_ADMIN_EMAIL;
    $to2 = $SITE_HELP_EMAIL;
    $from = $HTTP_POST_VARS['email'];
    $subject = "Someone has completed an online " . $SITE_CLASS_NAME . " Membership Application";
    $message = "\n
Someone has completed an online " . $SITE_CLASS_NAME . " Membership Application\n
--------------------------------------------------------------\n
\n

Skipper: " . $HTTP_POST_VARS['first_name'] . " " . $HTTP_POST_VARS['last_name'] . "\n
Date/Time: " . date("m/d/Y G:i a", time()) . "\n
";

    mail ($to, $subject, $message, "From: ".$HTTP_POST_VARS['name']." <$from>\r\n");
    #mail ($to2, $subject, $message, "From: ".$HTTP_POST_VARS['name'] ." <$from>\r\n");
    header ("Location: " . $SITE_PAYMENT_PAGE . "?member_id=" . $MEMBER_ID . "&token=" . $mymember->read("MEMBER_TOKEN"));
    }
    else {
      header ("Location: membership_edit_finish.php");
    }
}

else {
  if (isset($HTTP_GET_VARS['renew_id'])) {
    $title = "Edit";
    $mymember = new db_entry($DB,$DBmemberTable,"MEMBER_ID");
    $mymember->get_entry($HTTP_GET_VARS['renew_id'], $mydb->link, "MEMBER_TOKEN");
    $renew_member = get_object_vars($mymember);
    $mydb->close_database();
  }
}

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml"><head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<title>Join the <?=$SITE_CLASS_NAME ?></title>
<link href="mgmt_style.css" rel="stylesheet" type="text/css" />
<link rel="stylesheet" href="cc.css" />
<script type="text/JavaScript">
<!--
function MM_openBrWindow(theURL,winName,features) { //v2.0
  window.open(theURL,winName,features);
}
//-->
</script>
<script type="text/javascript">
<!--
function validateForm () {
  var today = new Date();
  var month = today.getMonth()+1;
  var year = today.getFullYear();
  var form = document.thisForm;

  var old_value = form.Submit.value;
  form.Submit.value = 'RECORDING DATA, DO NOT CLICK TWICE!';
  form.Submit.disabled = true;

  <?php if ($RECORD_USSA == 2) { ?>
  if (form.ussa.value == '') {
    alert('Please enter a US SAILING Number.');
    form.ussa.focus();
    form.Submit.disabled = false;
    form.Submit.value = old_value;
    return false;
  }
  <?php } ?>

  <?php if ($RECORD_SAIL_NUMBER == 2) { ?>
  if (form.sail_number.value == '') {
    alert('Please enter a Sail Number.');
    form.sail_number.focus();
    form.Submit.disabled = false;
    form.Submit.value = old_value;
    return false;
  }
  <?php } ?>
  <?php if ($RECORD_YACHT_CLUB == 2) { ?>
  if (form.yacht_club.value == '') {
    alert('Please enter a Yacht Club.');
    form.yacht_club.focus();
    form.Submit.disabled = false;
    form.Submit.value = old_value;
    return false;
  }
  <?php } ?>
  <?php if ($RECORD_BOAT_TYPE == 2) { ?>
  if (form.boat_type.value == '') {
    alert('Please enter a Boat Type.');
    form.boat_type.focus();
    form.Submit.disabled = false;
    form.Submit.value = old_value;
    return false;
  }
  <?php } ?>
  <?php if ($RECORD_DISTRICT == 2) { ?>
  if (form.district.value == '') {
    alert('Please enter a <?=$DISTRICT_VERBAGE ?>.');
    form.district.focus();
    form.Submit.disabled = false;
    form.Submit.value = old_value;
    return false;
  }
  <?php } ?>
  if (form.email.value == '') {
    alert('Please enter a valid email address.');
    form.email.focus();
    form.Submit.disabled = false;
    form.Submit.value = old_value;
    return false;
  }

  if (form.phone.value == '') {
    alert('Please enter a valid phone number.');
    form.phone.focus();
    form.Submit.disabled = false;
    form.Submit.value = old_value;
    return false;
  }
  <?php if (! $HTTP_GET_VARS['edit']) {  ?>
  if (form.last_name.value == '') {
    alert('Please enter a valid last name.');
    form.last_name.focus();
    form.Submit.disabled = false;
    form.Submit.value = old_value;
    return false;
  }
  if (form.first_name.value == '') {
    alert('Please enter a valid first name.');
    form.first_name.focus();
    form.Submit.disabled = false;
    form.Submit.value = old_value;
    return false;
  }
  <?php } ?>
  if (form.address.value == '') {
    alert('Please enter a valid address.');
    form.address.focus();
    form.Submit.disabled = false;
    form.Submit.value = old_value;
    return false;
  }
  if (form.birth_date.value == '') {
    alert('Please enter a valid birth date.');
    form.birth_date.focus();
    form.Submit.disabled = false;
    form.Submit.value = old_value;
    return false;
  }
  if (form.city.value == '') {
    alert('Please enter a valid city.');
    form.city.focus();
    form.Submit.disabled = false;
    form.Submit.value = old_value;
    return false;
  }
  if (form.state.value == '') {
    alert('Please enter a valid state.');
    form.state.focus();
    form.Submit.disabled = false;
    form.Submit.value = old_value;
    return false;
  }
  if (form.zip.value == '') {
    alert('Please enter a valid zip.');
    form.zip.focus();
    form.Submit.disabled = false;
    form.Submit.value = old_value;
    return false;
  }
  if (form.member_type.value == '') {
    alert('Please select your desired membership');
    form.member_type.focus();
    form.Submit.disabled = false;
    form.Submit.value = old_value;
    return false;
  }
  if (form.FLEET_NUMBER.value == '') {
    alert('Please enter a fleet number');
    form.FLEET_NUMBER.focus();
    form.Submit.disabled = false;
    form.Submit.value = old_value;
    return false;
  }

  return true;
}
// -->
</script><script type="text/javascript">
<!--


// change submit button when form is submitted to prevent double-submits
function SendCommit() {
  var val = document.thisForm.submit.value;
  if (val == 'Sending') {
    alert('Your transaction has already been sent.\\\nPlease wait for server response.');
    return false;
  } else if (val == 'Submit') {
    alert('When you commit this transaction, it may appear as if the server is not responding.  The server will actually be processing your registration in real time; the next page cannot be sent to you until the transaction is fully processed.');
    document.thisForm.submit.value = 'Sending';
  }
  return true;
}

function checkCharge() {
  switch(document.thisForm.country.value){
    case "USA":
      document.getElementById('sHeader').firstChild.data = "Total Due: $45.00";
      document.thisForm.amount.value = '45';
      document.thisForm.currency_code.value = 'USD';
      break
    // case "CAN":
    //   document.getElementById('sHeader').firstChild.data = "Total Due: CAD $45.00";
    //   document.thisForm.amount.value = '45';
    //   document.thisForm.currency_code.value = 'CAD';
    //   break
    // case "DEU":
    //   document.getElementById('sHeader').firstChild.data = "Total Due: EUR 30.00";
    //   document.thisForm.amount.value = '30';
    //   document.thisForm.currency_code.value = 'EUR';
    //   break
    default:
      document.getElementById('sHeader').firstChild.data = "Total Due: $45.00 USD";
      document.thisForm.amount.value = '45';
      document.thisForm.currency_code.value = 'USD';
      break
  }
}


function familyDisplay() {
  fcount = document.thisForm.family_member_count.value;
  document.getElementById('family_header').style.display = (fcount > 0) ? "block" : "none";

  for (y=1;y<=<?php echo $MEMBER_FAMILY_MAX ?>;y++) {
    document.getElementById('family' + y).style.display = (fcount >= y) ? "block" : "none";
  }
}
function familyDisplay2() {
  switch (document.thisForm.member_type.value){
    <?php
    foreach ($MEMBER_TYPE_FAMILY as $type => $fcount) {
    ?>
    case '<?php echo $type ?>':
      document.getElementById('family_count').style.display = "block";
      document.getElementById('institutional_boat_count').style.display = "none";
      document.thisForm.institutional_boat_count.value = 0;
      break
    <?php
    }
    ?>

    case 'INSTITUTIONAL':
      document.getElementById('institutional_boat_count').style.display = "block";
      document.thisForm.family_member_count.value = 0;
      document.getElementById('family_count').style.display = "none";
      break

    default:
      document.getElementById('family_count').style.display = "none";
      document.getElementById('institutional_boat_count').style.display = "none";
      document.thisForm.family_member_count.value = 0;
      document.thisForm.institutional_boat_count.value = 0;
      break
  }
  familyDisplay();
}

// -->
</script>
</head>
<body >
<form method="post" name="thisForm" action="<?php echo $_SERVER["PHP_SELF"]; ?>" onsubmit="return validateForm();" >
  <table width="461" border="0" align="center">
    <tbody>
      <tr>
        <td width="451" bgcolor="#000000" ><img src="<?=$SITE_PUBLIC_HEADER?>"  /></td>
      </tr>
      <tr>
        <td  ><p class="nav_main_group"><strong>&nbsp;Register : <?php echo $SITE_REGISTERING_YEAR . " " . $SITE_CLASS_NAME;?> Membership</strong></p></td>
      </tr>
      <tr>
        <td height="20"><div align="center" class="alert_label"><?php echo $m1 ?> </div>
          <div align="center"><br />
            <span class="small_text">Note: Your browser must allow <a href="#1"><u onclick="MM_openBrWindow('cookie_info.php','cookieinfo','scrollbars=yes,width=400,height=600')">cookies</u></a> to be set in order to register. </span></div>
        </div></td>
      </tr>
      <tr bordercolor="#ffffff">
        <td bgcolor="#FFFFFF">                </td>
      </tr>
      <tr bordercolor="#ffffff">
        <td bgcolor="#FFFFFF">
<table cellspacing="1" cellpadding="1" border="0" class="wide">
<tr>
  <td colspan="2"> </td>
</tr>
<tr>
  <td colspan="2"><br /></td>
</tr>
<tr>
  <td class="nb top"  >First Name <span class=nn></span>&nbsp;</td>
  <td class="nn top" >
    <?php if (! $HTTP_GET_VARS['edit']) { ?>
    <input name="first_name" size="20" maxlength="80" onfocus="this.select();" value="<?php echo $renew_member[FIRST_NAME] ?>" />
    <?php }
    else { ?>
    <?php echo $renew_member[FIRST_NAME] ?>
    <?php } ?>
    </td>
</tr>
<tr>
  <td class="nb top" >Last Name <span class=nn></span>&nbsp;</td>
  <td class="nn top">
    <?php if (! $HTTP_GET_VARS['edit']) { ?>
    <input name="last_name" size="20" maxlength="80" onfocus="this.select();" value="<?php echo $renew_member[LAST_NAME] ?>"/>
        <?php }
    else { ?>
        <?php echo $renew_member[LAST_NAME] ?>
    <?php } ?>
    </td>
</tr>
<tr>
  <td class="nb top" >Address</td>
  <td class="nn top"><input name="address" id="address" onfocus="this.select();" value="<?php echo $renew_member[ADDRESS] ?>" size="40"/></td>
</tr>
<tr>
  <td class="nb top" >City</td>
  <td class="nn top"><input id="city" name="city" onfocus="this.select();" value="<?php echo $renew_member[CITY] ?>"/></td>
</tr>
<tr>
  <td class="nb" >State/Province</td>
  <td class="nn"><span class="nn top">
    <input id="state" name="state" onfocus="this.select();" value="<?php echo $renew_member[STATE] ?>"/>
  </span></td>
</tr>
<tr>
  <td class="nb top" >Zip/Postal Code</td>
  <td class="nn top"><input id="zip" name="zip" onfocus="this.select();" value="<?php echo $renew_member[ZIP] ?>"/></td>
</tr>
<tr>
  <td class="nb" >Country</td>
  <td class="nn"><select name="country" class="form_input" id="select" >
    <?php
    foreach($COUNTRY_ARRAY as $ccode => $country_name) {
    ?>
    <option value="<?=$ccode ?>" <?php if ($renew_member[COUNTRY] == $ccode) echo "selected"; ?>>
    <?=$country_name ?>
    </option>
    <?php
    }
    ?>
  </select>
  </td>
</tr>
<tr><td colspan="2"><br /></td></tr>
<tr>
  <td class="nb top" >Phone Number&nbsp;</td>
  <td class="nn top">
    <input name="phone" size="20" maxlength="20" onfocus="this.select();" value="<?php echo $renew_member[PHONE] ?>"/>  </td>
</tr>
<tr>
  <td class="nb top" >Work  Number&nbsp;</td>
  <td class="nn top"><input name="work_phone" id="work_phone" onfocus="this.select();" value="<?php echo $renew_member[WORK_PHONE] ?>" size="20" maxlength="20"/>  </td>
</tr>
<tr>
  <td class="nb top" >Cell  Number&nbsp;</td>
  <td class="nn top"><input name="cell_phone" id="cell_phone" onfocus="this.select();" value="<?php echo $renew_member[CELL_PHONE] ?>" size="20" maxlength="20"/>  </td>
</tr>
<tr>
  <td class="nb top" >E-mail Address&nbsp;</td>
  <td class="nn top" width="100%">
    <input name="email" size="40" maxlength="80" onfocus="this.select();" value="<?php echo $renew_member[EMAIL] ?>"/>  </td>
</tr>
<tr><td colspan="2"><br /></td></tr><tr><td width="48%"  class="nb top">Birth Date <span class=nn>(mm/dd/yyyy)</span></td><td width="52%" class="nn top"><input id="birth_date" name="birth_date" onfocus="this.select();" value="<?php echo $renew_member[BIRTH_DATE] ?>"/></td></tr>
<tr><td class="nb top" >Gender</td><td class="nn top">
<input type="radio" name="gender" value="M" <?php if ($renew_member[GENDER] == 'M') echo "checked" ?>>
Male
<input type="radio" name="gender" value="F" <?php if ($renew_member[GENDER] == 'F') echo "checked" ?>>
Female</td></tr>
<?php if ($RECORD_USSA) { ?>
<tr>
  <td class="nb top" >US SAILING Number <?php if ($RECORD_USSA==2) { echo "<font size=3>*</font>"; } ?>&nbsp;</td>
  <td class="nn top"><input name="ussa" id="ussa" onfocus="this.select();" value="<?php echo $renew_member[USSA] ?>" size="20" maxlength="20"/>  </td>
</tr>
<?php } ?>
<?php if ($RECORD_YACHT_CLUB) { ?>
<tr>
  <td class="nb top" >Yacht Club <?php if ($RECORD_YACHT_CLUB==2) { echo "<font size=3>*</font>"; } ?></td>
  <td class="nn top">
      <?php
      if ($YACHT_CLUB_ARRAY) {
        ?>
        <select name="yacht_club"  id="select">
            <?php form_options_print($YACHT_CLUB_ARRAY, $renew_member[YACHT_CLUB]); ?>
          </select>
        <?php
      }
      else {
      ?>
        <input name="yacht_club" type="text"  size="30" value="<?php echo $renew_member[YACHT_CLUB] ?>"/>
      <?php
      }
      ?>
  </td>
</tr>
<?php } ?>
<?php if ($RECORD_DISTRICT) { ?>
<tr>
  <td class="nb top" ><?=$DISTRICT_VERBAGE?> <?php if ($RECORD_DISTRICT==2) { echo "<font size=3>*</font>"; } ?></td>
  <td class="nn top"><strong><font size="2" face="Verdana, Arial, Helvetica, sans-serif">
      <?php
      if ($DISTRICT_ARRAY) {
        ?>
        <select name="district"  id="select">
            <?php form_options_print($DISTRICT_ARRAY, $renew_member[DISTRICT]); ?>
          </select>
        <?php
      }
      else {
      ?>
        <input name="district" type="text"  size="30" value="<?php echo $renew_member[DISTRICT] ?>"/>
      <?php
      }
      ?>
  </font></strong></td>
</tr>
<?php } ?>
<?php if ($RECORD_BOAT_TYPE) { ?>
<tr>
  <td class="nb top" >Boat Type <?php if ($RECORD_BOAT_TYPE==2) { echo "<font size=3>*</font>"; } ?></td>
  <td class="nn top">
     <?php
        if ($BOAT_TYPE_ARRAY) {
          ?>
          <select name="boat_type"  id="select">
              <?php form_options_print($BOAT_TYPE_ARRAY, $renew_member[BOAT_TYPE]); ?>
            </select>
          <?php
        }
        else {
        ?>
          <input name="boat_type" type="text" id="form_input" size="30" value="<?php echo $renew_member[BOAT_TYPE] ?>"/>
        <?php
        }
      ?>
  </td>
</tr>
<?php } ?>
<?php if ($RECORD_SAIL_NUMBER) { ?>
<tr><td class="nb top" >Sail Number <?php if ($RECORD_SAIL_NUMBER==2) { echo "<font size=3>*</font>"; } ?></td><td class="nn top"><input id="sail_number" name="sail_number" onfocus="this.select();" value="<?php echo $renew_member[SAIL_NUMBER] ?>" />
    <br />
    <font size="1" face="Verdana, Arial, Helvetica, sans-serif"></font></td>
</tr>
<?php } ?>



<?php

    if ($CUSTOM_QUESTION_ARRAY) {
      foreach ($CUSTOM_QUESTION_ARRAY as $key => $value) {
        //$CUSTOM_QUESTION_ARRAY["BOAT_STATUS"]["TITLE"] = "I am:";
        //$CUSTOM_QUESTION_ARRAY["BOAT_STATUS"]["CHOICE_ARRAY"] = array('Owner'=>'Owner','Co-Owner'=>'Co-Owner','Helmsman Only'=>'Helmsman Only','Other'=>'Other');
?>
<tr>
  <td class="nb top" ><?php echo $CUSTOM_QUESTION_ARRAY[$key]["TITLE"] ?></td>
  <td class="nn top">
      <?php
      if ($CUSTOM_QUESTION_ARRAY[$key]["CHOICE_ARRAY"]) {
      ?>
        <select name="<?php echo $key ?>"  id="select">
            <?php form_options_print($CUSTOM_QUESTION_ARRAY[$key]["CHOICE_ARRAY"], $renew_member[$key]); ?>
          </select>
      <?php
      }
      else {
      ?>
        <input name="<?php echo $key ?>" type="text"  size="30" value="<?php echo $renew_member[$key] ?>"/>
      <?php
        if ($key == 'FLEET_NUMBER') {
          ?>
          <font size=1><a href="http://www.j22.com/fleets.html" target="_new">Click Here To View Fleets</a></font>
          <?php
        }
      }
      ?>
  </td>
</tr>
<?php
    }
}
?>


<tr>
<td colspan="2"><br /></td></tr>
</table>

                <p class="nav_main_group">&nbsp;&nbsp;<font color="#FFFFFF">Select Your Desired Membership Type </font></p>
                <table width="83%" border="0" align="center" cellpadding="0" cellspacing="0">
                  <tr>
                    <td><strong>Membership Benefits:</strong>
                    <br />
                      <br />
                      <?=$MEMBERSHIP_BENEFITS?>
                    <br />
                    </td>
                  </tr>
                </table>
                <br />
                <table cellspacing="1" cellpadding="1" border="0" class="wide">
<tr>
  <td  class="nb"><h5 id='sHeader' >&nbsp;&nbsp;&nbsp;Membership Type :
    <?php if (! $HTTP_GET_VARS['edit']) { ?>
    <select name="member_type" onChange="familyDisplay2()">
      <option selected="selected">Please Select</option>
      <?php
      foreach ($MEMBER_TYPE_ARRAY as $value => $text) {
        echo '<option value="' . $value . '" ';
        echo $renew_member[MEMBER_TYPE] == $value ? "selected" : "";
        echo '>' . $text . ' - $';
        echo $MEMBER_TYPE_OVERRIDE_COST[$value] ? $MEMBER_TYPE_OVERRIDE_COST[$value] : $MEMBER_TYPE_DEFAULT_COST;
        echo '</option>';
      }
      ?>
    </select>
    <?php
    }
    else { ?>
      <input value="<?php echo $renew_member[MEMBER_TYPE] ?>" type=hidden name="member_type"><?php echo $renew_member[MEMBER_TYPE]; ?>
    <?php } ?>
  </h5>
  <input name="currency_code" value="USD" type=hidden />


  <div id="family_count" style="DISPLAY: block">
  <table width="100%" border="0" cellspacing="0" cellpadding="0">
    <tr>
      <td>&nbsp;&nbsp;&nbsp;Family Members:
        <?php if (! $HTTP_GET_VARS['edit']) { ?>
        <select name="family_member_count" id="family_member_count" onchange="familyDisplay2()">
          <?php
          for ($xx=0; $xx <= $MEMBER_FAMILY_MAX; $xx++) {
          ?>
          <option value="<?php echo $xx ?>"><?php echo $xx ?></option>
          <?php } ?>
        </select>    <?php if ($MEMBER_FAMILY_CHARGE) { echo "$" . $MEMBER_FAMILY_CHARGE . " per additional member"; }?>
        <?php
        }
        else { ?>
          <input value="<?php echo $renew_member[FAMILY_MEMBER_COUNT] ?>" type=hidden name="family_member_count"><?php echo $renew_member[FAMILY_MEMBER_COUNT]; ?>
        <?php } ?>
        </td>
    </tr>
  </table>
  </div>

  <div id="institutional_boat_count" style="DISPLAY: block">
  <table width="100%" border="0" cellspacing="0" cellpadding="0">
    <tr>
      <td>&nbsp;&nbsp;&nbsp;Additional Boats:
                <select name="institutional_boat_count" id="institutional_boat_count" >
                    <option value="0">0</option>
                    <option value="1">1</option>
                    <option value="2">2</option>
                    <option value="3">3</option>
                    <option value="4">4</option>
                    <option value="5">5</option>
                    <option value="6">6</option>
                    <option value="7">7</option>
                    <option value="8">8</option>
                    <option value="9">9</option>
                    <option value="10">10</option>
                    <option value="11">11</option>
                    <option value="12">12</option>
                    <option value="13">13</option>
                    <option value="14">14</option>
                    <option value="15">15</option>
                  </select>    $10 per additional boat                </td>
    </tr>
  </table>
  </div>

  <br />
  &nbsp;</td>
  </tr>
</table>
<?php
  if ($MEMBER_FAMILY_MAX) {
?>
<div id="family_header" style="DISPLAY: block">
  <p class="nav_main_group">&nbsp;<font color="#FFFFFF">Family Information </font></p>
  <table width="83%" border="0" align="center" cellpadding="0" cellspacing="0">
   <tr>
     <td><?php echo $MEMBER_FAMILY_BENEFITS ?>
   </tr>
  </table>
  <br />
</div>
<?php
    for ($family_count=1;$family_count<=$MEMBER_FAMILY_MAX;$family_count++) {
?>
<div id="family<?=$family_count?>" style="DISPLAY: block">
  <table width="99%" border="0" align="center" cellpadding="0" cellspacing="0">
   <tr>
     <td>
       <table class="wide">
         <tr>
           <td colspan="2"  class="nb top"><u>FAMILY MEMBER <?=$family_count?>:</u><br />
           &nbsp;</td>
         </tr>
         <tr>
           <td width="60%"  class="nb top">First Name</td>
           <td width="40%" class="nn top"><input id="add<?=$family_count?>first" name="add<?=$family_count?>first" onfocus="this.select();" value="<?php echo $renew_member["ADD" . $family_count . "FIRST"] ?>"/></td>
         </tr>
         <tr>
           <td class="nb top" >Last Name</td>
           <td class="nn top"><input id="add<?=$family_count?>last" name="add<?=$family_count?>last" onfocus="this.select();" value="<?php echo $renew_member["ADD" . $family_count . "LAST"] ?>"/></td>
         </tr>
         <tr>
           <td class="nb top" >Date of Birth</td>
           <td class="nn top"><input id="add<?=$family_count?>dob" name="add<?=$family_count?>dob" onfocus="this.select();" value="<?php echo $renew_member["ADD" . $family_count . "DOB"] ?>"/></td>
         </tr>
         <tr>
           <td class="nb top" >Gender</td>
           <td class="nn top"><input type="radio" name="add<?=$family_count?>gender" value="M" <?php if ($renew_member["ADD" . $family_count . "GENDER"] == 'M' ) echo "checked" ?> />
             Male
             <input type="radio" name="add<?=$family_count?>gender" value="F" <?php if ($renew_member["ADD" . $family_count . "GENDER"] == 'F' ) echo "checked" ?> />
             Female </td>
         </tr>
         <tr>
           <td class="nb top" >E-mail Address &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td>
           <td class="nn top"><input id="add<?=$family_count?>email" name="add<?=$family_count?>email" onfocus="this.select();" value="<?php echo $renew_member["ADD" . $family_count . "EMAIL"] ?>"/></td>
         </tr>
         <tr>
           <td class="nb top" >Phone Number </td>
           <td class="nn top"><input id="add<?=$family_count?>phone" name="add<?=$family_count?>phone" onfocus="this.select();" value="<?php echo $renew_member["ADD" . $family_count . "PHONE"] ?>"/></td>
         </tr>
         <?php if ($RECORD_USSA_FAMILY) { ?>
         <tr>
           <td class="nb top" >US SAILING Number </td>
           <td class="nn top"><input id="add<?=$family_count?>ussa" name="add<?=$family_count?>ussa" onfocus="this.select();" value="<?php echo $renew_member["ADD" . $family_count . "USSA"] ?>"/></td>
         </tr>
         <?php } ?>
         <?php if ($RECORD_BOAT_TYPE) { ?>
         <tr>
           <td class="nb top" >Boat Type</td>
           <td class="nn top">
              <?php
               if ($BOAT_TYPE_ARRAY) {
                 ?>
                 <select name="add<?=$family_count?>boat"  id="select">
                     <?php form_options_print($BOAT_TYPE_ARRAY, $renew_member["ADD" . $family_count . "BOAT"]); ?>
                   </select>
                 <?php
               }
               else {
               ?>
                 <input name="add<?=$family_count?>boat" type="text" id="form_input" size="30" value="<?php echo $renew_member["ADD" . $family_count . "BOAT"] ?>"/>
               <?php
               }
             ?>
             <br />
             <span class="nsm"></span></td>
         </tr>
         <?php } ?>
         <?php if ($RECORD_SAIL_NUMBER) { ?>
         <tr>
           <td class="nb top" >Sail Number</td>
           <td class="nn top"><input id="add<?=$family_count?>sail" name="add<?=$family_count?>sail" onfocus="this.select();" value="<?php echo $renew_member["ADD" . $family_count . "SAIL"] ?>"/></td>
         </tr>
         <?php } ?>
       </table>
       <p>&nbsp;</p></td>
   </tr>
  </table>
</div>
<?php
  }
}

?>

<br />
<br />
<p class="nav_main_group">&nbsp;</p>
</td>
      </tr>

      <tr bordercolor="#ffffff">
        <td bgcolor="#FFFFFF"><div align="center">
          <br />
          <input type="hidden" name="renew_id" value="<?php echo $renew_member[MEMBER_TOKEN] ?>" />
          <?php if (! $HTTP_GET_VARS['edit']) { ?>
            
            <?php
              require_once('recaptchalib.php');
              $publickey = "6LeWyfQSAAAAAMQcObhcteBSKKG4uMxmFEyVP5FE"; // you got this from the signup page
              echo recaptcha_get_html($publickey);
            ?>
            <input name="Submit" type="submit" id="form_button" value="Process My Application" />
          <?php }
          else { ?>
          <input type="hidden" name="edit" value="1" />
          <input name="Submit" type="submit" id="form_button" value="Edit My Application" />
          <?php } ?>

        </div></td>
      </tr>
      <tr bordercolor="#ffffff">
        <td height="36" align="center" bgcolor="#FFFFFF"></td>
      </tr>
      <tr>
        <td height="10"><p class="nav_main_group"><div align="center">&nbsp;</div></p></td>
      </tr>
      <script language=JavaScript>
        familyDisplay2();
      </script>
      <tr>
        <td bgcolor="#FFFFFF" class="disclaimer_box"><?php require_once("footer.php"); ?></td>

      </tr>
    </tbody>
  </table>
</form>
</body>
</html>