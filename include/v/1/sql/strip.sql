/*
Copyright 2003-2012 John Vasko III

This file is part of Trade and Share.

Trade and Share is free software: you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation, either version 3 of the License, or
(at your option) any later version.

Trade and Share is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with Trade and Share.  If not, see <http://www.gnu.org/licenses/>.
*/

-- Contents/Description: Use the following commands after a full import to get the skeleton db of TS starting from a full db import.

delete from ts_active_offer_user;
delete from ts_active_transfer_user;
delete from ts_contact;
delete from ts_feed;
delete from ts_feedback;
delete from ts_group;
delete from ts_incident;
delete from ts_index_invited_user where user_id != 111;
delete from ts_index_offer_user;
delete from ts_index_rating_user;
delete from ts_index_tag where tag_id != 1;
delete from ts_index_transfer_user;
delete from ts_invite where user_id != 111;
delete from ts_invited where source_user_id != 111;
delete from ts_item;
delete from ts_link_contact_group;
delete from ts_link_contact_user;
delete from ts_link_tag;
delete from ts_link_team_user where user_id != 111;
delete from ts_location where id != 1;
delete from ts_lock;
delete from ts_login;
delete from ts_meripost;
delete from ts_meritopic;
delete from ts_metail;
delete from ts_minder where user_id != 111;
delete from ts_news;
delete from ts_note;
delete from ts_offer;
delete from ts_rating;
delete from ts_server where id != 1;
delete from ts_tag where id != 1;
delete from ts_team where user_id != 111;
delete from ts_transfer;
delete from ts_translation where kind_id = 11;
delete from ts_user where id != 111;
delete from ts_user_more where id != 111;
delete from ts_visit;

-- set |root| email and password = tradeandshare
UPDATE `tso`.`ts_user` SET `email` = 'user@domain.com' WHERE `ts_user`.`id` =111 LIMIT 1;
UPDATE `tso`.`ts_user` SET `password` = '98547af88af3d3aff2e1c10e430fd428' WHERE `ts_user`.`id` =111 LIMIT 1;

-- remove other potentially sensitive info
UPDATE `tso`.`ts_invite` SET `email` = 'user@domain.com' WHERE `ts_invite`.`id` =291 LIMIT 1 ;
UPDATE `tso`.`ts_server` SET `name` = 'domain.com' WHERE `ts_server`.`id` =1 LIMIT 1 ;
