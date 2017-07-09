DROP DATABASE Gw2Analyser;
CREATE DATABASE Gw2Analyser;
USE Gw2Analyser;

CREATE TABLE objective
(
obj_id varchar(10),
name varchar(40),
ppt_base int(2),
type varchar(10),
sector_id varchar(8),
map_id varchar(8),
map_type varchar(50),
coordX float(8,2),
coordY float(8,2),
coordZ float(8,2),
label_coordX float(8,2),
label_coordY float(8,2),
marker varchar(255),
compass_direction varchar(5),
chat_link varchar(20),
PRIMARY KEY (obj_id)
);

CREATE TABLE guild
(
guild_id varchar(60),
emblem_last_updated datetime,
name varchar(60),
tag varchar(10),
PRIMARY KEY(guild_id)
);

CREATE TABLE guild_emblem
(
id int(11) unsigned AUTO_INCREMENT,
guild_id varchar(60),
background_id int(5),
foreground_id int(5),
flags varchar(255),
background_color_id int(5),
foreground_primary_color_id int(5),
foreground_secondary_color_id int(5),
PRIMARY KEY(id),
FOREIGN KEY (guild_id) REFERENCES guild (guild_id)
ON DELETE CASCADE
);

CREATE TABLE server_info
(
server_id int(4),
name varchar(60),
PRIMARY KEY(server_id)
);

CREATE TABLE match_detail
(
id int(11) unsigned AUTO_INCREMENT,
match_id varchar(4),
week_num int(3),
start_time datetime,
end_time datetime,
PRIMARY KEY(id)
);

CREATE TABLE map_score
(
id int(11) unsigned AUTO_INCREMENT,
match_detail_id int(11) unsigned,
timeStamp datetime,
map_id enum("RedHome","BlueHome","GreenHome","Center"),
greenScore int(6),
blueScore int(6),
redScore int(6),
greenKills int(4),
blueKills int(4),
redKills int(4),
greenDeaths int(4),
blueDeaths int(4),
redDeaths int(4),
green_ppt int(3),
blue_ppt int(3),
red_ppt int(3),
PRIMARY KEY(id),
FOREIGN KEY(match_detail_id) references match_detail(id)
ON DELETE CASCADE
);

CREATE TABLE skirmish_score
(
id int(11) unsigned AUTO_INCREMENT,
match_detail_id int(11) unsigned,
timeStamp datetime,
skirmish_number int(2),
red_skirmish_score int(3),
blue_skirmish_score int(3),
green_skirmish_score int(3),
PRIMARY KEY(id),
FOREIGN KEY(match_detail_id) REFERENCES match_detail(id)
ON DELETE CASCADE
);

CREATE TABLE objective_upgrade
(
id int(4),
name varchar(64),
description text,
icon text,
PRIMARY KEY(id)
);

CREATE TABLE capture_history
(
id int(11) unsigned AUTO_INCREMENT,
match_detail_id int(11) unsigned,
timeStamp datetime,
last_flipped datetime,
obj_id  varchar(10),
owner_server int(4),
tick_timer float(3,1),
owner_color enum("Blue","Red","Green","Neutral"),
num_yaks_est int(3),
num_yaks int(3),
duration_owned time,
PRIMARY KEY(id),
FOREIGN KEY(obj_id) REFERENCES objective(obj_id),
FOREIGN KEY(match_detail_id) REFERENCES match_detail(id)
ON DELETE CASCADE
);

CREATE TABLE claim_history
(
id int(11) unsigned AUTO_INCREMENT,
capture_history_id int(11) unsigned,
claimed_by varchar(60),
claimed_at datetime,
duration_claimed time,
PRIMARY KEY(id),
FOREIGN KEY(claimed_by) REFERENCES guild(guild_id)
ON DELETE CASCADE,
FOREIGN KEY(capture_history_id) REFERENCES capture_history(id)
ON DELETE CASCADE
);

CREATE TABLE upgrade_history
(
id int(11) unsigned AUTO_INCREMENT,
timeStamp datetime,
capture_history_id int(11) unsigned,
upgrade_id int(4),
PRIMARY KEY (id),
FOREIGN KEY(upgrade_id) REFERENCES objective_upgrade(id),
FOREIGN KEY(capture_history_id) REFERENCES capture_history(id)
ON DELETE CASCADE
);

CREATE TABLE yak_history
(
id int(11) unsigned AUTO_INCREMENT,
timeStamp datetime,
capture_history_id int(11) unsigned,
num_yaks int(4),
PRIMARY KEY (id),
FOREIGN KEY(capture_history_id) REFERENCES capture_history(id)
ON DELETE CASCADE
);

CREATE TABLE server_linking
(
id int(11) unsigned AUTO_INCREMENT,
match_detail_id int(11) unsigned,
server_id int(4),
server_color enum("Red","Blue","Green"),
server_lead boolean,
server_population varchar(15),
PRIMARY KEY(id),
FOREIGN KEY(match_detail_id) REFERENCES match_detail(id)
ON DELETE CASCADE,
FOREIGN KEY(server_id) REFERENCES server_info(server_id)
);

CREATE TABLE log_code
(
id int(11),
type varchar(100),
message varchar(255),
PRIMARY KEY (id)
);

CREATE TABLE supply_route
(
id int(11) unsigned AUTO_INCREMENT,
from_obj varchar(10),
to_obj varchar(10),
estimated_travel_time float(3,1),
PRIMARY KEY (id),
FOREIGN KEY (from_obj) REFERENCES objective(obj_id),
FOREIGN KEY (to_obj) REFERENCES objective(obj_id)
);

CREATE USER 'gw2analyser'@'localhost' IDENTIFIED BY 'themirrorimage';
GRANT SELECT
ON Gw2Analyser.*
TO 'gw2analyser'@'localhost';
CREATE USER 'gw2datacollector'@'localhost' IDENTIFIED BY 'egamirrorimeht';
GRANT SELECT, INSERT, UPDATE, DELETE
ON Gw2Analyser.*
TO 'gw2datacollector'@'localhost';
CREATE USER 'gw2admin'@'localhost' IDENTIFIED BY 'J0rDa1n';
GRANT SELECT, INSERT, UPDATE, CREATE, DROP, ALTER, DELETE
ON Gw2Analyser.*
TO 'gw2admin'@'localhost';