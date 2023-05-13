delete from ospos_daily_total;
delete from ospos_inventory;
delete from ospos_item_quantities;

delete from ospos_items;
delete from ospos_items_taxes;
delete from ospos_messages;
delete from ospos_people where person_id IN (select person_id from ospos_customers);
delete from ospos_customers;

delete from ospos_receivings;
delete from ospos_receivings_items;
delete from ospos_reminders;
delete from ospos_sales;
delete from ospos_sessions;
delete from ospos_sales_items_taxes;
delete from ospos_sales_payments;

delete from ospos_sales_suspended;
delete from ospos_sales_suspended_items;
delete from ospos_sales_suspended_items_taxes;
delete from ospos_sales_suspended_payments;
delete from ospos_sessions;
delete from ospos_sms_sale;
delete from ospos_total;
delete from ospos_sales_items;
delete from ospos_test;



/*ALTER TABLE `ospos_items` ADD `ref_item_id` INT(10) NOT NULL DEFAULT '0' AFTER `code`; 

ALTER TABLE `ospos_items` ADD COLUMN IF NOT EXISTS `category_code` VARCHAR(255) NOT NULL DEFAULT '0' AFTER `item_id`;
UPDATE ospos_items SET category_code = replace(category, ' ', '_');
UPDATE ospos_items SET category_code = replace(category_code, "\'", "");
*/