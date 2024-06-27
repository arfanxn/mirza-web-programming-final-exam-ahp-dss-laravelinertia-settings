CREATE OR REPLACE VIEW goal_view AS SELECT * FROM goals WHERE goals.id = '01j17kxyy7e89jn6q3t9zgk523' ;
CREATE OR REPLACE VIEW goal_id_view AS SELECT id FROM goal_view;

CREATE OR REPLACE VIEW criteria_view AS SELECT * FROM criteria WHERE goal_id = (SELECT id FROM goal_view);
CREATE OR REPLACE VIEW count_criteria_view AS SELECT COUNT(criteria.id) AS count FROM criteria_view AS criteria;



CREATE OR REPLACE VIEW alternatives_view AS SELECT * FROM alternatives WHERE goal_id = (SELECT id FROM goal_view);


CREATE OR REPLACE VIEW pss_view AS
SELECT
	performance_scores.id,
	performance_scores.alternative_id,
	performance_scores.criterion_id,
	alternatives.name AS alternative_name,
	criteria.name AS criterion_name,
	alternatives.index AS alternative_index,
	criteria.index AS criterion_index,
	criteria.impact_type AS criterion_impact_type,
	performance_scores.`value`
FROM performance_scores
JOIN criteria ON criteria.id = performance_scores.criterion_id
JOIN alternatives ON alternatives.id = performance_scores.alternative_id
JOIN goals ON goals.id = criteria.goal_id
WHERE goals.id = (SELECT id FROM goal_view);


CREATE OR REPLACE VIEW pwcs_view AS
SELECT
	pairwise_comparisons.id,
	pairwise_comparisons.primary_criterion_id,
	pairwise_comparisons.secondary_criterion_id,
	primary_criteria.`name` AS primary_criterion_name,
	secondary_criteria.`name` AS secondary_criterion_name,
	primary_criteria.`index` AS primary_criterion_index,
	secondary_criteria.`index` AS secondary_criterion_index,
	pairwise_comparisons.`value`
FROM pairwise_comparisons
JOIN criteria AS primary_criteria ON primary_criteria.id = pairwise_comparisons.primary_criterion_id
JOIN criteria AS secondary_criteria ON secondary_criteria.id = pairwise_comparisons.secondary_criterion_id
JOIN goals ON goals.id = primary_criteria.goal_id
WHERE goals.id = (SELECT id FROM goal_view);


CREATE OR REPLACE VIEW pwcs_columns_totals_view AS SELECT
	secondary_criteria.`index` AS secondary_criterion_index,
	SUM(pwcs.`value`) as secondary_criterion_total
FROM pwcs_view as pwcs
JOIN criteria AS primary_criteria ON primary_criteria.id = pwcs.primary_criterion_id
JOIN criteria AS secondary_criteria ON secondary_criteria.id = pwcs.secondary_criterion_id
GROUP BY secondary_criteria.`index`;


CREATE OR REPLACE VIEW norm_pwcs_view AS SELECT
	pwcs.id,
	pwcs.primary_criterion_id,
	pwcs.secondary_criterion_id,
	pwcs.primary_criterion_name,
	pwcs.secondary_criterion_name,
	pwcs.primary_criterion_index,
	pwcs.secondary_criterion_index,
	pwcs.`value`,
	pwcs.`value` / pwcs_columns_totals.secondary_criterion_total AS norm_value
FROM pwcs_view AS pwcs
JOIN pwcs_columns_totals_view AS pwcs_columns_totals ON
	pwcs_columns_totals.secondary_criterion_index = pwcs.secondary_criterion_index
GROUP BY pwcs.primary_criterion_index, pwcs.secondary_criterion_index, pwcs.id;


CREATE OR REPLACE VIEW norm_pwcs_columns_totals_view AS SELECT
	secondary_criteria.`index` AS secondary_criterion_index,
	SUM(pwcs.`norm_value`) as secondary_criterion_norm_total
