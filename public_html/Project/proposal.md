IT 202-450

Project Name: Simple Bank
Project Summary:
Website Link:
Github Link:
Your Name: Matthew Treboschi



Milestone Features:
    Milestone 1 (taken from class):
        - [x] User will be able to create a new account
            - Form fields
                - [x] Username, Email, Password
                - [x] Email is Required and must be validated
                - [x] Username is Required
                - [x] Confirm Passwords Match
            - Users Table
                - [x] id, username, email, password, created, modified
            - [] Passwords must be hashed
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
                - [] Have a User Roles table (id, user_id, role_id, is_active, created, modified)
                - Include a function to check if a user has a specific role (not this milestone)
            - [] Everything should be styled
                - [] forms, nav bar, etc
            - [x] Any output messages/ errors should be 'user friendly'
                - Any technical errors or debug output displayed will result in a loss of points
            - [x] User will be able to see their profile
                - email, username, etc
            - [x] User will be able to edit their profile
                - Changing username/email should properly check if its available before allowing the change
            - Any ofther fields should be properly validated
            - [x] Allow password reset (only if existing password is provided)


    Milestone 2:
        - [x] Create the Accounts table (id, account_number [unique, always 12 characters], user_id, balance (default 0), account_type, created, modified)
        - [x] Project setup steps:
            - [x] Create these as initial setup scripts in the sql folder
                - [x] Create a system user if they don’t exist (this will never be logged into, it’s just to keep things working per system requirements)
                - [x] Create a world account in the Accounts table created below (if it doesn’t exist)
                    - [x] Account_number must be “000000000000”
                    - [x] User_id must be the id of the system user
                    - [x] Account type must be “world”
        - [x] Create the Transactions table (see reference below)
        - [x] Dashboard page
            - [x] Will have links for Create Account, My Accounts, Deposit, Withdraw Transfer, Profile
                - [x] Links that don’t have pages yet should just have href=”#”, you’ll update them later
        - [] User will be able to create a checking account
            - [x] System will generate a unique 12 digit account number
                - [x] Options (strike out the option you won’t do):
                    - [x] Option 1: Generate a random 12 digit/character value; must regenerate if a duplicate collision occurs
                    - [-] Option 2: Generate the number based on the id column; requires inserting a null first to get the last insert id, then update the record immediately after
            - [x] System will associate the account to the user
            - [x] Account type will be set as checking
            - [] Will require a minimum deposit of $5 (from the world account)
                - [] Entry will be recorded in the Transaction table as a transaction pair (per notes below)
                - [] Account Balance will be updated based on SUM of BalanceChange of AccountSrc
            - [] User will see user-friendly error messages when appropriate
            - [] User will see user-friendly success message when account is created successfully
                - [] Redirect user to their Accounts page
        - [] User will be able to list their accounts
            - [] Limit results to 5 for now
            - [] Show account number, account type and balance
        - [] User will be able to click an account for more information (a.ka. Transaction History page)
            - [] Show account number, account type, balance, opened/created date
            - [] Show transaction history (from Transactions table)
                - [] For now limit results to 10 latest
        - [] User will be able to deposit/withdraw from their account(s)
            - [] Form should have a dropdown of their accounts to pick from
                - [] World account should not be in the dropdown
            - [] Form should have a field to enter a positive numeric value
                - [] For now, allow any deposit value (0 - inf)
            - [] For withdraw, add a check to make sure they can’t withdraw more money than the account has
            - [] Form should allow the user to record a memo for the transaction
            - [] Each transaction is recorded as a transaction pair in the Transaction table per the details below
                - [] These will reflect on the transaction history page (Account page’s “more info”)
                - [] After each transaction pair, make sure to update the Account Balance by SUMing the BalanceChange for the AccountSrc
                    - [] This will be done after the insert
                - [] Deposits will be from the “world account”
                    - [] Must fetch the world account to get the id (do not hard code the id as it may change if the application migrates or gets rebuilt)
                - [] Withdraws will be to the “world account”
                    - [] Must fetch the world account to get the id (do not hard code the id as it may change if the application migrates or gets rebuilt)
                - [] Transaction type should show accordingly (deposit/withdraw)
            - [] Show appropriate user-friendly error messages
            - [] Show user-friendly success messages


    Milestone 3:
        - [] User will be able to transfer between their accounts
            - [] Form should include a dropdown first AccountSrc and a dropdown for AccountDest (only accounts the user owns; no world account)
            - [] Form should include a field for a positive numeric value
            - [] System shouldn’t allow the user to transfer more funds than what’s available in AccountSrc
            - [] Form should allow the user to record a memo for the transaction
            - [] Each transaction is recorded as a transaction pair in the Transaction table
                - [] These will reflect in the transaction history page
            - [] Show appropriate user-friendly error messages
            - [] Show user-friendly success messages
        - [] Transaction History page
            - [] Will show the latest 10 transactions by default
            - [] User will be able to filter transactions between two dates
            - [] User will be able to filter transactions by type (deposit, withdraw, transfer)
            - [] Transactions should paginate results after the initial 10
        - [] User’s profile page should record/show First and Last name
        - [] User will be able to transfer funds to another user’s account
            - [] Form should include a dropdown of the current user’s accounts (as AccountSrc)
            - [] Form should include a field for the destination user’s last name
            - [] Form should include a field for the last 4 digits of the destination user’s account number (to lookup AccountDest)
            - [] Form should include a field for a positive numerical value
            - [] Form should allow the user to record a memo for the transaction
            - [] System shouldn’t let the user transfer more than the balance of their account
            - [] System will lookup appropriate account based on destination user’s last name and the last 4 digits of the account number
            - [] Show appropriate user-friendly error messages
            - [] Show user-friendly success messages
            - [] Transaction will be recorded with the type as “ext-transfer”
            - [] Each transaction is recorded as a transaction pair in the Transaction table
                - [] These will reflect in the transaction history page


    Milestone 4:
        - [] User can set their profile to be public or private (will need another column in Users table)
            - [] If public, hide email address from other users
        - [] User will be able open a savings account
            - [] System will generate a 12 digit/character account number per the existing rules (see Checking Account above)
            - [] System will associate the account to the user
            - [] Account type will be set as savings
            - [] Will require a minimum deposit of $5 (from the world account)
                - [] Entry will be recorded in the Transaction table in a transaction pair (per notes below)
                - [] Account Balance will be updated based on SUM of BalanceChange of AccountSrc
            - [] System sets an APY that’ll be used to calculate monthly interest based on the balance of the account
                - [] Recommended to create a table for “system properties” and have this value stored there and fetched when needed, this will allow you to have an admin account change the value in the future)
            - [] User will see user-friendly error messages when appropriate
            - [] User will see user-friendly success message when account is created successfully
                - [] Redirect user to their Accounts page
        - [] User will be able to take out a loan
            - [] System will generate a 12 digit/character account number per the existing rules (see Checking Account above)
            - [] Account type will be set as loan
            - [] Will require a minimum value of $500
            - [] System will show an APY (before the user submits the form)
                - [] This will be used to add interest to the loan account
                - [] Recommended to create a table for “system properties” and have this value stored there and fetched when needed, this will allow you to have an admin account change the value in the future)
            - [] Form will have a dropdown of the user’s accounts of which to deposit the money into
            - [] Special Case for Loans:
                - [] Loans will show with a positive balance of what’s required to pay off (although it is a negative since the user owes it)
                - [] User will transfer funds to the loan account to pay it off
                - [] Transfers will continue to be recorded in the Transactions table
                - [] Loan account’s balance will be the balance minus any transfers to this account
                - [] Interest will be applied to the current loan balance and add to it (causing the user to owe more)
                - [] A loan with 0 balance will be considered paid off and will not accrue interest and will be eligible to be marked as closed
                - [] User can’t transfer more money from a loan once it’s been opened and a loan account should not appear in the Account Source dropdowns
            - [] User will see user-friendly error messages when appropriate
            - [] User will see user-friendly success message when account is created successfully
                - [] Redirect user to their Accounts page
        - [] Listing accounts and/or viewing Account Details should show any applicable APY or “-” if none is set for the particular account (may alternatively just hide the display for these types)
        - [] User will be able to close an account
            - [] User must transfer or withdraw all funds out of the account before doing so
            - [] Account should have a column “active” that will get set as false.
                - [] All queries for Accounts should be updated to pull only “active” = true accounts (i.e., dropdowns, My Accounts, etc)
                - [] Do not delete the record, this is a soft delete so it doesn’t break transactions
            - [] Closed accounts don’t show up anymore
            - [] If the account is a loan, it must be paid off in full first
        - [] Admin role (leave this section for last)
            - [] Will be able to search for users by firstname and/or lastname
            - [] Will be able to look-up specific account numbers (partial match).
            - [] Will be able to see the transaction history of an account
            - [] Will be able to freeze an account (this is similar to disable/delete but it’s a different column)
                - [] Frozen accounts still show in results, but they can’t be interacted with.
                - [] [Dev note]: Will want to add a column to Accounts table called frozen and default it to false
                    - [] Update transactions logic to not allow frozen accounts to be used for a transaction
            - [] Will be able to open accounts for specific users
            - [] Will be able to deactivate a user
                - [] Requires a new column on the Users table (i.e., is_active)
                - [] Deactivated users will be restricted from logging in
                    - “Sorry your account is no longer active”


