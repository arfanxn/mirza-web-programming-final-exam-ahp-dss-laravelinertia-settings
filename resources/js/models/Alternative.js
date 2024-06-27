export default class Alternative {
    constructor(
        id = null,
        name = null,
        index = null,
        created_at = null,
        updated_at = null,
    ) {
        this.id = id;
        this.name = name;
        this.index = index;
        this.created_at = created_at;
        this.updated_at = updated_at;
    }
}
