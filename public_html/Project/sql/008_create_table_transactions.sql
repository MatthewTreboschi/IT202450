CREATE TABLE IF NOT EXISTS  `Transactions`
(
    `id`         int auto_increment PRIMARY KEY,
    `source`    int UNIQUE,
    `dest`    int UNIQUE,
    `bal_change`  int,
    `transaction_type`  VARCHAR(30),
    `memo`  VARCHAR(100),
    `expected_total` int,
    `created`    timestamp default current_timestamp,
    `modified`   timestamp default current_timestamp on update current_timestamp,
    FOREIGN KEY (`source`) REFERENCES Accounts(`id`),
    FOREIGN KEY (`dest`) REFERENCES Accounts(`id`)
)