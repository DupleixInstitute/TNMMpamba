<?php
     //initialise customer information variables
	 $loan_category                     = NULL;
	 $cif								= NULL;
	 $one								= NULL;
	 $loan_amount						= NULL;
	 $deduct							= NULL;
	 $property_type						= NULL;
	 $open_market_value					= NULL;
	 $loan_type							= NULL;
	 $loan_maturity						= NULL;
	 $rate_type							= NULL;
	 $irate								= NULL;
	 $insurance_replacement				= NULL;
	 $insurance_premium					= NULL;
	 $loan_installment					= NULL;
	 $loanandinsurance					= NULL;
	 $customer_type						= NULL;
	 $rev4afford						= NULL;
	 $name								= NULL;
	 $surname							= NULL;
	 $spouse_name						= NULL;
	 $spouse_surname					= NULL;
	 $address							= NULL;
	 $street_add						= NULL;
	 $town								= NULL;
	 $country							= NULL;
	 $permanent_residence				= NULL;
	 $location							= NULL;
	 $day								= NULL;
	 $month								= NULL;
	 $year								= NULL;
	 $day2								= NULL;
	 $month2							= NULL;
	 $year2								= NULL;
	 $day22								= NULL;
	 $month22							= NULL;
	 $year22							= NULL;
	 $day23								= NULL;
	 $month23							= NULL;
	 $year23							= NULL;
	 $nationality						= NULL;
	 $marital_status					= NULL;
	 $marital_contract_type				= NULL;
	 $no_of_children					= NULL;
	 $to18								= NULL;
	 $to26								= NULL;
	 $to12								= NULL;
	 $aunts_uncles_cousins				= NULL;
	 $other								= NULL;
	 $years_at_address					= NULL;
	 $rent								= NULL;
	 $education							= NULL;
	 $professional						= NULL;
	 $employment						= NULL;
	 $years_at_job						= NULL;
	 $revenues							= NULL;
	 $annual_sal						= NULL;
	 $fixed_perm_allowances				= NULL;
	 $total_rev_for_afford				= NULL;
	 $hp_annual_sal						= NULL;
	 $hp_fixed_perm_allowances			= NULL;
	 $hp_total_rev_for_afford			= NULL;
	 $total_allowable_household_revenues= NULL;
	 $mainbank							= NULL;
	 $secondbank						= NULL;
	 $thirdbank							= NULL;
	 $Savings							= NULL;
	 $Deposit							= NULL;
	 $Share								= NULL;
	 $ST								= NULL;
	 $Mortgages							= NULL;
	 $why_renegotiate					= NULL;
	 $itc_ref							= NULL;
	 $itc_sub_no						= NULL;
	 $paid_debts						= NULL;
	 $judgement							= NULL;
	 $gender							= NULL;
	 $default_data						= NULL;
	 $trace_alerts						= NULL;
	 $number_of_dependants				= NULL;
	 $dependants_at_home				= NULL;
	 $relationship						= NULL;
	 $total_bbs_products				= NULL;
	 $renegotiate						= NULL;
	 $loan_arrears						= NULL;
	 $cards_held						= NULL;
	 $card_held_since					= NULL;
	 $loans_outstanding					= NULL;
	 $affordability_ratio				= NULL;
	 $blacklisted						= NULL;
	 $fraud_alert						= NULL;
	 $ltv_policy						= NULL;
	 $affordability_policy				= NULL;
	 $ltv								= NULL;
	 $w_education_level_score			= NULL;
	 $age_score 						= NULL;
	 $card_held_since_score 			= NULL;
	 $default_data_score 				= NULL;
	 $dependants_at_home_score 			= NULL;
	 $education_level_score 			= NULL;
	 $employment_score 					= NULL;
	 $judgement_score 					= NULL;
	 $loan_arrears_score 				= NULL;
	 $loan_maturity_score				= NULL;
	 $loans_outstanding_score 			= NULL;
	 $location_score					= NULL;
	 $no_of_children_score 				= NULL;
	 $paid_debts_score 					= NULL;
	 $professional_score 				= NULL;
	 $relationship_score 				= NULL;
	 $renegotiate_score					= NULL;
	 $revenues_score 					= NULL;
	 $total_bbs_products_score 			= NULL;
	 $total_ICT 						= NULL;
	 $total_length 						= NULL;
	 $total_personal 					= NULL;
	 $total_personal					= NULL;
	 $trace_alerts_score				= NULL;
	 $w_age_score 						= NULL;
	 $w_card_held_since_score 			= NULL;
	 $w_default_data_score 				= NULL;
	 $w_dependants_at_home_score 		= NULL;
	 $w_education_level_score 			= NULL;
	 $w_employment_score 				= NULL;
	 $w_judgement_score 				= NULL;
	 $w_loan_arrears_score 				= NULL;
	 $w_loan_maturity_score				= NULL;
	 $w_loans_outstanding_score			= NULL;
	 $w_location_score					= NULL;
	 $w_no_of_children_score 			= NULL;
	 $w_paid_debts_score 				= NULL;
	 $w_professional_score 				= NULL;
	 $w_relationship_score 				= NULL;
	 $w_renegotiate_score				= NULL;
	 $w_revenues_score 					= NULL;
	 $deduct_score 					    = NULL;
	 $w_total_bbs_products_score 		= NULL;
	 $w_total_ICT 						= NULL;
	 $w_total_length 					= NULL;
	 $w_total_personal 					= NULL;
	 $w_total_personal					= NULL;
	 $w_trace_alerts_score				= NULL;
	 $w_years_at_job_score 				= NULL;
	 $years_at_job_score 				= NULL;
	 $affordability_ratio_score 		= NULL;
	 $marital_status_score 				= NULL;
	 $rate_type_score 					= NULL;
	 $score								= NULL;
	 $total_ICT							= NULL;
	 $total_length						= NULL;
	 $total_personal					= NULL;
	 $total_personal					= NULL;
	 $total_product						= NULL;
	 $total_product 					= NULL;
	 $w_affordability_ratio_score 		= NULL;
	 $w_marital_status_score 			= NULL;
	 $w_rate_type_score 				= NULL;
	 $w_score							= NULL;
	 $w_total_ICT						= NULL;
	 $w_total_length					= NULL;
	 $w_total_personal					= NULL;
	 $w_total_personal					= NULL;
	 $w_total_product					= NULL;
	 $w_total_product 					= NULL;
	 $installment1						= NULL;
	 $ainstallment1						= NULL;
	 $ainstallment2						= NULL;
	 $binstallment1						= NULL;
	 $binstallment2						= NULL;
	 $binstallment3						= NULL;
	 $cinstallment1						= NULL;
	 $cinstallment2						= NULL;
	 $cinstallment3						= NULL;
	 $cinstallment4						= NULL;
	 $perm_res							= NULL;
	 $tax2								= NULL;
	 $tax								= NULL;
	 $email								= NULL;
	 $nationality_comment               = NULL;

?>