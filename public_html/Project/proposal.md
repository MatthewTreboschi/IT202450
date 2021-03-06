IT 202-450

Project Name: Simple Bank

Project Summary:

Website Link:

Github Link:

Your Name: Matthew Treboschi

presentation https://youtu.be/4F4HybD2_NM

Milestone Features:


1. Milestone 1 (taken from class):
	- [x] User will be able to create a new account
		- Form fields
			- [x] Username, Email, Password
			- [x] Email is Required and must be validated
			- [x] Username is Required
			- [x] Confirm Passwords Match
		- Users Table
			- [x] id, username, email, password, created, modified
		- [ ] Passwords must be hashed
		- [x] Email should be unique
		- [x] Username should be unique
		- [x] System should let user know if username or email is taken and allow the user to correct the error without wiping the form
			- the only fields that may be cleared are password fields
	- [x] User will be able to log into their account (given they enter the correct credentials)
		- Form
			- [x] User can login with email or username
			- [x] Password is required
		- [x] User should see friendly error messages when an account doesn't exist or passwords dont match
		- [x] logging in should fetch the user's details and save them in the session
		- [x] User will be directed to a landing page upon login
			- [x] non logged in users shouldnt have access
			-this can be a home, profile, or a dashboard.
		- [x] User will be able to log out
			- [x] logging out redirects to login
			- [x] users should see a message that theyve successfully logged out
			- [x] hitting the back button shouldnt let them back in
		- Basic security rules
			- Authentication
				- [x] Function to check if user is logged in
				- [x] Function should be called on appropriate pages that allow logged in users
			- Authorization
				- [x] have a roles table (id, name description, active, modified)
		- Basic Roles Implemented
			- [x] Have a Roles table (id, name, description, active, modified)
			- [ ] Have a User Roles table (id, user_id, role_id, is_active, created, modified)
			- Include a function to check if a user has a specific role (not this milestone)
		- [ ] Everything should be styled
			- [ ] forms, nav bar, etc
		- [x] Any output messages/ errors should be 'user friendly'
			- Any technical errors or debug output displayed will result in a loss of points
		- [x] User will be able to see their profile
			- email, username, etc
		- [x] User will be able to edit their profile
			- Changing username/email should properly check if its available before allowing the change
		- Any ofther fields should be properly validated
		- [x] Allow password reset (only if existing password is provided)


2. Milestone 2:
	- [x] [07/20/21] Create the Accounts table (id, account_number [unique, always 12 characters], user_id, balance (default 0), account_type, created, modified)
	- [x] [07/20/21] Project setup steps:
		- [x] [07/20/21] Create these as initial setup scripts in the sql folder
			- [x] [07/20/21] Create a system user if they don???t exist (this will never be logged into, it???s just to keep things working per system requirements)
			- [x] [07/20/21] Create a world account in the Accounts table created below (if it doesn???t exist)
				- [x] [07/20/21] Account_number must be ???000000000000???
				- [x] [07/20/21] User_id must be the id of the system user
				- [x] [07/20/21] Account type must be ???world???
	- [x] [07/20/21] Create the Transactions table (see reference below)
	- [x] [07/22/21] Dashboard page
		- [x] [07/22/21] Will have links for Create Account, My Accounts, Deposit, Withdraw Transfer, Profile
			- [x] [07/22/21] Links that don???t have pages yet should just have href=???#???, you???ll update them later
	- [x] [07/26/21] User will be able to create a checking account
		- [x] [07/22/21] System will generate a unique 12 digit account number
			- [x] [07/22/21] Options (strike out the option you won???t do):
				- [x] [07/22/21] Option 1: Generate a random 12 digit/character value; must regenerate if a duplicate collision occurs
				- ~~Option 2: Generate the number based on the id column; requires inserting a null first to get the last insert id, then update the record immediately after~~
		- [x] [07/22/21] System will associate the account to the user
		- [x] [07/22/21] Account type will be set as checking
		- [x] [07/23/21] Will require a minimum deposit of $5 (from the world account)
			- [x] [07/23/21] Entry will be recorded in the Transaction table as a transaction pair (per notes below)
			- [x] [07/23/21] Account Balance will be updated based on SUM of BalanceChange of AccountSrc
		- [x] [07/23/21] User will see user-friendly error messages when appropriate
		- [x] [07/23/21] User will see user-friendly success message when account is created successfully
			- [x] [07/23/21] Redirect user to their Accounts page
	- [x] [07/26/21] User will be able to list their accounts
		- [x] [07/26/21] Limit results to 5 for now
		- [x] [07/26/21] Show account number, account type and balance
	- [x] [07/26/21] User will be able to click an account for more information (a.ka. Transaction History page)
		- [x] [07/26/21] Show account number, account type, balance, opened/created date
		- [x] [07/26/21] Show transaction history (from Transactions table)
			- [x] [07/26/21] For now limit results to 10 latest
	- [x] [07/27/21] User will be able to deposit/withdraw from their account(s)
		- [x] [07/26/21] Form should have a dropdown of their accounts to pick from
			- [x] [07/26/21] World account should not be in the dropdown
		- [x] [07/26/21] Form should have a field to enter a positive numeric value
			- [x] [07/26/21] For now, allow any deposit value (0 - inf)
		- [x] [07/27/21] For withdraw, add a check to make sure they can???t withdraw more money than the account has
		- [x] [07/26/21] Form should allow the user to record a memo for the transaction
		- [x] [07/27/21] Each transaction is recorded as a transaction pair in the Transaction table per the details below
			- [x] [07/26/21] These will reflect on the transaction history page (Account page???s ???more info???)
			- [x] [07/27/21] After each transaction pair, make sure to update the Account Balance by SUMing the BalanceChange for the AccountSrc
				- [x] [07/27/21] This will be done after the insert
			- [x] [07/26/21] Deposits will be from the ???world account???
				- [x] [07/26/21] Must fetch the world account to get the id (do not hard code the id as it may change if the application migrates or gets rebuilt)
			- [x] [07/26/21] Withdraws will be to the ???world account???
				- [x] [07/26/21] Must fetch the world account to get the id (do not hard code the id as it may change if the application migrates or gets rebuilt)
			- [x] [07/26/21] Transaction type should show accordingly (deposit/withdraw)
		- [x] [07/27/21] Show appropriate user-friendly error messages
		- [x] [07/27/21] Show user-friendly success messages

