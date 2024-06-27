export default class Criterion {
    constructor(
        id = null,
        goal_id = null,
        name = null,
        impact_type = null,
        index = null,
        weight = null,
        created_at = null,
        updated_at = null,
    ) {
        this.id = id;
        this.goal_id = goal_id;
        this.name = name;
        this.impact_type = impact_type;
        this.index = index;
        this.weight = weight;
        this.created_at = created_at;
        this.updated_at = updated_at;
    }
}