FROM norm_pwcs_view as pwcs
JOIN criteria AS primary_criteria ON primary_criteria.id = pwcs.primary_criterion_id
JOIN criteria AS secondary_criteria ON secondary_criteria.id = pwcs.secondary_criterion_id
GROUP BY secondary_criteria.`index`;


CREATE OR REPLACE VIEW pwcs_vectors_view AS
SELECT
	norm_pwcs.primary_criterion_index,
	SUM(norm_pwcs.`norm_value`) AS primary_criterion_vector
FROM norm_pwcs_view AS norm_pwcs
GROUP BY norm_pwcs.primary_criterion_index;


CREATE OR REPLACE VIEW pwcs_weights_view AS
SELECT
	pwcs_vectors.primary_criterion_index,
	(pwcs_vectors.primary_criterion_vector / (SELECT * FROM count_criteria_view)) AS primary_criterion_weight
FROM pwcs_vectors_view AS pwcs_vectors
GROUP BY pwcs_vectors.primary_criterion_index;


CREATE OR REPLACE VIEW pwcs_eigen_values_view AS
SELECT
	sq.primary_criterion_index,
	SUM(sq.primary_criterion_multiplied_by_weight) AS primary_criterion_eigen_value
FROM (
	SELECT
		pwcs.primary_criterion_name,
		pwcs.primary_criterion_index,
		pwcs.secondary_criterion_name,
		pwcs.secondary_criterion_index,
		pwcs.`value`,
		pwcs_weights.primary_criterion_weight,
		(pwcs.`value` * pwcs_weights.primary_criterion_weight) AS primary_criterion_multiplied_by_weight
	FROM pwcs_view as pwcs
	JOIN pwcs_weights_view AS pwcs_weights
		ON pwcs_weights.primary_criterion_index = pwcs.secondary_criterion_index
) AS sq
GROUP BY sq.primary_criterion_index;


CREATE OR REPLACE VIEW pwcs_vectors_weights_eigen_values_view AS
SELECT
	primary_criteria.id AS primary_criterion_id,
	primary_criteria.name AS primary_criterion_name,
	primary_criteria.index AS primary_criterion_index,
	pwcs_vectors.primary_criterion_vector AS primary_criterion_vector,
	pwcs_weights.primary_criterion_weight AS primary_criterion_weight,
	pwcs_eigen_values.primary_criterion_eigen_value AS primary_criterion_eigen_value
FROM criteria_view AS primary_criteria
JOIN pwcs_vectors_view AS pwcs_vectors
	ON pwcs_vectors.primary_criterion_index = primary_criteria.`index`
JOIN pwcs_weights_view AS pwcs_weights
	ON pwcs_weights.primary_criterion_index = primary_criteria.`index`
JOIN pwcs_eigen_values_view AS pwcs_eigen_values
	ON pwcs_eigen_values.primary_criterion_index = primary_criteria.`index`;



CREATE OR REPLACE VIEW pwcs_t_statistic_view AS
SELECT
	(SUM(sq.primary_criterion_divided_by_eigen_value) / (SELECT * FROM count_criteria_view)) as t_statistic
FROM (
	SELECT
		pwcs_weights.primary_criterion_index,
		pwcs_weights.primary_criterion_weight,
		pwcs_eigen_values.primary_criterion_eigen_value,
		(pwcs_eigen_values.primary_criterion_eigen_value / pwcs_weights.primary_criterion_weight) AS primary_criterion_divided_by_eigen_value
	FROM pwcs_weights_view AS pwcs_weights
	JOIN pwcs_eigen_values_view AS pwcs_eigen_values
		ON pwcs_eigen_values.primary_criterion_index = pwcs_weights.primary_criterion_index
) AS sq;


CREATE OR REPLACE VIEW pwcs_confidence_interval_view AS
SELECT
	((t_statistic - (SELECT * FROM count_criteria_view)) / ((SELECT * FROM count_criteria_view) - 1))
		AS confidence_interval
