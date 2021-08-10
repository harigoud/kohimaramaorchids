<?php

if(!$_POST) exit;
ini_set("sendmail_from", "support@nzinfotech.co.nz");


$to = 'dbutcher@xtra.co.nz';
$subject = 'Message from Kohimarama Orchids Website';

/**************************************************/


$headers = 'From: '.$_POST['txtemail'];

date_default_timezone_set("Pacific/Auckland");

$date = date ("l, F jS, Y"); 
$time = date ("h:i A"); 

$msg = 'Submitted by '.$_POST['txtname']." $date at $time.\n\n";

$msg .= 'Contact Number: '.$_POST['txtphone']."\n\n";

$msg .= $_POST['txtenquiry'];

$return = "";

if (empty($_POST['txtemail']) || !validEmail($_POST['txtemail']))
{
    $return .= '"Enter valid email":1,';
}
if (empty($_POST['txtname']))
{
    $return .= '"Enter your name":1,';
}
if (empty($_POST['txtphone']))
{
    $return .= '"Enter your phone number":1,';
}
if (empty($_POST['txtenquiry']))
{
    $return .= '"Enter your Enquiry":1,';
}

// if no previous errors have been set
if (empty($return))
{
    if (mail($to, $subject, $msg, $headers))
    {
		$return .= '"success":1';
		header("Location: http://www.kohimaramaorchids.co.nz/successquery.htm");


        
    }
    else
    {
        $return .= '"success":0';
    }
}

echo '{'.$return.'}';



function validEmail($email)
{
   $isValid = true;
   $atIndex = strrpos($email, "@");
   if (is_bool($atIndex) && !$atIndex)
   {
      $isValid = false;
   }
   else
   {
      $domain = substr($email, $atIndex+1);
      $local = substr($email, 0, $atIndex);
      $localLen = strlen($local);
      $domainLen = strlen($domain);
      if ($localLen < 1 || $localLen > 64)
      {
         // local part length exceeded
         $isValid = false;
      }
      else if ($domainLen < 1 || $domainLen > 255)
      {
         // domain part length exceeded
         $isValid = false;
      }
      else if ($local[0] == '.' || $local[$localLen-1] == '.')
      {
         // local part starts or ends with '.'
         $isValid = false;
      }
      else if (preg_match('/\\.\\./', $local))
      {
         // local part has two consecutive dots
         $isValid = false;
      }
      else if (!preg_match('/^[A-Za-z0-9\\-\\.]+$/', $domain))
      {
         // character not valid in domain part
         $isValid = false;
      }
      else if (preg_match('/\\.\\./', $domain))
      {
         // domain part has two consecutive dots
         $isValid = false;
      }
      else if(!preg_match('/^(\\\\.|[A-Za-z0-9!#%&`_=\\/$\'*+?^{}|~.-])+$/',
                 str_replace("\\\\","",$local)))
      {
         // character not valid in local part unless
         // local part is quoted
         if (!preg_match('/^"(\\\\"|[^"])+"$/',
             str_replace("\\\\","",$local)))
         {
            $isValid = false;
         }
      }
      if ($isValid && !(checkdnsrr($domain,"MX") || checkdnsrr($domain,"A")))
      {
         // domain not found in DNS
         $isValid = false;
      }
   }
   return $isValid;
}
