// Set the LDAP server information
$ldapServer = "ldap://csbe.local";
$ldapPort = 389;
$ldapBaseDn = "dc=example,dc=com"; // Set the user credentials
$username = "Administrator";
$password = "CsBe12345"; // Connect to the LDAP server

$ldapConn = ldap_connect($ldapServer, $ldapPort);

if (!$ldapConn) {
    die("Could not connect to LDAP server.");
} // Bind to the LDAP server using the user credentials

$ldapBind = ldap_bind($ldapConn, "cn=$username,$ldapBaseDn", $password);

if (!$ldapBind) {
    die("Invalid username or password.");
} // Search for the user in the LDAP directory

$searchFilter = "(sAMAccountName=$username)";
$searchResult = ldap_search($ldapConn, $ldapBaseDn, $searchFilter);

if (!$searchResult) {
    die("Could not search LDAP directory.");
} // Get the user's LDAP entry

$userEntry = ldap_first_entry($ldapConn, $searchResult);

if (!$userEntry) {
    die("User not found in LDAP directory.");
} // Get the user's attributes from the LDAP entry

$userAttributes = ldap_get_attributes($ldapConn, $userEntry); // Close the LDAP connection

ldap_close($ldapConn); // Print the user's attributes

echo $userAttributes;
