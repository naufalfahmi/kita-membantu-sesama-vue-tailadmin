-- Add alias column to program_share_types table
ALTER TABLE program_share_types ADD COLUMN alias VARCHAR(255) NULL AFTER `key`;
CREATE INDEX idx_program_share_types_alias ON program_share_types(alias, deleted_at);

-- Populate default aliases for existing data
UPDATE program_share_types SET alias = 'Program' WHERE `key` = 'program' AND deleted_at IS NULL;
UPDATE program_share_types SET alias = 'Operasional' WHERE `key` = 'ops_2' AND deleted_at IS NULL;
UPDATE program_share_types SET alias = 'Gaji Karyawan' WHERE `key` = 'ops_1' AND deleted_at IS NULL;

-- Verify changes
SELECT id, name, `key`, alias FROM program_share_types WHERE deleted_at IS NULL ORDER BY alias;