Notes/References:
        - Account Number Requirements
        - Should be 12 characters long
        - “World” account should be “000000000000” (this is used for deposit/withdraw showing the movement of money outside of the bank)
        - Each transaction must be recorded as two separate inserts to the transaction table
        - *Transaction Table Minimum Requirements
        - Each action for a set of accounts will be in pairs. The colors in the table below highlight what this means.
        - The first source/dest is the account that triggered the action to the dest account. This typically will be a negative change.
        - The second source/dest is the dest account's half of the transaction info.
        - source/dest will swap in the second half of the transaction
        - [x] BalanceChange will invert in the second half of the transaction
            - This typically will be a positive change
        - [x] Src/Dest are the account id’s affected (Accounts.id, not Accounts.account_number).
        - [x] BalanceChange is the difference in the account balance (i.e., a deposit of $50) (deposit subtracts from source for the first part and adds to source for the second part.)
        - [x] TransactionType is a built-in identifier to track the action (i.e., deposit, withdraw, transfer, ext-transfer)
        - [x] Memo user-defined notes
        - [x] ExpectedTotal is the account’s final value after the transaction, respectively. This is not to be used as the “Account Balance” it’s recorded for bookkeeping and review purposes.
        - [x] Created is when the transaction occurred
        - The below Transaction/Ledger table should total (SUM) up to zero to show that your bank is in balance. Otherwise, something bad happened with the 