3. Milestone 3:
	- [x] [07/28/21] User will be able to transfer between their accounts
		- [x] [07/28/21] Form should include a dropdown first AccountSrc and a dropdown for AccountDest (only accounts the user owns; no world account)
		- [x] [07/28/21] Form should include a field for a positive numeric value
		- [x] [07/28/21] System shouldn???t allow the user to transfer more funds than what???s available in AccountSrc
		- [x] [07/28/21] Form should allow the user to record a memo for the transaction
		- [x] [07/28/21] Each transaction is recorded as a transaction pair in the Transaction table
			- [x] [07/28/21] These will reflect in the transaction history page
		- [x] [07/28/21] Show appropriate user-friendly error messages
		- [x] [07/28/21] Show user-friendly success messages
	- [x] [07/29/21] Transaction History page
		- [x] [07/26/21] Will show the latest 10 transactions by default
		- [x] [07/29/21] User will be able to filter transactions between two dates
		- [x] [07/29/21] User will be able to filter transactions by type (deposit, withdraw, transfer)
		- [x] [07/29/21] Transactions should paginate results after the initial 10
	- [x] [07/29/21] User???s profile page should record/show First and Last name
	- [x] [07/30/21] User will be able to transfer funds to another user???s account
		- [x] [07/28/21] Form should include a dropdown of the current user???s accounts (as AccountSrc)
		- [x] [07/30/21] Form should include a field for the destination user???s last name
		- [x] [07/30/21] Form should include a field for the last 4 digits of the destination user???s account number (to lookup AccountDest)
		- [x] [07/28/21] Form should include a field for a positive numerical value
		- [x] [07/28/21] Form should allow the user to record a memo for the transaction
		- [x] [07/28/21] System shouldn???t let the user transfer more than the balance of their account
		- [x] [07/30/21] System will lookup appropriate account based on destination user???s last name and the last 4 digits of the account number
		- [x] [07/30/21] Show appropriate user-friendly error messages
		- [x] [07/30/21] Show user-friendly success messages
		- [x] [07/28/21] Transaction will be recorded with the type as ???ext-transfer???
		- [x] [07/28/21] Each transaction is recorded as a transaction pair in the Transaction table
			- [x] [07/28/21] These will reflect in the transaction history page


