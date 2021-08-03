ALTER TABLE Accounts
ADD COLUMN apy DOUBLE(5,3) default 00.000;
ALTER TABLE Accounts
ADD COLUMN last_apy timestamp;
ALTER TABLE Accounts
DROP COLUMN next_apy;
Update Accounts
SET last_apy = CURRENT_TIMESTAMP
WHERE last_apy IS NULL and account_type = "Savings" OR account_type = "Loan";