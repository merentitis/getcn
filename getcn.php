<?php
//This is written originally by https://stackoverflow.com/users/1190151/daniel-murphy
//We just need six varaiables here
$baseDN = 'dc=domain,dc=com';
$adminDN = "cn=Lakis Lalakis,dc=domain,dc=com";//this is the admin distinguishedName
$adminPswd = "ll12344";
$username = 'llakis';//this is the user samaccountname
$userpass = 'll1234';
$ldap_conn = ldap_connect('ldap://ds.domain.com');//I'm using LDAPS here
ldap_set_option($ldap_conn->ldap, LDAP_OPT_PROTOCOL_VERSION, 3);
if (! $ldap_conn) {
        echo ("<p style='color: red;'>Couldn't connect to LDAP service</p>");
    }
else {    
        echo ("<p style='color: green;'>Connection to LDAP service successful!</p>");
     }
//If we want anonymous bind:
//$ldapBindAdmin = ldap_bind($ldap_conn);
//Else: The first step is to bind the administrator so that we can search user info
$ldapBindAdmin = ldap_bind($ldap_conn, $adminDN, $adminPswd);
if ($ldapBindAdmin){
    echo ("<p style='color: green;'>Admin binding and authentication successful!!!</p>");
    $filter = '(uid='.$username.')';
    //Get some attributes, we only  need "cn" for this example
    $attributes = array("cn", "telephonenumber", "mail", "uid");
    $result = ldap_search($ldap_conn, $baseDN, $filter, $attributes);
    $entries = ldap_get_entries($ldap_conn, $result);  
    $userDN = $entries[0]["cn"][0];  
    echo ('<p style="color:green;">Username used (uid): '.$username.'</p>');
    echo ('<p style="color:green;">I have the user DN: '.$userDN.'</p>');
//Okay, we're in! But now we need bind the user now that we have the user's DN
//$ldapBindUSER = ldap_bind($ldap_conn, $userDN, $userpass);
//$ldapbindUSER = ldap_bind($ldap_conn, "$userDN" . "$baseDN", $userpass);
  $ldapbindUSER = ldap_bind($ldap_conn, "cn=$userDN".','.$baseDN, $userpass);
    if($ldapbindUSER){
        echo ("<p style='color: green;'>User binding and authentication successful!!!</p>");        
        ldap_unbind($ldap_conn); // Clean up after ourselves.
    } else {
        echo ("<p style='color: red;'>There was a problem binding the user to LDAP :(</p>");   
    }     
} else {
    echo ("<p style='color: red;'>There was a problem binding the admin to LDAP :(</p>");   
} 
?>

