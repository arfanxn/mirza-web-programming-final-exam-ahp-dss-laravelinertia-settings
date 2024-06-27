export default class PairwiseComparison {
    constructor(
        id = null,
        primary_criterion = null,
        primary_criterion_id = null,
        secondary_criterion = null,
        secondary_criterion_id = null,
        value = null,
        created_at = null,
        updated_at = null,
    ) {
        this.id = id;
        this.primary_criterion = primary_criterion;
        this.primary_criterion_id = primary_criterion_id;
        this.secondary_criterion = secondary_criterion;
        this.secondary_criterion_id = secondary_criterion_id;
        this.value = value;
        this.created_at = created_at;
        this.updated_at = updated_at;
    }
}
