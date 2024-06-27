export default class PerformanceScore {
    constructor(
        id = null,
        alternative_id = null,
        alternative = null,
        criterion_id = null,
        criterion = null,
        value = null,
        created_at = null,
        updated_at = null,
    ) {
        this.id = id;
        this.alternative_id = alternative_id;
        this.alternative = alternative;
        this.criterion_id = criterion_id;
        this.criterion = criterion;
        this.value = value;
        this.created_at = created_at;
        this.updated_at = updated_at;
    }
}
