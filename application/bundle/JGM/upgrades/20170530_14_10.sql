CREATE INDEX IF NOT EXISTS adminrights_admin_id_module_id_idx ON adminrights USING btree(admin_id, module_id);
CREATE INDEX IF NOT EXISTS account_lower_account_id ON account USING btree (LOWER(account));
CREATE INDEX IF NOT EXISTS account_clientbank_id_idx ON account USING btree (clientbank_id);
CREATE INDEX clientbank_lower_clientbank_id ON clientbank USING btree (clientbank);


ALTER table userlog add column user_id_added integer;
ALTER table userlog add column date_added timestamp;