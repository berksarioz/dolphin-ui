ALTER TABLE ngs_sample_conds ADD concentration VARCHAR( 255 )
ALTER TABLE ngs_sample_conds ADD duration VARCHAR( 255 )



ALTER TABLE ngs_sample_conds ADD UNIQUE (
   sample_id,
   cond_id
);


CREATE TABLE new_ngs_conds (
  id INT(11) NOT NULL AUTO_INCREMENT,
  cond_symbol VARCHAR(45),
  condition VARCHAR(45),
  PRIMARY KEY (id)
)

INSERT INTO new_ngs_conds
SELECT * FROM ngs_conds GROUP BY ngs_conds.condition




INSERT INTO new_ngs_sample_conds SELECT * FROM ngs_sample_conds;

????
update new_ngs_sample_conds set new_ngs_sample_conds.condition_name=ngs_conds.condition
from new_ngs_sample_conds
join ngs_conds on (new_ngs_sample_conds.cond_id=ngs_conds.id)
????