4. Milestone 4:
	- [x] [08/02/21] User can set their profile to be public or private (will need another column in Users table)
		- [x] [08/02/21] If public, hide email address from other users
	- [x] [07/31/21] User will be able open a savings account
		- [x] [07/31/21] System will generate a 12 digit/character account number per the existing rules (see Checking Account above)
		- [x] [07/31/21] System will associate the account to the user
		- [x] [07/31/21] Account type will be set as savings
		- [x] [07/31/21] Will require a minimum deposit of $5 (from the world account)
			- [x] [07/29/21] Entry will be recorded in the Transaction table in a transaction pair (per notes below)
			- [x] [07/29/21] Account Balance will be updated based on SUM of BalanceChange of AccountSrc
		- [x] [07/31/21] System sets an APY that???ll be used to calculate monthly interest based on the balance of the account
			- [x] [07/31/21] Recommended to create a table for ???system properties??? and have this value stored there and fetched when needed, this will allow you to have an admin account change the value in the future)
		- [x] [07/31/21] User will see user-friendly error messages when appropriate
		- [x] [07/31/21] User will see user-friendly success message when account is created successfully
			- [x] [07/31/21] Redirect user to their Accounts page
	- [x] [07/31/21] User will be able to take out a loan
		- [x] [07/31/21] System will generate a 12 digit/character account number per the existing rules (see Checking Account above)
		- [x] [07/31/21] Account type will be set as loan
		- [x] [07/31/21] Will require a minimum value of $500
		- [x] [07/31/21] System will show an APY (before the user submits the form)
			- [x] [07/31/21] This will be used to add interest to the loan account
			- [x] [07/31/21] Recommended to create a table for ???system properties??? and have this value stored there and fetched when needed, this will allow you to have an admin account change the value in the future)
		- [x] [07/31/21] Form will have a dropdown of the user???s accounts of which to deposit the money into
		- [x] [07/31/21] Special Case for Loans:
			- [x] [07/31/21] Loans will show with a positive balance of what???s required to pay off (although it is a negative since the user owes it)
			- [x] [07/31/21] User will transfer funds to the loan account to pay it off
			- [x] [07/31/21] Transfers will continue to be recorded in the Transactions table
			- [x] [07/31/21] Loan account???s balance will be the balance minus any transfers to this account
			- [x] [07/31/21] Interest will be applied to the current loan balance and add to it (causing the user to owe more)
			- [x] [07/31/21] A loan with 0 balance will be considered paid off and will not accrue interest and will be eligible to be marked as closed
			- [x] [07/31/21] User can???t transfer more money from a loan once it???s been opened and a loan account should not appear in the Account Source dropdowns
		- [x] [07/31/21] User will see user-friendly error messages when appropriate
		- [x] [07/31/21] User will see user-friendly success message when account is created successfully
			- [x] [07/29/21] Redirect user to their Accounts page
	- [x] [08/02/21] Listing accounts and/or viewing Account Details should show any applicable APY or ???-??? if none is set for the particular account (may alternatively just hide the display for these types)
	- [x] [08/02/21] User will be able to close an account
		- [x] [08/02/21] User must transfer or withdraw all funds out of the account before doing so
		- [x] [08/02/21] Account should have a column ???active??? that will get set as false.
			- [x] [08/02/21] All queries for Accounts should be updated to pull only ???active??? = true accounts (i.e., dropdowns, My Accounts, etc)
			- [x] [08/02/21] Do not delete the record, this is a soft delete so it doesn???t break transactions
		- [x] [08/02/21] Closed accounts don???t show up anymore
		- [x] [08/02/21] If the account is a loan, it must be paid off in full first
	- [x] [08/02/21] Admin role (leave this section for last)
		- [x] [08/02/21] Will be able to search for users by firstname and/or lastname
		- [x] [08/02/21] Will be able to look-up specific account numbers (partial match).
		- [x] [08/02/21] Will be able to see the transaction history of an account
		- [x] [08/02/21] Will be able to freeze an account (this is similar to disable/delete but it???s a different column)
			- [x] [08/02/21] Frozen accounts still show in results, but they can???t be interacted with.
			- [x] [08/02/21] [Dev note]: Will want to add a column to Accounts table called frozen and default it to false
				- [ ] Update transactions logic to not allow frozen accounts to be used for a transaction
		- [x] [08/02/21] Will be able to open accounts for specific users
		- [x] [08/02/21] Will be able to deactivate a user
			- [x] [08/02/21] Requires a new column on the Users table (i.e., is_active)
			- [x] [08/02/21] Deactivated users will be restricted from logging in
					- ???Sorry your account is no longer active???


Notes/References:
	- Account Number Requirements
		- Should be 12 characters long
		- ???World??? account should be ???000000000000??? (this is used for deposit/withdraw showing the movement of money outside of the bank)
		- Each transaction must be recorded as two separate inserts to the transaction table
	- *Transaction Table Minimum Requirements
		- Each action for a set of accounts will be in pairs. The colors in the table below highlight what this means.
		- The first source/dest is the account that triggered the action to the dest account. This typically will be a negative change.
		- The second source/dest is the dest account's half of the transaction info.
		- source/dest will swap in the second half of the transaction
		- [x] BalanceChange will invert in the second half of the transaction
			- This typically will be a positive change
		- [ ] Src/Dest are the account id???s affected (Accounts.id, not Accounts.account_number).
		- [x] BalanceChange is the difference in the account balance (i.e., a deposit of $50) (deposit subtracts from source for the first part and adds to source for the second part.)
		- [x] TransactionType is a built-in identifier to track the action (i.e., deposit, withdraw, transfer, ext-transfer)
		- [x] Memo user-defined notes
		- [x] ExpectedTotal is the account???s final value after the transaction, respectively. This is not to be used as the ???Account Balance??? it???s recorded for bookkeeping and review purposes.
		- [x] Created is when the transaction occurred
		- The below Transaction/Ledger table should total (SUM) up to zero to show that your bank is in balance. Otherwise, something bad happened with the 



