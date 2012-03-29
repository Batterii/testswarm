<?php
/**
 * "Login" action.
 *
 * @since 0.1.0
 * @package TestSwarm
 */

class LoginAction extends Action {

	public function doAction() {

		$request = $this->getContext()->getRequest();

		// Already logged in ?
		if ( $request->getSessionData( "username" ) && $request->getSessionData( "auth" ) == "yes" ) {
			$username = $request->getSessionData( "username" );
		// Try logging in
		} else {

			if ( !$request->wasPosted() ) {
				$this->setError( "requires-post" );
				return;
			}

			$username = preg_replace("/[^a-zA-Z0-9_ -]/", "", $request->getVal( "username" ) );
			$password = $request->getVal( "password" );

			if ( !$username || !$password ) {
				$this->setError( "missing-parameters" );
				return;
			}

			$result = mysql_queryf(
				"SELECT id
				FROM users
				WHERE	name = %s
				AND 	password = SHA1(CONCAT(seed, %s))
				LIMIT 1;",
				$username,
				$password
			);

			if ( mysql_num_rows( $result ) > 0 ) {
				// Start logged-in session
				$request->setSessionData( "username", $username );
				$request->setSessionData( "auth", "yes" );

			} else {
				$this->setError( "invalid-input" );
				return;
			}
		}

		// We're still here, logged-in succeeded!
		$this->setData( array(
			"status" => "logged-in",
			"username" => $username,
		) );
		return;
	}
}