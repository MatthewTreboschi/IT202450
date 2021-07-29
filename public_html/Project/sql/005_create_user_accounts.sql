CREATE TABLE IF NOT EXISTS  `Accounts`
(
    `id`         int auto_increment PRIMARY KEY,
    `account_number`    VARCHAR(12) UNIQUE,
    `user_id`  int,
    `balance`  int default 0,
    `account_type`  VARCHAR(40),
    `created`    timestamp default current_timestamp,
    `modified`   timestamp default current_timestamp on update current_timestamp,
    FOREIGN KEY (`user_id`) REFERENCES Users(`id`)
)