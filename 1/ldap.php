<?php
	
	$ldap_dn = "uid="."einstein".",dc=example,dc=com";
	$ldap_password = "password";

	
	$ldap_con = ldap_connect("ldap.forumsys.com");
	ldap_set_option($ldap_con, LDAP_OPT_PROTOCOL_VERSION, 3);

    if (empty($ldap_password)) {
        echo "You have to write an Password";
        return;
    }

	if(@ldap_bind($ldap_con,$ldap_dn,$ldap_password))
			
	$ldap_dn = "cn=read-only-admin,dc=example,dc=com";
	
	$ldap_con = ldap_connect("ldap.forumsys.com");
	
	ldap_set_option($ldap_con, LDAP_OPT_PROTOCOL_VERSION, 3);
	
	if(ldap_bind($ldap_con, $ldap_dn, $ldap_password)) {

		$filter = "(cn=*)";
		$result = ldap_search($ldap_con,"dc=example,dc=com",$filter) or exit("Unable to search");
		$entries = ldap_get_entries($ldap_con, $result);
		
        $response = json_encode($entries);
		echo $response;
    }
	else {
		echo "Invalid Credential";
    }
?>