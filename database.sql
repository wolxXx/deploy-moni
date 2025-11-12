CREATE SCHEMA IF NOT EXISTS deploy_monitor;

USE deploy_monitor;

CREATE TABLE IF NOT EXISTS deployments
(
    id         INT AUTO_INCREMENT,
    name       VARCHAR(255),
    group_name VARCHAR(255),
    created_at DATETIME,
    PRIMARY KEY (id)
);
CREATE TABLE IF NOT EXISTS  deployment_key
(
    id         INT AUTO_INCREMENT,
    hint       VARCHAR(255),
    value VARCHAR(255),
    valid_from DATETIME NULL ,
    valid_until DATETIME NULL ,
    created_at DATETIME NOT NULL ,
    PRIMARY KEY (id),
    unique key (value)
);

ALTER TABLE deployments ADD KEY (group_name);