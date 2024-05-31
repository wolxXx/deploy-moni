delete from deployments;
delete from deployment_key;

insert into
    deployments(name, group_name, created_at)
values
    ("17.2.12-rc1", "my target 1", "2024-02-13 12:34:56"),
    ("17.3.0", "my target 1", "2024-02-13 12:34:56"),
    ("18.0.0-rc1", "my target 1", "2024-02-13 12:34:56"),
    ("18.0.1-hf1", "my target 1", "2024-02-13 12:34:56"),
    ("feature/t-1234-test1", "ran dom foo", "2024-02-13 12:34:56"),
    ("feature/t-1337", "foo bar", "2024-02-13 12:34:56")
;

insert into deployment_key (hint, value, valid_from, valid_until, created_at)
values ("default key, please delete!", "0bd089c5-0920-4efd-a6ed-d2763420d34a", "2024-01-01 00:00:00" , "2024-01-02 00:00:00", now())