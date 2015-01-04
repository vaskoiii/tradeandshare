-- author: vaskoiii
-- description: strip the database of all private data and leave a skeleton database ( modified dates are unchanged )

-- todo fix up the sql statements that have weird starting ids

-- README
delete from ts_active_offer_user;
-- ts_boolean
delete from ts_channel where id != 1;
delete from ts_class where id != 1;
delete from ts_contact;
delete from ts_cost;
delete from ts_cycle;
-- ts_decision
-- ts_dialect
-- ts_direction
-- ts_display
-- ts_doc
-- ts_element
delete from ts_feed;
delete from ts_feedback;
-- ts_file
delete from ts_filer;
delete from ts_gauge_renewal;
-- ts_grade
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
-- ts_kind
delete from ts_link_contact_group;
delete from ts_link_contact_user;
delete from ts_link_tag;
delete from ts_link_team_user where user_id != 111;
delete from ts_location where id != 1;
delete from ts_lock;
delete from ts_login;
delete from ts_membership;
delete from ts_meripost;
delete from ts_meritopic;
-- ts_meritype
delete from ts_metail;
delete from ts_minder where user_id != 111;
delete from ts_news;
delete from ts_note;
delete from ts_offer;
-- ts_page
-- ts_phase
-- ts_point
delete from ts_pubkey;
-- ts_range
delete from ts_rating;
delete from ts_renewage;
delete from ts_renewal;
delete from ts_server where id != 1;
update ts_server set name='localhost';
delete from ts_status;
delete from ts_tag where id != 1;
delete from ts_team where user_id != 111;
-- ts_theme
-- ts_timeframe
delete from ts_transaction;
delete from ts_transfer;
delete from ts_user where id != 111;
delete from ts_user_more where id != 111;
update ts_user set password='98547af88af3d3aff2e1c10e430fd428', email='root@localhost';
delete from ts_visit;
-- allow anything that is not a tag
delete from ts_translation where kind_id = 11;
-- allow english only
delete from ts_translation where dialect_id != 2;
delete from ts_vote;       

-- additional cleanup
-- todo make description fields for elements optional
update ts_translation set description='' where kind_id in ( 14, 19, 12, 4, 1, 5, 18 ) ;
update ts_translation set description='' where description like '%\_%' ;

-- todo there are no default categories