FROM pwcs_t_statistic_view;


CREATE OR REPLACE VIEW pwcs_risk_interval_view AS
SELECT
	CASE
		WHEN count_criteria.count = 3 THEN 0.58
		WHEN count_criteria.count = 4 THEN 0.9
		WHEN count_criteria.count = 5 THEN 1.12
		WHEN count_criteria.count = 6 THEN 1.24
		WHEN count_criteria.count = 7 THEN 1.32
		ELSE -1 -- something is wrong
	END AS risk_interval
FROM count_criteria_view AS count_criteria;


CREATE OR REPLACE VIEW pwcs_consistent_interval_view AS
SELECT
	(pwcs_confidence_interval.confidence_interval / (SELECT * FROM pwcs_risk_interval_view)) AS consistent_interval
FROM pwcs_confidence_interval_view AS pwcs_confidence_interval;


CREATE OR REPLACE VIEW pwcs_consistent_interval_check_view AS
SELECT
	pwcs_consistent_interval.consistent_interval,
	CASE
		WHEN consistent_interval <= 0.1 THEN true
		ELSE false
	END AS is_consistent
FROM pwcs_consistent_interval_view AS pwcs_consistent_interval;


CREATE OR REPLACE VIEW pwcs_t_ci_ri_cr_view AS
SELECT
	(SELECT t_statistic FROM pwcs_t_statistic_view) AS t_statistic,
	(SELECT confidence_interval FROM pwcs_confidence_interval_view) AS confidence_interval,
	(SELECT risk_interval FROM pwcs_risk_interval_view) AS risk_interval,
	(SELECT consistent_interval FROM pwcs_consistent_interval_view) AS consistent_interval,
	(SELECT is_consistent FROM pwcs_consistent_interval_check_view) AS is_consistent;


CREATE OR REPLACE VIEW pss_criteria_min_max_view AS
SELECT
	pss.criterion_index,
	MIN(pss.`value`) AS criterion_min,
	MAX(pss.`value`) AS criterion_max
FROM pss_view AS pss
GROUP BY pss.criterion_index;


CREATE OR REPLACE VIEW pss_caas_vectors_view AS
SELECT
	pss.alternative_id,
	pss.criterion_id,
	pss.alternative_name,
	pss.criterion_name,
	pss.alternative_index,
	pss.criterion_index,
	pss.`value`,
	CASE
		WHEN pss.criterion_impact_type = 0 THEN (pss_criteria_min_max.criterion_min / pss.`value`)
		ELSE (pss.`value` / pss_criteria_min_max.criterion_max)
	END AS vector
FROM pss_view AS pss
JOIN pss_criteria_min_max_view AS pss_criteria_min_max
	ON pss_criteria_min_max.criterion_index = pss.criterion_index;


CREATE OR REPLACE VIEW pss_cass_criteria_vectors_sum_view AS
SELECT
	pss_caas_vectors.criterion_index AS criterion_index,
	SUM(pss_caas_vectors.vector) as criterion_vector_sum
FROM pss_caas_vectors_view AS pss_caas_vectors
GROUP BY pss_caas_vectors.criterion_index;


CREATE OR REPLACE VIEW pss_caas_weights_view AS
SELECT
	pss.alternative_id,
	pss.criterion_id,
	pss.alternative_name,
	pss.criterion_name,
	pss.alternative_index,
	pss.criterion_index,
	pss.`value`,
	pss_caas_vectors.vector AS caas_vector,
	(pss_caas_vectors.vector / pss_cass_criteria_vectors_sum.criterion_vector_sum) AS caas_weight
FROM pss_view AS pss
JOIN pss_caas_vectors_view AS pss_caas_vectors
	ON pss_caas_vectors.alternative_index = pss.alternative_index AND pss_caas_vectors.criterion_index = pss.criterion_index
