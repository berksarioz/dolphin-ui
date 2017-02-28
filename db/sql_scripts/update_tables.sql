/* Adding concentration and duration columns to ngs_sample_conds */
ALTER TABLE ngs_sample_conds ADD concentration VARCHAR( 255 )
ALTER TABLE ngs_sample_conds ADD duration VARCHAR( 255 )
ALTER TABLE ngs_sample_conds ADD concentration_unit VARCHAR( 255 )
ALTER TABLE ngs_sample_conds ADD duration_unit VARCHAR( 255 )


/* temporary ngs_conds table */
CREATE TABLE new_ngs_conds (
  id INT(11) NOT NULL AUTO_INCREMENT,
  cond_symbol VARCHAR(45),
  condition VARCHAR(45),
  PRIMARY KEY (id)
)

/* get rid of duplicates grouping by condition */
INSERT INTO new_ngs_conds
SELECT * FROM ngs_conds GROUP BY ngs_conds.condition


/* temporary ngs_sample_conds table */
CREATE TABLE new_ngs_sample_conds LIKE ngs_sample_conds
INSERT INTO new_ngs_sample_conds SELECT * FROM ngs_sample_conds;


/* add the conditions to the new table to pick the correct condtion id */
UPDATE new_ngs_sample_conds
SET new_ngs_sample_conds.condition_name=(SELECT ngs_conds.condition
  FROM ngs_conds
  WHERE ngs_conds.id=new_ngs_sample_conds.cond_id);

  /* cond_id 46 need to be 14, (49, 60, 61) => (47), 63 => 55 */
  /* now replace the cond_id's by condition_names by looking up from
   new_ngs_conds table which doesn't have the duplicates */
   UPDATE new_ngs_sample_conds
   SET new_ngs_sample_conds.cond_id=(SELECT new_ngs_conds.id
     FROM new_ngs_conds
     WHERE new_ngs_conds.condition=new_ngs_sample_conds.condition_name)


ALTER TABLE new_ngs_sample_conds
DROP COLUMN condition_name

DROP TABLE ngs_sample_conds
CREATE TABLE ngs_sample_conds LIKE new_ngs_sample_conds
INSERT INTO ngs_sample_conds SELECT * FROM new_ngs_sample_conds GROUP BY sample_id, cond_id

/* Allow only one row to have the same pair of sample and condition */
ALTER TABLE ngs_sample_conds ADD UNIQUE (
   sample_id,
   cond_id
);

/* recreate ngs_conds */
DROP TABLE ngs_conds
CREATE TABLE ngs_conds LIKE new_ngs_conds
INSERT INTO ngs_conds SELECT * FROM new_ngs_conds
