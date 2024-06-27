export default class Goal {
    constructor(
        id = null,
        user_id = null,
        title = null,
        slug = null,
        description = null,
        criteria = null,
        alternatives = null,
        performance_scores = null,
        pairwise_comparisons = null,
        created_at = null,
        updated_at = null,
    ) {
        this.id = id;
        this.user_id = user_id;
        this.title = title;
        this.slug = slug;
        this.description = description;
        this.criteria = criteria;
        this.alternatives = alternatives;
        this.pairwise_comparisons = pairwise_comparisons;
        this.performance_scores = performance_scores;
        this.created_at = created_at;
        this.updated_at = updated_at;
    }
}