JOIN pss_cass_criteria_vectors_sum_view AS pss_cass_criteria_vectors_sum
	ON pss_cass_criteria_vectors_sum.criterion_index = pss.criterion_index;


CREATE OR REPLACE VIEW pss_caas_vectors_n_weights_view AS
SELECT
	sq.alternative_id,
	sq.criterion_id,
	sq.alternative_name,
	sq.criterion_name,
	sq.alternative_index,
	sq.criterion_index,
	sq.caas_vector,
	sq.caas_weight
FROM pss_caas_weights_view AS sq;


CREATE OR REPLACE VIEW pss_caas_weights_multiplied_by_pwcs_weights_view AS
SELECT
	pss_caas_weights.alternative_id,
	pss_caas_weights.criterion_id,
	pss_caas_weights.alternative_name,
	pss_caas_weights.criterion_name,
	pss_caas_weights.alternative_index,
	pss_caas_weights.criterion_index,
	(pss_caas_weights.caas_weight * pwcs_weights.primary_criterion_weight) AS caas_weight_multiplied_by_pwcs_weight
FROM pss_caas_weights_view AS pss_caas_weights
JOIN pwcs_weights_view AS pwcs_weights
	ON pwcs_weights.primary_criterion_index = pss_caas_weights.criterion_index;


CREATE OR REPLACE VIEW pss_caas_wmbpw_alternatives_sum_view AS
SELECT
	pss_caas_weights_multiplied_by_pwcs_weights.alternative_index AS alternative_index,
	SUM(pss_caas_weights_multiplied_by_pwcs_weights.caas_weight_multiplied_by_pwcs_weight) AS alternative_sum
FROM pss_caas_weights_multiplied_by_pwcs_weights_view AS pss_caas_weights_multiplied_by_pwcs_weights
GROUP BY pss_caas_weights_multiplied_by_pwcs_weights.alternative_index;


CREATE OR REPlACE VIEW rankingization_view AS
SELECT
	alternatives.id AS alternative_id,
	alternatives.name AS alternative_name,
	alternatives.`index` AS alternative_index,
	pss_caas_wmbpw_alternatives_sum.alternative_sum AS alternative_score,
	RANK() OVER(ORDER BY pss_caas_wmbpw_alternatives_sum.alternative_sum DESC) alternative_rank
FROM pss_caas_wmbpw_alternatives_sum_view AS pss_caas_wmbpw_alternatives_sum
JOIN alternatives_view AS alternatives
	ON alternatives.`index` = pss_caas_wmbpw_alternatives_sum.alternative_index;


SELECT * FROM criteria_view;


SELECT * FROM alternatives_view;


SELECT * FROM pss_view;


SELECT * FROm pwcs_view;


SELECT * FROM norm_pwcs_view;


SELECT * FROM pwcs_vectors_weights_eigen_values_view;


SELECT * FROM pwcs_t_ci_ri_cr_view;


(SELECT * FROM pss_caas_vectors_n_weights_view) ORDER BY criterion_index;


SELECT
	primary_criterion.`id`,
	primary_criterion.`name`,
	pwcs_weights.primary_criterion_index,
	pwcs_weights.primary_criterion_weight
FROM pwcs_weights_view AS pwcs_weights JOIN criteria_view AS primary_criterion
	ON primary_criterion.`index` = pwcs_weights.primary_criterion_index;


SELECT
	pss_caas_weights.alternative_id, pss_caas_weights.criterion_id,
	pss_caas_weights.alternative_name, pss_caas_weights.criterion_name,
	pss_caas_weights.alternative_index, pss_caas_weights.criterion_index,
	pss_caas_weights.caas_weight
FROM pss_caas_weights_view AS pss_caas_weights
ORDER BY pss_caas_weights.criterion_index;


SELECT * FROM pss_caas_weights_multiplied_by_pwcs_weights_view;


SELECT * FROM rankingization_view;
